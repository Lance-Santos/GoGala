<!-- resources/views/landing.blade.php -->

@extends('layouts.app')

@section('content')
<div class="h-screen overflow-y-scroll font-sans text-white bg-gray-900 snap-y snap-mandatory scroll-smooth">
    <section class="snap-start relative w-full h-screen flex flex-col items-center justify-center bg-gradient-to-br from-[#4f46e5] to-[#475569] text-center">
        <h1 class="mb-6 text-6xl font-bold text-white drop-shadow-md animate-fade-in">EMS</h1>
        <div class="flex space-x-4 animate-fade-in">
            <x-button text="View Events" class="px-6 py-3 text-lg" href="{{route('events')}}"/>
        </div>
    </section>

    <!-- Feature Section -->
    <section class="flex flex-col items-center justify-center h-screen px-4 text-center bg-gray-900 snap-start">
        <div x-data="{ show: false }" x-intersect="show = true" class="max-w-2xl mx-auto">
            <x-icon name="calendar" class="w-16 h-16 mb-4 text-white animate-fade-in-large" />
            <h2 x-show="show" x-transition.duration.800ms class="mb-4 text-4xl font-semibold">Effortless Event Scheduling</h2>
            <p x-show="show" x-transition.delay.200ms class="max-w-lg text-lg text-gray-400">
                Plan and organize events with ease using our powerful scheduling tools, designed for a seamless experience.
            </p>
        </div>
    </section>

    <!-- Notification Section -->
    <section class="flex flex-col items-center justify-center h-screen px-4 text-center bg-gray-900 snap-start">
        <div x-data="{ show: false }" x-intersect="show = true" class="max-w-2xl mx-auto">
            <x-icon name="bell" class="w-16 h-16 mb-4 text-white animate-fade-in-large" />
            <h2 x-show="show" x-transition.duration.800ms class="mb-4 text-4xl font-semibold">Instant Notifications</h2>
            <p x-show="show" x-transition.delay.200ms class="max-w-lg text-lg text-gray-400">
                Stay updated with real-time notifications on event changes, ensuring you’re always informed.
            </p>
        </div>
    </section>

    <!-- Analytics Section -->
    <section class="flex flex-col items-center justify-center h-screen px-4 text-center bg-gray-900 snap-start">
        <div x-data="{ show: false }" x-intersect="show = true" class="max-w-2xl mx-auto">
            <x-icon name="chart-bar" class="w-16 h-16 mb-4 text-white animate-fade-in-large" />
            <h2 x-show="show" x-transition.duration.800ms class="mb-4 text-4xl font-semibold">Detailed Analytics</h2>
            <p x-show="show" x-transition.delay.200ms class="max-w-lg text-lg text-gray-400">
                Track and measure the success of your events with comprehensive analytics and insights.
            </p>
        </div>
    </section>

    <!-- Support Section -->
    <section class="flex flex-col items-center justify-center h-screen px-4 text-center bg-gray-900 snap-start">
        <div x-data="{ show: false }" x-intersect="show = true" class="max-w-2xl mx-auto">
            <x-icon name="chat-bubble-left" class="w-16 h-16 mb-4 text-white animate-fade-in-large" />
            <h2 x-show="show" x-transition.duration.800ms class="mb-4 text-4xl font-semibold">24/7 Support</h2>
            <p x-show="show" x-transition.delay.200ms class="max-w-lg text-lg text-gray-400">
                Our support team is here to assist you around the clock, ensuring smooth and successful events.
            </p>
        </div>
    </section>

    <!-- Call to Action Section -->
    <section class="snap-start h-screen flex flex-col items-center justify-center bg-gradient-to-br from-[#4f46e5] to-[#475569] text-center">
        <div x-data="{ show: false }" x-intersect="show = true">
            <h2 x-show="show" x-transition.duration.800ms class="mb-6 text-4xl font-semibold text-white">Ready to Get Started?</h2>
            <p x-show="show" x-transition.delay.200ms class="mb-8 text-lg text-gray-200">
                Sign up today and revolutionize your event management experience with EMS.
            </p>
            <x-link href="https://google.com.br" text="Join Now" class="text-lg px-8 py-4 font-bold bg-white text-[#4f46e5] rounded-lg shadow-lg hover:bg-gray-100" />
        </div>
    </section>

    <!-- Footer Section -->
    <footer class="w-full py-8 text-gray-400 bg-gray-900 snap-end">
        <div class="px-4 mx-auto text-center max-w-7xl">
            <p class="mb-4">© {{ now()->year }} Event Management System (EMS) - All Rights Reserved</p>
            <x-link href="https://google.com.br" text="Visit us" />
        </div>
    </footer>
</div>

<style>
/* Snap Scrolling */
.snap-y {
    scroll-snap-type: y mandatory;
}
.snap-start {
    scroll-snap-align: start;
}

/* Fade-in Animation */
.animate-fade-in {
    opacity: 0;
    transform: translateY(20px);
    animation: fadeIn 1s forwards;
}
.animate-fade-in-large {
    opacity: 0;
    transform: translateY(30px);
    animation: fadeInLarge 1s forwards;
}

/* Keyframes for fade-in */
@keyframes fadeIn {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
@keyframes fadeInLarge {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
@endsection
