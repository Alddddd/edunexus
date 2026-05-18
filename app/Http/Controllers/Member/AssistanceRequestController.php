<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\AssistanceProgram;
use App\Models\AssistanceRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AssistanceRequestController extends Controller
{
    public function create()
    {
        $programs = AssistanceProgram::where('status', 'Active')->get();

        return view('member.assistance-requests.create', compact('programs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'program_id' => [
                'required',
                Rule::exists('assistance_programs', 'id')->where('status', 'Active'),
            ],
        ], [
            'program_id.exists' => 'Please select an active assistance program.',
        ]);

        $program = AssistanceProgram::findOrFail($request->program_id);

        $validated = $request->validate([
            'program_id' => [
                'required',
                Rule::exists('assistance_programs', 'id')->where('status', 'Active'),
            ],
            'requested_amount' => ['required', 'numeric', 'min:1', 'max:' . $program->maximum_amount],
            'reason' => ['nullable', 'string'],
        ], [
            'program_id.exists' => 'Please select an active assistance program.',
            'requested_amount.max' => 'The requested amount cannot exceed the selected program maximum of PHP ' . number_format($program->maximum_amount, 2) . '.',
        ]);

        $validated['member_id'] = auth()->id();
        $validated['status'] = 'Pending';

        AssistanceRequest::create($validated);

        return redirect()
            ->route('member.dashboard')
            ->with('success', 'Assistance request submitted successfully.');
    }
}
