<x-admin-layout title="Users">
<div class="max-w-5xl mx-auto space-y-6 text-[#F5F0E6]">
@if(session('success'))<div class="bg-green-600 border-2 border-[#F5F0E6] p-3 text-sm font-black text-white">{{ session('success') }}</div>@endif
<div class="bg-[#1A1A1A] border-2 border-[#F5F0E6] p-5"><h1 class="text-2xl font-black">USERS ({{ $users->total() }})</h1></div>
<div class="bg-[#1A1A1A] border-2 border-[#F5F0E6] overflow-x-auto">
<table class="table-auto w-full text-left"><thead><tr class="bg-[#141414] text-xs font-black uppercase"><th class="p-3">Nama</th><th class="p-3">Email</th><th class="p-3">Role</th><th class="p-3">Aktif</th><th class="p-3">Aksi</th></tr></thead>
<tbody class="divide-y divide-zinc-800 text-xs font-bold">
@foreach($users as $u)<tr class="hover:bg-zinc-900/60">
<td class="p-3 text-white">{{ $u->name }}</td><td class="p-3">{{ $u->email }}</td><td class="p-3">{{ $u->role }}</td><td class="p-3">{{ $u->is_active ? '✅' : '❌' }}</td>
<td class="p-3 space-x-1"><a href="{{ route('admin.users.edit', $u) }}" class="px-2 py-1 bg-blue-600 text-white text-[10px] font-black">EDIT</a>
<form method="POST" action="{{ route('admin.users.toggle-active', $u) }}" class="inline">@csrf @method('PATCH')<button class="px-2 py-1 bg-amber-600 text-white text-[10px] font-black">TOGGLE</button></form></td></tr>
@endforeach
</tbody></table></div>
<div class="text-white">{{ $users->links() }}</div>
</div></x-admin-layout>
