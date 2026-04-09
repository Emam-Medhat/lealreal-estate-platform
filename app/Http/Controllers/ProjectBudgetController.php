<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;

class ProjectBudgetController extends Controller
{
    public function index(Project $project)
    {
        return view('projects.budgets.index', compact('project'));
    }
    public function create(Project $project)
    {
        return view('projects.budgets.create', compact('project'));
    }
    public function store(Request $request, Project $project)
    {
        return back();
    }
    public function show(Project $project, $budget)
    {
        return view('projects.budgets.show', compact('project'));
    }
    public function edit(Project $project, $budget)
    {
        return view('projects.budgets.edit', compact('project'));
    }
    public function update(Request $request, Project $project, $budget)
    {
        return back();
    }
    public function destroy(Project $project, $budget)
    {
        return back();
    }
    public function addExpense(Project $project, $budget)
    {
        return back();
    }
    public function addIncome(Project $project, $budget)
    {
        return back();
    }
    public function getExpenses(Project $project, $budget)
    {
        return response()->json([]);
    }
    public function getIncomes(Project $project, $budget)
    {
        return response()->json([]);
    }
    public function getUtilization(Project $project, $budget)
    {
        return response()->json([]);
    }
    public function getForecast(Project $project, $budget)
    {
        return response()->json([]);
    }
}
