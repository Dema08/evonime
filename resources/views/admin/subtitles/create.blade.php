<x-admin-layout title="Tambah Subtitle">
<div class="max-w-2xl mx-auto space-y-6 text-[#F5F0E6]">
<h1 class="text-2xl font-black">TAMBAH SUBTITLE</h1>
<form method="POST" action="{{ route('admin.episodes.subtitles.store', $episode) }}" enctype="multipart/form-data" class="bg-[#1A1A1A] border-2 border-[#F5F0E6] p-6 space-y-4">
@csrf
<div class="grid grid-cols-2 gap-3">
<div><label class="text-xs font-black">BAHASA *</label><select name="language" class="w-full px-3 py-2 bg-[#141414] border-2 border-[#F5F0E6] text-sm text-white"><option value="id">Indonesia</option><option value="en">English</option><option value="jp">Japanese</option></select>@error('language')<p class="text-red-400 text-xs">{{ $message }}</p>@enderror</div>
<div><label class="text-xs font-black">LABEL *</label><input name="label" value="{{ old('label') }}" class="w-full px-3 py-2 bg-[#141414] border-2 border-[#F5F0E6] text-sm text-white"></div>
</div>
<div class="grid grid-cols-2 gap-3">
<div><label class="text-xs font-black">FORMAT</label><select name="format" class="w-full px-3 py-2 bg-[#141414] border-2 border-[#F5F0E6] text-sm text-white"><option value="vtt">vtt</option><option value="srt">srt</option><option value="ass">ass</option></select></div>
<div><label class="text-xs font-black">FILE (max 1MB)</label><input type="file" name="file" accept=".vtt,.srt,.ass" class="text-xs"></div>
</div>
<div><label class="text-xs font-black">URL (wajib jika tanpa file)</label><input name="url" value="{{ old('url') }}" class="w-full px-3 py-2 bg-[#141414] border-2 border-[#F5F0E6] text-sm text-white"></div>
<div><label class="text-xs font-black"><input type="checkbox" name="is_default" value="1" class="accent-red-600"> DEFAULT</label></div>
<div><button class="px-6 py-2 bg-[#E63946] border-2 border-[#F5F0E6] text-xs font-black">SIMPAN</button></div>
</form></div></x-admin-layout>
