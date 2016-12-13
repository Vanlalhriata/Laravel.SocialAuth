<?php

namespace App\Http\Controllers\Api\V1\Auth;

use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\JWTAuth;

class LoginController extends Controller
{
    private $validator;
    private $JWTAuth;

    public function __construct(JWTAuth $JWTAuth)
    {
        $this->JWTAuth = $JWTAuth;
    }

    public function login(Request $request)
    {
        $params = $request->all();

        $validationErrors = $this->validateParameters($params);
        if (!empty($validationErrors))
        {
            return response()->json(['error' => $validationErrors], 400);
        }

        $credentials = $request->only(['email', 'password']);
        $token =  $this->generateToken($credentials);   // false if failed

        if (!$token)
        {
            return response()->json(['error' => 'Invalid credentials'], 400);
        }
        
        return response()->json([
            'error' => null,
            'token' => $token
        ]);
    }

    private function validateParameters($params)
    {
        if (empty($this->validator))
        {
            $this->validator = Validator::make($params, [
                'email' => 'required|email',
                'password' => 'required'
            ]);
        }

        $this->validator->setData($params);

        if ($this->validator->fails())
        {
            return $this->validator->errors();
        }
    }

    private function generateToken($credentials)
    {
        $customClaims = ['exp' => 5000000000];  // Set to expire in 100+ years
        return $this->JWTAuth->attempt($credentials, $customClaims);
    }
}
