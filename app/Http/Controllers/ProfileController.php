<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('profile.edit', [
            'user' => Auth::user()
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ]);

        DB::table('users')
            ->where('id', $user->id)
            ->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
            ]);

        return redirect()->route('profile.edit')->with('status', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = Auth::user();
        
        DB::table('users')
            ->where('id', $user->id)
            ->update([
                'password' => Hash::make($validated['password']),
            ]);

        return redirect()->route('profile.edit')->with('status', 'Password berhasil diperbarui.');
    }
} 