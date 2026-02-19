# PANDUAN LENGKAP INSTALASI & OPERASIONAL SISTEM ANTREAN

Sistem antrean real-time yang dibuat menggunakan laravel 12, spatie Laravel Permission, livewire, reverb, flux-ui.

| Informasi | Detail |
| :--- | :--- |
| **Versi** | 1.0 (Februari 2026) |
| **Framework** | Laravel 12 (Stable) |
| **Database** | SQLite (Portable)|
| **Real-time** | Laravel Reverb (Websocket)|
| **UI Stack** | Livewire 3 & Flux UI|
| **Kreator** | Andhika Putra Pratama|

---

## 1. PERSIAPAN AWAL (PREREQUISITES)
Pastikan lingkungan server/komputer memiliki:
* **PHP**: v8.2 atau v8.3+ (Sangat disarankan v8.3 untuk Laravel 12).
* **Node.js**: Versi LTS & NPM.
* **Composer**: Versi terbaru.
* **Ekstensi PHP**: `sqlite3`, `gd`, `intl`, `mbstring`, `openssl`.

---

## 2. LANGKAH INSTALASI (LOCAL/DEVELOPMENT)
Silahkan baca file dengan cermat "PANDUAN_SISTEM.txt" untuk menjalankan sistem antrean ini

## 3. Akses Login Default:
### Untuk role: admin

```bash
    Email: admin@mail.com (Cek DatabaseSeeder untuk detail).
    Password: password123.
```
### Untuk role:customer_service

```bash
    Email: customer_service@mail.com (Cek DatabaseSeeder untuk detail).
    Password: password123.
```
