# Courier Service API

REST API sederhana untuk mengelola **master data Kurir (Courier)**, dibuat dengan Laravel.
Modul ini menyediakan operasi CRUD lengkap beserta fitur pencarian, filter level, sorting, dan pagination pada endpoint listing.

## Fitur

- CRUD kurir: `index`, `store`, `show`, `update`, `destroy`.
- Listing (`index`) mendukung:
  - **Pagination**.
  - **Sorting default** berdasarkan nama kurir (A–Z).
  - **Override sorting** berdasarkan tanggal pendaftaran (`created_at`).
  - **Pencarian** multi-kata: `?search=budi+agung` cocok dengan `Budiono Hadi Agung`.
  - **Filter level**: `?level=2,3` hanya menampilkan kurir level 2 atau 3.
- Validasi input lengkap untuk `store` & `update` menggunakan Form Request.
- Feature test untuk seluruh endpoint dan opsi listing.

## Struktur Tabel `couriers`

| Kolom           | Tipe            | Keterangan                          |
| --------------- | --------------- | ----------------------------------- |
| `id`            | bigint          | Primary key                         |
| `name`          | string          | Nama kurir (di-index)               |
| `phone`         | string (unique) | Nomor telepon                       |
| `email`         | string (unique) | Email, opsional                     |
| `vehicle_type`  | string          | Jenis kendaraan, opsional           |
| `vehicle_plate` | string          | Plat nomor, opsional                |
| `level`         | tinyint         | Level kurir **1–5** (default 1)     |
| `is_active`     | boolean         | Status aktif (default `true`)       |
| `created_at`    | timestamp       | Tanggal kurir didaftarkan           |
| `updated_at`    | timestamp       | Tanggal terakhir diperbarui         |

## Persyaratan

- PHP >= 8.2
- Composer

Project ini memakai **SQLite** secara default, jadi tidak perlu setup database server.

## Cara Menjalankan

```bash
# 1. Install dependency
composer install

# 2. Salin file environment & generate app key
cp .env.example .env
php artisan key:generate

# 3. Siapkan database SQLite & jalankan migrasi
touch database/database.sqlite
php artisan migrate

# (opsional) isi data contoh
php artisan db:seed

# 4. Jalankan server
php artisan serve
```

API akan tersedia di `http://127.0.0.1:8000`.

## Menjalankan Test

```bash
php artisan test
```

## Daftar Endpoint

Base URL: `/api`

| Method        | Endpoint         | Keterangan                     |
| ------------- | ---------------- | ------------------------------ |
| `GET`         | `/couriers`      | List kurir (pagination/filter) |
| `POST`        | `/couriers`      | Tambah kurir baru              |
| `GET`         | `/couriers/{id}` | Detail satu kurir              |
| `PUT`/`PATCH` | `/couriers/{id}` | Perbarui kurir                 |
| `DELETE`      | `/couriers/{id}` | Hapus kurir                    |

### Query Parameter untuk `GET /couriers`

| Parameter   | Contoh                | Keterangan                                                |
| ----------- | --------------------- | --------------------------------------------------------- |
| `search`    | `?search=budi+agung`  | Cari berdasarkan nama (cocok semua kata)                  |
| `level`     | `?level=2,3`          | Filter kurir dengan level tertentu                        |
| `sort`      | `?sort=registered_at` | Urutkan berdasarkan `name` (default) atau `registered_at` |
| `direction` | `?direction=desc`     | Arah urutan: `asc` (default) atau `desc`                  |
| `per_page`  | `?per_page=25`        | Jumlah data per halaman (default 15, maks 100)            |

### Format Response

Semua response memakai **response formatter** yang konsisten (lihat `app/Support/ApiResponse.php`).

**List (paginate)** — `GET /couriers`:

```json
{
  "success": true,
  "code": 200,
  "message": "Couriers retrieved successfully.",
  "page": 1,
  "per_page": 15,
  "query": "budi agung",
  "limit": 15,
  "data": [ ... ]
}
```

**Non-paginate** — `show`, `store`, `update`, `destroy`:

```json
{
  "code": 200,
  "message": "Courier retrieved successfully.",
  "data": { ... }
}
```

### Contoh Body untuk `POST` / `PUT`

```json
{
  "name": "Budiono Hadi Agung",
  "phone": "081234567890",
  "email": "budi@example.com",
  "vehicle_type": "motorcycle",
  "vehicle_plate": "B 1234 XYZ",
  "level": 3,
  "is_active": true
}
```

### Aturan Validasi

| Field           | Aturan                    |
| --------------- | ------------------------- |
| `name`          | wajib, string, maks 255   |
| `phone`         | wajib, unik, maks 32      |
| `email`         | opsional, format email, unik |
| `vehicle_type`  | opsional, string          |
| `vehicle_plate` | opsional, string, maks 32 |
| `level`         | wajib, integer, antara 1–5 |
| `is_active`     | opsional, boolean         |

> Pada `update`, semua field bersifat opsional (partial update); hanya field yang dikirim yang divalidasi & diperbarui.
