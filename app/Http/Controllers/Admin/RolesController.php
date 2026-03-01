<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\MainController;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesController extends MainController {

    protected $controllerView = 'admin.pages.roles.';
    protected $controllerName = 'roles';

    public function __construct() {
        $this->title = 'Phân quyền';
        view()->share(['mainTitle' => $this->title, 'params' => $this->params, 'isDataTable' => $this->isDataTable, 'buttomGroup' => $this->buttomGroup, 'controllerName' => $this->controllerName]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $this->metaTitle = 'Danh sách ' . $this->title;
        $roles = Role::orderBy('id', 'DESC')->paginate(5);
        return view($this->controllerView . 'index', [
                            'title' => $this->title,
                            'metaTitle' => $this->metaTitle
                                ], compact('roles'))
                        ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        $permissions = Permission::get();
        $this->title = "Danh sách quyền";
        $this->metaTitle = 'Thêm quyền';
        return view($this->controllerView . 'create',[
            'title' => $this->metaTitle,
            'metaTitle' => $this->title
                ], compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $this->validate($request, [
            'name' => 'required|unique:roles,name',
            'permission' => 'required',
        ]);

        $role = Role::create(['name' => $request->get('name')]);
        $role->syncPermissions($request->get('permission'));

        return redirect()->route($this->controllerName . '.index')
                        ->with('success', 'Role created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role) {
        $role = $role;
        $rolePermissions = $role->permissions;
        $this->title = "Danh sách quyền";
        $this->metaTitle = 'Phân quyền cho tài khoản ' . ucfirst($role->name);
        return view($this->controllerView . 'show', [
            'title' => $this->metaTitle,
            'metaTitle' => $this->title
                ], compact('role', 'rolePermissions'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $role) {
        $role = $role;
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        $permissions = Permission::get();
        $this->title = "Danh sách quyền";
        $this->metaTitle = 'Phân quyền cho tài khoản ' . ucfirst($role->name);
        return view($this->controllerView . 'edit', [
            'title' => $this->metaTitle,
            'metaTitle' => $this->title
                ], compact('role', 'rolePermissions', 'permissions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Role $role, Request $request) {
        $this->validate($request, [
            'name' => 'required',
            'permission' => 'required',
        ]);

        $role->update($request->only('name'));

        $role->syncPermissions($request->get('permission'));

        return redirect()->route($this->controllerName . '.index')
                        ->with('success', 'Role updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role) {
        $role->delete();

        return redirect()->route($this->controllerName . '.index')
                        ->with('success', 'Role deleted successfully');
    }

}
