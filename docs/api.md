# Dokumentasi Resmi Evonime REST API v1

Platform Streaming Anime Modern dengan Arsitektur Clean, Repository Pattern & Service Layer.

- **Base URL:** `http://localhost:8000/api/v1`
- **Autentikasi:** Bearer Token via Laravel Sanctum
- **Format Header:** `Accept: application/json`, `Content-Type: application/json`

---

## 1. Standar Format Response

### 1.1 Success Response (`200 OK` / `201 Created`)
```json
{
  "success": true,
  "message": "OK",
  "data": { ... }
}
```

### 1.2 Paginated Response (`200 OK`)
```json
{
  "success": true,
  "message": "Katalog anime berhasil dimuat.",
  "data": [ ... ],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 100,
    "last_page": 7,
    "from": 1,
    "to": 15
  },
  "links": {
    "first": "http://localhost:8000/api/v1/animes?page=1",
    "prev": null,
    "next": "http://localhost:8000/api/v1/animes?page=2",
    "last": "http://localhost:8000/api/v1/animes?page=7"
  }
}
```

### 1.3 Error Response (`400`, `401`, `403`, `404`, `422`, `500`)
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": [
      "Alamat email wajib diisi."
    ],
    "password": [
      "Password minimal 8 karakter."
    ]
  }
}
```

### 1.4 Rate Limit Response (`429 Too Many Requests`)
```json
{
  "success": false,
  "message": "Terlalu banyak percobaan. Coba lagi dalam 30 detik.",
  "retry_after": 30
}
```

---

## 2. Rate Limiting Rules

| Tipe Rate Limit | Batas | Target |
| :--- | :--- | :--- |
| **API Guest** | 60 request / menit | Berdasarkan IP Client |
| **API Authenticated** | 120 request / menit | Berdasarkan User ID |
| **Auth Endpoint (Login/Register)** | 5 request / menit | Berdasarkan IP + Email |
| **Progress Upload (`/watch/progress`)** | 30 request / menit | Berdasarkan User ID / IP |

---

## 3. Daftar Endpoint API v1

| Method | Endpoint | Auth | Rate Limit | Deskripsi |
| :--- | :--- | :--- | :--- | :--- |
| `GET` | `/home` | Public | 60/menit | Data beranda (featured, latest, popular, genres) |
| `GET` | `/animes` | Public | 60/menit | Katalog anime dengan filter & paginasi |
| `GET` | `/animes/search` | Public | 60/menit | Pencarian anime (keyword, paginasi) |
| `GET` | `/animes/{slug}` | Public | 60/menit | Detail anime beserta relasi episode & related |
| `GET` | `/animes/{slug}/episodes` | Public | 60/menit | Daftar seluruh episode suatu anime |
| `GET` | `/animes/{slug}/related` | Public | 60/menit | Daftar anime sejenis / rekomendasi terkait |
| `GET` | `/episodes/latest` | Public | 60/menit | Daftar episode rilis terbaru |
| `GET` | `/episodes/{id}` | Public | 60/menit | Detail episode, sources, subtitles, next & prev |
| `GET` | `/episodes/{id}/sources` | Public | 60/menit | Daftar stream sources aktif untuk episode |
| `GET` | `/episodes/{id}/subtitles` | Public | 60/menit | Daftar subtitle episode (vtt, srt, ass) |
| `GET` | `/genres` | Public | 60/menit | Daftar genre beserta counter jumlah anime |
| `GET` | `/genres/{slug}` | Public | 60/menit | Detail genre dan katalog anime di dalamnya |
| `POST` | `/stream/url` | Public / Auth | 60/menit | Generate temporary signed URL streaming HMAC |
| `POST` | `/auth/register` | Public | 5/menit | Pendaftaran user baru & penerbitan token |
| `POST` | `/auth/login` | Public | 5/menit | Login user & penerbitan token Sanctum |
| `GET` | `/auth/me` | Bearer Token | 120/menit | Informasi akun pengguna yang sedang login |
| `POST` | `/auth/logout` | Bearer Token | 120/menit | Revoke access token Sanctum yang aktif |
| `GET` | `/watch/{episodeId}` | Bearer Token | 120/menit | Payload halaman nonton (episode, history, continue) |
| `POST` | `/watch/progress` | Bearer Token | 30/menit | Catat riwayat/progres tontonan (via Queue Job) |
| `GET` | `/watch/continue` | Bearer Token | 120/menit | Daftar episode "Lanjut Nonton" user |
| `GET` | `/watch/history` | Bearer Token | 120/menit | Riwayat tontonan user berhalaman (paginated) |
| `DELETE` | `/watch/history/{episodeId}` | Bearer Token | 120/menit | Hapus episode dari riwayat tontonan user |
| `GET` | `/user/profile` | Bearer Token | 120/menit | Profil user lengkap |
| `PATCH` | `/user/profile` | Bearer Token | 120/menit | Update nama dan/atau avatar user |
| `PATCH` | `/user/password` | Bearer Token | 120/menit | Ganti password akun user |

---

## 4. Contoh Detail Endpoint & Response

### 1. `GET /api/v1/home`
**Request:**
```http
GET /api/v1/home HTTP/1.1
Host: localhost:8000
Accept: application/json
```
**Response (`200 OK`):**
```json
{
  "success": true,
  "message": "Beranda berhasil dimuat.",
  "data": {
    "featured": [
      {
        "id": 1,
        "title": "Solo Leveling",
        "title_alternative": "Na Honjaman Level Up",
        "slug": "solo-leveling",
        "type": "tv",
        "status": "ongoing",
        "release_date": "2024-01-07",
        "rating": 9.4,
        "total_episodes": 12,
        "duration": 24,
        "studio": "A-1 Pictures",
        "season": "winter",
        "year": 2024,
        "poster_url": "http://localhost:8000/storage/posters/solo.jpg",
        "banner_url": "http://localhost:8000/storage/banners/solo.jpg",
        "views_count": 15000,
        "is_featured": true,
        "genres": [
          { "id": 1, "name": "Action", "slug": "action" },
          { "id": 2, "name": "Fantasy", "slug": "fantasy" }
        ],
        "created_at": "2026-01-01T00:00:00.000000Z"
      }
    ],
    "latest": [ ... ],
    "popular": [ ... ],
    "genres": [ ... ]
  }
}
```

---

### 2. `GET /api/v1/animes?status=ongoing&type=tv&page=1&per_page=15`
**Response (`200 OK`):**
```json
{
  "success": true,
  "message": "Katalog anime berhasil dimuat.",
  "data": [
    {
      "id": 1,
      "title": "Frieren: Beyond Journey's End",
      "title_alternative": "Sousou no Frieren",
      "slug": "frieren",
      "type": "tv",
      "status": "ongoing",
      "release_date": "2023-09-29",
      "rating": 9.8,
      "total_episodes": 28,
      "duration": 24,
      "studio": "Madhouse",
      "season": "fall",
      "year": 2023,
      "poster_url": "http://localhost:8000/storage/posters/frieren.jpg",
      "banner_url": null,
      "views_count": 28000,
      "is_featured": true,
      "genres": [
        { "id": 2, "name": "Fantasy", "slug": "fantasy" }
      ],
      "created_at": "2026-01-01T00:00:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 1,
    "last_page": 1,
    "from": 1,
    "to": 1
  },
  "links": {
    "first": "http://localhost:8000/api/v1/animes?page=1",
    "prev": null,
    "next": null,
    "last": "http://localhost:8000/api/v1/animes?page=1"
  }
}
```

---

### 3. `GET /api/v1/animes/{slug}`
**Response (`200 OK`):**
```json
{
  "success": true,
  "message": "Detail anime berhasil dimuat.",
  "data": {
    "id": 1,
    "title": "Solo Leveling",
    "title_alternative": "Ore dake Level Up na Ken",
    "slug": "solo-leveling",
    "synopsis": "10 tahun lalu, Gerbang misterius yang menghubungkan dunia nyata dengan dunia sihir terbuka...",
    "type": "tv",
    "status": "ongoing",
    "release_date": "2024-01-07",
    "rating": 9.4,
    "total_episodes": 12,
    "duration": 24,
    "studio": "A-1 Pictures",
    "season": "winter",
    "year": 2024,
    "poster_url": "http://localhost:8000/storage/posters/solo.jpg",
    "banner_url": "http://localhost:8000/storage/banners/solo.jpg",
    "views_count": 15000,
    "is_featured": true,
    "genres": [
      { "id": 1, "name": "Action", "slug": "action" }
    ],
    "episodes": [
      {
        "id": 1,
        "anime_id": 1,
        "episode_number": 1,
        "title": "I'm Used to It",
        "duration": 24,
        "thumbnail_url": "http://localhost:8000/storage/thumbnails/ep1.jpg",
        "aired_at": "2024-01-07T00:00:00.000000Z",
        "status": "ready",
        "views_count": 1200
      }
    ],
    "related": [
      {
        "id": 2,
        "title": "Demon Slayer",
        "slug": "demon-slayer",
        "poster_url": "http://localhost:8000/storage/posters/ds.jpg",
        "rating": 9.2
      }
    ],
    "created_at": "2026-01-01T00:00:00.000000Z"
  }
}
```

---

### 4. `GET /api/v1/animes/{slug}/episodes`
**Response (`200 OK`):**
```json
{
  "success": true,
  "message": "Daftar episode berhasil dimuat.",
  "data": [
    {
      "id": 1,
      "anime_id": 1,
      "episode_number": 1,
      "title": "I'm Used to It",
      "duration": 24,
      "thumbnail_url": "http://localhost:8000/storage/thumbnails/ep1.jpg",
      "aired_at": "2024-01-07T00:00:00.000000Z",
      "status": "ready",
      "views_count": 1200
    }
  ]
}
```

---

### 5. `GET /api/v1/animes/search?q=Solo`
**Response (`200 OK`):**
```json
{
  "success": true,
  "message": "Hasil pencarian anime berhasil dimuat.",
  "data": [
    {
      "id": 1,
      "title": "Solo Leveling",
      "slug": "solo-leveling",
      "type": "tv",
      "rating": 9.4
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 1,
    "last_page": 1,
    "from": 1,
    "to": 1
  },
  "links": {
    "first": "http://localhost:8000/api/v1/animes/search?page=1",
    "prev": null,
    "next": null,
    "last": "http://localhost:8000/api/v1/animes/search?page=1"
  }
}
```

---

### 6. `GET /api/v1/episodes/{id}`
**Response (`200 OK`):**
```json
{
  "success": true,
  "message": "Detail episode berhasil dimuat.",
  "data": {
    "id": 1,
    "anime_id": 1,
    "episode_number": 1,
    "title": "I'm Used to It",
    "synopsis": "Sung Jin-woo memasuki dungeon rank-D bersama tim penyerang.",
    "duration": 24,
    "thumbnail_url": "http://localhost:8000/storage/thumbnails/ep1.jpg",
    "aired_at": "2024-01-07T00:00:00.000000Z",
    "status": "ready",
    "views_count": 1200,
    "anime": {
      "id": 1,
      "title": "Solo Leveling",
      "slug": "solo-leveling"
    },
    "stream_sources": [
      {
        "id": 1,
        "server_name": "FastCDN",
        "quality": "1080p",
        "format": "hls",
        "priority": 1,
        "stream_url": "http://localhost:8000/stream/1?uid=0&expires=1789887766&token=1789887766.ab89ef..."
      }
    ],
    "subtitles": [
      {
        "id": 1,
        "language": "id",
        "label": "Indonesian",
        "format": "vtt",
        "url": "http://localhost:8000/storage/subtitles/ep1_id.vtt",
        "is_default": true
      }
    ],
    "next": {
      "id": 2,
      "episode_number": 2,
      "title": "If I Had One More Chance"
    },
    "prev": null
  }
}
```

---

### 7. `GET /api/v1/episodes/{id}/sources`
**Response (`200 OK`):**
```json
{
  "success": true,
  "message": "Daftar stream source berhasil dimuat.",
  "data": [
    {
      "id": 1,
      "server_name": "FastCDN",
      "quality": "1080p",
      "format": "hls",
      "priority": 1,
      "stream_url": "http://localhost:8000/stream/1?uid=0&expires=1789887766&token=..."
    }
  ]
}
```

---

### 8. `GET /api/v1/episodes/{id}/subtitles`
**Response (`200 OK`):**
```json
{
  "success": true,
  "message": "Daftar subtitle berhasil dimuat.",
  "data": [
    {
      "id": 1,
      "language": "id",
      "label": "Indonesian",
      "format": "vtt",
      "url": "http://localhost:8000/storage/subtitles/ep1_id.vtt",
      "is_default": true
    }
  ]
}
```

---

### 9. `GET /api/v1/genres`
**Response (`200 OK`):**
```json
{
  "success": true,
  "message": "Daftar genre berhasil dimuat.",
  "data": [
    {
      "id": 1,
      "name": "Action",
      "slug": "action",
      "animes_count": 14
    },
    {
      "id": 2,
      "name": "Fantasy",
      "slug": "fantasy",
      "animes_count": 9
    }
  ]
}
```

---

### 10. `POST /api/v1/stream/url`
**Request Body:**
```json
{
  "episode_id": 1,
  "quality": "1080p"
}
```
**Response (`200 OK`):**
```json
{
  "success": true,
  "message": "URL stream berhasil digenerate.",
  "data": {
    "episode_id": 1,
    "quality": "1080p",
    "stream_url": "http://localhost:8000/stream/1?uid=0&expires=1789887766&token=1789887766.3fbc...&q=1080p",
    "token": "1789887766.3fbc...",
    "expires_at": 1789887766,
    "ttl_seconds": 7200
  }
}
```

---

### 11. `POST /api/v1/auth/register`
**Request Body:**
```json
{
  "name": "Dema Adzhani",
  "email": "dema@example.com",
  "password": "Password123#",
  "password_confirmation": "Password123#"
}
```
**Response (`201 Created`):**
```json
{
  "success": true,
  "message": "Registrasi berhasil.",
  "data": {
    "user": {
      "id": 3,
      "name": "Dema Adzhani",
      "email": "dema@example.com",
      "avatar_url": "https://ui-avatars.com/api/?name=Dema+Adzhani&background=random",
      "role": "user",
      "created_at": "2026-09-20T01:30:00.000000Z"
    },
    "token": "1|N6G8P7k9...",
    "token_type": "Bearer",
    "expires_at": null
  }
}
```

---

### 12. `POST /api/v1/auth/login`
**Request Body:**
```json
{
  "email": "dema@example.com",
  "password": "Password123#"
}
```
**Response (`200 OK`):**
```json
{
  "success": true,
  "message": "Login berhasil.",
  "data": {
    "user": {
      "id": 3,
      "name": "Dema Adzhani",
      "email": "dema@example.com",
      "avatar_url": "https://ui-avatars.com/api/?name=Dema+Adzhani&background=random",
      "role": "user",
      "created_at": "2026-09-20T01:30:00.000000Z"
    },
    "token": "2|X9G7K2...",
    "token_type": "Bearer",
    "expires_at": null
  }
}
```

---

### 13. `GET /api/v1/auth/me`
**Header:** `Authorization: Bearer <token>`
**Response (`200 OK`):**
```json
{
  "success": true,
  "message": "Profil pengguna berhasil dimuat.",
  "data": {
    "id": 3,
    "name": "Dema Adzhani",
    "email": "dema@example.com",
    "avatar_url": "https://ui-avatars.com/api/?name=Dema+Adzhani&background=random",
    "role": "user",
    "created_at": "2026-09-20T01:30:00.000000Z"
  }
}
```

---

### 14. `POST /api/v1/auth/logout`
**Header:** `Authorization: Bearer <token>`
**Response (`200 OK`):**
```json
{
  "success": true,
  "message": "Logout berhasil."
}
```

---

### 15. `GET /api/v1/watch/{episodeId}`
**Header:** `Authorization: Bearer <token>`
**Response (`200 OK`):**
```json
{
  "success": true,
  "message": "Halaman nonton berhasil dimuat.",
  "data": {
    "episode": {
      "id": 1,
      "anime_id": 1,
      "episode_number": 1,
      "title": "I'm Used to It",
      "duration": 24,
      "thumbnail_url": "http://localhost:8000/storage/thumbnails/ep1.jpg",
      "status": "ready",
      "stream_sources": [ ... ],
      "subtitles": [ ... ],
      "next": { ... },
      "prev": null
    },
    "history": {
      "id": 12,
      "progress_seconds": 650,
      "duration_seconds": 1440,
      "completed": false,
      "progress_percent": 45,
      "last_watched_at": "2026-09-20T01:35:00.000000Z"
    },
    "continue": [ ... ],
    "recommended": [ ... ]
  }
}
```

---

### 16. `POST /api/v1/watch/progress`
**Header:** `Authorization: Bearer <token>`
**Request Body:**
```json
{
  "episode_id": 1,
  "progress_seconds": 650,
  "duration_seconds": 1440
}
```
**Response (`200 OK`):**
```json
{
  "success": true,
  "message": "Progres menonton berhasil dicatat.",
  "data": {
    "episode_id": 1,
    "progress_seconds": 650,
    "duration_seconds": 1440
  }
}
```

---

### 17. `GET /api/v1/watch/continue`
**Header:** `Authorization: Bearer <token>`
**Response (`200 OK`):**
```json
{
  "success": true,
  "message": "Daftar lanjut nonton berhasil dimuat.",
  "data": [
    {
      "id": 12,
      "progress_seconds": 650,
      "duration_seconds": 1440,
      "completed": false,
      "progress_percent": 45,
      "last_watched_at": "2026-09-20T01:35:00.000000Z",
      "episode": {
        "id": 1,
        "anime_id": 1,
        "episode_number": 1,
        "title": "I'm Used to It",
        "thumbnail_url": "http://localhost:8000/storage/thumbnails/ep1.jpg"
      }
    }
  ]
}
```

---

### 18. `GET /api/v1/watch/history?page=1&per_page=15`
**Header:** `Authorization: Bearer <token>`
**Response (`200 OK`):**
```json
{
  "success": true,
  "message": "Riwayat menonton berhasil dimuat.",
  "data": [
    {
      "id": 12,
      "progress_seconds": 650,
      "duration_seconds": 1440,
      "completed": false,
      "progress_percent": 45,
      "last_watched_at": "2026-09-20T01:35:00.000000Z",
      "episode": {
        "id": 1,
        "episode_number": 1,
        "title": "I'm Used to It"
      }
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 1,
    "last_page": 1,
    "from": 1,
    "to": 1
  },
  "links": {
    "first": "http://localhost:8000/api/v1/watch/history?page=1",
    "prev": null,
    "next": null,
    "last": "http://localhost:8000/api/v1/watch/history?page=1"
  }
}
```

---

### 19. `DELETE /api/v1/watch/history/{episodeId}`
**Header:** `Authorization: Bearer <token>`
**Response (`200 OK`):**
```json
{
  "success": true,
  "message": "Riwayat menonton berhasil dihapus."
}
```

---

### 20. `PATCH /api/v1/user/profile`
**Header:** `Authorization: Bearer <token>`
**Request Body (Multipart or JSON):**
```json
{
  "name": "Dema Adzhani Updated"
}
```
**Response (`200 OK`):**
```json
{
  "success": true,
  "message": "Profil berhasil diperbarui.",
  "data": {
    "id": 3,
    "name": "Dema Adzhani Updated",
    "email": "dema@example.com",
    "avatar_url": "https://ui-avatars.com/api/?name=Dema+Adzhani+Updated&background=random",
    "role": "user",
    "created_at": "2026-09-20T01:30:00.000000Z"
  }
}
```

---

### 21. `PATCH /api/v1/user/password`
**Header:** `Authorization: Bearer <token>`
**Request Body:**
```json
{
  "current_password": "Password123#",
  "password": "NewSecretPassword2026#",
  "password_confirmation": "NewSecretPassword2026#"
}
```
**Response (`200 OK`):**
```json
{
  "success": true,
  "message": "Password berhasil diperbarui."
}
```
