@props([
    'name' => 'Action',
    'active' => false
])

<a href="/anime?genre={{ strtolower($name) }}" class="px-4 py-2 text-xs md:text-sm font-black rounded-xl transition-all duration-200 uppercase tracking-wide border-2 border-white {{ $active ? 'bg-[#E63946] text-white shadow-[3px_3px_0px_#FFFFFF]' : 'bg-[#161616] text-white shadow-[2px_2px_0px_#FFFFFF] hover:bg-[#E63946]' }}">
    {{ $name }}
</a>
