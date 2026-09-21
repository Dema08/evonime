<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RecordWatchRequest;
use App\Services\WatchHistoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Halaman /history (Web) — riwayat tontonan user.
 *
 * Tracking level-EPISODE: video diputar via iframe pihak ketiga yang
 * cross-origin sehingga video.currentTime tidak bisa dibaca.
 */
class HistoryController extends Controller
{
    public function __construct(protected readonly WatchHistoryService $historyService) {}

    public function index(Request $request): View
    {
        return view('history', [
            'history' => $this->historyService->history((int) $request->user()->id, 20),
        ]);
    }

    /**
     * Catat tontonan episode dari halaman watch (jalur browser/session + CSRF).
     *
     * Route /api/v1/watch/record tetap tersedia untuk klien API (Bearer token),
     * tetapi route web ini yang dipakai halaman watch karena middleware grup
     * "api" tidak menjalankan sesi (StartSession).
     */
    public function record(RecordWatchRequest $request): JsonResponse
    {
        $history = $this->historyService->record(
            (int) $request->user()->id,
            (int) $request->input('episode_id')
        );

        return response()->json([
            'success' => true,
            'history_id' => $history->id,
            'episode_id' => (int) $history->episode_id,
            'completed' => (bool) $history->completed,
            'last_watched_at' => $history->last_watched_at?->toIso8601String(),
        ]);
    }

    public function destroy(Request $request, int $id): RedirectResponse
    {
        $deleted = $this->historyService->remove((int) $request->user()->id, $id);

        return back()->with(
            $deleted ? 'success' : 'error',
            $deleted ? 'Riwayat tontonan berhasil dihapus.' : 'Riwayat tontonan tidak ditemukan.'
        );
    }

    public function complete(Request $request, int $id): RedirectResponse
    {
        $completed = $this->historyService->markCompleted((int) $request->user()->id, $id);

        return back()->with(
            $completed ? 'success' : 'error',
            $completed ? 'Episode ditandai selesai.' : 'Riwayat tontonan tidak ditemukan.'
        );
    }

    public function clear(Request $request): RedirectResponse
    {
        $deleted = $this->historyService->clearAll((int) $request->user()->id);

        return back()->with(
            'success',
            $deleted > 0
                ? "{$deleted} riwayat tontonan berhasil dihapus."
                : 'Belum ada riwayat tontonan untuk dihapus.'
        );
    }
}
