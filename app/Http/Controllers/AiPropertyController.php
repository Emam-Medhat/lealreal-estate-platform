<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AiPropertyController extends Controller
{
    public function save(Request $request, $id)
    {
        return response()->json(['message' => 'Property saved successfully']);
    }
}
