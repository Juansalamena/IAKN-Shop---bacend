# Dokumentasi Backend API Toko Online Multi-Gerai (IAKN Shop)

Dokumentasi ini disusun mengacu pada dokumen **Product Requirements Document (PRD): Backend Toko Online Multi-Gerai**.

---

## 1. Arsitektur & Keamanan Sistem
* **Multi-tenancy Model**: Single Database, Single Schema dengan pemisahan gerai berbasis Foreign Key `store_id` pada tabel `products`, `orders`, dan `users`.
* **Integritas & Anti-IDOR**: Middleware `EnsureStoreOwnership` dan validasi controller mencegah Admin Toko A membaca atau mengubah data milik Toko B melalui modifikasi payload atau parameter URL.
* **Performa**: Database indexing pada kolom `store_id`, `category_id`, `sku`, `status`, dan composite indexes `['store_id', 'is_active']`, `['store_id', 'status']`.
* **Split Order Logic**: Ketika Customer memesan produk dari lebih dari satu gerai dalam 1 sesi checkout, sistem secara otomatis memecah pesanan menjadi beberapa `Order` terpisah (1 Order per Gerai) dalam transaksi database ACID.

---

## 2. Kredensial Akun Pengujian (Semua Password: `password`)
| Role | Email | Gerai Terkait | Hak Akses |
|---|---|---|---|
| **Super Admin** | `superadmin@iakn.com` | Seluruh Gerai (Global) | Kelola gerai, buat admin toko, pantau semua order & laporan |
| **Admin Gerai Ambon** | `admin.ambon@iakn.com` | IAKN Shop - Gerai Ambon (`store_id: 1`) | CRUD produk toko Ambon, proses order toko Ambon |
| **Admin Gerai Saparua**| `admin.saparua@iakn.com`| IAKN Shop - Gerai Saparua (`store_id: 2`)| CRUD produk toko Saparua, proses order toko Saparua |
| **Customer** | `customer@iakn.com` | - | Belanja produk lintas gerai, checkout, tracking pesanan |

---

## 3. Daftar Endpoint RESTful API Sesuai PRD

### A. Modul Autentikasi & Otorisasi
| Method | Endpoint | Role | Deskripsi |
|---|---|---|---|
| `POST` | `/api/auth/login` | Publik | Autentikasi user dan generate Sanctum token |
| `POST` | `/api/auth/register` | Publik | Registrasi akun customer baru |
| `POST` | `/api/auth/logout` | Token Bearer | Hapus/revoke token akses saat ini |
| `GET` | `/api/auth/me` | Token Bearer | Ambil profil user yang sedang login |

#### Contoh Request Login:
```bash
curl -X POST http://127.0.0.1:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "superadmin@iakn.com",
    "password": "password"
  }'
```

---

### B. Modul Manajemen Gerai (Khusus Super Admin)
Header wajib: `Authorization: Bearer <token_super_admin>`

| Method | Endpoint | Deskripsi |
|---|---|---|
| `GET` | `/api/admin/stores` | Daftar seluruh gerai beserta ringkasan metrik |
| `POST` | `/api/admin/stores` | Registrasi gerai baru |
| `GET` | `/api/admin/stores/{id}` | Detail gerai spesifik |
| `PUT` | `/api/admin/stores/{id}` | Update informasi gerai |
| `POST` | `/api/admin/store-admins` | Membuat kredensial login Admin Toko dan memetakan ke `store_id` |
| `GET` | `/api/admin/store-admins` | Daftar seluruh akun Admin Toko |
| `GET` | `/api/admin/stores/reports` | Agregasi data transaksi seluruh gerai (omset, status, per-gerai) |
| `GET` | `/api/admin/all-orders` | Pantau seluruh pesanan lintas gerai (filter store, status, tanggal) |
| `GET` | `/api/admin/orders/{id}` | Detail pesanan lintas gerai |

#### Contoh Buat Admin Toko Baru:
```bash
curl -X POST http://127.0.0.1:8000/api/admin/store-admins \
  -H "Authorization: Bearer <TOKEN>" \
  -H "Content-Type: application/json" \
  -d '{
    "store_id": 1,
    "name": "Budi Santoso",
    "email": "budi.ambon@iakn.com",
    "password": "password123",
    "phone": "08123456789"
  }'
```

---

### C. Modul Manajemen Produk (Admin Toko & Publik)

#### Untuk Admin Toko:
Header wajib: `Authorization: Bearer <token_store_admin>`
*Backend otomatis menyematkan `store_id` dari sesi admin toko yang login.*

