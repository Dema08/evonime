# Query Optimization — Evonime

Cache: redis, prefix `evonime_cache_`. Aturan: `Cache::remember` di repository untuk data publik,
per-user untuk histori. Invalidasi via `AnimeService::invalidateCache()` / `EpisodeService::invalidateCache()`.

## 1. Homepage featured (N+1 genres + SELECT *)
BEFORE: `Anime::where(...)->get()` lalu loop `$a->genres` → 1 + N query, SELECT *.
AFTER (`AnimeRepository::getFeatured`): `with(['genres:id,name,slug'])` + `select([...minimal])` + `Cache::remember('homepage:featured',1800)`.
Hasil: 2 query tetap (animes + genres), hit berikutnya 0 query (redis).

## 2. Detail anime + episodes (N+1 episodes.genres)
BEFORE: `$anime->episodes` di blade loop + `$ep->sources` → puluhan query.
AFTER (`findBySlug`): `with(['genres','episodes'=>select minimal])` + cache `anime:slug:{slug}` 30 mnt.
Hasil: 3 query (anime, genres, episodes), hit berikutnya 0 query.

## 3. Search LIKE '%keyword%' (full scan)
BEFORE: `where('title','like','%kw%')` → index tidak terpakai, scan jutaan baris.
AFTER (`search`): `>=4 char → whereFullText` (index FULLTEXT `animes_search_fulltext`); `<4 char → where('title','like','kw%')` (prefix, index B-Tree terpakai) + cache `search:{hash}` 5 mnt.
Hasil: index selalu terpakai, tidak pernah leading-wildcard.

## 4. Counter views (SELECT lalu UPDATE)
BEFORE: `$a=Anime::find($id); $a->views_count++; $a->save();` → 2 query + race condition.
AFTER (`incrementViews`): `whereKey($id)->increment('views_count')` → 1 query UPDATE atomik.
Catatan: dipanggil dari job `IncrementAnimeViews` via `->afterResponse()` agar response tidak menunggu.

## 5. Continue watching + count episode (loop count)
BEFORE: loop histori lalu `$ep->anime` + `Anime::find()->episodes()->count()` per baris → N+1.
AFTER (`getContinueWatching`): `with(['episode.anime'])` nested eager + `withCount('episodes')` di related + cache `user:{id}:continue` 5 mnt.
Hasil: 3 query flat + cache per user.

---
Catatan DB::raw: hanya dipakai jika perlu (tidak ada di repository ini). `whereFullText` dan `increment`
sudah di-compile Query Builder menjadi SQL optimal tanpa raw.
