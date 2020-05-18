<?php

namespace App\Http\Controllers\Dashboard;
use App\Role;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:read_roles')->only(['index']);
        $this->middleware('permission:create_roles')->only(['create', 'store']);
        $this->middleware('permission:update_roles')->only(['edit', 'update']);
        $this->middleware('permission:delete_roles')->only(['destroy']);

    }// 
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::whereRoleNot(['super_admin', 'admin', 'user'])
            ->whenSearch(request()->search)
            ->withCount('users')
            
            
            ->paginate(5);
        return view('dashboard.roles.index', compact('roles'));
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('dashboard.roles.create');
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'required|array|min:1',
        ]);

        $role = Role::create($request->all());
        $role->attachPermissions($request->permissions);

        session()->flash('success', 'Data added successfully');
        return redirect()->route('dashboard.roles.index');
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $role)
    {
                return view('dashboard.roles.edit', compact('role'));


        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id,
            'permissions' => 'required|array|min:1',
        ]);

        $role->update($request->all());
        $role->syncPermissions($request->permissions);

        session()->flash('success', 'Data updated successfully');
        return redirect()->route('dashboard.roles.index');
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        $role->delete();
        session()->flash('success', 'Data deleted successfully');
        return redirect()->route('dashboard.roles.index');
        //
    }
}
