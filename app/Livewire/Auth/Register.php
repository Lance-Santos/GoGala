<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\Rules\Password;
// use App\Providers\RouteServiceProvider;

class Register extends Component
{
    // Envelopes
    public $passwordConfirmation = '';
    public string $first_name = '';
    public string $middle_name = '';
    public string $last_name = '';
    public string $suffix = '';
    public string $username = '';
    public string $contact_number = '';
    public string $email = '';
    public string $gender = '';
    public string $password = '';
    public string $password_confirmation = '';
    public $regions;
    public array $province = [];
    public array $city = [];
    // public function mount()
    // {
    //     $this->loadRegion();
    //     $this->getAllRegions();
    // }

    // public function loadRegion()
    // {
    //     $json = File::get(storage_path('app/regions.json'));
    //     $this->regions = json_decode($json,true);
    // }
    // public function getAllRegions()
    // {
    //     return array_map(function ($region) {
    //         return ['label' => $region['region_name'], 'value' => $region['region_name']];
    //     }, $this->regions);
    // }

    public function getAllProvinces(){

    }
    public function register(): void
{
    // Remove whitespaces from the contact number
    $this->contact_number = preg_replace('/\s+/', '', $this->contact_number);

    $validated = $this->validate([
        'first_name' => ['required', 'string', 'max:255'],
        'middle_name' => ['required', 'string', 'max:255'],
        'last_name' => ['required', 'string', 'max:255'],
        'suffix' => ['nullable', 'string', 'max:10'],
        'username' => ['required', 'string', 'max:255', 'unique:users,username'],
        'contact_number' => ['required', 'regex:/^[0-9]{10}$/'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
        'gender' => ['required', 'in:male,female'],
        // 'province' => ['required', 'string'],
        // 'city' => ['required', 'string'],
        // 'address_line_1' => ['required', 'string', 'max:255'],
        // 'address_line_2' => ['nullable', 'string', 'max:255'],
        'password' => ['required', 'string', 'confirmed', Password::defaults()],
    ]);

    // Add role_id to validated data
    $validated['role_id'] = 1;
    $validated['password'] = Hash::make($validated['password']);

    event(new Registered($user = User::create($validated)));
    Auth::login($user);
    $this->redirect(route('home'));
}



    public function render()
    {
        return view('livewire.auth.register')->extends('layouts.auth');
    }
}
