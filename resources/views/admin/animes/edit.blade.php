<x-admin-layout title="Edit Anime">
<div class="max-w-4xl mx-auto space-y-6 text-[#F5F0E6]">
<h1 class="text-3xl font-black">EDIT: {{ $anime->title }}</h1>
<form method="POST" action="{{ route('admin.animes.update', $anime) }}" enctype="multipart/form-data" class="bg-[#1A1A1A] border-2 border-[#F5F0E6] p-6 space-y-4">
@csrf @method('PUT')
<div class="grid grid-cols-1 md:grid-cols-2 gap-3">
<div><label class="text-xs font-black">JUDUL *</label><input name="title" value="{{ old('title', $anime->title) }}" class="w-full px-3 py-2 bg-[#141414] border-2 border-[#F5F0E6] text-sm text-white">@error('title')<p class="text-red-400 text-xs">{{ $message }}</p>@enderror</div>
<div><label class="text-xs font-black">SLUG</label><input name="slug" value="{{ old('slug', $anime->slug) }}" class="w-full px-3 py-2 bg-[#141414] border border-zinc-700 text-sm text-zinc-300"></div>
</div>
<div><label class="text-xs font-black">SINOPSIS *</label><textarea name="synopsis" rows="3" class="w-full px-3 py-2 bg-[#141414] border-2 border-[#F5F0E6] text-sm text-white">{{ old('synopsis', $anime->synopsis) }}</textarea></div>
<div class="grid grid-cols-2 md:grid-cols-4 gap-3">
<div><label class="text-xs font-black">TYPE</label><select name="type" class="w-full px-2 py-2 bg-[#141414] border-2 border-[#F5F0E6] text-sm text-white">@foreach(['tv','movie','ova','ona','special'] as $t)<option value="{{ $t }}" @selected(old('type',$anime->type)===$t)>{{ $t }}</option>@endforeach</select></div>
<div><label class="text-xs font-black">STATUS</label><select name="status" class="w-full px-2 py-2 bg-[#141414] border-2 border-[#F5F0E6] text-sm text-white">@foreach(['ongoing','completed','upcoming','hiatus'] as $s)<option value="{{ $s }}" @selected(old('status',$anime->status)===$s)>{{ $s }}</option>@endforeach</select></div>
<div><label class="text-xs font-black">TAHUN</label><input type="number" name="year" value="{{ old('year',$anime->year) }}" class="w-full px-2 py-2 bg-[#141414] border-2 border-[#F5F0E6] text-sm text-white"></div>
<div><label class="text-xs font-black">RATING</label><input type="number" step="0.1" name="rating" value="{{ old('rating',$anime->rating) }}" class="w-full px-2 py-2 bg-[#141414] border-2 border-[#F5F0E6] text-sm text-white"></div>
</div>
<div><label class="text-xs font-black">GENRES *</label><div class="grid grid-cols-2 md:grid-cols-4 gap-2">
@foreach($genres as $g)<label class="text-xs font-bold bg-[#141414] border border-zinc-700 px-2 py-2"><input type="checkbox" name="genres[]" value="{{ $g->id }}" @checked(in_array($g->id, old('genres', $selectedGenres))) class="accent-red-600"> {{ $g->name }}</label>@endforeach
</div></div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-3">
<div><label class="text-xs font-black">POSTER BARU</label>@if($anime->poster_url)<p class="text-[11px] text-zinc-500">Saat ini: {{ $anime->poster_url }}</p>@endif<input type="file" name="poster" accept="image/*" class="text-xs"></div>
<div><label class="text-xs font-black">BANNER BARU</label>@if($anime->banner_url)<p class="text-[11px] text-zinc-500">Saat ini: {{ $anime->banner_url }}</p>@endif<input type="file" name="banner" accept="image/*" class="text-xs"></div>
</div>
<div class="flex gap-4 text-xs font-black">
<label><input type="checkbox" name="is_published" value="1" @checked(old('is_published',$anime->is_published)) class="accent-red-600"> PUBLISH</label>
<label><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured',$anime->is_featured)) class="accent-amber-500"> FEATURED</label>
</div>
<div class="flex gap-3"><button class="px-6 py-2 bg-[#E63946] border-2 border-[#F5F0E6] text-xs font-black">UPDATE</button>
<a href="{{ route('admin.animes.index') }}" class="px-6 py-2 bg-[#141414] border-2 border-[#F5F0E6] text-xs font-black">BATAL</a></div>
</form></div></x-admin-layout>
