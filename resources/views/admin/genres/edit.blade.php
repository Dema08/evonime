<x-admin-layout title="Edit Genre">
<div class="max-w-xl mx-auto space-y-6 text-[#F5F0E6]">
<h1 class="text-2xl font-black">EDIT: {{ $genre->name }}</h1>
<form method="POST" action="{{ route('admin.genres.update', $genre) }}" class="bg-[#1A1A1A] border-2 border-[#F5F0E6] p-6 space-y-4">
@csrf @method('PUT')
<div><label class="text-xs font-black">NAMA *</label><input name="name" value="{{ old('name', $genre->name) }}" class="w-full px-3 py-2 bg-[#141414] border-2 border-[#F5F0E6] text-sm text-white"></div>
<div><label class="text-xs font-black">SLUG</label><input name="slug" value="{{ old('slug', $genre->slug) }}" class="w-full px-3 py-2 bg-[#141414] border border-zinc-700 text-sm text-zinc-300"></div>
<div><label class="text-xs font-black">DESKRIPSI</label><textarea name="description" rows="2" class="w-full px-3 py-2 bg-[#141414] border-2 border-[#F5F0E6] text-sm text-white">{{ old('description', $genre->description) }}</textarea></div>
<div><button class="px-6 py-2 bg-[#E63946] border-2 border-[#F5F0E6] text-xs font-black">UPDATE</button></div>
</form></div></x-admin-layout>
