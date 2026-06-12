<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
        'name' => 'required|string|max:255|regex:/^[А-Яа-яЁё]+$/u',
        'middlename' => 'required|string|max:255|regex:/^[А-Яа-яЁё]+$/u',
        'lastname' => 'required|string|max:255|regex:/^[А-Яа-яЁё]+$/u',
        'login' => 'required|string|max:255|regex:/^[A-Za-z0-9_]+$/|unique:' . User::class,
        'email' => 'required|string|email|max:255|unique:' . User::class,
        'number' => 'required|string|max:255',
        'role' => 'required|string|max:255',
        'organization_name' => 'nullable|string|max:255',
        'password' => ['required', 'confirmed', Rules\Password::defaults()],
    ], [
        // Сообщения для кириллицы
        'name.regex' => 'Имя должно содержать только русские буквы (А-Я, а-я)',
        'middlename.regex' => 'Отчество должно содержать только русские буквы (А-Я, а-я)',
        'lastname.regex' => 'Фамилия должна содержать только русские буквы (А-Я, а-я)',
        // Сообщения для латиницы
        'login.regex' => 'Логин может содержать только латинские буквы, цифры и знак подчёркивания',
    ]);

        $user = User::create([
            'name' => $request->name,
            'middlename' => $request->middlename,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'login' => $request->login,
            'number' => $request->number,
            'role' => $request->role,
            'organization_name' => $request->organization_name,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
