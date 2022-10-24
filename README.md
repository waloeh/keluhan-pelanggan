## **Petunjuk Pengunaan**
Proyek ini menggunakan framework [Laravel 9](https://laravel.com/docs/9.x/deployment#server-requirements) yang siap digunakan untuk keperluan tes keahlian web developer

### **Software pendukung yang wajib disiapkan sebelum memulai**

- Git minimal versi 2 atau versi terbaru

----

### **Clone Repository**

#### **- SourceTree**
- Clone Repository
  ```
  https://bitbucket.org/eskalink-id/keluhanpelanggan.git
  ```
- Checkout branch sesuai Nomor Tes
  ```
  contoh: git checkout JKT-22000
  ```

#### **- Command Line atau Terminal (Linux)**
- Clone repository
  ```
  git clone https://bitbucket.org/eskalink-id/keluhanpelanggan.git
  ```
- Pada Command line arahkan ke folder keluhanpelanggan
- Checkout branch sesuai Nomor Tes (via Command atau via Source Tree)
  ```
  contoh: git checkout JKT-22000
  ```

----

### **Installation**

- Buka Command Line arahkan ke folder ***keluhanpelanggan***
- kemudian jalankan perintah:
  ```
  composer install
  ```
- selanjutnya, jalankan perintah:
  ```
  php -r "copy('.env.example', '.env');";
  ```
- selanjutnya, jalankan perintah:
  ```
  php artisan key:generate
  ```
- selanjutnya, buat database dengan nama ***keluhan_pelanggan***
- selanjutnya, sesuaikan parameter pada file **.env**, seperti berikut:
  ```
  DB_CONNECTION=mysql
  DB_HOST=127.0.0.1
  DB_PORT=3306
  DB_DATABASE=keluhan_pelanggan
  DB_USERNAME=root
  DB_PASSWORD=
  ```
- selanjutnya, jalankan perintah:
  ```
  php artisan migrate
  ```
- selanjutnya, jalankan perintah berikut untuk compiled assets (development):
  ```
  - npm install
  - npm run dev
  ```
- selanjutnya, buka command line yang baru, jalankan perintah:
  ```
  php artisan serve
  ```

- selanjutnya, buka browser dengan url [http://localhost:8000](http://localhost:8000)
- selanjutnya, klik link **Register** dan lengkapi isian pada form register
- Selamat melanjutkan Tes, dengan membaca ketentuan pada **Soal Tes Keahlian**

----

## **Soal Tes Keahlian**

1. Pada halaman Keluhan Pelanggan buat fungsi create, read, update dan delete (CRUD)menggunakan axios atau ajax

2. Buat Model dengan nama **KeluhanPelanggan** serta migrationnya, dengan struktur table seperti berikut:
   ```
   Nama Tabel: keluhan_pelanggan
   Struktur Tabel:
   --------------------------------------------------------------
   | Column Name    | Type Data | Length | Note                 |
   --------------------------------------------------------------
   | id             | string    | 20     | not null primary key |
   | nama           | string    | 50     | not null             |
   | email          | string    | 20     | not null             |
   | nomor_hp       | integer   |        | null                 |
   | flag_aktif     | boolean   |        | default true         |
   | status_keluhan | varchar   | 1      | not null default 'O' |
   | keluhan        | text      |        | not null             |
   --------------------------------------------------------------
   ```

3. Buat Controller  dengan nama KeluhanPelangganController untuk proses CRUD
4. Ketika melakukan create dan update data berikan validasi sesuai dengan atribute table
5. Buat command scheduller : 
   ```
   - Menghapus keluhan jika flag_aktif bernilai false dan status_keluhan bernilai O (O = Open)
   - Untuk mengubah nilai status_keluhan menjadi C (C = Close) jika sudah lewat 1 hari sejak keluhan dibuat
   ```

**Selamat mengerjakan, semoga sukses**

----


**Catatan: Jangan Lupa di push kembali ke bitbucket ya!**

*berikut ini cara commit dan push:*

```

- git config --local user.email "your@email.com"
- git config --local user.name "Yourname"

- git add .
- git commit -a -m "Commit hasil tes"
- git push -u origin JKT-22000
```

