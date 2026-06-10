<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') | {{ $office?->name ?? 'Admin' }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="h-full bg-gray-50">

<div class="flex h-full">
    {{-- Desktop sidebar --}}
    <aside class="hidden lg:flex lg:w-72 lg:flex-shrink-0 lg:flex-col lg:h-full bg-white border-r border-gray-200">
        <div class="flex h-16 items-center gap-3 px-6 border-b border-gray-100 flex-shrink-0">
            @if($office?->logo)
                <img src="{{ asset('storage/' . $office->logo) }}" class="h-9 w-9 rounded-lg object-cover" alt="logo">
            @else
                <div class="h-9 w-9 rounded-lg bg-amber-500 flex items-center justify-center flex-shrink-0">
                    <span class="text-white font-bold text-sm">{{ substr($office?->name ?? 'A', 0, 1) }}</span>
                </div>
            @endif
            <div class="min-w-0">
                <p class="text-sm font-semibold text-gray-900 truncate">{{ $office?->name ?? 'Admin' }}</p>
                <p class="text-xs text-gray-500">Money Changer</p>
            </div>
        </div>
        @include('layouts._sidebar-nav')
        <div class="border-t border-gray-100 p-4 flex-shrink-0">
            @include('layouts._sidebar-user')
        </div>
    </aside>

    {{-- Main area --}}
    <div class="flex flex-1 flex-col min-w-0 h-full">
        <header class="flex items-center h-16 px-4 lg:px-8 gap-4 bg-white/90 backdrop-blur-sm border-b border-gray-200 flex-shrink-0">
            <button x-data x-on:click="$dispatch('open-mobile-sidebar')" class="lg:hidden p-2 -ml-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
            </button>
            <div>
                <h1 class="text-lg font-bold text-gray-900">@yield('heading', 'Dashboard')</h1>
                @if(trim($__env->yieldContent('subheading')))
                    <p class="text-sm text-gray-500">@yield('subheading')</p>
                @endif
            </div>
            <div class="flex-1"></div>
            @if(trim($__env->yieldContent('actions')))
                <div class="flex items-center gap-2">@yield('actions')</div>
            @endif
        </header>

        <div class="flex-1 overflow-y-auto p-4 lg:p-8">
            @if(session('success'))
                <div class="mb-6 rounded-xl bg-emerald-50 border border-emerald-200 p-4 flex items-start gap-3">
                    <svg class="w-5 h-5 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-sm text-emerald-700">{{ session('success') }}</p>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 rounded-xl bg-red-50 border border-red-200 p-4 flex items-start gap-3">
                    <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                </div>
            @endif
            @if($errors->any())
                <div class="mb-6 rounded-xl bg-red-50 border border-red-200 p-4 flex items-start gap-3">
                    <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                    <div>
                        <p class="text-sm font-medium text-red-800">{{ $errors->count() }} kesalahan:</p>
                        <ul class="mt-1 list-disc pl-4 space-y-0.5">
                            @foreach($errors->all() as $error)
                                <li class="text-sm text-red-700">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            @yield('content')
        </div>
    </div>
</div>

{{-- Mobile sidebar --}}
<div x-data="{ open: false }"
     x-on:open-mobile-sidebar.window="open = true"
     class="lg:hidden">
    <div x-show="open" x-cloak x-on:click="open = false"
         class="fixed inset-0 z-40 bg-gray-900/60 backdrop-blur-sm"></div>
    <div x-show="open" x-cloak
         x-on:click.outside="open = false"
         class="fixed inset-y-0 left-0 z-50 w-72 bg-white border-r border-gray-200 flex flex-col shadow-2xl"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="-translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="-translate-x-full">
        <div class="flex h-16 items-center gap-3 px-6 border-b border-gray-100 flex-shrink-0">
            @if($office?->logo)
                <img src="{{ asset('storage/' . $office->logo) }}" class="h-9 w-9 rounded-lg object-cover" alt="logo">
            @else
                <div class="h-9 w-9 rounded-lg bg-amber-500 flex items-center justify-center flex-shrink-0">
                    <span class="text-white font-bold text-sm">{{ substr($office?->name ?? 'A', 0, 1) }}</span>
                </div>
            @endif
            <p class="text-sm font-semibold text-gray-900 truncate">{{ $office?->name ?? 'Admin' }}</p>
        </div>
        @include('layouts._sidebar-nav')
        <div class="border-t border-gray-100 p-4 flex-shrink-0">
            @include('layouts._sidebar-user')
        </div>
    </div>
</div>

@stack('scripts')
</body>
</html>
