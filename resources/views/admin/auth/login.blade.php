<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - {{ $office?->name ?? 'Admin Panel' }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-gradient-to-br from-amber-50 via-white to-orange-50">
    <div class="flex min-h-full flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="text-center">
                @if($office?->logo)
                    <img src="{{ asset('storage/' . $office->logo) }}" class="mx-auto h-16 w-auto rounded-xl shadow-sm">
                @else
                    <div class="mx-auto h-14 w-14 rounded-2xl bg-amber-500 flex items-center justify-center shadow-lg">
                        <span class="text-white font-bold text-xl">{{ substr($office?->name ?? 'A', 0, 1) }}</span>
                    </div>
                @endif
                <h2 class="mt-5 text-2xl font-bold text-gray-900">{{ $office?->name ?? 'Admin Panel' }}</h2>
                <p class="mt-1.5 text-sm text-gray-500">Silakan login untuk melanjutkan</p>
            </div>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white px-8 py-8 shadow-xl rounded-2xl ring-1 ring-gray-100">
                @if(session('error'))
                    <div class="mb-5 rounded-xl bg-red-50 border border-red-200 p-3.5 flex items-start gap-3">
                        <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                        <p class="text-sm text-red-700">{{ session('error') }}</p>
                    </div>
                @endif
                @if($errors->any())
                    <div class="mb-5 rounded-xl bg-red-50 border border-red-200 p-3.5">
                        <ul class="list-disc pl-4 space-y-1">
                            @foreach($errors->all() as $error)
                                <li class="text-sm text-red-700">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.login.post') }}" class="space-y-5">
                    @csrf
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                               class="block w-full rounded-xl border border-gray-200 px-4 py-3 text-sm shadow-sm placeholder:text-gray-400 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 transition-all" placeholder="you@gmail.com">
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
                        <input type="password" id="password" name="password" required
                               class="block w-full rounded-xl border border-gray-200 px-4 py-3 text-sm shadow-sm placeholder:text-gray-400 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 transition-all" placeholder="Masukkan password">
                    </div>
                    <div class="flex items-center gap-2.5">
                        <input type="checkbox" id="remember" name="remember" class="h-4 w-4 rounded border-gray-300 text-amber-600 focus:ring-amber-500">
                        <label for="remember" class="text-sm text-gray-600">Ingat saya</label>
                    </div>
                    <button type="submit" class="w-full rounded-xl bg-amber-500 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-amber-600 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition-all duration-150 active:scale-[0.98]">
                        Masuk
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
