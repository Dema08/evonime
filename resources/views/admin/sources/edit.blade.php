<x-admin-layout title="Edit Source">
<div class="max-w-2xl mx-auto space-y-6 text-[#F5F0E6]">
<h1 class="text-2xl font-black">EDIT SOURCE</h1>
<form method="POST" action="{{ route('admin.sources.update', $source) }}" class="bg-[#1A1A1A] border-2 border-[#F5F0E6] p-6 space-y-4">
@csrf @method('PUT')
<input type="hidden" name="episode_id" value="{{ $episode->id }}">
<div><label class="text-xs font-black">SERVER</label><input name="server_name" value="{{ old('server_name', $source->server_name) }}" class="w-full px-3 py-2 bg-[#141414] border-2 border-[#F5F0E6] text-sm text-white"></div>
<div><label class="text-xs font-black">URL</label><input name="url" value="{{ old('url', $source->url) }}" class="w-full px-3 py-2 bg-[#141414] border-2 border-[#F5F0E6] text-sm text-white"></div>
<div class="grid grid-cols-3 gap-3">
<div><label class="text-xs font-black">QUALITY</label><select name="quality" class="w-full px-3 py-2 bg-[#141414] border-2 border-[#F5F0E6] text-sm text-white">@foreach(['360p','480p','720p','1080p'] as $q)<option value="{{ $q }}" @selected(old('quality', $source->quality instanceof \BackedEnum ? $source->quality->value : $source->quality)===$q)>{{ $q }}</option>@endforeach</select></div>
<div><label class="text-xs font-black">FORMAT</label><select name="format" class="w-full px-3 py-2 bg-[#141414] border-2 border-[#F5F0E6] text-sm text-white">@foreach(['hls','mp4','dash'] as $f)<option value="{{ $f }}" @selected($source->format===$f)>{{ $f }}</option>@endforeach</select></div>
<div><label class="text-xs font-black">PRIORITY</label><input type="number" name="priority" value="{{ old('priority', $source->priority) }}" class="w-full px-3 py-2 bg-[#141414] border-2 border-[#F5F0E6] text-sm text-white"></div>
</div>
<div><label class="text-xs font-black"><input type="checkbox" name="is_active" value="1" @checked($source->is_active) class="accent-red-600"> AKTIF</label></div>
<div><button class="px-6 py-2 bg-[#E63946] border-2 border-[#F5F0E6] text-xs font-black">UPDATE</button></div>
</form></div></x-admin-layout>
