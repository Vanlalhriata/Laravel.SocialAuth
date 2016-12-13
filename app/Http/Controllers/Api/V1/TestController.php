<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\JWTAuth;

class TestController extends Controller
{
    public function test(JWTAuth $JWTAuth)
    {
        $user = $JWTAuth->parseToken()->toUser();

        return 'Hello '.$user->name;
    }
}
