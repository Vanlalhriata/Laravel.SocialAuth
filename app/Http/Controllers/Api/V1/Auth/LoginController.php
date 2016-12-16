<?php

namespace App\Http\Controllers\Api\V1\Auth;

use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\JWTAuth;
use Socialite;
use GuzzleHttp\Exception\ClientException;
use App\Models\User;
use Ramsey\Uuid\Uuid;

class LoginController extends Controller
{
    private $validAuthProviders = ['email', 'facebook'];

    private $JWTAuth;
    private $authProvider;

    public function __construct(JWTAuth $JWTAuth)
    {
        $this->JWTAuth = $JWTAuth;
    }

    public function login(Request $request)
    {
        $this->authProvider = $request['auth-provider'];

        $validationErrors = $this->validateParameters($request);
        if (!empty($validationErrors))
        {
            return response()->json(['error' => $validationErrors], 400);
        }

        $generateTokenResult =  $this->generateToken($request);   // {'error' => null, 'token' => null}

        if (null != $generateTokenResult['error'])
        {
            return response()->json($generateTokenResult, 400);
        }
        
        return response()->json($generateTokenResult);
    }

    private function validateParameters($request)
    {
        if (!in_array($this->authProvider, $this->validAuthProviders))
        {
            return 'Invalid auth-provider';
        }

        if ($this->authProvider == 'email')
        {
            $validationRules = [
                'email' => 'required|email',
                'password' => 'required',
            ];
        }
        else
        {
            $validationRules = [
                'social-id' => 'required',
                'access-token' => 'required',
            ];
        }

        $validator = Validator::make($request->all(), $validationRules);

        if ($validator->fails())
        {
            return $validator->errors();
        }
    }

    private function generateToken($request)
    {
        $result = ['error' => null, 'token' => null];

        $customClaims = ['exp' => 5000000000];  // Set to expire in 100+ years

        if ($this->authProvider == 'email')
        {
            $credentials = $request->only(['email', 'password']);
            $token = $this->JWTAuth->attempt($credentials, $customClaims);

            if (!$token)
            {
                $result['error'] = 'Invalid credentials';
                return $result;
            }

            $result['token'] = $token;
        }
        else
        {
            // This will create a new user in db if necessary
            $getUserFromAccessTokenResult = $this->getUserFromAccessToken($request['access-token']);

            if (null != $getUserFromAccessTokenResult['error'])
            {
                $result['error'] = $getUserFromAccessTokenResult['error'];
                return $result;
            }

            $result['token'] = $this->JWTAuth->fromUser($getUserFromAccessTokenResult['user'], $customClaims);
        }

        return $result;
    }

    private function getUserFromAccessToken($accessToken)
    {
        $result = ['error' => null, 'user' => null];

        try
        {
            $socialUser = Socialite::driver('facebook')->userFromToken($accessToken);
        }
        catch (ClientException $e)
        {
            $result['error'] = json_decode($e->getResponse()->getBody())->error->message;
            return $result;
        }

        // Create new user if necessary
        $user = User::where([
            ['auth_provider', '=', $this->authProvider],
            ['social_id', '=', $socialUser->id],
        ])->get()->first();

        if (null === $user)
        {
            $user = User::create([
                'id' => Uuid::uuid4()->toString(),
                'name' => $socialUser->name,
                'email' => null,
                'password' => null,
                'auth_provider' => $this->authProvider,
                'social_id' => $socialUser->id,
            ]);
        }

        $result['user'] = $user;
        return $result;
    }


}
