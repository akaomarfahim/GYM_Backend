<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserType;

class UserTypeController extends Controller
{
    public function index()
    {
        $userTypes = UserType::pluck('name');
        return response()->json(['user_types' => $userTypes]);
    }
}