<?php

namespace App\Services;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function login(array $credentials, $remember = false)
    {
        return Auth::attempt($credentials, $remember);
    }

    public function logout()
    {
        Auth::logout();
    }

    /**
     * Get the authenticated user
     * 
     * @return User|null
     */
    public function getUser(): ?User
    {
        return Auth::user();
    }
}

