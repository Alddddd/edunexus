<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssistanceProgram;
use Illuminate\Http\Request;

class AssistanceProgramController extends Controller
{
    public function index()
    {
        $programs = AssistanceProgram::latest()
            ->paginate(5)
            ->withQueryString();

        return view('admin.assistance-programs.index', compact('programs'));
    }

    public function create()
    {
        return view('admin.assistance-programs.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'program_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'merchant_category' => ['required', 'string', 'max:255'],
            'maximum_amount' => ['required', 'numeric', 'min:1'],
            'expiration_days' => ['required', 'integer', 'min:1'],
            'status' => ['required', 'in:Active,Inactive'],
        ]);

        $validated['created_by'] = auth()->id();

        AssistanceProgram::create($validated);

        return redirect()
            ->route('admin.assistance-programs.index')
            ->with('success', 'Assistance program created successfully.');
    }
}
