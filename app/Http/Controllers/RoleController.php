<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;


class RoleController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth:api');
    // }
    public function index()
    {
        $roles = Role::with('permissions')->get();
        return response()->json($roles);
    }

    public function store(Request $request)
    {
        // dd(auth()->user());
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles',
            // 'user_id'=>'required',
            'permissions' => 'sometimes|array|exists:permissions,id'
        ]);
// dd($request->permissions);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $role = Role::create(['name' => $request->name]);
        // $user = User::find($request->user_id);
        // $user->assignRole($request->name);

        if ($request->has('permissions')) {
            $permissions = Permission::whereIn('id',$request->permissions)->pluck('name')->toArray();
            $role->syncPermissions($permissions);
          
        }

        return response()->json($role, 201);
    }

    public function show($id)
    {
        $role = Role::find($id)->with('permissions');
        return response()->json($role);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles,name,'.$id,
            'permissions' => 'sometimes|array|exists:permissions,id'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $role = Role::find($id);
        $role->name = $request->name;
        $role->save();

        if ($request->has('permissions')) {
            $permissions = Permission::whereIn('id',$request->permissions)->pluck('name')->toArray();
            $role->syncPermissions($permissions);
        }

        return response()->json($role);
    }

    public function destroy($id)
    {
        $role = Role::find($id);
        $role->delete();
        return response()->json(null, 204);
    }

    // public function restore($id)
    // {
    //     if ($this->user()->cant('restore_centers'))
    //         return handleResponse(["success" => false, "message" => "Not Auth"], Response::HTTP_FORBIDDEN);

    //     $center = Center::withTrashed()->findOrFail($id);

    //     $center->user()->withTrashed()->first()->restore();
    //     $center->restore();


    //     return handleResponse($center, Response::HTTP_ACCEPTED);
    // }
}
