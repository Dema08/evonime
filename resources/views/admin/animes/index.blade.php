<x-admin-layout title="Daftar Anime - EVONIME Admin">
    <div class="max-w-[1400px] mx-auto space-y-6 text-[#F5F0E6]">
        @if (session('success'))
            <div class="bg-green-600 border-2 border-[#F5F0E6] p-4 text-sm font-black text-white">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="bg-red-600 border-2 border-[#F5F0E6] p-4 text-sm font-black text-white">{{ session('error') }}</div>
        @endif

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-[#1A1A1A] border-2 border-[#F5F0E6] shadow-[6px_6px_0px_#F5F0E6] p-6">
            <div>
                <h1 class="text-3xl font-black" style="font-family: 'Anton', sans-serif;">ANIME ({{ $animes->total() }})</h1>
                <p class="text-xs text-zinc-400 font-bold">Kelola katalog anime</p>
            </div>
            <a href="{{ route('admin.animes.create') }}" class="px-5 py-2.5 text-xs font-black bg-[#E63946] text-white border-2 border-[#F5F0E6] shadow-[3px_3px_0px_#F5F0E6]">+ TAMBAH ANIME</a>
        </div>

        <div class="bg-[#1A1A1A] border-2 border-[#F5F0E6] p-4">
            <form method="GET" action="{{ route('admin.animes.index') }}" class="flex flex-col md:flex-row gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul..." class="flex-1 px-4 py-2 bg-[#141414] border-2 border-[#F5F0E6] text-sm text-white placeholder-zinc-500 focus:outline-none focus:border-[#E63946]">
                <select name="status" class="px-4 py-2 bg-[#141414] border-2 border-[#F5F0E6] text-sm text-white">
                    <option value="">Semua Status</option>
                    @foreach (['ongoing', 'completed', 'upcoming', 'hiatus'] as $st)
                        <option value="{{ $st }}" @selected(request('status') === $st)>{{ ucfirst($st) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-5 py-2 text-xs font-black bg-[#141414] hover:bg-[#E63946] text-white border-2 border-[#F5F0E6] transition-colors">FILTER</button>
            </form>
        </div>

        <div class="bg-[#1A1A1A] border-2 border-[#F5F0E6] shadow-[6px_6px_0px_#F5F0E6] overflow-x-auto">
            <table class="table-auto w-full text-left">
                <thead>
                    <tr class="bg-[#141414] text-xs font-black uppercase tracking-wider">
                        <th class="p-3">Judul</th>
                        <th class="p-3">Type</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">Publish</th>
                        <th class="p-3">Rating</th>
                        <th class="p-3">Views</th>
                        <th class="p-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800 text-xs font-bold">
                    @forelse ($animes as $anime)
                        <tr class="hover:bg-zinc-900/60">
                            <td class="p-3 text-white font-black">{{ $anime->title }}</td>
                            <td class="p-3"><span class="px-2 py-0.5 bg-[#141414] border border-[#F5F0E6] text-[10px]">{{ $anime->type }}</span></td>
                            <td class="p-3">{{ $anime->status }}</td>
                            <td class="p-3">{{ $anime->is_published ? '✅' : '❌' }}</td>
                            <td class="p-3 text-amber-400">★ {{ number_format($anime->rating ?? 0, 1) }}</td>
                            <td class="p-3">{{ number_format($anime->views_count) }}</td>
                            <td class="p-3 space-x-1 whitespace-nowrap">
                                <a href="{{ route('admin.animes.show', $anime) }}" class="px-2 py-1 bg-[#141414] border border-[#F5F0E6] text-[10px] font-black hover:bg-zinc-700">VIEW</a>
                                <a href="{{ route('admin.animes.episodes.index', $anime) }}" class="px-2 py-1 bg-[#141414] border border-[#F5F0E6] text-[10px] font-black hover:bg-zinc-700">EPS</a>
                                <a href="{{ route('admin.animes.edit', $anime) }}" class="px-2 py-1 bg-blue-600 text-white text-[10px] font-black hover:bg-blue-700">EDIT</a>
                                <form method="POST" action="{{ route('admin.animes.toggle-featured', $anime) }}" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="px-2 py-1 {{ $anime->is_featured ? 'bg-amber-500' : 'bg-zinc-600' }} text-white text-[10px] font-black">FEAT</button>
                                </form>
                                <form method="POST" action="{{ route('admin.animes.destroy', $anime) }}" class="inline" x-data @submit="if(!confirm('Hapus anime ini beserta semua episode?')) $event.preventDefault()">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="px-2 py-1 bg-red-600 text-white text-[10px] font-black hover:bg-red-700">DEL</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="p-6 text-center text-zinc-500">Belum ada anime. <a href="{{ route('admin.animes.create') }}" class="text-[#E63946] font-black">Tambah sekarang →</a></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="text-white">{{ $animes->links() }}</div>
    </div>
</x-admin-layout>
