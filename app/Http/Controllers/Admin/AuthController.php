<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests\AuthPostRequest as mainRequest;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
use App\Repositories\User\UserRepositoryInterface as RepositoryInterface;

class AuthController extends Authenticatable {

    protected $controllerView = 'admin.pages.auth.';
    protected $controllerName = 'auth';
    protected $mainModel;

    public function __construct(RepositoryInterface $repository) {
        $this->mainModel = $repository;
        view()->share(['controllerName' => $this->controllerName]);
    }

    public function login() {
        if (Auth::check()) {
            return redirect()->route("dashboard");
        }
        $this->metaTitle = 'Login ' . $this->metaTitle;
        //query
        //$data = $request->session()->all();
        return view($this->controllerView . 'login', [
            'metaTitle' => $this->metaTitle
        ]);
    }

    public function postlogin(mainRequest $request) {
        if ($request->method("POST")) {
            $params = $request->all();
            //$result = $this->mainModel->getItem($params, ['task' => 'user-auth-login']);
            $login = [
                "username" => $params['username'],
                "password" => $params['password'],
                'status' => 1
            ];
//            $user = Auth::getProvider()->retrieveByCredentials($login);
            if (Auth::attempt($login)) {
                return redirect()->route("dashboard");
            } else {
                return redirect()->route($this->controllerName . "/login")->with('notify_error', 'Tài khoản hoặc mật khẩu không chính xác!');
            }
        }
    }

    public function logout(Request $request) {
        // if ($request->session()->has("authInfo")) {
        //    $request->session()->pull("authInfo");
        // }
        Auth::logout();
        return redirect()->route($this->controllerName . "/login");
    }

}
