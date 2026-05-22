<?php

namespace App\Http\Controllers\Auditor;

use App\Http\Controllers\Controller;
use App\Models\AssistanceRequest;
use App\Models\BlockchainTransaction;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Response;

class DashboardController extends Controller
{
    public function index()
    {
        return view('auditor.dashboard', [
            'totalClaims' => AssistanceRequest::where('is_claimed', true)->count(),
            'confirmedProofs' => BlockchainTransaction::where('blockchain_status', 'Confirmed')->count(),
            'pendingProofs' => BlockchainTransaction::where('blockchain_status', 'Pending')->count(),
            'recentTransactions' => BlockchainTransaction::latest('recorded_at')->latest('id')->take(12)->get(),
        ]);
    }

    public function exportCsv()
    {
        $filename = 'edunexus-auditor-verification-records-' . now()->format('Y-m-d') . '.csv';

        return Response::streamDownload(function () {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'proof_type',
                'reference',
                'status',
                'timestamp',
                'full_proof_hash',
                'morph_verification_status',
            ]);

            BlockchainTransaction::query()
                ->latest('recorded_at')
                ->latest('id')
                ->chunk(200, function (EloquentCollection $transactions) use ($handle) {
                    foreach ($transactions as $transaction) {
                        fputcsv($handle, $this->exportRow($transaction));
                    }
                });

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function exportPdf()
    {
        $rows = BlockchainTransaction::query()
            ->latest('recorded_at')
            ->latest('id')
            ->take(100)
            ->get()
            ->map(fn (BlockchainTransaction $transaction) => $this->exportRow($transaction))
            ->all();

        $pdf = $this->simplePdf('EduNexUs Auditor Verification Records', [
            ['Proof Type', 'Reference', 'Status', 'Timestamp', 'Full Proof Hash', 'Morph Verification Status'],
            ...$rows,
        ]);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="edunexus-auditor-verification-records-' . now()->format('Y-m-d') . '.pdf"',
        ]);
    }

    private function exportRow(BlockchainTransaction $transaction): array
    {
        $payload = json_decode($transaction->payload ?: '[]', true) ?: [];

        return [
            $payload['event_type'] ?? data_get($payload, 'proof_bundle.event_type', $transaction->transaction_type),
            $transaction->reference_code ?: 'N/A',
            $payload['status'] ?? $transaction->blockchain_status,
            optional($transaction->recorded_at)->format('Y-m-d H:i:s') ?: '',
            $payload['proof_hash'] ?? $transaction->transaction_hash ?? '',
            $transaction->blockchain_status,
        ];
    }

    private function simplePdf(string $title, array $rows): string
    {
        $lines = [$title, 'Generated: ' . now()->format('Y-m-d H:i:s'), ''];

        foreach ($rows as $row) {
            $lines[] = implode(' | ', array_map(fn ($value) => preg_replace('/\s+/', ' ', (string) $value), $row));
        }

        $content = "BT\n/F1 9 Tf\n36 806 Td\n12 TL\n";

        foreach ($lines as $line) {
            foreach (str_split($line, 112) ?: [''] as $part) {
                $content .= '(' . str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $part) . ") Tj\nT*\n";
            }
        }

        $content .= 'ET';
        $objects = [
            "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n",
            "2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n",
            "3 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 842] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>\nendobj\n",
            "4 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>\nendobj\n",
            "5 0 obj\n<< /Length " . strlen($content) . " >>\nstream\n{$content}\nendstream\nendobj\n",
        ];

        $pdf = "%PDF-1.4\n";
        $offsets = [0];

        foreach ($objects as $object) {
            $offsets[] = strlen($pdf);
            $pdf .= $object;
        }

        $xrefOffset = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n0000000000 65535 f \n";

        for ($i = 1; $i <= count($objects); $i++) {
            $pdf .= str_pad((string) $offsets[$i], 10, '0', STR_PAD_LEFT) . " 00000 n \n";
        }

        return $pdf . "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\nstartxref\n{$xrefOffset}\n%%EOF";
    }
}
