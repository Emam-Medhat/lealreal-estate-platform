<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;

class ProjectDocumentController extends Controller
{
    public function index(Project $project)
    {
        return view('projects.documents.index', compact('project'));
    }
    public function create(Project $project)
    {
        return view('projects.documents.create', compact('project'));
    }
    public function store(Request $request, Project $project)
    {
        return back();
    }
    public function show(Project $project, $document)
    {
        return view('projects.documents.show', compact('project'));
    }
    public function edit(Project $project, $document)
    {
        return view('projects.documents.edit', compact('project'));
    }
    public function update(Request $request, Project $project, $document)
    {
        return back();
    }
    public function destroy(Project $project, $document)
    {
        return back();
    }
    public function download(Project $project, $document)
    {
        return back();
    }
    public function approve(Project $project, $document)
    {
        return back();
    }
    public function reject(Project $project, $document)
    {
        return back();
    }
    public function upload(Project $project, $document)
    {
        return back();
    }
    public function createVersion(Project $project, $document)
    {
        return back();
    }
    public function getVersions(Project $project, $document)
    {
        return response()->json([]);
    }
    public function preview(Project $project, $document)
    {
        return back();
    }
    public function search(Project $project)
    {
        return response()->json([]);
    }
}
