<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Menu;
use App\Models\Order;
use App\Enums\UnitOfMeasurement;
use App\Enums\MenuType;
use App\Enums\MenuStatus;
use App\Enums\OrderStatus;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ExportController extends Controller
{
    /**
     * Helper to get common export info.
     */
    private function getExportInfo(): array
    {
        return [
            'exportedBy' => auth()->user()->full_name,
            'exportedAt' => now()->format('F d, Y h:i A'),
        ];
    }

    /**
     * Helper to build a styled Excel sheet with title, export info, headers, and data.
     */
    private function buildExcelSheet(string $title, array $headers, array $data, string $exportedBy, string $exportedAt): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle($title);

        $lastCol = chr(64 + count($headers)); // A=65, so 65-1+count
        $columns = array_map(fn($i) => chr(65 + $i), range(0, count($headers) - 1));

        // Title
        $sheet->setCellValue('A1', "RestoTrack - {$title}");
        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Export info
        $midCol = $columns[intdiv(count($columns), 2)];
        $sheet->setCellValue('A2', "Exported By: {$exportedBy}");
        $sheet->mergeCells("A2:" . $columns[intdiv(count($columns), 2) - 1] . "2");
        $sheet->setCellValue("{$midCol}2", "Date Exported: {$exportedAt}");
        $sheet->mergeCells("{$midCol}2:{$lastCol}2");
        $sheet->getStyle("A2:{$lastCol}2")->getFont()->setSize(10)->setItalic(true);

        // Column headers
        foreach ($headers as $index => $header) {
            $sheet->setCellValue($columns[$index] . '4', $header);
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1A4D2E']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
        ];
        $sheet->getStyle("A4:{$lastCol}4")->applyFromArray($headerStyle);

        // Data rows
        $row = 5;
        foreach ($data as $rowData) {
            foreach (array_values($rowData) as $colIndex => $value) {
                $sheet->setCellValue($columns[$colIndex] . $row, $value);
            }

            $dataStyle = [
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
            ];
            $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray($dataStyle);

            // Left-align first column (name)
            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

            $row++;
        }

        // Auto-size columns
        foreach ($columns as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Total row
        $sheet->setCellValue("A{$row}", "Total Records: " . count($data));
        $sheet->mergeCells("A{$row}:{$lastCol}{$row}");
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        return $spreadsheet;
    }

    /**
     * Helper to download an Excel spreadsheet.
     */
    private function downloadExcel(Spreadsheet $spreadsheet, string $filenamePrefix)
    {
        $writer = new Xlsx($spreadsheet);
        $filename = $filenamePrefix . '_' . date('YmdHis') . '.xlsx';
        $tempPath = storage_path('app/' . $filename);
        $writer->save($tempPath);

        return response()->download($tempPath, $filename)->deleteFileAfterSend(true);
    }

    /*
    |--------------------------------------------------------------------------
    | Product Exports
    |--------------------------------------------------------------------------
    */

    private function getProductData(): array
    {
        return Product::orderBy('name', 'asc')->get()->map(function (Product $product) {
            return [
                'name' => $product->name,
                'initial_stock' => $product->initial_stock,
                'current_stock' => $product->remaining_stock,
                'unit' => UnitOfMeasurement::getLabel($product->unit_of_measurement->value),
                'status' => $product->status->description,
                'expiration_date' => $product->expiration_date->format('M d, Y'),
                'date_added' => $product->created_at->format('M d, Y'),
            ];
        })->toArray();
    }

    public function productsPdf()
    {
        $products = $this->getProductData();
        $info = $this->getExportInfo();

        $pdf = Pdf::loadView('exports.products-pdf', [
            'products' => $products,
            'exportedBy' => $info['exportedBy'],
            'exportedAt' => $info['exportedAt'],
        ])->setPaper('a4', 'landscape');

        return $pdf->download('Products_' . date('YmdHis') . '.pdf');
    }

    public function productsExcel()
    {
        $products = $this->getProductData();
        $info = $this->getExportInfo();
        $headers = ['Product Name', 'Initial Stock', 'Current Stock', 'Unit', 'Status', 'Expiration Date', 'Date Added'];

        $spreadsheet = $this->buildExcelSheet('Product List', $headers, $products, $info['exportedBy'], $info['exportedAt']);

        return $this->downloadExcel($spreadsheet, 'Products');
    }

    /*
    |--------------------------------------------------------------------------
    | Menu Exports
    |--------------------------------------------------------------------------
    */

    private function getMenuData(): array
    {
        return Menu::orderBy('name', 'asc')->get()->map(function (Menu $menu) {
            return [
                'name' => $menu->name,
                'price' => $menu->formatted_price,
                'category' => $menu->category->description,
                'status' => $menu->status->description,
                'date_added' => $menu->created_at->format('M d, Y'),
            ];
        })->toArray();
    }

    public function menuPdf()
    {
        $menus = $this->getMenuData();
        $info = $this->getExportInfo();

        $pdf = Pdf::loadView('exports.menu-pdf', [
            'menus' => $menus,
            'exportedBy' => $info['exportedBy'],
            'exportedAt' => $info['exportedAt'],
        ])->setPaper('a4', 'landscape');

        return $pdf->download('Menu_' . date('YmdHis') . '.pdf');
    }

    public function menuExcel()
    {
        $menus = $this->getMenuData();
        $info = $this->getExportInfo();
        $headers = ['Menu Item', 'Price', 'Category', 'Status', 'Date Added'];

        $spreadsheet = $this->buildExcelSheet('Menu List', $headers, $menus, $info['exportedBy'], $info['exportedAt']);

        return $this->downloadExcel($spreadsheet, 'Menu');
    }

    /*
    |--------------------------------------------------------------------------
    | Sales Report Exports
    |--------------------------------------------------------------------------
    */

    private function getSalesReportData(?string $startDate = null, ?string $endDate = null): array
    {
        $query = Order::with(['items', 'cashier', 'creator', 'payments'])
            ->where('status', OrderStatus::COMPLETED)
            ->orderBy('created_at', 'desc');

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        return $query->get()
            ->map(function (Order $order) {
                $itemNames = $order->items->map(fn($item) => $item->name . ' x' . $item->quantity)->implode(', ');

                $statusLabel = match ($order->status->value) {
                    OrderStatus::COMPLETED => 'Completed',
                    OrderStatus::PENDING => 'Pending',
                    OrderStatus::INPREPARATION => 'In Preparation',
                    OrderStatus::READY => 'Ready',
                    OrderStatus::SERVED => 'Served',
                    OrderStatus::CANCELLED => 'Cancelled',
                    default => 'Unknown',
                };

                $paymentMethods = $order->payments->pluck('method')->unique()->map(fn($m) => ucfirst($m))->implode(', ');

                $customerType = $order->discount_type ? strtoupper($order->discount_type) : 'Regular';

                $amountPaid = $order->discount_total ?? $order->total;

                return [
                    'order_id' => $order->id,
                    'date_time' => $order->created_at->format('M d, Y h:i A'),
                    'items' => $itemNames,
                    'subtotal' => '₱' . number_format($order->subtotal, 2),
                    'tax' => '₱' . number_format($order->tax, 2),
                    'total' => '₱' . number_format($order->total, 2),
                    'discount' => $order->discount_amount > 0 ? '-₱' . number_format($order->discount_amount, 2) . ' (' . $order->discount_type . ')' : '-',
                    'amount_paid' => '₱' . number_format($amountPaid, 2),
                    'payment_type' => $paymentMethods ?: '-',
                    'customer_type' => $customerType,
                    'status' => $statusLabel,
                    'cashier_name' => $order->cashier ? $order->cashier->full_name : '-',
                    'server_name' => $order->creator ? $order->creator->full_name : '-',
                ];
            })->toArray();
    }

    public function salesReportPdf(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $orders = $this->getSalesReportData($startDate, $endDate);
        $info = $this->getExportInfo();

        $dateRange = null;
        if ($startDate && $endDate) {
            $dateRange = \Carbon\Carbon::parse($startDate)->format('M d, Y') . ' - ' . \Carbon\Carbon::parse($endDate)->format('M d, Y');
        } elseif ($startDate) {
            $dateRange = 'From ' . \Carbon\Carbon::parse($startDate)->format('M d, Y');
        } elseif ($endDate) {
            $dateRange = 'Until ' . \Carbon\Carbon::parse($endDate)->format('M d, Y');
        }

        $pdf = Pdf::loadView('exports.sales-report-pdf', [
            'orders' => $orders,
            'exportedBy' => $info['exportedBy'],
            'exportedAt' => $info['exportedAt'],
            'dateRange' => $dateRange,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('SalesReport_' . date('YmdHis') . '.pdf');
    }

    public function salesReportExcel(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $orders = $this->getSalesReportData($startDate, $endDate);
        $info = $this->getExportInfo();
        $headers = ['Order ID', 'Date & Time', 'Items', 'Subtotal', 'Tax', 'Total', 'Discount', 'Amount Paid', 'Payment Type', 'Customer Type', 'Status', 'Cashier Name', 'Server Name'];

        $spreadsheet = $this->buildExcelSheet('Sales Report', $headers, $orders, $info['exportedBy'], $info['exportedAt']);

        return $this->downloadExcel($spreadsheet, 'SalesReport');
    }
}
