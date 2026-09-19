<x-admin-layout title="Genres">
<div class="max-w-4xl mx-auto space-y-6 text-[#F5F0E6]">
@if(session('success'))<div class="bg-green-600 border-2 border-[#F5F0E6] p-3 text-sm font-black text-white">{{ session('success') }}</div>@endif
<div class="flex justify-between items-center bg-[#1A1A1A] border-2 border-[#F5F0E6] p-5">
<h1 class="text-2xl font-black">GENRES ({{ $genres->total() }})</h1>
<a href="{{ route('admin.genres.create') }}" class="px-4 py-2 bg-[#E63946] border-2 border-[#F5F0E6] text-xs font-black">+ GENRE</a>
</div>
<div class="bg-[#1A1A1A] border-2 border-[#F5F0E6] overflow-x-auto">
<table class="table-auto w-full text-left"><thead><tr class="bg-[#141414] text-xs font-black uppercase"><th class="p-3">Nama</th><th class="p-3">Slug</th><th class="p-3">Anime</th><th class="p-3">Aksi</th></tr></thead>
<tbody class="divide-y divide-zinc-800 text-xs font-bold">
@foreach($genres as $g)<tr class="hover:bg-zinc-900/60">
<td class="p-3 text-white">{{ $g->name }}</td><td class="p-3">{{ $g->slug }}</td><td class="p-3">{{ $g->animes_count ?? '-' }}</td>
<td class="p-3 space-x-1"><a href="{{ route('admin.genres.edit', $g) }}" class="px-2 py-1 bg-blue-600 text-white text-[10px] font-black">EDIT</a>
<form method="POST" action="{{ route('admin.genres.destroy', $g) }}" class="inline" onsubmit="return confirm('Hapus genre?')">@csrf @method('DELETE')<button class="px-2 py-1 bg-red-600 text-white text-[10px] font-black">DEL</button></form></td></tr>
@endforeach
</tbody></table></div>
<div class="text-white">{{ $genres->links() }}</div>
</div></x-admin-layout>
