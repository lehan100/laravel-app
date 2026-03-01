<?php

namespace App\Repositories\User;

use App\Repositories\EloquentRepository;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class UserEloquentRepository extends EloquentRepository implements UserRepositoryInterface {

    private $FIELDSELECT = array('id', 'fullname', 'username', 'email', 'group', 'status');
    private $USER_GROUP;

    /**
     * get model
     * @return string
     */
    public function __construct() {
        parent::__construct();
        $this->USER_GROUP = config('configs.user_group_name');
    }

    public function getModel() {
        return \App\Models\User::class;
    }

    public function listItems($params = null, $options = null) {
        $result = null;
        if ($options['task'] == "admin-list-items") {
            $query = $this->_model->select($this->FIELDSELECT)
                    ->orderBy('id', 'asc');
            $result = $query->paginate($params['pagination']['totalItemsPerPage']);
        }
        return $result;
    }

    // @Override
    public function getItem($params = null, $options = null) {
        $result = null;
        if ($options['task'] == 'user-auth-login') {
            $password = md5($params['password']);
            $result = $this->_model->select($this->FIELDSELECT)->where([
                        ['status', "=", 1],
                        ['group', "=", 1],
                        ['username', "=", $params['username']],
                        ['password', "=", $password],
                    ])->first();
            // if ($result) {
            //     Auth::guard('admin')->login($result);
            // }
        }
        if ($options['task'] == 'get-item') {
            array_push($this->FIELDSELECT, "password");
            $result = $this->_model->select($this->FIELDSELECT)->where('id', $params['id'])->first()->toArray();
        }
        //$result = ($result != null) ? $result->toArray() : null;
        return $result;
    }

    // @Override
    public function saveItem($params = null, $options = null) {
        $result = 0;
        if ($options['task'] == "admin-update-multi-status") {
            if (isset($params['aid']) && count($params['aid']) > 0) {
                $result = $this->_model->whereIn('id', $params['aid'])->update(['status' => $params['value']]);
            }
        }
        if ($options['task'] == 'change-status') {
            $status = (isset($params['status']) && $params['status'] == 0) ? 1 : 0;
            $result = $this->_model->where('id', $params['id'])->update(['status' => $status]);
        }
        DB::beginTransaction();
        try {
            if ($options['task'] == 'add-item') {
                $row = new $this->_model;
            }

            if ($options['task'] == 'edit-item') {
                $row = $this->_model->where('id', $params['id'])->first();
            }
            $row->name = $this->USER_GROUP[$params['group']];
            $row->fullname = $params['fullname'];
            $row->username = $params['username'];
            $row->email = $params['email'];
            $row->group = $params['group'];
            $row->password = ($params['password']) ? bcrypt($params['password']) : $params['password_old'];
            $row->status = isset($params['status']) ? 1 : 0;
            $row->save();
            $result = $row->id;
            $role = Role::firstOrCreate(['name' => $this->USER_GROUP[$params['group']]]);
            foreach ((new Role())->pluck('name')->toArray() as $roleName) {
                $row->removeRole($roleName);
            }
            $row->assignRole([$role->id]);
//            if ($params['group'] == 1) {
//                $permissions = Permission::pluck('id', 'id')->all();
//                $role->syncPermissions($permissions);
//            }
            DB::commit();
            return ($result > 0) ? $result : FALSE;
        } catch (\Exception $e) {
            DB::rollBack();
            return FALSE;
        }
    }

    // @Override
    public function deleteItem($params = null, $options = null) {
        $result = 0;
        if ($options['task'] == 'delete-item-multi') {
            if (isset($params['aid']) && count($params['aid']) > 0) {
                $result = $this->_model->whereIn('id', $params['aid'])->delete();
            }
        }
        if ($options['task'] == 'delete-item') {
            if (isset($params['id'])) {
                $result = $this->_model->where('id', $params['id'])->delete();
            }
        }
        return ($result > 0) ? TRUE : FALSE;
    }

}
