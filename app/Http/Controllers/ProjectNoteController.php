<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;

class ProjectNoteController extends Controller
{
    public function index(Project $project)
    {
        return view('projects.notes.index', compact('project'));
    }
    public function create(Project $project)
    {
        return view('projects.notes.create', compact('project'));
    }
    public function store(Request $request, Project $project)
    {
        return back();
    }
    public function show(Project $project, $note)
    {
        return view('projects.notes.show', compact('project'));
    }
    public function edit(Project $project, $note)
    {
        return view('projects.notes.edit', compact('project'));
    }
    public function update(Request $request, Project $project, $note)
    {
        return back();
    }
    public function destroy(Project $project, $note)
    {
        return back();
    }
    public function toggleVisibility(Project $project, $note)
    {
        return back();
    }
    public function pin(Project $project, $note)
    {
        return back();
    }
    public function unpin(Project $project, $note)
    {
        return back();
    }
    public function getPinned(Project $project)
    {
        return response()->json([]);
    }
    public function search(Project $project)
    {
        return response()->json([]);
    }
    public function getByVisibility(Project $project)
    {
        return response()->json([]);
    }
}
