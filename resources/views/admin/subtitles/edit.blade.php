<x-admin-layout title="Edit Subtitle">
<div class="max-w-2xl mx-auto space-y-6 text-[#F5F0E6]">
<h1 class="text-2xl font-black">EDIT SUBTITLE</h1>
<form method="POST" action="{{ route('admin.subtitles.update', $subtitle) }}" enctype="multipart/form-data" class="bg-[#1A1A1A] border-2 border-[#F5F0E6] p-6 space-y-4">
@csrf @method('PUT')
<input type="hidden" name="episode_id" value="{{ $episode->id }}">
<div><label class="text-xs font-black">LABEL</label><input name="label" value="{{ old('label', $subtitle->label) }}" class="w-full px-3 py-2 bg-[#141414] border-2 border-[#F5F0E6] text-sm text-white"></div>
<div><label class="text-xs font-black">FILE BARU</label><input type="file" name="file" accept=".vtt,.srt,.ass" class="text-xs"><p class="text-[11px] text-zinc-500">Saat ini: {{ $subtitle->url }}</p></div>
<div><label class="text-xs font-black">URL</label><input name="url" value="{{ old('url', $subtitle->url) }}" class="w-full px-3 py-2 bg-[#141414] border-2 border-[#F5F0E6] text-sm text-white"></div>
<input type="hidden" name="language" value="{{ $subtitle->language instanceof \BackedEnum ? $subtitle->language->value : $subtitle->language }}">
<input type="hidden" name="format" value="{{ $subtitle->format }}">
<div><label class="text-xs font-black"><input type="checkbox" name="is_default" value="1" @checked($subtitle->is_default) class="accent-red-600"> DEFAULT</label></div>
<div><button class="px-6 py-2 bg-[#E63946] border-2 border-[#F5F0E6] text-xs font-black">UPDATE</button></div>
</form></div></x-admin-layout>
