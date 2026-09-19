<x-app-layout title="Login - EVONIME">

    <div class="relative min-h-[75vh] flex items-center justify-center py-12 px-4">
        
        <!-- Cinematic Background Overlay -->
        <div class="absolute inset-0 w-full h-full overflow-hidden pointer-events-none">
            <img src="https://images.unsplash.com/photo-1607604276583-eef5d076aa5f?q=80&w=1600&auto=format&fit=crop" alt="Anime Background" class="w-full h-full object-cover filter blur-md opacity-25">
            <div class="absolute inset-0 bg-gradient-to-t from-[#070707] via-[#070707]/80 to-[#070707]"></div>
        </div>

        <!-- Glass Login Card -->
        <div class="relative z-10 w-full max-w-md bg-[#101010]/90 backdrop-blur-xl border border-zinc-800 p-8 rounded-3xl shadow-2xl space-y-6">
            
            <div class="text-center space-y-1">
                <a href="/" class="text-3xl font-extrabold tracking-wider inline-block">
                    <span class="text-white">EVO<span class="text-violet-500">NIME</span></span>
                </a>
                <h2 class="text-xl font-bold text-white pt-2">Welcome Back</h2>
                <p class="text-xs text-zinc-400">Sign in to your account to resume streaming</p>
            </div>

            <!-- Login Form (Frontend Only) -->
            <form onsubmit="event.preventDefault(); window.showToast('Demo Login Successful!'); setTimeout(() => window.location.href='/', 1000);" class="space-y-4">
                
                <div>
                    <label class="block text-xs font-bold uppercase text-zinc-400 mb-1.5">Email Address</label>
                    <input type="email" required placeholder="yourname@domain.com" class="w-full bg-[#1A1A1A] border border-zinc-800 text-white placeholder-zinc-500 text-sm rounded-xl px-4 py-3 focus:outline-none focus:border-violet-500">
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-bold uppercase text-zinc-400">Password</label>
                        <a href="#" onclick="event.preventDefault(); window.showToast('Demo Password Reset link sent!')" class="text-xs text-violet-400 hover:underline">Forgot password?</a>
                    </div>
                    <input type="password" required placeholder="••••••••" class="w-full bg-[#1A1A1A] border border-zinc-800 text-white placeholder-zinc-500 text-sm rounded-xl px-4 py-3 focus:outline-none focus:border-violet-500">
                </div>

                <div class="flex items-center gap-2 pt-1">
                    <input type="checkbox" id="remember" class="w-4 h-4 accent-violet-600 rounded cursor-pointer">
                    <label for="remember" class="text-xs text-zinc-300 font-medium cursor-pointer">Remember me for 30 days</label>
                </div>

                <button type="submit" class="w-full py-3.5 bg-violet-600 hover:bg-violet-500 text-white font-bold text-sm rounded-xl transition-all shadow-[0_0_20px_rgba(124,58,237,0.5)] hover:shadow-[0_0_25px_rgba(124,58,237,0.8)]">
                    Login
                </button>
            </form>

            <div class="text-center pt-2 border-t border-zinc-800/80 text-xs text-zinc-400">
                Don't have an account? 
                <a href="/register" class="text-violet-400 font-bold hover:underline ml-1">Create Account</a>
            </div>

        </div>

    </div>

</x-app-layout>
