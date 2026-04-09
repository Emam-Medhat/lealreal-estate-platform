<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;

class ProjectExpenseController extends Controller
{
    public function index(Project $project)
    {
        return view('projects.expenses.index', compact('project'));
    }
    public function create(Project $project)
    {
        return view('projects.expenses.create', compact('project'));
    }
    public function store(Request $request, Project $project)
    {
        return back();
    }
    public function show(Project $project, $expense)
    {
        return view('projects.expenses.show', compact('project'));
    }
    public function edit(Project $project, $expense)
    {
        return view('projects.expenses.edit', compact('project'));
    }
    public function update(Request $request, Project $project, $expense)
    {
        return back();
    }
    public function destroy(Project $project, $expense)
    {
        return back();
    }
    public function approve(Project $project, $expense)
    {
        return back();
    }
    public function reject(Project $project, $expense)
    {
        return back();
    }
    public function processPayment(Project $project, $expense)
    {
        return back();
    }
    public function uploadReceipt(Project $project, $expense)
    {
        return back();
    }
    public function downloadReceipt(Project $project, $expense)
    {
        return back();
    }
    public function getByCategory(Project $project)
    {
        return response()->json([]);
    }
    public function getSummary(Project $project)
    {
        return response()->json([]);
    }
}
