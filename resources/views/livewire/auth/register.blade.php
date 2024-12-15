<div class="max-w-3xl p-8 mx-auto bg-white rounded-lg shadow-lg">
    <h2 class="mb-8 text-4xl font-bold text-center text-gray-800">{{ __('Register') }}</h2>
    <form wire:submit.prevent="register" class="space-y-8">
        <!-- Name Fields Row -->
        <div class="grid grid-cols-1 gap-6 md:grid-cols-4">
            <x-input label="First Name *" hint="Enter your first name" wire:model="first_name" required class="col-span-2" />
            <x-input label="Middle Name *" hint="Enter your middle name" wire:model="middle_name" required class="col-span-2" />
            <x-input label="Last Name *" hint="Enter your last name" wire:model="last_name" required class="col-span-2" />
            <x-input label="Suffix (Optional)" hint="e.g., Jr., III" wire:model="suffix" class="col-span-2" />
        </div>

        <!-- Username and Contact Number Row -->
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <x-input label="Username *" hint="Choose a unique username" wire:model="username" required />
            <x-input label="Contact Number *" hint="+63 999 999 9999" prefix="(+63)" wire:model="contact_number" x-mask="999 999 9999" required />
        </div>

        <!-- Email and Gender Row -->
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <x-input label="Email *" hint="Your email address" wire:model="email" required type="email" />
            <x-select.styled label="Gender *"
                :options="[['label' => 'Male', 'value' => 'male'], ['label' => 'Female', 'value' => 'female'], ['label' => 'Prefer not to say', 'value' => 'hidden']]"
                select="label:label|value:value"
                wire:model="gender" required />
        </div>

        <!-- Password Row -->
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <x-password label="Password *" hint="Your secure password" generator :rules="['min:8', 'symbols', 'numbers', 'mixed']" wire:model="password" required />
            <x-password label="Confirm Password *" hint="Repeat your password" wire:model="password_confirmation" required />
        </div>

        <!-- Submit Button Row -->
        <div class="flex items-center justify-between mt-8">
            <x-link href="{{ route('login') }}" text="Already have an account? Log in here!" class="text-sm text-gray-600 hover:text-gray-800" />
            <x-button wire:click="register" loading class="px-4 py-2 font-semibold text-white transition duration-200 bg-blue-600 rounded hover:bg-blue-700">
                Register
            </x-button>
        </div>
    </form>
</div>
