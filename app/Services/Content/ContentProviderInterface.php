<?php

namespace App\Services\Content;

// Integrasi sumber konten LEGAL saja (contoh: API lisensi / feed resmi).
// JANGAN dipakai untuk scraping bajakan. Implementasikan provider di sini.
interface ContentProviderInterface
{
    /** @return array<int, array{external_id: string, title: string}> */
    public function fetchCatalog(int $page = 1): array;
}
