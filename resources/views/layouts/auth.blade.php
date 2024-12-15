@extends('layouts.base')

@section('body')
    <x-toast />
    <div class="sm:px-6 lg:px-8">
        @yield('content')
        @isset($slot)
            {{ $slot }}
        @endisset
    </div>
@endsection
