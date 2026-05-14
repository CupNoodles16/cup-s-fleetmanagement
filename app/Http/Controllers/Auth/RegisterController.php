<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\EmployeeProfile;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisterController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Validation rules
        $request->validate([
            // Personal Information
            'surname'                        => 'required|string|max:255',
            'first_name'                     => 'required|string|max:255',
            'middle_name'                    => 'nullable|string|max:255',
            'suffix'                         => 'nullable|string|max:10',
            'birth_date'                     => 'required|date|before:-18 years',
            'sex'                            => 'required|in:male,female,other',
            'marital_status'                 => 'required|in:single,married,divorced,widowed',

            // Contact Information
            'phone_number'                   => 'required|string|max:20',
            'address'                        => 'required|string|max:500',
            'emergency_contact_name'         => 'required|string|max:255',
            'emergency_contact_relationship' => 'required|string|max:100',
            'emergency_contact_number'       => 'required|string|max:20',

            // Account Credentials
            'email'                          => 'required|string|email|max:255|unique:users',
            'password'                       => ['required', 'confirmed', Rules\Password::min(8)],
            'role'                           => 'required|in:dispatcher',

            // Documents (Optional)
            'health_card'                    => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'nbi_clearance'                  => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'police_clearance'               => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        // Create User
        $user = User::create([
            'name'  => $request->first_name . ' ' . $request->surname,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        // Handle file uploads
        $healthCardPath = null;
        $nbiClearancePath = null;
        $policeClearancePath = null;

        if ($request->hasFile('health_card')) {
            $healthCardPath = $request->file('health_card')->store('documents/health_cards', 'public');
        }

        if ($request->hasFile('nbi_clearance')) {
            $nbiClearancePath = $request->file('nbi_clearance')->store('documents/nbi_clearances', 'public');
        }

        if ($request->hasFile('police_clearance')) {
            $policeClearancePath = $request->file('police_clearance')->store('documents/police_clearances', 'public');
        }

        // Create Employee Profile
        EmployeeProfile::create([
            'user_id'                            => $user->id,
            'surname'                            => $request->surname,
            'first_name'                         => $request->first_name,
            'middle_name'                        => $request->middle_name,
            'suffix'                             => $request->suffix,
            'birth_date'                         => $request->birth_date,
            'sex'                                => $request->sex,
            'marital_status'                     => $request->marital_status,
            'phone_number'                       => $request->phone_number,
            'address'                            => $request->address,
            'emergency_contact_name'             => $request->emergency_contact_name,
            'emergency_contact_number'           => $request->emergency_contact_number,
            'emergency_contact_relationship'     => $request->emergency_contact_relationship,
            'health_card_path'                   => $healthCardPath,
            'nbi_clearance_path'                 => $nbiClearancePath,
            'police_clearance_path'              => $policeClearancePath,
        ]);

        event(new Registered($user));

        // Auto-login after registration (optional - remove if you don't want auto-login)
        Auth::login($user);

        // Redirect to dashboard
        return redirect()->route('dashboard')->with('success', 'Dispatcher account created successfully!');
    }
}
