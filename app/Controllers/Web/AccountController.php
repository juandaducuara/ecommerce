<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Infrastructure\Persistence\Models\UserModel;

class AccountController extends BaseController
{
    public function index()
    {
        $userModel = new UserModel();
        $user = $userModel->find(session()->get('user_id'));

        return view('web/account/index', [
            'title' => 'Mi Cuenta',
            'user'  => $user,
        ]);
    }
}
