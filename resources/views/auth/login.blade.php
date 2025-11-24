<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />
    
    <div class="mb-6">
        <h3 class="text-2xl font-bold text-gray-800 text-center">Đăng Nhập</h3>
        <p class="text-sm text-gray-600 text-center mt-1">Vui lòng nhập thông tin đăng nhập</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Email" class="text-gray-700 font-medium" />
            <x-text-input id="email" 
                class="block mt-2 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition duration-200" 
                type="email" 
                name="email" 
                :value="old('email')" 
                required 
                autofocus 
                autocomplete="username"
                placeholder="admin@binhan.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" value="Mật khẩu" class="text-gray-700 font-medium" />
            <x-text-input id="password" 
                class="block mt-2 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition duration-200"
                type="password"
                name="password"
                required 
                autocomplete="current-password"
                placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" 
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 transition duration-200" 
                    name="remember">
                <span class="ms-2 text-sm text-gray-600 hover:text-gray-800">Ghi nhớ đăng nhập</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-indigo-600 hover:text-indigo-800 font-medium transition duration-200" 
                   href="{{ route('password.request') }}">
                    Quên mật khẩu?
                </a>
            @endif
        </div>

        <!-- Submit Button -->
        <div>
            <button type="submit" 
                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-200 transform hover:scale-[1.02]">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                </svg>
                Đăng nhập
            </button>
        </div>
        
        <!-- Test Accounts Info -->
        <div class="mt-6 pt-6 border-t border-gray-200">
            <p class="text-xs text-gray-500 text-center mb-3">Tài khoản test:</p>
            <div class="grid grid-cols-2 gap-2 text-xs">
                <div class="bg-gray-50 rounded p-2">
                    <p class="font-semibold text-gray-700">Admin</p>
                    <p class="text-gray-500">admin@binhan.com</p>
                </div>
                <div class="bg-gray-50 rounded p-2">
                    <p class="font-semibold text-gray-700">Dispatcher</p>
                    <p class="text-gray-500">dispatcher@binhan.com</p>
                </div>
                <div class="bg-gray-50 rounded p-2">
                    <p class="font-semibold text-gray-700">Accountant</p>
                    <p class="text-gray-500">accountant@binhan.com</p>
                </div>
                <div class="bg-gray-50 rounded p-2">
                    <p class="font-semibold text-gray-700">Driver</p>
                    <p class="text-gray-500">driver@binhan.com</p>
                </div>
            </div>
            <p class="text-xs text-gray-400 text-center mt-2">Password: <code class="bg-gray-100 px-1 rounded">password</code></p>
        </div>
    </form>
</x-guest-layout>
