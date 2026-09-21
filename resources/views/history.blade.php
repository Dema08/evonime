<x-app-layout title="Watch History - EVONIME">

    <div class="max-w-[1400px] mx-auto px-4 md:px-6 space-y-8 mt-4 pt-24 md:pt-28 text-[#F5F0E6]">

        @if (session('success'))
            <div class="bg-[#1A1A1A] border-2 border-green-500 shadow-[3px_3px_0px_#F5F0E6] p-4 text-sm font-black text-green-400">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="bg-[#1A1A1A] border-2 border-[#E63946] shadow-[3px_3px_0px_#F5F0E6] p-4 text-sm font-black text-[#E63946]">
                {{ session('error') }}
            </div>
        @endif

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b-2 border-[#F5F0E6] pb-6">
            <div>
                <h1 class="text-2xl md:text-4xl font-black text-[#F5F0E6] tracking-tight flex items-center gap-3" style="font-family: 'Anton', sans-serif;">
                    <span class="w-2.5 h-7 bg-[#E63946] border border-[#F5F0E6] inline-block"></span>
                    WATCH HISTORY CHAPTERS
                </h1>
                <p class="text-xs md:text-sm text-zinc-400 font-bold mt-1">
                    {{ $history->total() }} episode tercatat &bull; lanjut tonton ditandai per episode
                </p>
            </div>

            @if ($history->total() > 0)
                <form method="POST" action="{{ route('history.clear') }}" onsubmit="return confirm('Hapus SEMUA riwayat tontonan?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 text-xs font-black rounded-lg bg-[#1A1A1A] border-2 border-[#F5F0E6] hover:bg-[#E63946] hover:text-white transition-all">
                        CLEAR HISTORY
                    </button>
                </form>
            @endif
        </div>

        @if ($history->isEmpty())
            <!-- Empty State -->
            <div class="text-center py-16 space-y-4 bg-[#1A1A1A] border-2 border-[#F5F0E6] shadow-[4px_4px_0px_#F5F0E6]">
                <p class="text-sm font-black text-zinc-400">BELUM ADA RIWAYAT TONTONAN</p>
                <a href="/anime" class="inline-block px-5 py-2.5 bg-[#E63946] text-white text-xs font-black border-2 border-[#F5F0E6] shadow-[3px_3px_0px_#F5F0E6] hover:scale-105 transition-all">
                    JELAJAHI ANIME ▶
                </a>
            </div>
        @else
            <!-- History List Grid -->
            <div id="history-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($history as $wh)
                    @php
                        $anime = $wh->anime;
                        $episode = $wh->episode;
                        $thumb = $episode?->thumbnail_url ?: ($anime?->banner_url ?: $anime?->poster_url);
                        $watchUrl = '/watch/' . $anime?->slug . '/' . $episode?->episode_number;
                    @endphp
                    @continue(! $anime || ! $episode)
                    <div class="flex items-center gap-4 p-3 bg-[#1A1A1A] border-2 border-[#F5F0E6] shadow-[3px_3px_0px_#F5F0E6] hover:shadow-[5px_5px_0px_#F5F0E6] transition-all group">
                        <a href="{{ $watchUrl }}" class="relative w-32 aspect-video bg-zinc-900 border border-[#F5F0E6] overflow-hidden flex-shrink-0">
                            <img referrerpolicy="no-referrer" src="{{ $thumb }}" alt="{{ $anime->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            <div class="absolute inset-0 bg-[#0D0D0D]/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center halftone-bg">
                                <div class="w-8 h-8 bg-[#E63946] text-white border border-[#F5F0E6] flex items-center justify-center shadow">
                                    <svg class="w-4 h-4 fill-current ml-0.5" viewBox="0 0 24 24">
                                        <path d="M8 5v14l11-7z"/>
                                    </svg>
                                </div>
                            </div>
                        </a>

                        <div class="flex-grow min-w-0 space-y-1.5">
                            <h3 class="text-sm font-black text-[#F5F0E6] group-hover:text-[#E63946] transition-colors truncate">
                                {{ $anime->title }}
                            </h3>
                            <p class="text-xs text-zinc-400 font-bold">
                                Episode {{ $episode->episode_number }} &bull;
                                {{ $wh->last_watched_at?->diffForHumans() ?? 'Baru saja' }}
                            </p>

                            <!-- Progress Line (0% untuk tracking level-episode) -->
                            <div class="w-full h-2 bg-[#141414] border border-[#F5F0E6] overflow-hidden">
                                <div class="h-full bg-[#E63946]" style="width: {{ $wh->progressPercent() }}%"></div>
                            </div>

                            <div class="flex flex-wrap items-center gap-2 pt-1 text-[11px]">
                                <a href="{{ $watchUrl }}" class="font-black text-[#E63946] hover:underline">
                                    LANJUT ▶
                                </a>

                                @if ($wh->completed)
                                    <span class="px-2 py-0.5 bg-[#1A1A1A] text-zinc-400 border border-zinc-700 font-black">✔ SELESAI</span>
                                @else
                                    <form method="POST" action="{{ route('history.complete', $wh->id) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-2 py-0.5 bg-[#1A1A1A] text-[#F5F0E6] border border-[#F5F0E6] font-black hover:bg-[#E63946] hover:text-white transition-colors">
                                            TANDAI SELESAI
                                        </button>
                                    </form>
                                @endif

                                <form method="POST" action="{{ route('history.destroy', $wh->id) }}" onsubmit="return confirm('Hapus riwayat episode ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2 py-0.5 bg-[#1A1A1A] text-[#E63946] border border-[#E63946] font-black hover:bg-[#E63946] hover:text-white transition-colors">
                                        HAPUS
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="pt-2">{{ $history->links() }}</div>
        @endif
    </div>

</x-app-layout>
