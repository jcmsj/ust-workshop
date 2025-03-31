<x-guest-layout>
    <div class="h-full">
        <!-- Simple hero for private app -->
        <div class="hero min-h-screen bg-base-200">
            <div class="hero-content text-center">
                <div class="text-4xl flex flex-col items-center">
                    <h1 class=" font-bold text-primary">
                        <img src="/CTP-logo.png" alt="" class="h-64 m-auto">
                        <img src="/LEADS.webp" alt="" class="h-20 m-auto">
                    </h1>
                    <div class="max-w-lg">
                        <p class="py-6">Streamline your customer relationship management with our powerful lead tracking
                            and conversion optimization platform.</p>

                        @guest
                        <a class="btn btn-primary btn-lg text-2xl p-2" href="/app/login">
                            <x-heroicon-o-lock-closed class="h-8 w-8 inline-block" />
                            Login
                        </a>
                        @endguest
                        @auth
                        <a class="btn btn-primary btn-lg text-2xl p-2"
                            href=" {{Auth::user()->isAdmin() ? route('filament.admin.pages.dashboard') : route('filament.app.pages.dashboard')}}">
                            <x-heroicon-o-home class="h-8 w-8 inline-block" />
                            {{
                            Auth::user()->isAdmin() ? 'Go to admin dashboard' : 'Go to my dashboard'
                            }}
                        </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
