<x-admin-layout title="Edit User">
<div class="max-w-xl mx-auto space-y-6 text-[#F5F0E6]">
<h1 class="text-2xl font-black">EDIT: {{ $user->name }}</h1>
<form method="POST" action="{{ route('admin.users.update', $user) }}" class="bg-[#1A1A1A] border-2 border-[#F5F0E6] p-6 space-y-4">
@csrf @method('PATCH')
<div><label class="text-xs font-black">ROLE</label><select name="role" class="w-full px-3 py-2 bg-[#141414] border-2 border-[#F5F0E6] text-sm text-white">@foreach(['user','moderator','admin'] as $r)<option value="{{ $r }}" @selected($user->role===$r)>{{ $r }}</option>@endforeach</select></div>
<div><label class="text-xs font-black"><input type="checkbox" name="is_active" value="1" @checked($user->is_active) class="accent-red-600"> AKTIF</label></div>
<div><button class="px-6 py-2 bg-[#E63946] border-2 border-[#F5F0E6] text-xs font-black">UPDATE</button></div>
</form></div></x-admin-layout>
