<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadService
{
    /**
     * Upload gambar (poster/banner/thumbnail) ke disk public dengan nama unik UUID.
     * Hapus file lama jika ada. Max 2MB (2048 KB).
     */
    public function uploadImage(UploadedFile $file, string $folder, ?string $oldPath = null): string
    {
        if ($oldPath) {
            $this->deleteFile($oldPath);
        }

        $extension = $file->getClientOriginalExtension();
        $filename = Str::uuid().'.'.($extension ?: 'jpg');
        $path = trim($folder, '/').'/'.$filename;

        Storage::disk('public')->putFileAs($folder, $file, $filename);

        return $path;
    }

    /**
     * Upload file subtitle (.vtt/.srt/.ass) ke disk public. Max 1MB (1024 KB).
     */
    public function uploadSubtitle(UploadedFile $file, string $folder = 'subtitles', ?string $oldPath = null): string
    {
        if ($oldPath) {
            $this->deleteFile($oldPath);
        }

        $extension = strtolower($file->getClientOriginalExtension());
        $filename = Str::uuid().'.'.($extension ?: 'vtt');
        $path = trim($folder, '/').'/'.$filename;

        Storage::disk('public')->putFileAs($folder, $file, $filename);

        return $path;
    }

    /**
     * Hapus file dari disk public jika ada.
     */
    public function deleteFile(?string $path): bool
    {
        if ($path && Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }

        return false;
    }
}
