<x-admin-layout title="Detail Anime">
<div class="max-w-4xl mx-auto space-y-6 text-[#F5F0E6]">
<h1 class="text-3xl font-black">{{ $anime->title }}</h1>
<div class="bg-[#1A1A1A] border-2 border-[#F5F0E6] p-6 space-y-2 text-sm">
<p><b>Slug:</b> {{ $anime->slug }}</p>
<p><b>Type/Status:</b> {{ $anime->type }} / {{ $anime->status }}</p>
<p><b>Genres:</b> {{ $anime->genres->pluck('name')->join(', ') }}</p>
<p><b>Episodes:</b> {{ $anime->episodes->count() }}</p>
<p><b>Sinopsis:</b> {{ $anime->synopsis }}</p>
</div>
<div class="flex gap-3">
<a href="{{ route('admin.animes.episodes.index', $anime) }}" class="px-4 py-2 bg-blue-600 text-xs font-black">KELOLA EPISODE</a>
<a href="{{ route('admin.animes.edit', $anime) }}" class="px-4 py-2 bg-[#141414] border-2 border-[#F5F0E6] text-xs font-black">EDIT</a>
<a href="{{ route('admin.animes.index') }}" class="px-4 py-2 bg-[#141414] border border-zinc-700 text-xs font-black">KEMBALI</a>
</div></div></x-admin-layout>