| Method | Endpoint | Deskripsi |
|---|---|---|
| `GET` | `/api/store/products` | Daftar produk khusus milik gerai yang login |
| `POST` | `/api/store/products` | Tambah produk baru (otomatis terikat ke `store_id` admin) |
| `GET` | `/api/store/products/{id}` | Detail produk toko (terproteksi anti-IDOR) |
| `PUT` | `/api/store/products/{id}` | Update produk atau stok (terproteksi anti-IDOR) |
| `DELETE` | `/api/store/products/{id}` | Hapus produk toko (terproteksi anti-IDOR) |

#### Contoh Input Produk Baru oleh Admin Toko:
```bash
curl -X POST http://127.0.0.1:8000/api/store/products \
  -H "Authorization: Bearer <TOKEN_ADMIN_AMBON>" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Topi Almamater IAKN",
    "sku": "AMB-TPI-004",
    "description": "Topi bordir logo IAKN bahan katun twill",
    "price": 45000,
    "stock": 100,
    "image": "https://example.com/topi.jpg"
  }'
```

#### Untuk Publik / Customer:
| Method | Endpoint | Filter / Parameter | Deskripsi |
|---|---|---|---|
| `GET` | `/api/products` | `?store_id=1&category_id=2&search=batik` | Katalog produk publik dengan filter dan pencarian |
| `GET` | `/api/products/{id}` | - | Detail produk publik |
| `GET` | `/api/stores` | - | Daftar gerai yang aktif |
| `GET` | `/api/categories` | - | Daftar kategori produk |

---

### D. Modul Manajemen Pesanan (Order Management & Split Order)

#### 1. Checkout Customer (Fitur Split Order):
Header: `Authorization: Bearer <token_customer>`
`POST /api/orders/checkout`

Jika customer membeli barang dari 2 gerai berbeda (contoh: 1 produk Gerai Ambon dan 1 produk Gerai Saparua), backend secara otomatis memecah menjadi **2 Order ID berbeda**.

#### Contoh Payload Checkout:
```json
{
  "shipping_address": "Jl. Wolter Monginsidi No. 12, Ambon",
  "notes": "Pesanan untuk asrama mahasiswa",
  "items": [
    {
      "product_id": 1,
      "quantity": 2
    },
    {
      "product_id": 4,
      "quantity": 1
    }
  ]
}
```

#### Contoh Respon Split Order:
```json
{
  "success": true,
  "message": "Checkout successful! Your order was automatically split into 2 separate store orders.",
  "split_order": true,
  "total_orders_created": 2,
  "grand_total": 545000,
  "data": [
    {
      "id": 5,
      "order_number": "ORD-20261026-S1-A9F82B",
      "customer_id": 4,
      "store_id": 1,
      "total_amount": "350000.00",
      "status": "PENDING",
      "store": {
        "id": 1,
        "name": "IAKN Shop - Gerai Ambon"
      },
      "items": [
        {
          "product_name": "Almamater Resmi IAKN Ambon (Navy)",
          "price": "175000.00",
          "quantity": 2,
          "subtotal": "350000.00"
        }
      ]
    },
    {
      "id": 6,
      "order_number": "ORD-20261026-S2-C3E71D",
      "customer_id": 4,
      "store_id": 2,
      "total_amount": "195000.00",
      "status": "PENDING",
      "store": {
        "id": 2,
        "name": "IAKN Shop - Gerai Saparua"
      },
      "items": [
        {
          "product_name": "Kemeja Batik Khas Maluku Saparua",
          "price": "195000.00",
          "quantity": 1,
          "subtotal": "195000.00"
        }
      ]
    }
  ]
}
```

#### 2. Pesanan untuk Admin Toko:
Header: `Authorization: Bearer <token_store_admin>`
* `GET /api/store/orders`: Hanya menampilkan pesanan yang masuk ke gerai yang bersangkutan.
* `GET /api/store/orders/{id}`: Detail pesanan gerai.
* `PUT /api/store/orders/{id}/status`: Memperbarui status pesanan (`PENDING` -> `PROCESSED` -> `SHIPPED` -> `DELIVERED` / `CANCELLED`). *Terproteksi anti-IDOR.*

---

## 4. Pengujian Otomatis (Automated Tests)
Semua skenario pengujian telah diuji dan lulus 100%:
```bash
php artisan test --filter=MultiStoreApiTest
```
Hasil:
- `✓ auth login generates token and returns role`
- `✓ super admin can create store and store admin`
- `✓ customer cannot access super admin endpoints`
- `✓ store admin creates product with auto injected store id`
- `✓ store admin cannot update other store product (Anti-IDOR)`
- `✓ split order checkout creates two separate orders`
- `✓ store admin can update status of their own order`
- `✓ public can browse and filter products`
- `✓ super admin can view aggregated reports`
