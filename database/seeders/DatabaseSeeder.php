<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database according to PRD Multi-Gerai.
     */
    public function run(): void
    {
        // 1. Super Admin
        User::updateOrCreate(
            ['email' => 'superadmin@iakn.com'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
                'store_id' => null,
                'phone' => '081234567890',
                'address' => 'Kantor Pusat IAKN Ambon',
            ]
        );

        // Also ensure admin@admin.com can access as super_admin
        User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
                'store_id' => null,
                'phone' => '081111111111',
            ]
        );

        // 2. Stores (Gerai)
        $storeAmbon = Store::updateOrCreate(
            ['slug' => 'iakn-gerai-ambon'],
            [
                'name' => 'IAKN Shop - Gerai Ambon',
                'address' => 'Jl. Pattimura No. 10, Ambon, Maluku',
                'phone' => '082199887766',
                'is_active' => true,
            ]
        );

        $storeSaparua = Store::updateOrCreate(
            ['slug' => 'iakn-gerai-saparua'],
            [
                'name' => 'IAKN Shop - Gerai Saparua',
                'address' => 'Jl. Benteng Duurstede No. 4, Saparua, Maluku Tengah',
                'phone' => '082155443322',
                'is_active' => true,
            ]
        );

        // 3. Store Admins
        User::updateOrCreate(
            ['email' => 'admin.ambon@iakn.com'],
            [
                'name' => 'Admin Gerai Ambon',
                'password' => Hash::make('password'),
                'role' => 'store_admin',
                'store_id' => $storeAmbon->id,
                'phone' => '082112345671',
                'address' => 'Kompleks Kampus IAKN Ambon',
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin.saparua@iakn.com'],
            [
                'name' => 'Admin Gerai Saparua',
                'password' => Hash::make('password'),
                'role' => 'store_admin',
                'store_id' => $storeSaparua->id,
                'phone' => '082112345672',
                'address' => 'Kota Saparua',
            ]
        );

        // 4. Customer
        $customer = User::updateOrCreate(
            ['email' => 'customer@iakn.com'],
            [
                'name' => 'John Doe (Mahasiswa)',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'store_id' => null,
                'phone' => '081399881122',
                'address' => 'Asrama Mahasiswa IAKN, Ambon',
            ]
        );

        // 5. Categories
        $catTekstil = Category::updateOrCreate(['slug' => 'pakaian-seragam'], ['name' => 'Pakaian & Seragam']);
        $catBuku = Category::updateOrCreate(['slug' => 'buku-kuliah'], ['name' => 'Buku & Modul Kuliah']);
        $catAksesoris = Category::updateOrCreate(['slug' => 'aksesoris-elektronik'], ['name' => 'Aksesoris Elektronik']);
        $catSouvenir = Category::updateOrCreate(['slug' => 'souvenir-iakn'], ['name' => 'Souvenir & Merchandise']);

        // 6. Products for Gerai Ambon
        $prodAmbon1 = Product::updateOrCreate(
            ['sku' => 'AMB-ALM-001'],
            [
                'store_id' => $storeAmbon->id,
                'category_id' => $catTekstil->id,
                'name' => 'Almamater Resmi IAKN Ambon (Navy)',
                'description' => 'Jaket almamater resmi mahasiswa IAKN dengan logo bordir presisi tinggi.',
                'price' => 175000.00,
                'stock' => 50,
                'image' => 'https://images.unsplash.com/photo-1556905055-8f358a7a47b2?w=500&q=80',
                'is_active' => true,
            ]
        );

        $prodAmbon2 = Product::updateOrCreate(
            ['sku' => 'AMB-BK-002'],
            [
                'store_id' => $storeAmbon->id,
                'category_id' => $catBuku->id,
                'name' => 'Buku Panduan Akademik & Teologi Modern',
                'description' => 'Buku referensi kuliah wajib edisi revisi terbaru kurikulum MBKM.',
                'price' => 85000.00,
                'stock' => 30,
                'image' => 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=500&q=80',
                'is_active' => true,
            ]
        );

        Product::updateOrCreate(
            ['sku' => 'AMB-TMB-003'],
            [
                'store_id' => $storeAmbon->id,
                'category_id' => $catSouvenir->id,
                'name' => 'Tumbler Stainless Steel Logo IAKN',
                'description' => 'Tumbler insulasi 500ml tahan panas dan dingin hingga 12 jam.',
                'price' => 65000.00,
                'stock' => 75,
                'image' => 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=500&q=80',
                'is_active' => true,
            ]
        );

        // 7. Products for Gerai Saparua
        $prodSaparua1 = Product::updateOrCreate(
            ['sku' => 'SPR-KMS-001'],
            [
                'store_id' => $storeSaparua->id,
                'category_id' => $catTekstil->id,
                'name' => 'Kemeja Batik Khas Maluku Saparua',
                'description' => 'Kemeja batik katun premium dengan motif tenun khas Maluku Tengah.',
                'price' => 195000.00,
                'stock' => 40,
                'image' => 'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?w=500&q=80',
                'is_active' => true,
            ]
        );

        Product::updateOrCreate(
            ['sku' => 'SPR-FLS-002'],
            [
                'store_id' => $storeSaparua->id,
                'category_id' => $catAksesoris->id,
                'name' => 'Flashdisk Kayu Ukir Souvenir 64GB',
                'description' => 'USB 3.0 flash drive 64GB dengan casing kayu ukir handmade Saparua.',
                'price' => 95000.00,
                'stock' => 60,
                'image' => 'https://images.unsplash.com/photo-1618424181497-157f25b6ddd5?w=500&q=80',
                'is_active' => true,
            ]
        );

        // 8. Sample Initial Orders (To show in reports and dashboards)
        $orderAmbon = Order::updateOrCreate(
            ['order_number' => 'ORD-20261026-S' . $storeAmbon->id . '-DEMO01'],
            [
                'customer_id' => $customer->id,
                'store_id' => $storeAmbon->id,
                'total_amount' => 260000.00,
                'status' => Order::STATUS_SHIPPED,
                'shipping_address' => 'Jl. Wolter Monginsidi, Paso, Ambon',
                'notes' => 'Tolong bungkus rapi untuk kado wisuda.',
            ]
        );

        OrderItem::firstOrCreate(
            ['order_id' => $orderAmbon->id, 'product_id' => $prodAmbon1->id],
            [
                'product_name' => $prodAmbon1->name,
                'price' => 175000.00,
                'quantity' => 1,
                'subtotal' => 175000.00,
            ]
        );

        OrderItem::firstOrCreate(
            ['order_id' => $orderAmbon->id, 'product_id' => $prodAmbon2->id],
            [
                'product_name' => $prodAmbon2->name,
                'price' => 85000.00,
                'quantity' => 1,
                'subtotal' => 85000.00,
            ]
        );

        $orderSaparua = Order::updateOrCreate(
            ['order_number' => 'ORD-20261026-S' . $storeSaparua->id . '-DEMO02'],
            [
                'customer_id' => $customer->id,
                'store_id' => $storeSaparua->id,
                'total_amount' => 195000.00,
                'status' => Order::STATUS_PENDING,
                'shipping_address' => 'Desa Booi, Kec. Saparua',
                'notes' => 'Diambil sendiri di gerai.',
            ]
        );

        OrderItem::firstOrCreate(
            ['order_id' => $orderSaparua->id, 'product_id' => $prodSaparua1->id],
            [
                'product_name' => $prodSaparua1->name,
                'price' => 195000.00,
                'quantity' => 1,
                'subtotal' => 195000.00,
            ]
        );
    }
}
