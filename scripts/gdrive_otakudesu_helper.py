#!/usr/bin/env python3
"""
Evonime - Dual-Mode Streaming Helper (Otakudesu + Google Drive 1080p)
Akun Penyimpanan: shenriu44@gmail.com (5TB Storage)

Fitur:
1. Google Drive Helper:
   - Ekstrak File ID dari berbagai format link sharing
   - Generate URL embed preview: https://drive.google.com/file/d/{id}/preview
   - Generate direct stream URL: https://drive.google.com/uc?export=download&id={id}
   - Launch / generate command pemutaran langsung di VLC Player Desktop

2. Otakudesu Scraper:
   - Cari anime dan ekstrak link episode serta embed server
   - Mendukung fallback antar provider jika salah satu server down/error
"""

import sys
import re
import json
import argparse
import subprocess
import urllib.parse

# Pastikan UTF-8 encoding di Windows terminal
if hasattr(sys.stdout, 'reconfigure'):
    sys.stdout.reconfigure(encoding='utf-8', errors='replace')
if hasattr(sys.stderr, 'reconfigure'):
    sys.stderr.reconfigure(encoding='utf-8', errors='replace')

try:
    import urllib.request
except ImportError:
    pass


class GoogleDriveStreamer:
    """Helper untuk memproses video anime 1080p dari Google Drive."""

    @staticmethod
    def extract_file_id(url_or_id: str) -> str:
        """Ekstrak Google Drive File ID dari URL atau string mentah."""
        clean = url_or_id.strip()

        # Pola alphanumeric ID standar Google Drive (25-50 karakter)
        if re.match(r"^[a-zA-Z0-9_-]{25,50}$", clean):
            return clean

        # Pola: /file/d/{id}/...
        match = re.search(r"/file/d/([a-zA-Z0-9_-]+)", clean)
        if match:
            return match.group(1)

        # Pola: ?id={id} atau &id={id}
        match = re.search(r"[?&]id=([a-zA-Z0-9_-]+)", clean)
        if match:
            return match.group(1)

        raise ValueError(f"Tidak dapat mengekstrak File ID dari input: {url_or_id}")

    @classmethod
    def get_stream_urls(cls, url_or_id: str) -> dict:
        """Menghasilkan semua format URL streaming Google Drive."""
        file_id = cls.extract_file_id(url_or_id)
        direct_url = f"https://drive.google.com/uc?export=download&id={file_id}"
        preview_url = f"https://drive.google.com/file/d/{file_id}/preview"
        vlc_url = direct_url

        return {
            "file_id": file_id,
            "quality": "1080p Full HD",
            "storage_account": "shenriu44@gmail.com (5TB)",
            "embed_preview_url": preview_url,
            "direct_stream_url": direct_url,
            "vlc_network_stream_url": vlc_url,
            "vlc_command": f'vlc "{direct_url}"',
        }

    @classmethod
    def play_in_vlc(cls, url_or_id: str) -> bool:
        """Membuka direct stream Google Drive langsung di VLC Media Player."""
        data = cls.get_stream_urls(url_or_id)
        stream_url = data["direct_stream_url"]

        print(f"\n🎬 Membuka video 1080p di VLC Media Player...")
        print(f"🔗 URL: {stream_url}\n")

        # Coba jalankan vlc dari PATH
        vlc_commands = [
            ["vlc", stream_url],
            [r"C:\Program Files\VideoLAN\VLC\vlc.exe", stream_url],
            [r"C:\Program Files (x86)\VideoLAN\VLC\vlc.exe", stream_url],
        ]

        for cmd in vlc_commands:
            try:
                subprocess.Popen(cmd)
                print("✅ VLC berhasil dijalankan!")
                return True
            except FileNotFoundError:
                continue

        print("⚠️ VLC executable tidak ditemukan otomatis.")
        print(f"👉 Silakan buka VLC manual -> Media (Ctrl+N) -> Tempel URL:\n{stream_url}")
        return False


class OtakudesuScraperSimple:
    """Scraper sederhana untuk mencari anime di Otakudesu."""

    BASE_URL = "https://otakudesu.cloud"

    @classmethod
    def search(cls, query: str) -> list:
        encoded = urllib.parse.quote(query)
        url = f"{cls.BASE_URL}/?s={encoded}&post_type=anime"
        headers = {
            "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
        }

        try:
            req = urllib.request.Request(url, headers=headers)
            with urllib.request.urlopen(req, timeout=10) as resp:
                html = resp.read().decode("utf-8", errors="ignore")

            # Ekstrak judul dan link sederhana
            results = []
            matches = re.findall(
                r'<h2><a href="([^"]+)">(.*?)</a></h2>', html
            )
            for link, title in matches:
                clean_title = re.sub(r"<[^>]+>", "", title).strip()
                results.append({"title": clean_title, "url": link})

            return results
        except Exception as e:
            print(f"⚠️ Gagal menghubungi Otakudesu: {e}")
            return []


def main():
    parser = argparse.ArgumentParser(
        description="Evonime Dual-Mode Streaming Helper (Google Drive 1080p + Otakudesu)"
    )
    subparsers = parser.add_subparsers(dest="command")

    # Command: gdrive
    gdrive_parser = subparsers.add_parser("gdrive", help="Generate / Play Google Drive 1080p Stream")
    gdrive_parser.add_argument("url_or_id", help="URL Google Drive atau File ID")
    gdrive_parser.add_argument("--vlc", action="store_true", help="Putar langsung di VLC Player Desktop")

    # Command: otaku
    otaku_parser = subparsers.add_parser("otaku", help="Cari anime di Otakudesu")
    otaku_parser.add_argument("query", help="Judul anime yang dicari")

    # Command: attach (artisan shortcut)
    attach_parser = subparsers.add_parser("attach", help="Tautkan file Google Drive ke database via Artisan")
    attach_parser.add_argument("anime", help="Slug atau ID Anime")
    attach_parser.add_argument("episode", type=int, help="Nomor Episode")
    attach_parser.add_argument("url_or_id", help="URL Google Drive atau File ID")

    args = parser.parse_args()

    if args.command == "gdrive":
        urls = GoogleDriveStreamer.get_stream_urls(args.url_or_id)
        print("\n" + "=" * 60)
        print("🌟 GOOGLE DRIVE 1080p STREAMING METADATA")
        print("=" * 60)
        print(f"Account           : {urls['storage_account']}")
        print(f"File ID           : {urls['file_id']}")
        print(f"Quality           : {urls['quality']}")
        print(f"Embed Preview     : {urls['embed_preview_url']}")
        print(f"Direct Stream URL : {urls['direct_stream_url']}")
        print(f"VLC Command       : {urls['vlc_command']}")
        print("=" * 60 + "\n")

        if args.vlc:
            GoogleDriveStreamer.play_in_vlc(args.url_or_id)

    elif args.command == "otaku":
        print(f"\n🔍 Mencari '{args.query}' di Otakudesu...")
        results = OtakudesuScraperSimple.search(args.query)
        if not results:
            print("Tidak ada hasil ditemukan atau server Otakudesu sedang proteksi Cloudflare.")
        else:
            for i, r in enumerate(results, 1):
                print(f"{i}. {r['title']}\n   Link: {r['url']}")

    elif args.command == "attach":
        cmd = [
            "php", "artisan", "anime:attach-gdrive",
            args.anime, str(args.episode), args.url_or_id
        ]
        print(f"Menjalankan Artisan: {' '.join(cmd)}")
        subprocess.run(cmd)

    else:
        parser.print_help()


if __name__ == "__main__":
    main()
