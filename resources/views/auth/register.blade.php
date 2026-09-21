<x-app-layout title="Register - EVONIME">

    <div class="relative min-h-[80vh] flex items-center justify-center pt-28 pb-16 px-4">
        
        <!-- Background Overlay -->
        <div class="absolute inset-0 w-full h-full overflow-hidden pointer-events-none">
            <img referrerpolicy="no-referrer" src="https://images.unsplash.com/photo-1578632767115-351597cf2477?q=80&w=1600&auto=format&fit=crop" alt="Anime Background" class="w-full h-full object-cover filter blur-md opacity-25">
            <div class="absolute inset-0 bg-gradient-to-t from-[#070707] via-[#070707]/80 to-[#070707]"></div>
        </div>

        <!-- Glass Register Card -->
        <div class="relative z-10 w-full max-w-md bg-[#101010]/90 backdrop-blur-xl border border-zinc-800 p-8 rounded-3xl shadow-2xl space-y-6">
            
            <div class="text-center space-y-1">
                <a href="/" class="text-3xl font-extrabold tracking-wider inline-block">
                    <span class="text-white">EVO<span class="text-red-500">NIME</span></span>
                </a>
                <h2 class="text-xl font-bold text-white pt-2">Create Account</h2>
                <p class="text-xs text-zinc-400">Join EVONIME to save watchlists & sync history</p>
            </div>

            <!-- Validation Errors -->
            @if ($errors->any())
                <div class="p-3 bg-red-500/20 border border-red-500 text-xs font-bold text-red-200 rounded-xl">
                    ⚠️ {{ $errors->first() }}
                </div>
            @endif

            <!-- Register with Google Button -->
            <a href="{{ route('auth.google') }}" class="w-full py-3.5 bg-zinc-900 hover:bg-zinc-800 text-white font-bold text-sm rounded-xl border border-zinc-700 flex items-center justify-center gap-3 transition cursor-pointer shadow-lg">
                <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                </svg>
                <span>Register with Google</span>
            </a>

            <!-- Separator: atau -->
            <div class="relative flex items-center justify-center my-2">
                <div class="border-t border-zinc-800 w-full"></div>
                <span class="bg-[#101010] px-3 text-xs uppercase font-bold tracking-widest text-zinc-500">atau</span>
                <div class="border-t border-zinc-800 w-full"></div>
            </div>

            <!-- Register Form -->
            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase text-zinc-400 mb-1.5">Username</label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="AnimeMaster99" class="w-full bg-[#1A1A1A] border border-zinc-800 text-white placeholder-zinc-500 text-sm rounded-xl px-4 py-3 focus:outline-none focus:border-red-500">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-zinc-400 mb-1.5">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="yourname@domain.com" class="w-full bg-[#1A1A1A] border border-zinc-800 text-white placeholder-zinc-500 text-sm rounded-xl px-4 py-3 focus:outline-none focus:border-red-500">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-zinc-400 mb-1.5">Password</label>
                    <input type="password" name="password" required placeholder="••••••••" class="w-full bg-[#1A1A1A] border border-zinc-800 text-white placeholder-zinc-500 text-sm rounded-xl px-4 py-3 focus:outline-none focus:border-red-500">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-zinc-400 mb-1.5">Confirm Password</label>
                    <input type="password" name="password_confirmation" required placeholder="••••••••" class="w-full bg-[#1A1A1A] border border-zinc-800 text-white placeholder-zinc-500 text-sm rounded-xl px-4 py-3 focus:outline-none focus:border-red-500">
                </div>

                <button type="submit" class="manga-button-primary w-full py-3.5 text-white font-extrabold text-sm rounded-xl cursor-pointer">
                    REGISTER NOW
                </button>
            </form>

            <div class="text-center pt-2 border-t border-zinc-800/80 text-xs text-zinc-400">
                Already have an account? 
                <a href="{{ route('login') }}" class="text-red-400 font-bold hover:underline ml-1">Login</a>
            </div>

        </div>

    </div>

</x-app-layout>
