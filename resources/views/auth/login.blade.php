<x-app-layout title="Admin Login - EVONIME">

    <div class="relative min-h-[80vh] flex items-center justify-center py-12 px-4 speed-lines">
        
        <!-- Cinematic Manga Background Overlay -->
        <div class="absolute inset-0 w-full h-full overflow-hidden pointer-events-none">
            <div class="absolute inset-0 bg-[#0D0D0D]"></div>
            <div class="absolute inset-0 halftone-bg opacity-40"></div>
        </div>

        <!-- Manga Panel Login Card -->
        <div class="relative z-10 w-full max-w-md bg-[#1A1A1A] border-4 border-[#F5F0E6] p-8 shadow-[8px_8px_0px_#F5F0E6] space-y-6 text-[#F5F0E6]">
            
            <!-- Chapter Stamp -->
            <div class="flex items-center justify-between border-b-2 border-[#F5F0E6] pb-3">
                <span class="text-[10px] font-black tracking-widest bg-[#E63946] text-white px-2 py-0.5 border border-[#F5F0E6]">SECURE AUTH // PANEL 01</span>
                <span class="text-xs font-mono font-bold text-zinc-400">ADMIN ACCESS</span>
            </div>

            <div class="text-center space-y-1">
                <a href="/" class="text-3xl font-black tracking-wider inline-block" style="font-family: 'Anton', sans-serif;">
                    <span class="text-[#0D0D0D] bg-[#F5F0E6] px-2 py-0.5 border-2 border-[#F5F0E6] shadow-[2px_2px_0px_#E63946]">EVO</span><span class="text-[#E63946] ml-1">NIME</span>
                </a>
                <h2 class="text-2xl font-black text-[#F5F0E6] pt-2" style="font-family: 'Anton', sans-serif;">ADMIN LOGIN</h2>
                <p class="text-xs text-zinc-400 font-bold">Authorized personnel only</p>
            </div>

            <!-- Validation Errors -->
            @if ($errors->any())
                <div class="p-3 bg-[#E63946]/20 border-2 border-[#E63946] text-xs font-bold text-[#F5F0E6] shadow-[2px_2px_0px_#E63946]">
                    ⚠️ {{ $errors->first() }}
                </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf
                
                <div>
                    <label class="block text-xs font-black uppercase text-zinc-300 mb-1.5">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="admin@example.com" class="w-full bg-[#141414] border-2 border-[#F5F0E6] text-[#F5F0E6] placeholder-zinc-600 text-sm font-bold px-4 py-3 focus:outline-none focus:border-[#E63946] shadow-[2px_2px_0px_#F5F0E6]">
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-black uppercase text-zinc-300">Password</label>
                    </div>
                    <input type="password" name="password" required placeholder="••••••••" class="w-full bg-[#141414] border-2 border-[#F5F0E6] text-[#F5F0E6] placeholder-zinc-600 text-sm font-bold px-4 py-3 focus:outline-none focus:border-[#E63946] shadow-[2px_2px_0px_#F5F0E6]">
                </div>

                <div class="flex items-center gap-2 pt-1">
                    <input type="checkbox" name="remember" id="remember" class="w-4 h-4 accent-[#E63946] rounded cursor-pointer">
                    <label for="remember" class="text-xs text-zinc-300 font-bold cursor-pointer">Remember me for 30 days</label>
                </div>

                <button type="submit" class="manga-button-primary w-full py-3.5 text-sm uppercase rounded-lg text-white font-extrabold">
                    LOGIN →
                </button>
            </form>

            <div class="text-center pt-3 border-t-2 border-zinc-800 text-xs text-zinc-400 font-bold">
                Default Seeder: <span class="text-[#E63946] font-mono">admin@example.com</span> / <span class="text-[#E63946] font-mono">Admin12345!</span>
            </div>

        </div>

    </div>

</x-app-layout>
