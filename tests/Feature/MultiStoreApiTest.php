<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MultiStoreApiTest extends TestCase
{
    protected User $superAdmin;
    protected User $adminAmbon;
    protected User $adminSaparua;
    protected User $customer;
    protected Store $storeAmbon;
    protected Store $storeSaparua;
    protected Product $productAmbon;
    protected Product $productSaparua;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed data if needed
        $this->storeAmbon = Store::firstOrCreate(
            ['slug' => 'iakn-gerai-ambon'],
            ['name' => 'IAKN Shop - Gerai Ambon', 'is_active' => true]
        );

        $this->storeSaparua = Store::firstOrCreate(
            ['slug' => 'iakn-gerai-saparua'],
            ['name' => 'IAKN Shop - Gerai Saparua', 'is_active' => true]
        );

        $this->superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@iakn.com'],
            ['name' => 'Super Admin', 'password' => bcrypt('password'), 'role' => 'super_admin']
        );

        $this->adminAmbon = User::firstOrCreate(
            ['email' => 'admin.ambon@iakn.com'],
            ['name' => 'Admin Ambon', 'password' => bcrypt('password'), 'role' => 'store_admin', 'store_id' => $this->storeAmbon->id]
        );

        $this->adminSaparua = User::firstOrCreate(
            ['email' => 'admin.saparua@iakn.com'],
            ['name' => 'Admin Saparua', 'password' => bcrypt('password'), 'role' => 'store_admin', 'store_id' => $this->storeSaparua->id]
        );

        $this->customer = User::firstOrCreate(
            ['email' => 'customer@iakn.com'],
            ['name' => 'Customer Test', 'password' => bcrypt('password'), 'role' => 'customer']
        );

        $this->productAmbon = Product::firstOrCreate(
            ['sku' => 'TEST-AMB-01'],
            [
                'store_id' => $this->storeAmbon->id,
                'name' => 'Produk Test Ambon',
                'description' => 'Desc test',
                'price' => 100000,
                'stock' => 20,
                'image' => 'https://example.com/test.jpg',
                'is_active' => true,
            ]
        );

        $this->productSaparua = Product::firstOrCreate(
            ['sku' => 'TEST-SPR-01'],
            [
                'store_id' => $this->storeSaparua->id,
                'name' => 'Produk Test Saparua',
                'description' => 'Desc test',
                'price' => 150000,
                'stock' => 15,
                'image' => 'https://example.com/test2.jpg',
                'is_active' => true,
            ]
        );
    }

    /**
     * Test 1: Authentication for all roles
     */
    public function test_auth_login_generates_token_and_returns_role(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'superadmin@iakn.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.user.role', 'super_admin')
            ->assertJsonStructure(['data' => ['token', 'user']]);
    }

    /**
     * Test 1b: Google Sign In Authentication
     */
    public function test_google_login_authenticates_or_creates_customer(): void
    {
        $uniqueEmail = 'student.' . uniqid() . '@gmail.com';
        $googleId = 'gid_' . uniqid();

        $response = $this->postJson('/api/auth/google', [
            'email' => $uniqueEmail,
            'google_id' => $googleId,
            'name' => 'Mahasiswa IAKN Google',
            'avatar' => 'https://lh3.googleusercontent.com/photo.jpg',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.user.email', $uniqueEmail)
            ->assertJsonPath('data.user.role', 'customer')
            ->assertJsonPath('data.user.google_id', $googleId)
            ->assertJsonStructure(['data' => ['token', 'user']]);
    }

    /**
     * Test 2: Super Admin store management
     */
    public function test_super_admin_can_create_store_and_store_admin(): void
    {
        $token = $this->superAdmin->createToken('test', ['super_admin'])->plainTextToken;

        // Create store
        $storeResponse = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/admin/stores', [
                'name' => 'IAKN Shop - Cabang Tual ' . uniqid(),
                'address' => 'Kota Tual',
                'phone' => '081233445566',
            ]);

        $storeResponse->assertStatus(201)
            ->assertJsonPath('success', true);

        $newStoreId = $storeResponse->json('data.id');

        // Create store admin
        $adminResponse = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/admin/store-admins', [
                'store_id' => $newStoreId,
                'name' => 'Admin Tual',
                'email' => 'admin.tual.' . uniqid() . '@iakn.com',
                'password' => 'secret123',
            ]);

        $adminResponse->assertStatus(201)
            ->assertJsonPath('data.store_id', $newStoreId);
    }

    /**
     * Test 3: Non Super Admin cannot access store management
     */
    public function test_customer_cannot_access_super_admin_endpoints(): void
    {
        $token = $this->customer->createToken('test', ['customer'])->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/admin/stores', [
                'name' => 'Unauthorized Store',
            ]);

        $response->assertStatus(403);
    }

    /**
     * Test 4: Store Admin creates product - backend auto injects store_id
     */
    public function test_store_admin_creates_product_with_auto_injected_store_id(): void
    {
        $token = $this->adminAmbon->createToken('test', ['store_admin'])->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/store/products', [
                'name' => 'Produk Baru Ambon',
                'sku' => 'AMB-NEW-' . rand(100, 999),
                'description' => 'Deskripsi produk',
                'price' => 50000,
                'stock' => 10,
                'image' => 'https://example.com/img.jpg',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.store_id', $this->storeAmbon->id);
    }

    /**
     * Test 5: IDOR Protection - Store Admin B cannot edit Store Admin A's product
     */
    public function test_store_admin_cannot_update_other_store_product(): void
    {
        // Admin Saparua tries to update Admin Ambon's product
        $tokenSaparua = $this->adminSaparua->createToken('test', ['store_admin'])->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$tokenSaparua}")
            ->putJson("/api/store/products/{$this->productAmbon->id}", [
                'name' => 'Hacked Product Name',
                'price' => 1000,
            ]);

        $response->assertStatus(403);
    }

    /**
     * Test 6: SPLIT ORDER CHECKOUT
     * When customer orders items from Gerai Ambon and Gerai Saparua in 1 checkout,
     * backend MUST split into 2 separate orders.
     */
    public function test_split_order_checkout_creates_two_separate_orders(): void
    {
        $token = $this->customer->createToken('test', ['customer'])->plainTextToken;

        $initialStockAmbon = $this->productAmbon->fresh()->stock;
        $initialStockSaparua = $this->productSaparua->fresh()->stock;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/orders/checkout', [
                'shipping_address' => 'Jl. Jenderal Sudirman No. 1, Ambon',
                'notes' => 'Tolong kirim bersamaan jika memungkinkan',
                'items' => [
                    [
                        'product_id' => $this->productAmbon->id,
                        'quantity' => 2,
                    ],
                    [
                        'product_id' => $this->productSaparua->id,
                        'quantity' => 1,
                    ],
                ],
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('split_order', true)
            ->assertJsonPath('total_orders_created', 2);

        $orders = $response->json('data');
        $this->assertCount(2, $orders);

        $storeIds = collect($orders)->pluck('store_id')->toArray();
        $this->assertContains($this->storeAmbon->id, $storeIds);
        $this->assertContains($this->storeSaparua->id, $storeIds);

        // Verify stock deduction
        $this->assertEquals($initialStockAmbon - 2, $this->productAmbon->fresh()->stock);
        $this->assertEquals($initialStockSaparua - 1, $this->productSaparua->fresh()->stock);
    }

    /**
     * Test 7: Store Admin order status update and isolation
     */
    public function test_store_admin_can_update_status_of_their_own_order(): void
    {
        // Create an order for Ambon
        $order = Order::create([
            'customer_id' => $this->customer->id,
            'store_id' => $this->storeAmbon->id,
            'total_amount' => 100000,
            'status' => Order::STATUS_PENDING,
            'shipping_address' => 'Ambon',
        ]);

        $tokenAmbon = $this->adminAmbon->createToken('test', ['store_admin'])->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$tokenAmbon}")
            ->putJson("/api/store/orders/{$order->id}/status", [
                'status' => 'PROCESSED',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'PROCESSED');

        // Reset auth guard cache between requests
        $this->app['auth']->forgetGuards();

        // Admin Saparua cannot change Ambon's order
        $tokenSaparua = $this->adminSaparua->createToken('test', ['store_admin'])->plainTextToken;

        $forbiddenResponse = $this->withHeader('Authorization', "Bearer {$tokenSaparua}")
            ->putJson("/api/store/orders/{$order->id}/status", [
                'status' => 'SHIPPED',
            ]);

        $forbiddenResponse->assertStatus(403);
    }

    /**
     * Test 8: Public product browsing & filtering
     */
    public function test_public_can_browse_and_filter_products(): void
    {
        $response = $this->getJson("/api/products?store_id={$this->storeAmbon->id}");

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $items = $response->json('data.data');
        foreach ($items as $item) {
            $this->assertEquals($this->storeAmbon->id, $item['store_id']);
        }
    }

    /**
     * Test 9: Super Admin reports aggregation
     */
    public function test_super_admin_can_view_aggregated_reports(): void
    {
        $token = $this->superAdmin->createToken('test', ['super_admin'])->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/admin/stores/reports');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => [
                    'summary' => ['total_revenue', 'total_orders'],
                    'by_store',
                    'by_status',
                ],
            ]);
    }
}
