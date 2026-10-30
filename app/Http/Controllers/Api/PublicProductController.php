<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicProductController extends Controller
{
    /**
     * GET /api/products
     * Public endpoint for customers to browse products with filtering and search
     */
    public function index(Request $request): JsonResponse
    {
        $query = Product::with(['store:id,name,slug,phone,address', 'category:id,name,slug'])
            ->where('is_active', true)
            ->whereHas('store', function ($q) {
                $q->where('is_active', true);
            });

        // Filter by store
        if ($request->filled('store_id')) {
            $query->where('store_id', $request->input('store_id'));
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        // Search by product name, SKU, or description
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        if (in_array($sortBy, ['price', 'name', 'created_at']) && in_array(strtolower($sortOrder), ['asc', 'desc'])) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $perPage = (int) $request->input('per_page', 12);
        $products = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    /**
     * GET /api/products/{id}
     * Public product detail
     */
    public function show(int $id): JsonResponse
    {
        $product = Product::with(['store', 'category'])
            ->where('is_active', true)
            ->find($id);

        if (! $product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $product,
        ]);
    }

    /**
     * GET /api/categories
     * Public list of categories
     */
    public function categories(): JsonResponse
    {
        $categories = Category::withCount('products')->get();

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    /**
     * GET /api/stores
     * Public list of active stores
     */
    public function stores(): JsonResponse
    {
        $stores = Store::where('is_active', true)
            ->withCount('products')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $stores,
        ]);
    }
}
