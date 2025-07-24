## **Featur**
- input keluhan pelanggan
- expor data keluhan pelanggan (xlsx, pdf, csv, txt)
- Dashboard
  - Pie Chart Keluhan By Status
  - Bar Char Time Series By Status
  - Table Top 10 Kleuhan Pelanggan
## **Petunjuk Pengunaan**
Proyek ini menggunakan framework [Laravel 9](https://laravel.com/docs/9.x/deployment#server-requirements) dan paket pengembangan antarmuka ***laravel/ui*** yang siap digunakan dan [Vue js 3](https://vuejs.org/) untuk antarmukanya.

### **Software pendukung yang perlu disiapkan sebelum memulai**

- PHP minimal versi 8.0.2 atau versi terbaru
- Composer
- NodeJS minimal versi 16 atau versi terbaru
- Git minimal versi 2 atau versi terbaru (Optional)

## **Github Repository**

### **Clone Repository**

- Clone via Git Bash (Windows) atau Terminal (Linux)
  ```
  git clone https://github.com/waloeh/keluhan-pelanggan.git
  ```
----


### **Panduan Instalasi**

- Buka Command Line arahkan ke folder ***keluhan-pelanggan***
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
  APP_URL=http://localhost:8000

  DB_CONNECTION=mysql
  DB_HOST=127.0.0.1
  DB_PORT=3306
  DB_DATABASE=keluhan_pelanggan
  DB_USERNAME=root
  DB_PASSWORD=
  ```
- selanjutnya, jalankan perintah:
  ```
  php artisan migrate --seed
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
- selanjutnya **login** dengan akun barusan

----

[![Tonton Demo]([https://img.youtube.com/vi/ID_VIDEO/0.jpg)](https://www.youtube.com/watch?v=ID_VIDEO](https://www.youtube.com/watch?v=9H0eJp7idbk))



