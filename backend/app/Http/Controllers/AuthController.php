<?php

namespace App\Http\Controllers;

use Exception;

use App\Notifications\PushNotification;
use App\Http\Controllers\BaseController;

use App\Models\User;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class AuthController extends BaseController
{
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        $credentials['is_active'] = true;
        
        if (!Auth::attempt($credentials)) {
            return $this->sendResponse(false, __('auth.failed'), [
                'errors' => [
                    'email' => [__('auth.failed')],
                ]
            ]);
        }

        $request->session()->regenerate();
        
        return $this->sendResponse(true, 'Logged in successfully', [
            'user' => $request->user(),
            'session' => session()->all(),
        ]);
    }

    public function check(Request $request): JsonResponse
    {
        // dd(Auth::check(), Auth::user(), session()->all(), session()->getId());

        $message = Auth::check() ? 'Authenticated' : 'Rejected';

        return $this->sendResponse(Auth::check(), $message, [
            'user' => Auth::user(),
            'session' => session()->all(),
        ]);
    }

    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:246|min:4',
            'email' => 'required|string|email|max:246|unique:users',
            'password' => 'required|string|min:8',
        ]);
        
        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);
        $user->assignRole('member');

        Profile::create([
            'user_id' => $user->id,
            'email_address' => $validated['email'],
            'first_name' => $validated['name'],
        ]);
        
        $request->session()->regenerate();

        return $this->sendResponse(true, 'User has been registered', ['user' => $user]);
    }

    public function logout(Request $request): JsonResponse
    {
        // Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $this->sendResponse(true, 'Logged out successfully');
    }

    private function createToken($user) {
        return ($user)->createToken('appToken')->plainTextToken;
    }

    protected function whisperToAdmin($user) {
        $data = [
            'title' => __('Newly Registered Member'),
            'headline' => __(':email joined the club!', ['email' => $user->email]), 
            'description' => __('New member has successfully registered.'),
            'icon' => 'user-plus',
            'category' => 'account',
            'variant' => 'primary',
        ];

        $superusers = User::role('admin')->get();
        Notification::send($superusers, new PushNotification($data));
    }

    public function getLogin() {
        return response()->json([
            'message' => "Unauthenticated"
        ]);
        return redirect()->route('welcome');
    }
        
    public function getRegister() {
        return response()->json([
            'message' => "Unauthenticated"
        ]);
        return redirect()->route('welcome');
    }
}
