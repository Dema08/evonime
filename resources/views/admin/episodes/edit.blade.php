<x-admin-layout title="Edit Episode">
<div class="max-w-3xl mx-auto space-y-6 text-[#F5F0E6]">
<h1 class="text-2xl font-black">EDIT EP {{ $episode->episode_number }}</h1>
<form method="POST" action="{{ route('admin.episodes.update', $episode) }}" enctype="multipart/form-data" class="bg-[#1A1A1A] border-2 border-[#F5F0E6] p-6 space-y-4">
@csrf @method('PUT')
<input type="hidden" name="anime_id" value="{{ $anime->id }}">
<div><label class="text-xs font-black">JUDUL</label><input name="title" value="{{ old('title', $episode->title) }}" class="w-full px-3 py-2 bg-[#141414] border-2 border-[#F5F0E6] text-sm text-white"></div>
<div><label class="text-xs font-black">SINOPSIS</label><textarea name="synopsis" rows="2" class="w-full px-3 py-2 bg-[#141414] border-2 border-[#F5F0E6] text-sm text-white">{{ old('synopsis', $episode->synopsis) }}</textarea></div>
<div class="grid grid-cols-3 gap-3">
<div><label class="text-xs font-black">NOMOR</label><input type="number" name="episode_number" value="{{ old('episode_number', $episode->episode_number) }}" class="w-full px-3 py-2 bg-[#141414] border-2 border-[#F5F0E6] text-sm text-white"></div>
<div><label class="text-xs font-black">DURASI</label><input type="number" name="duration" value="{{ old('duration', $episode->duration) }}" class="w-full px-3 py-2 bg-[#141414] border-2 border-[#F5F0E6] text-sm text-white"></div>
<div><label class="text-xs font-black">STATUS</label><select name="status" class="w-full px-3 py-2 bg-[#141414] border-2 border-[#F5F0E6] text-sm text-white">@foreach(['draft','processing','ready','failed','hidden'] as $s)<option value="{{ $s }}" @selected(old('status', $episode->status instanceof \BackedEnum ? $episode->status->value : $episode->status)===$s)>{{ $s }}</option>@endforeach</select></div>
</div>
<div><label class="text-xs font-black">THUMBNAIL BARU</label><input type="file" name="thumbnail" accept="image/*" class="text-xs"></div>
<div><button class="px-6 py-2 bg-[#E63946] border-2 border-[#F5F0E6] text-xs font-black">UPDATE</button></div>
</form></div></x-admin-layout>
