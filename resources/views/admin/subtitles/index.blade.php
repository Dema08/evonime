<x-admin-layout title="Subtitles">
<div class="max-w-5xl mx-auto space-y-6 text-[#F5F0E6]">
@if(session('success'))<div class="bg-green-600 border-2 border-[#F5F0E6] p-3 text-sm font-black text-white">{{ session('success') }}</div>@endif
<div class="flex justify-between items-center bg-[#1A1A1A] border-2 border-[#F5F0E6] p-5">
<div><h1 class="text-xl font-black">SUBTITLE EP {{ $episode->episode_number }}</h1></div>
<a href="{{ route('admin.episodes.subtitles.create', $episode) }}" class="px-4 py-2 bg-[#E63946] border-2 border-[#F5F0E6] text-xs font-black">+ SUBTITLE</a>
</div>
<div class="bg-[#1A1A1A] border-2 border-[#F5F0E6] overflow-x-auto">
<table class="table-auto w-full text-left"><thead><tr class="bg-[#141414] text-xs font-black uppercase"><th class="p-3">Bahasa</th><th class="p-3">Label</th><th class="p-3">Default</th><th class="p-3">Aksi</th></tr></thead>
<tbody class="divide-y divide-zinc-800 text-xs font-bold">
@forelse($subtitles as $s)<tr class="hover:bg-zinc-900/60">
<td class="p-3 text-white">{{ $s->language instanceof \BackedEnum ? $s->language->value : $s->language }}</td><td class="p-3">{{ $s->label }}</td><td class="p-3">{{ $s->is_default ? '⭐' : '-' }}</td>
<td class="p-3 space-x-1"><a href="{{ route('admin.subtitles.edit', $s) }}" class="px-2 py-1 bg-blue-600 text-white text-[10px] font-black">EDIT</a>
<form method="POST" action="{{ route('admin.subtitles.destroy', $s) }}" class="inline" onsubmit="return confirm('Hapus subtitle?')">@csrf @method('DELETE')<button class="px-2 py-1 bg-red-600 text-white text-[10px] font-black">DEL</button></form></td></tr>
@empty<tr><td colspan="4" class="p-6 text-center text-zinc-500">Belum ada subtitle.</td></tr>@endforelse
</tbody></table></div>
<div class="text-white">{{ $subtitles->links() }}</div>
</div></x-admin-layout>
