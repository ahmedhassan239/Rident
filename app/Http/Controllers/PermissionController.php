<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::all();
        return response()->json($permissions);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:permissions',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $permission = Permission::create(['name' => $request->name]);
        return response()->json($permission, 201);
    }

    public function show($id)
    {
        $permission = Permission::find($id);
        return response()->json($permission);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:permissions,name,'.$id,
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $permission = Permission::find($id);
        $permission->name = $request->name;
        $permission->save();

        return response()->json($permission);
    }

    public function destroy($id)
    {
        $permission = Permission::find($id);
        $permission->delete();
        return response()->json(null, 204);
    }
}
