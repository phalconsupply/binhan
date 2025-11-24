<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ setting('site_name', config('app.name', 'Laravel')) }}</title>

        <!-- Favicon -->
        @php
            $faviconPath = setting('site_favicon');
            $faviconUrl = $faviconPath ? asset('storage/' . $faviconPath) : asset('favicon.ico');
        @endphp
        @if($faviconPath && file_exists(public_path('storage/' . $faviconPath)))
            <link rel="icon" type="image/png" href="{{ $faviconUrl }}">
        @elseif(file_exists(public_path('favicon.ico')))
            <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
        @endif

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            body {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            }
        </style>
    </head>
    <body class="antialiased" style="font-family: 'Inter', sans-serif;">
        <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-md w-full space-y-8">
                <!-- Logo and Title -->
                <div class="text-center">
                    @php
                        $logoPath = setting('site_logo');
                        $logoUrl = $logoPath ? asset('storage/' . $logoPath) : asset('logo.png');
                    @endphp
                    @if($logoPath && file_exists(public_path('storage/' . $logoPath)))
                        <img src="{{ $logoUrl }}" alt="{{ setting('company_name', 'Binhan') }}" class="mx-auto h-20 w-auto max-w-xs object-contain mb-4">
                    @elseif(file_exists(public_path('logo.png')))
                        <img src="{{ asset('logo.png') }}" alt="Binhan Logo" class="mx-auto h-20 w-auto max-w-xs object-contain mb-4">
                    @endif
                    <h2 class="text-3xl font-extrabold text-white">
                        {{ setting('site_name', 'Hệ Thống Quản Lý Xe Cấp Cứu') }}
                    </h2>
                    <p class="mt-2 text-sm text-indigo-100">
                        {{ setting('company_name', 'Binhan Ambulance Management System') }}
                    </p>
                </div>

                <!-- Login Form Card -->
                <div class="bg-white rounded-xl shadow-2xl overflow-hidden">
                    <div class="px-8 py-10">
                        {{ $slot }}
                    </div>
                </div>
                
                <!-- Footer -->
                <div class="text-center">
                    <p class="text-xs text-indigo-100">
                        © {{ date('Y') }} Binhan. All rights reserved.
                    </p>
                </div>
            </div>
        </div>
    </body>
</html>
