<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class FolderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    public function index()
    {
        $folders = Folder::with('children')->whereNull('parent_id')->get();
        return response()->json($folders);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'parent_id' => 'nullable|exists:folders,id'
        ]);

        $folder = Folder::create($request->only(['name', 'parent_id']));
        return response()->json($folder, 201);
    }

    public function show($id)
    {
        $folder = Folder::with('children.files')->findOrFail($id);
        return response()->json($folder);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:255'
        ]);

        $folder = Folder::findOrFail($id);
        $folder->update($request->only(['name']));
        return response()->json($folder);
    }

    public function destroy($id)
    {
        $folder = Folder::findOrFail($id);
        $folder->delete();
        return response()->json("Successfully Deleted", 204);
    }
}


