<?php

namespace App\Http\Controllers;

use JWTAuth;

abstract class ApiController extends Controller
{
    protected $user;

    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }
}