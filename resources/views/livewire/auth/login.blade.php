<div class="max-w-md p-8 mx-auto bg-white rounded-lg shadow-md">
    <h2 class="mb-6 text-4xl font-semibold text-center text-gray-800">{{ __('Login') }}</h2>
    <form wire:submit.prevent="authenticate" class="space-y-6">
        <!-- Email Field -->
        <div>
            <x-input label="Email" wire:model="email" required type="email" class="rounded-md shadow-sm focus:ring focus:ring-blue-200"/>
        </div>

        <!-- Password Field -->
        <div>
            <x-password label="Password" wire:model="password" required class="rounded-md shadow-sm focus:ring focus:ring-blue-200"/>
        </div>

        <!-- Remember Me Checkbox -->
        <div class="flex items-center">
            <x-checkbox label="Remember me" wire:model='remember' class="text-blue-600"/>
        </div>

        <!-- Submit Button Row -->
        <div class="flex items-center justify-between mt-4">
            <x-link href="{{ route('register') }}" text="Don't have an account? Register here!" class="text-sm text-gray-600 hover:text-blue-600" />
            <x-button wire:click="authenticate" loading class="px-6 py-2 font-semibold text-white transition duration-200 bg-blue-600 rounded-md hover:bg-blue-700">
                Login
            </x-button>
        </div>
    </form>
</div>
