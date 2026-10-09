<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Login — Kenora Community Training Centre Workshop Registration">
    <title>Sign In — Kenora Training Centre</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full">
    <div class="flex min-h-full flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="text-center text-4xl mb-3">🏛️</div>
            <h1 class="text-center text-2xl font-bold tracking-tight text-gray-900">
                Kenora Training Centre
            </h1>
            <p class="mt-2 text-center text-sm text-gray-600">Workshop Registration System</p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white px-6 py-8 shadow-md rounded-lg sm:px-10">
                <h2 class="mb-6 text-center text-xl font-semibold text-gray-800">Sign in to your account</h2>

                @if($errors->any())
                    <div class="mb-4 rounded-md bg-red-50 p-4 border border-red-200">
                        <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login.post') }}" class="space-y-5">
                    @csrf
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                            Email address
                        </label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="email"
                            class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm
                                   focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500
                                   @error('email') border-red-400 bg-red-50 @enderror"
                        >
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                            Password
                        </label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm
                                   focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        >
                    </div>

                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600">
                        <label for="remember" class="ml-2 block text-sm text-gray-700">Remember me</label>
                    </div>

                    <button
                        type="submit"
                        class="w-full flex justify-center rounded-md bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white
                               shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2
                               focus-visible:outline-indigo-600 transition"
                    >
                        Sign in
                    </button>
                </form>

                <div class="mt-6 rounded-md bg-gray-50 p-3 text-xs text-gray-500 border border-gray-200">
                    <strong class="text-gray-700">Demo accounts:</strong><br>
                    admin@example.com / manager@example.com / staff@example.com<br>
                    <span class="text-gray-400">Password: <code>password</code></span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
