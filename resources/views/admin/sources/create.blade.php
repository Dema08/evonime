<x-admin-layout title="Tambah Source">
<div class="max-w-2xl mx-auto space-y-6 text-[#F5F0E6]">
<h1 class="text-2xl font-black">TAMBAH SOURCE EP {{ $episode->episode_number }}</h1>
<form method="POST" action="{{ route('admin.episodes.sources.store', $episode) }}" class="bg-[#1A1A1A] border-2 border-[#F5F0E6] p-6 space-y-4">
@csrf
<div class="grid grid-cols-2 gap-3">
<div><label class="text-xs font-black">SERVER *</label><input name="server_name" value="{{ old('server_name', 'Server 1') }}" class="w-full px-3 py-2 bg-[#141414] border-2 border-[#F5F0E6] text-sm text-white">@error('server_name')<p class="text-red-400 text-xs">{{ $message }}</p>@enderror</div>
<div><label class="text-xs font-black">QUALITY *</label><select name="quality" class="w-full px-3 py-2 bg-[#141414] border-2 border-[#F5F0E6] text-sm text-white">@foreach(['360p','480p','720p','1080p'] as $q)<option value="{{ $q }}">{{ $q }}</option>@endforeach</select></div>
</div>
<div><label class="text-xs font-black">URL / PATH *</label><input name="url" value="{{ old('url') }}" placeholder="episodes/1/720p/master.m3u8" class="w-full px-3 py-2 bg-[#141414] border-2 border-[#F5F0E6] text-sm text-white">@error('url')<p class="text-red-400 text-xs">{{ $message }}</p>@enderror</div>
<div class="grid grid-cols-3 gap-3">
<div><label class="text-xs font-black">FORMAT</label><select name="format" class="w-full px-3 py-2 bg-[#141414] border-2 border-[#F5F0E6] text-sm text-white">@foreach(['hls','mp4','dash'] as $f)<option value="{{ $f }}">{{ $f }}</option>@endforeach</select></div>
<div><label class="text-xs font-black">PRIORITY</label><input type="number" name="priority" value="{{ old('priority', 0) }}" class="w-full px-3 py-2 bg-[#141414] border-2 border-[#F5F0E6] text-sm text-white"></div>
<div class="flex items-end pb-2"><label class="text-xs font-black"><input type="checkbox" name="is_active" value="1" checked class="accent-red-600"> AKTIF</label></div>
</div>
<div><button class="px-6 py-2 bg-[#E63946] border-2 border-[#F5F0E6] text-xs font-black">SIMPAN</button></div>
</form></div></x-admin-layout>
