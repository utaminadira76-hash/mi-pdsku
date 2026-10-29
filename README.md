## Microservices E-Commerce (UTS)
Repositori ini berisi implementasi sistem microservices untuk UTS mata kuliah Microservices. Terdapat tiga layanan utama yang saling terintegrasi menggunakan REST API:

Product Service (Node.js/Express) - Mengelola katalog produk.
Cart Service (Laravel) - Mengelola keranjang belanja pengguna.
Order Service (Laravel) - Mengelola pesanan (checkout) dari keranjang.

## Arsitektur Sistem
Sistem ini menggunakan pendekatan Database per Service. Setiap service memiliki database MySQL-nya sendiri dan tidak diizinkan untuk membaca/menulis langsung ke database service lain. Komunikasi antar service dilakukan melalui HTTP Client.

## Alur Checkout (POST /api/orders):
Client mengirim request ke Order Service.
Order Service memanggil Cart Service (GET /api/carts/{userId}) untuk mengambil isi keranjang.
Order Service memanggil Product Service (GET /products/{id}) untuk mengambil detail produk (nama, harga, stok).
Order Service memvalidasi stok, menghitung total, dan menyimpan pesanan beserta snapshot nama dan harga produk ke database miliknya.
Order Service memanggil Cart Service (DELETE /api/carts/{userId}) untuk mengosongkan keranjang.

## Cara Menjalankan
Pastikan Docker Desktop sudah terpasang dan berjalan di komputer Anda
Clone repositori ini:
bash
git clone https://github.com/utaminadira76-hash/mi-pdsku
cd mi-pdsku
Jalankan seluruh sistem menggunakan Docker Compose:
bash
docker compose up --build -d
Tunggu beberapa saat hingga seluruh container menyala.
Seluruh sistem siap diuji!

## Daftar Endpoint (Order Service)
Berikut adalah endpoint yang tersedia di Order Service (berjalan di http://localhost:3003):
Method	Endpoint	Deskripsi
GET	/api/health	Cek status service
POST	/api/orders	Checkout pesanan dari keranjang
GET	/api/orders/{id}	Detail pesanan beserta item
GET	/api/orders/user/{userId}	Daftar pesanan user (bisa filter ?status=PENDING/PAID)
PATCH	/api/orders/{id}/pay	Simulasi pembayaran (PENDING -> PAID)

## Environment Variables
Konfigurasi Order Service diatur melalui environment variable:

DB\_HOST : order-db
DB\_DATABASE : order\_service
PRODUCT\_SERVICE\_URL : http://product-service:3000
CART\_SERVICE\_URL : http://cart-service:3002/api
SERVICE\_TIMEOUT : 5 (detik)

## Keputusan Desain \& Keterbatasan

Snapshot Harga: Nama dan harga produk disalin saat checkout agar pesanan lama tidak berubah jika harga berubah.
Database Transaction: Proses simpan pesanan dibungkus DB::transaction() (all-or-nothing).
Kegagalan Kosongkan Keranjang: Jika gagal, pesanan tidak dibatalkan, tetap 201, dan dicatat di log.
Keterbatasan: Tidak ada pengurangan stok nyata dan autentikasi (sesuai batasan soal).

## Penulis
Nama: Nadira Utami
NIM: 2401162015

