<x-app-layout title="Register - EVONIME">

    <div class="relative min-h-[80vh] flex items-center justify-center py-12 px-4">
        
        <!-- Background Overlay -->
        <div class="absolute inset-0 w-full h-full overflow-hidden pointer-events-none">
            <img src="https://images.unsplash.com/photo-1578632767115-351597cf2477?q=80&w=1600&auto=format&fit=crop" alt="Anime Background" class="w-full h-full object-cover filter blur-md opacity-25">
            <div class="absolute inset-0 bg-gradient-to-t from-[#070707] via-[#070707]/80 to-[#070707]"></div>
        </div>

        <!-- Glass Register Card -->
        <div class="relative z-10 w-full max-w-md bg-[#101010]/90 backdrop-blur-xl border border-zinc-800 p-8 rounded-3xl shadow-2xl space-y-6">
            
            <div class="text-center space-y-1">
                <a href="/" class="text-3xl font-extrabold tracking-wider inline-block">
                    <span class="text-white">EVO<span class="text-violet-500">NIME</span></span>
                </a>
                <h2 class="text-xl font-bold text-white pt-2">Create Account</h2>
                <p class="text-xs text-zinc-400">Join EVONIME to save watchlists & sync history</p>
            </div>

            <!-- Register Form -->
            <form onsubmit="event.preventDefault(); window.showToast('Account Created Successfully!'); setTimeout(() => window.location.href='/login', 1000);" class="space-y-4">
                
                <div>
                    <label class="block text-xs font-bold uppercase text-zinc-400 mb-1.5">Username</label>
                    <input type="text" required placeholder="AnimeMaster99" class="w-full bg-[#1A1A1A] border border-zinc-800 text-white placeholder-zinc-500 text-sm rounded-xl px-4 py-3 focus:outline-none focus:border-violet-500">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-zinc-400 mb-1.5">Email Address</label>
                    <input type="email" required placeholder="yourname@domain.com" class="w-full bg-[#1A1A1A] border border-zinc-800 text-white placeholder-zinc-500 text-sm rounded-xl px-4 py-3 focus:outline-none focus:border-violet-500">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-zinc-400 mb-1.5">Password</label>
                    <input type="password" required placeholder="••••••••" class="w-full bg-[#1A1A1A] border border-zinc-800 text-white placeholder-zinc-500 text-sm rounded-xl px-4 py-3 focus:outline-none focus:border-violet-500">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-zinc-400 mb-1.5">Confirm Password</label>
                    <input type="password" required placeholder="••••••••" class="w-full bg-[#1A1A1A] border border-zinc-800 text-white placeholder-zinc-500 text-sm rounded-xl px-4 py-3 focus:outline-none focus:border-violet-500">
                </div>

                <button type="submit" class="w-full py-3.5 bg-violet-600 hover:bg-violet-500 text-white font-bold text-sm rounded-xl transition-all shadow-[0_0_20px_rgba(124,58,237,0.5)] hover:shadow-[0_0_25px_rgba(124,58,237,0.8)]">
                    Register
                </button>
            </form>

            <div class="text-center pt-2 border-t border-zinc-800/80 text-xs text-zinc-400">
                Already have an account? 
                <a href="/login" class="text-violet-400 font-bold hover:underline ml-1">Login</a>
            </div>

        </div>

    </div>

</x-app-layout>
