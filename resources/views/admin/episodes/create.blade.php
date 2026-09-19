<x-admin-layout title="Tambah Episode">
<div class="max-w-3xl mx-auto space-y-6 text-[#F5F0E6]">
<h1 class="text-2xl font-black">TAMBAH EPISODE</h1>
<form method="POST" action="{{ route('admin.animes.episodes.store', $selectedAnime ?? $animes->first()) }}" enctype="multipart/form-data" class="bg-[#1A1A1A] border-2 border-[#F5F0E6] p-6 space-y-4">
@csrf
<div><label class="text-xs font-black">ANIME *</label>
<select name="anime_id" class="w-full px-3 py-2 bg-[#141414] border-2 border-[#F5F0E6] text-sm text-white">
@foreach($animes as $a)<option value="{{ $a->id }}" @selected(($selectedAnime->id ?? old('anime_id')) == $a->id)>{{ $a->title }}</option>@endforeach
</select>@error('anime_id')<p class="text-red-400 text-xs">{{ $message }}</p>@enderror</div>
<div class="grid grid-cols-2 gap-3">
<div><label class="text-xs font-black">NOMOR *</label><input type="number" name="episode_number" value="{{ old('episode_number', $nextNumber ?? 1) }}" min="1" class="w-full px-3 py-2 bg-[#141414] border-2 border-[#F5F0E6] text-sm text-white">@error('episode_number')<p class="text-red-400 text-xs">{{ $message }}</p>@enderror</div>
<div><label class="text-xs font-black">STATUS *</label><select name="status" class="w-full px-3 py-2 bg-[#141414] border-2 border-[#F5F0E6] text-sm text-white">@foreach(['draft','processing','ready','failed','hidden'] as $s)<option value="{{ $s }}">{{ $s }}</option>@endforeach</select></div>
</div>
<div><label class="text-xs font-black">JUDUL</label><input name="title" value="{{ old('title') }}" class="w-full px-3 py-2 bg-[#141414] border-2 border-[#F5F0E6] text-sm text-white"></div>
<div><label class="text-xs font-black">SINOPSIS</label><textarea name="synopsis" rows="2" class="w-full px-3 py-2 bg-[#141414] border-2 border-[#F5F0E6] text-sm text-white">{{ old('synopsis') }}</textarea></div>
<div class="grid grid-cols-2 gap-3">
<div><label class="text-xs font-black">DURASI (detik)</label><input type="number" name="duration" value="{{ old('duration') }}" class="w-full px-3 py-2 bg-[#141414] border-2 border-[#F5F0E6] text-sm text-white"></div>
<div><label class="text-xs font-black">TAYANG</label><input type="datetime-local" name="aired_at" value="{{ old('aired_at') }}" class="w-full px-3 py-2 bg-[#141414] border-2 border-[#F5F0E6] text-sm text-white"></div>
</div>
<div><label class="text-xs font-black">THUMBNAIL (max 2MB)</label><input type="file" name="thumbnail" accept="image/*" class="text-xs"></div>
<div><button class="px-6 py-2 bg-[#E63946] border-2 border-[#F5F0E6] text-xs font-black">SIMPAN</button></div>
</form></div></x-admin-layout>
