<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;  

class AuthService
{
    protected UserRepository $users;

    public function __construct(UserRepository $users)
    {
        $this->users = $users;
    }

    public function register(array $data): array
    {
        // Validate input
        $validator = Validator::make($data, [
            'name'     => 'required|string',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:8',
        ]);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        // Create user via repository
        $user = $this->users->create($data);

        // Generate OTP
        $otp = rand(100000, 999999);
        $user->otp = $otp;
        $user->otp_expires_at = now()->addMinutes(10);
        $user->save();

        // Send OTP email
        Mail::to($user->email)->send(new OtpMail($otp));

        return ['message' => 'User registered successfully. Please check your email for OTP.'];
    }

    public function login(array $data): array
    {
        $validator = Validator::make($data, [
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        $user = $this->users->findByEmail($data['email']);

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return ['message' => 'Invalid credentials'];
        }

        if (! $user->email_verified_at) {
            return ['message' => 'Email not verified'];
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'message' => 'Login successful',
            'user'    => $user,
            'token'   => $token,
        ];
    }
    
    public function verifyOtp(array $data): array
    {
        $validator = Validator::make($data, [
            'email' => 'required|email',
            'otp'   => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        $user = $this->users->findByEmail($data['email']);

        if (! $user) {
            return ['message' => 'User not found'];
        }

        if ($user->email_verified_at) {
            return ['message' => 'Email already verified'];
        }

        if ($user->otp !== $data['otp'] || now()->greaterThan($user->otp_expires_at)) {
            return ['message' => 'Invalid or expired OTP'];
        }

        $user->email_verified_at = now();
        $user->otp = null;
        $user->otp_expires_at = null;
        $user->save();

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'message' => 'Email verified successfully',
            'user'    => $user,
            'token'   => $token,
        ];
    }

    public function resendOtp(array $data): array
    {
        $validator = Validator::make($data, [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        $user = $this->users->findByEmail($data['email']);

        if (! $user) {
            return ['message' => 'User not found'];
        }

        if ($user->email_verified_at) {
            return ['message' => 'Email already verified'];
        }

        // If current OTP still valid, block resend
        if ($user->otp && now()->lessThan($user->otp_expires_at)) {
            return ['message' => 'Current OTP is still valid. Please use it.'];
        }

        // Generate new OTP
        $otp = rand(100000, 999999);
        $user->otp = $otp;
        $user->otp_expires_at = now()->addMinutes(10);
        $user->save();

        Mail::to($user->email)->send(new OtpMail($otp));

        return ['message' => 'A new OTP has been sent to your email.'];
    }
}
