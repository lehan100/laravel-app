<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\InterfaceModels;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;

class Users extends Authenticatable implements InterfaceModels {

    use HasApiTokens,
        HasFactory,
        Notifiable,
        HasRoles;

    protected $table = 'users';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $guard_name = 'web';
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // @Override
    public function listItems($params = null, $options = null) {
        $result = null;
        if ($options['task'] == "admin-list-items") {
            $query = $this->select('id', 'fullname', 'username', 'email', 'group', 'status', 'created')
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
            $result = $this->select('id', 'username', 'fullname', 'email', 'group', 'created')->where([
                        ['status', "=", 1],
                        ['group', "=", 1],
                        ['username', "=", $params['username']],
                        ['password', "=", $password],
                    ])->first();
            if ($result) {
                Auth::guard('admin')->login($result);
            }
        }
        if ($options['task'] == 'get-item') {
            $result = $this->select('id', 'fullname', 'email', 'username', 'password', 'group', 'status')->where('id', $params['id'])->first()->toArray();
        }
        //$result = ($result != null) ? $result->toArray() : null;
        return $result;
    }

    // @Override
    public function saveItem($params = null, $options = null) {
        $result = 0;
        if ($options['task'] == "admin-update-multi-status") {
            if (isset($params['aid']) && count($params['aid']) > 0) {
                $result = $this->whereIn('id', $params['aid'])->update(['status' => $params['value']]);
            }
        }
        if ($options['task'] == 'change-status') {
            $status = (isset($params['status']) && $params['status'] == 0) ? 1 : 0;
            $result = $this->where('id', $params['id'])->update(['status' => $status]);
        }
        if ($options['task'] == 'add-item') {
            $row = new User;
            $row->fullname = $params['fullname'];
            $row->username = $params['username'];
            $row->email = $params['email'];
            $row->group = $params['group'];
            $row->password = ($params['password']) ? md5($params['password']) : $params['password_old'];
            $row->status = isset($params['status']) ? 1 : 0;
            $row->created = time();
            $row->save();
            $result = $row->id;
            // $result = $this->insert([
            //     'fullname' => $params['fullname'],
            //     'username' => $params['username'],
            //     'email' => $params['email'],
            //     'password' => ($params['password']) ? md5($params['password']) : $params['password_old'],
            //     'status' => isset($params['status']) ? 1 : 0,
            //     'created' => time()
            // ]);
        }

        if ($options['task'] == 'edit-item') {
            $row = User::where('id', $params['id'])->first();
            $row->fullname = $params['fullname'];
            $row->username = $params['username'];
            $row->email = $params['email'];
            $row->group = $params['group'];
            $row->password = ($params['password']) ? md5($params['password']) : $params['password_old'];
            $row->status = isset($params['status']) ? 1 : 0;
            $row->save();
            $result = $row->id;
            // $result = $this->where('id', $params['id'])->update([
            //     'fullname' => $params['fullname'],
            //     'username' => $params['username'],
            //     'email' => $params['email'],
            //     'password' => ($params['password']) ? md5($params['password']) : $params['password_old'],
            //     'status' => isset($params['status']) ? 1 : 0
            // ]);
        }
        return ($result > 0) ? $result : FALSE;
    }

    // @Override
    public function deleteItem($params = null, $options = null) {
        $result = 0;
        if ($options['task'] == 'delete-item-multi') {
            if (isset($params['aid']) && count($params['aid']) > 0) {
                $result = $this->whereIn('id', $params['aid'])->delete();
            }
        }
        if ($options['task'] == 'delete-item') {
            if (isset($params['id'])) {
                $result = $this->where('id', $params['id'])->delete();
            }
        }
        return ($result > 0) ? TRUE : FALSE;
    }

}
