# Evonime - Platform Streaming Anime

## Persyaratan
- PHP 8.3+
- Composer
- Node.js 18+
- MySQL 8+ (via Laragon/XAMPP)
- Git

## Instalasi (3 Langkah)

### 1. Clone & Setup
```bash
git clone <repo-url> evonime
cd evonime
composer setup
```

### 2. Jalankan Server Development
```bash
composer dev
```

## Caching & Redis

Aplikasi ini dirancang untuk berjalan secara otomatis tanpa memerlukan Redis (Default menggunakan driver `file` untuk cache & session, serta `sync` untuk queue). 

Jika Redis tersedia dan aktif di sistem Anda, aplikasi akan otomatis mendeteksinya dan menggunakan Redis. Untuk mengaktifkannya, uncomment baris Redis pada file `.env`:

```env
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

## Default Admin Account
- **Email:** `admin@evonime.test`
- **Password:** `password`
- **Role:** `admin`

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

