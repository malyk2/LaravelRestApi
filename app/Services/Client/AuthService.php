<?php

namespace App\Services\Client;

use Illuminate\Http\Request;
use App\Services\CoreService;
use GuzzleHttp\Client;
use App\Repositories\UserRepository;
use App\User;
use App\Role;
use App\Hash;
use App\Http\Resources\Client\User\Short as UserShortResourse;
use App\Events\Auth\Register as AuthRegisterEvent;
use App\Events\Auth\Reset as AuthResetEvent;
use App\Events\Auth\Login as AuthLoginEvent;

class AuthService extends CoreService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function login(Request $request)
    {
        $data = $request->only(['email', 'password']);
        if (auth()->attempt($data)) {
            $me = auth()->user();

            $tokens = $this->getPassportTokens($data);

            $result = (new UserShortResourse($me))->additional(compact('tokens'));

            return response()->result($result, __('You was successfully logged in'));
        } else {
            customThrow(__('Incorrect email or password'), 422);
        }
    }

    protected function getPassportTokens(array $data)
    {
        $client = new Client;

        $passwordParams = [
            'form_params' => [
                'grant_type' => 'password',
                'client_id' => config('auth.passport.grand.client_id'),
                'client_secret' => config('auth.passport.grand.client_secret'),
                'username' => array_get($data, 'email'),
                'password' => array_get($data, 'password'),
                'scope' => '*',
            ],
            'http_errors' => false,
        ];

        $response = $client->post(url('oauth/token'), $passwordParams);

        $result = json_decode($response->getBody()->getContents());

        customThrowIf( ! $result || empty($result->access_token), $result->message ?? __('Can\'t get token'));

        return $result;
    }
}
