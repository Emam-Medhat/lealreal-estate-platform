<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InspectionPhotoController extends Controller
{
    public function index()
    {
        return view('inspection-photos.index');
    }
    public function create()
    {
        return view('inspection-photos.create');
    }
    public function store(Request $request)
    {
        return back();
    }
    public function show($photo)
    {
        return view('inspection-photos.show');
    }
    public function edit($photo)
    {
        return view('inspection-photos.edit');
    }
    public function update(Request $request, $photo)
    {
        return back();
    }
    public function destroy($photo)
    {
        return back();
    }
    public function setPrimary($photo)
    {
        return back();
    }
    public function togglePublic($photo)
    {
        return back();
    }
    public function download($photo)
    {
        return back();
    }
    public function addTag($photo)
    {
        return back();
    }
    public function removeTag($photo, $tag)
    {
        return back();
    }
    public function bulkUpload(Request $request)
    {
        return back();
    }
    public function bulkDelete(Request $request)
    {
        return back();
    }
    public function bulkUpdateTags(Request $request)
    {
        return back();
    }
    public function inspectionGallery($inspection)
    {
        return view('inspection-photos.gallery');
    }
    public function propertyGallery($property)
    {
        return view('inspection-photos.gallery');
    }
    public function export(Request $request)
    {
        return back();
    }
}
