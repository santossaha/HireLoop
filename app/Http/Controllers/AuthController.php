<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vendor;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        $departments = Department::all();
        $internalPocs = User::whereIn('role', ['admin', 'poc', 'hod'])->get();
        
        return view('auth.register', compact('departments', 'internalPocs'));
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'type' => 'required|in:company,freelancer',
            'contact_number' => 'required|string|max:20',
            'skype' => 'nullable|string|max:255',
            'slack' => 'nullable|string|max:255',
            'poc_name' => 'required|string|max:255',
            'internal_poc_id' => 'required|exists:users,id',
            'budget_3_years' => 'required|numeric|min:0',
            'budget_5_years' => 'required|numeric|min:0',
            'budget_7_years' => 'required|numeric|min:0',
            'budget_10_years' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->route('register')
                ->withErrors($validator)
                ->withInput();
        }

        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'vendor',
        ]);

        // Create vendor profile
        Vendor::create([
            'user_id' => $user->id,
            'type' => $request->type,
            'contact_number' => $request->contact_number,
            'skype' => $request->skype,
            'slack' => $request->slack,
            'poc_name' => $request->poc_name,
            'internal_poc_id' => $request->internal_poc_id,
            'budget_3_years' => $request->budget_3_years,
            'budget_5_years' => $request->budget_5_years,
            'budget_7_years' => $request->budget_7_years,
            'budget_10_years' => $request->budget_10_years,
            'status' => 'pending', // Default status
        ]);

        // Send notification to internal POC and HOD (will be implemented later)

        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Registration successful! Your account is pending approval.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }
}