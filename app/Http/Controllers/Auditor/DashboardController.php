<?php

namespace App\Http\Controllers\Auditor;

use App\Http\Controllers\Controller;
use App\Models\AssistanceRequest;
use App\Models\BlockchainTransaction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $recentTransactions = $this->verificationQuery($request)
            ->paginate(5)
            ->withQueryString();

        return view('auditor.dashboard', [
            'totalClaims' => AssistanceRequest::where('is_claimed', true)->count(),
            'confirmedProofs' => BlockchainTransaction::where('blockchain_status', 'Confirmed')->count(),
            'pendingProofs' => BlockchainTransaction::where('blockchain_status', 'Pending')->count(),
            'recentTransactions' => $recentTransactions,
            'filters' => $request->only(['search', 'status', 'type']),
        ]);
    }

    public function exportCsv(Request $request)
    {
        $filename = 'edunexus-auditor-verification-records-' . now()->format('Y-m-d') . '.csv';
        $transactions = $this->currentPageRecords($request);

        return Response::streamDownload(function () use ($transactions) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'proof_type',
                'reference',
                'status',
                'timestamp',
                'full_proof_hash',
                'morph_verification_status',
            ]);

            foreach ($transactions as $transaction) {
                fputcsv($handle, $this->exportRow($transaction));
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function exportPdf(Request $request)
    {
        $rows = $this->currentPageRecords($request)
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

    private function currentPageRecords(Request $request)
    {
        return $this->verificationQuery($request)
            ->forPage(max((int) $request->query('page', 1), 1), 5)
            ->get();
    }

    private function verificationQuery(Request $request): Builder
    {
        $query = BlockchainTransaction::query()
            ->latest('recorded_at')
            ->latest('id');

        if (filled($request->query('status'))) {
            $query->where('blockchain_status', $request->query('status'));
        }

        if (filled($request->query('type'))) {
            $query->where('transaction_type', $request->query('type'));
        }

        if (filled($request->query('search'))) {
            $search = trim((string) $request->query('search'));

            $query->where(function (Builder $query) use ($search) {
                $query->where('reference_code', 'like', '%' . $search . '%')
                    ->orWhere('transaction_hash', 'like', '%' . $search . '%')
                    ->orWhere('payload', 'like', '%' . $search . '%');
            });
        }

        return $query;
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
