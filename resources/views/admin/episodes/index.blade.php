<x-admin-layout title="Episode">
<div class="max-w-6xl mx-auto space-y-6 text-[#F5F0E6]">
@if(session('success'))<div class="bg-green-600 border-2 border-[#F5F0E6] p-3 text-sm font-black text-white">{{ session('success') }}</div>@endif
<div class="flex justify-between items-center bg-[#1A1A1A] border-2 border-[#F5F0E6] p-5">
<div><h1 class="text-2xl font-black">EPISODE @if($anime ?? null): {{ $anime->title }}@endif</h1></div>
<a href="{{ ($anime ?? null) ? route('admin.animes.episodes.create', $anime) : '#' }}" class="px-4 py-2 bg-[#E63946] border-2 border-[#F5F0E6] text-xs font-black">+ EPISODE</a>
</div>
<div class="bg-[#1A1A1A] border-2 border-[#F5F0E6] overflow-x-auto">
<table class="table-auto w-full text-left"><thead><tr class="bg-[#141414] text-xs font-black uppercase"><th class="p-3">No</th><th class="p-3">Judul</th><th class="p-3">Status</th><th class="p-3">Views</th><th class="p-3">Aksi</th></tr></thead>
<tbody class="divide-y divide-zinc-800 text-xs font-bold">
@forelse($episodes as $ep)<tr class="hover:bg-zinc-900/60">
<td class="p-3">EP {{ $ep->episode_number }}</td><td class="p-3 text-white">{{ $ep->title ?? '-' }}</td>
<td class="p-3">{{ $ep->status instanceof \BackedEnum ? $ep->status->value : $ep->status }}</td>
<td class="p-3">{{ number_format($ep->views_count) }}</td>
<td class="p-3 space-x-1 whitespace-nowrap">
<a href="{{ route('admin.episodes.sources.index', $ep) }}" class="px-2 py-1 bg-[#141414] border border-[#F5F0E6] text-[10px] font-black">SRC</a>
<a href="{{ route('admin.episodes.subtitles.index', $ep) }}" class="px-2 py-1 bg-[#141414] border border-[#F5F0E6] text-[10px] font-black">SUB</a>
<a href="{{ route('admin.episodes.edit', $ep) }}" class="px-2 py-1 bg-blue-600 text-white text-[10px] font-black">EDIT</a>
<form method="POST" action="{{ route('admin.episodes.status', $ep) }}" class="inline">@csrf @method('PATCH')
<select name="status" onchange="this.form.submit()" class="bg-[#141414] border border-zinc-600 text-[10px] px-1 py-1">
@foreach(['draft','processing','ready','failed','hidden'] as $s)<option value="{{ $s }}" @selected(($ep->status instanceof \BackedEnum ? $ep->status->value : $ep->status) === $s)>{{ $s }}</option>@endforeach</select></form>
<form method="POST" action="{{ route('admin.episodes.destroy', $ep) }}" class="inline" onsubmit="return confirm('Hapus episode ini?')">@csrf @method('DELETE')<button class="px-2 py-1 bg-red-600 text-white text-[10px] font-black">DEL</button></form>
</td></tr>
@empty<tr><td colspan="5" class="p-6 text-center text-zinc-500">Belum ada episode.</td></tr>@endforelse
</tbody></table></div>
<div class="text-white">{{ $episodes->links() }}</div>
</div></x-admin-layout>
