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
                <h2 class="text-2xl font-bold text-slate-900">Verify Email</h2>
                <p class="text-sm text-slate-500 mt-2">Almost there! Just one more step.</p>
            </div>

            <!-- Verify Email Card -->
            <div class="bg-white px-8 py-8 shadow-xl overflow-hidden sm:rounded-2xl border border-slate-100">
                <div class="mb-6 text-sm text-slate-600">
                    {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
                </div>

                @if (session('status') == 'verification-link-sent')
                    <div class="mb-6 font-medium text-sm text-green-600 bg-green-50 p-4 rounded-lg border border-green-100 flex items-start gap-2">
                        <i data-lucide="check-circle" class="h-5 w-5 mt-0.5 resize-none"></i>
                        <span>{{ __('A new verification link has been sent to the email address you provided during registration.') }}</span>
                    </div>
                @endif

                <div class="mt-4 flex flex-col gap-4">
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-lg text-sm font-bold text-white bg-sky-600 hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 transition-all transform hover:-translate-y-0.5">
                            {{ __('Resend Verification Email') }} <i data-lucide="send" class="ml-2 h-5 w-5"></i>
                        </button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}" class="text-center">
                        @csrf
                        <button type="submit" class="text-sm text-slate-600 hover:text-red-600 underline focus:outline-none transition-colors">
                            {{ __('Log Out') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
