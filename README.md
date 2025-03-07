# Laravel 12 - PEMINJAMAN - Panduan Instalasi

## Persyaratan Sistem

Sebelum menginstal proyek ini, pastikan sistem Anda memenuhi persyaratan berikut:

-   **PHP** >= 8.2
-   **Composer** terbaru
-   **Database** (MySQL)
-   **Git**

## Langkah Instalasi

### 1. Clone Repository

```sh
git clone https://github.com/BeniSyach/Peminjaman_backend_frontend.git
cd Peminjaman_backend_frontend
```

Gantilah `username/repository` dengan nama repository yang sesuai.

### 2. Instal Dependensi

#### Backend (Laravel)

```sh
composer install
```

### 3. Konfigurasi Environment

Salin file `.env.example` menjadi `.env`:

```sh
cp .env.example .env
```

Lalu, ubah konfigurasi sesuai kebutuhan, seperti pengaturan database, penyimpanan, dan lainnya.

### 4. Generate Application Key

```sh
php artisan key:generate
```

### 5. Konfigurasi Database

Pastikan database sudah dibuat, lalu jalankan migrasi:

```sh
php artisan migrate --seed
```

### 6. Jalankan Server

Jalankan server Laravel dengan perintah berikut:

```sh
php artisan serve
```

### 7. (Opsional) Konfigurasi Sanctum

Jika menggunakan Laravel Sanctum untuk otentikasi:

```sh
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

## Penggunaan

Akses aplikasi melalui:

```
http://127.0.0.1:8000
```

atau domain yang telah dikonfigurasi.

## Troubleshooting

-   Jika ada error terkait permission, coba jalankan:
    ```sh
    chmod -R 775 storage bootstrap/cache
    ```
-   Jika ada masalah cache, jalankan:
    ```sh
    php artisan cache:clear
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear
    ```
