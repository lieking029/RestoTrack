<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\WasteManagementService;
use Illuminate\Http\Request;

class WasteManagementController extends Controller
{
    public function __construct(
        protected WasteManagementService $wasteService
    ) {}

    /**
     * Display the waste management dashboard.
     */
    public function index()
    {
        $wasteStats = $this->wasteService->getWasteStats();
        $expirySummary = $this->wasteService->getExpirySummary();
        $recentWasteLogs = $this->wasteService->getRecentWasteLogs(5);
        $dailyWasteChart = $this->wasteService->getDailyWasteChart(7);
        $topWastedProducts = $this->wasteService->getWasteByProduct(5);

        return view('admin.waste-management.index', compact(
            'wasteStats',
            'expirySummary',
            'recentWasteLogs',
            'dailyWasteChart',
            'topWastedProducts'
        ));
    }

    /**
     * Display expiry tracking page.
     */
    public function expiry(Request $request)
    {
        $filter = $request->get('filter', 'all');

        $products = match ($filter) {
            'expired' => $this->wasteService->getExpiredProducts(),
            'today' => Product::whereDate('expiration_date', now())->where('remaining_stock', '>', 0)->get(),
            '3days' => $this->wasteService->getExpiringProducts(3),
            '7days' => $this->wasteService->getExpiringProducts(7),
            '30days' => $this->wasteService->getExpiringProducts(30),
            default => Product::where('remaining_stock', '>', 0)
                ->whereNotNull('expiration_date')
                ->orderBy('expiration_date')
                ->get(),
        };

        $expirySummary = $this->wasteService->getExpirySummary();

        return view('admin.waste-management.expiry', compact('products', 'expirySummary', 'filter'));
    }

    /**
     * Display waste log history.
     */
    public function logs(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $wasteLogs = $this->wasteService->getWasteLogs($startDate, $endDate);
        $wasteStats = $this->wasteService->getWasteStats($startDate, $endDate);

        return view('admin.waste-management.logs', compact('wasteLogs', 'wasteStats', 'startDate', 'endDate'));
    }

    /**
     * Show form to log waste for a product.
     */
    public function create(Request $request)
    {
        $productId = $request->get('product_id');
        $product = $productId ? Product::find($productId) : null;

        $products = Product::where('remaining_stock', '>', 0)
            ->orderBy('name')
            ->get();

        $wasteReasons = WasteManagementService::getWasteReasons();

        return view('admin.waste-management.create', compact('products', 'product', 'wasteReasons'));
    }

    /**
     * Store a new waste log.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string',
            'notes' => 'nullable|string|max:500',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        if ($validated['quantity'] > $product->remaining_stock) {
            return back()->withErrors(['quantity' => 'Quantity exceeds available stock.'])->withInput();
        }

        $this->wasteService->logWaste(
            $product,
            $validated['quantity'],
            $validated['reason'],
            auth()->id(),
            $validated['notes']
        );

        alert()->success('Waste logged successfully');
        return redirect()->route('admin.waste-management.index');
    }

    /**
     * Dispose an expired product.
     */
    public function dispose(Request $request, Product $product)
    {
        if ($product->remaining_stock <= 0) {
            alert()->error('Product has no stock to dispose');
            return back();
        }

        $notes = $request->get('notes', 'Disposed due to expiration');

        $this->wasteService->disposeExpired($product, auth()->id(), $notes);

        alert()->success("Successfully disposed {$product->name}");
        return back();
    }

    /**
     * Bulk dispose expired products.
     */
    public function bulkDispose(Request $request)
    {
        $validated = $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id',
        ]);

        $count = 0;
        foreach ($validated['product_ids'] as $productId) {
            $product = Product::find($productId);
            if ($product && $product->remaining_stock > 0) {
                $this->wasteService->disposeExpired($product, auth()->id());
                $count++;
            }
        }

        alert()->success("Successfully disposed {$count} expired product(s)");
        return redirect()->route('admin.waste-management.expiry', ['filter' => 'expired']);
    }
}
