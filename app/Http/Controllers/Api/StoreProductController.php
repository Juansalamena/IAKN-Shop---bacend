<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StoreProductController extends Controller
{
    /**
     * GET /api/store/products
     * List products belonging to the store of the logged in Store Admin
     */
    public function index(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $query = Product::with('category', 'store');

        // Store Admin only sees products of their store
        if ($user->isStoreAdmin()) {
            $query->where('store_id', $user->store_id);
        } elseif ($request->filled('store_id')) {
            // Super Admin can filter by store_id
            $query->where('store_id', $request->input('store_id'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        $products = $query->orderBy('id', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    /**
     * POST /api/store/products
     * Input new product
     * Backend automatically attaches store_id from the authenticated user's session
     */
    public function store(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        // Determine store_id
        if ($user->isStoreAdmin()) {
            $storeId = $user->store_id;
        } elseif ($user->isSuperAdmin()) {
            $request->validate(['store_id' => 'required|exists:stores,id']);
            $storeId = (int) $request->input('store_id');
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:100',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'required', // Can be file upload or URL string
            'category_id' => 'nullable|exists:categories,id',
            'is_active' => 'nullable|boolean',
        ]);

        // Handle image upload if provided as file
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        } elseif (is_string($request->input('image'))) {
            $imagePath = $request->input('image');
        }

        $product = Product::create([
            'store_id' => $storeId, // Injected by backend, prevents over-posting
            'category_id' => $validated['category_id'] ?? null,
            'name' => $validated['name'],
            'sku' => $validated['sku'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'image' => $imagePath,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully.',
            'data' => $product->load('category', 'store'),
        ], 201);
    }

    /**
     * GET /api/store/products/{id}
     * Get single product detail with store ownership validation
     */
    public function show(Request $request, int $id): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $product = Product::with('category', 'store')->find($id);

        if (! $product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.',
            ], 404);
        }

        // Anti-IDOR validation
        if ($user->isStoreAdmin() && $product->store_id !== $user->store_id) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden: You cannot access a product that does not belong to your store.',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $product,
        ]);
    }

    /**
     * PUT /api/store/products/{id}
     * Update product details or stock with store ownership validation
     */
    public function update(Request $request, int $id): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $product = Product::find($id);

        if (! $product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.',
            ], 404);
        }

        // Anti-IDOR validation
        if ($user->isStoreAdmin() && $product->store_id !== $user->store_id) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden: You cannot update a product from another store.',
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'sku' => 'sometimes|required|string|max:100',
            'description' => 'sometimes|required|string',
            'price' => 'sometimes|required|numeric|min:0',
            'stock' => 'sometimes|required|integer|min:0',
            'image' => 'nullable',
            'category_id' => 'nullable|exists:categories,id',
            'is_active' => 'nullable|boolean',
        ]);

        // Never allow altering store_id during update to prevent privilege escalation
        unset($validated['store_id']);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully.',
            'data' => $product->fresh(['category', 'store']),
        ]);
    }

    /**
     * DELETE /api/store/products/{id}
     * Delete product with store ownership validation
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $product = Product::find($id);

        if (! $product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.',
            ], 404);
        }

        if ($user->isStoreAdmin() && $product->store_id !== $user->store_id) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden: You cannot delete a product from another store.',
            ], 403);
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully.',
        ]);
    }
}
