<x-guest-layout>
    <div class="min-h-screen flex flex-col justify-center items-center pt-24 pb-12 bg-slate-50 hero-pattern relative">
        <!-- Decorative Elements -->
        <div class="absolute top-40 left-10 w-72 h-72 bg-sky-100 rounded-full blur-3xl opacity-30 pointer-events-none"></div>
        <div class="absolute bottom-20 right-10 w-72 h-72 bg-orange-100 rounded-full blur-3xl opacity-30 pointer-events-none"></div>

        <div class="w-full sm:max-w-md px-6 py-4 relative z-10">
            <!-- Header Text -->
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-sky-600 rounded-2xl flex items-center justify-center text-white mx-auto mb-4 shadow-lg transform rotate-3 hover:rotate-0 transition-all duration-300">
                    <span class="font-bold text-2xl">N</span>
                </div>
                <h2 class="text-2xl font-bold text-slate-900">Reset Password</h2>
                <p class="text-sm text-slate-500 mt-2">Create a new secure password</p>
            </div>

            <!-- Reset Password Card -->
            <div class="bg-white px-8 py-8 shadow-xl overflow-hidden sm:rounded-2xl border border-slate-100">
                <form method="POST" action="{{ route('password.store') }}" x-data="{ loading: false }" @submit="loading = true">
                    @csrf

                    <!-- Password Reset Token -->
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <!-- Email Address -->
                    <div class="space-y-1">
                        <x-input-label for="email" :value="__('Email Address')" class="text-slate-700 font-semibold" />
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i data-lucide="mail" class="h-5 w-5 text-slate-400"></i>
                            </div>
                            <x-text-input id="email" class="block mt-1 w-full pl-10 border-slate-200 focus:border-sky-500 focus:ring-sky-500 rounded-lg py-2.5" 
                                            type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" placeholder="name@example.com" />
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div class="mt-4 space-y-1">
                        <x-input-label for="password" :value="__('New Password')" class="text-slate-700 font-semibold" />
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i data-lucide="lock" class="h-5 w-5 text-slate-400"></i>
                            </div>
                            <x-text-input id="password" class="block mt-1 w-full pl-10 border-slate-200 focus:border-sky-500 focus:ring-sky-500 rounded-lg py-2.5" 
                                            type="password" name="password" required autocomplete="new-password" placeholder="Min. 8 characters" />
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Confirm Password -->
                    <div class="mt-4 space-y-1">
                        <x-input-label for="password_confirmation" :value="__('Confirm New Password')" class="text-slate-700 font-semibold" />
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i data-lucide="lock-check" class="h-5 w-5 text-slate-400"></i>
                            </div>
                            <x-text-input id="password_confirmation" class="block mt-1 w-full pl-10 border-slate-200 focus:border-sky-500 focus:ring-sky-500 rounded-lg py-2.5"
                                            type="password"
                                            name="password_confirmation" required autocomplete="new-password" placeholder="Re-enter new password" />
                        </div>
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>

                    <div class="mt-6">
                        <button type="submit" :disabled="loading" :class="{'opacity-75 cursor-wait': loading}" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-lg text-sm font-bold text-white bg-sky-600 hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 transition-all transform hover:-translate-y-0.5">
                            <span x-show="!loading">{{ __('Reset Password') }}</span>
                            <span x-show="loading">{{ __('Resetting...') }}</span>
                            <i data-lucide="refresh-cw" class="ml-2 h-5 w-5" x-show="!loading"></i>
                            <i data-lucide="loader-2" class="ml-2 h-5 w-5 animate-spin" x-show="loading" style="display: none;"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
