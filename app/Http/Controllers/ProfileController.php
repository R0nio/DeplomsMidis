<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Project;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        try {
            $user = Auth::user();

           
            $favoriteProjects = null;
            if ($user->role === 'Investor') {
                $favoriteProjects = $user->favoriteProjects()
                    ->with(['photos', 'user'])
                    ->latest('favorites.created_at')
                    ->limit(10)
                    ->get();
            }
            

            $myProjects = null;
            if ($user->role === 'Organisator') {
                $myProjects = $user->projects()
                    ->with(['photos'])
                    ->latest()
                    ->limit(10)
                    ->get();
            }

            return Inertia::render('Profile/Edit', [
                'mustVerifyEmail' => $user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail,
                'status' => session('status'),
                'favoriteProjects' => $favoriteProjects,
                'myProjects' => $myProjects,
            ]);

        } catch (\Exception $e) {
            Log::error('Profile edit error: ' . $e->getMessage());
            
            return back()->withErrors(['error' => 'Ошибка при загрузке профиля']);
        }
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
