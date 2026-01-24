<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;

class ProjectRiskController extends Controller
{
    public function index(Project $project)
    {
        return view('projects.risks.index', compact('project'));
    }
    public function create(Project $project)
    {
        return view('projects.risks.create', compact('project'));
    }
    public function store(Request $request, Project $project)
    {
        return back();
    }
    public function show(Project $project, $risk)
    {
        return view('projects.risks.show', compact('project'));
    }
    public function edit(Project $project, $risk)
    {
        return view('projects.risks.edit', compact('project'));
    }
    public function update(Request $request, Project $project, $risk)
    {
        return back();
    }
    public function destroy(Project $project, $risk)
    {
        return back();
    }
    public function toggleStatus(Project $project, $risk)
    {
        return back();
    }
    public function updateLevel(Project $project, $risk)
    {
        return back();
    }
    public function addAction(Project $project, $risk)
    {
        return back();
    }
    public function removeAction(Project $project, $risk)
    {
        return back();
    }
    public function mitigate(Project $project, $risk)
    {
        return back();
    }
    public function getByLevel(Project $project)
    {
        return response()->json([]);
    }
    public function getByStatus(Project $project)
    {
        return response()->json([]);
    }
    public function getMatrix(Project $project)
    {
        return response()->json([]);
    }
    public function getAssessment(Project $project)
    {
        return response()->json([]);
    }
}
