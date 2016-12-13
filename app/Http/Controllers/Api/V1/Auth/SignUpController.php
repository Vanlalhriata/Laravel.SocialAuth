<?php

namespace App\Http\Controllers\Api\V1\Auth;

use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Ramsey\Uuid\Uuid;
use Tymon\JWTAuth\JWTAuth;
use PDOException;

class SignUpController extends Controller
{
    private $validator; 

    public function signup(Request $request, JWTAuth $JWTAuth)
    {
        $params = $request->all();

        $validationErrors = $this->validateParameters($params);
        if (!empty($validationErrors))
        {
            return response()->json(['error' => $validationErrors], 400);
        }

        try
        {
            $user = User::create([
                'id' => Uuid::uuid4()->toString(),
                'name' => $params['name'],
                'email' => $params['email'],
                'password' => bcrypt($params['password']),
            ]);

            $customClaims = ['exp' => 5000000000];  // Set to expire in 100+ years
            $token = $JWTAuth->fromUser($user, $customClaims);
        }
        catch (PDOException $e)
        {
            return response()->json(['error' => 'Exception: '.$e->getCode()], 500);
        }

        return response()->json([
            'error' => null,
            'token' => $token
        ], 201);
    }

    private function validateParameters($params)
    {
        if (empty($this->validator))
        {
            $this->validator = Validator::make($params, [
                'name' => 'required|max:255',
                'email' => 'required|email|max:255|unique:users',
                'password' => 'required|min:8'
            ]);
        }

        $this->validator->setData($params);

        if ($this->validator->fails())
        {
            return $this->validator->errors();
        }
    }
}
