@props([
    'name' => 'Action',
    'active' => false
])

<a href="/anime?genre={{ strtolower($name) }}" class="inline-flex items-center justify-center px-4 py-2 text-xs md:text-sm font-semibold rounded-xl transition-all duration-200 border {{ $active ? 'bg-violet-600 text-white border-violet-500 shadow-[0_0_15px_rgba(124,58,237,0.5)]' : 'bg-[#151515] hover:bg-violet-600 text-zinc-300 hover:text-white border-zinc-800 hover:border-violet-500/50' }}">
    {{ $name }}
</a>
