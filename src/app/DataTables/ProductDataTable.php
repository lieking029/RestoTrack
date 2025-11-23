<?php

namespace App\DataTables;

use App\Models\Product;
use App\Enums\InventoryStatus;
use App\Enums\UnitOfMeasurement;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ProductDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<Product> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->setRowId('id')
            ->addColumn('actions', function (Product $product) {
                return view('admin.product.components.action', compact('product'));
            })
            ->editColumn('name', function (Product $product) {
                return '<div class="product-name">' . e($product->name) . '</div>';
            })
            ->editColumn('initial_stock', function (Product $product) {
                return '<span class="badge bg-info">' . $product->initial_stock . '</span>';
            })
            ->editColumn('remaining_stock', function (Product $product) {
                $percentage = $product->stock_percentage;
                $badgeClass = $percentage > 50 ? 'success' : ($percentage > 20 ? 'warning' : 'danger');

                return '<div class="d-flex align-items-center gap-2">
                    <span class="badge bg-' . $badgeClass . '">' . $product->remaining_stock . '</span>
                    <small class="text-muted">(' . $percentage . '%)</small>
                </div>';
            })
            ->editColumn('unit_of_measurement', function (Product $product) {
                return '<span class="badge bg-secondary">' . UnitOfMeasurement::getLabel($product->unit_of_measurement->value) . '</span>';
            })
            ->editColumn('status', function (Product $product) {
                $icon = match ($product->status->value) {
                    InventoryStatus::OnStock => '<i class="fas fa-check-circle"></i>',
                    InventoryStatus::LowOnStock => '<i class="fas fa-exclamation-circle"></i>',
                    InventoryStatus::NoStock => '<i class="fas fa-times-circle"></i>',
                    default => ''
                };

                $badgeClass = match ($product->status->value) {
                    InventoryStatus::OnStock => 'success',
                    InventoryStatus::LowOnStock => 'warning',
                    InventoryStatus::NoStock => 'danger',
                    default => 'secondary'
                };

                return '<span class="badge bg-' . $badgeClass . '">' . $icon . ' ' . $product->status->description . '</span>';
            })
            ->editColumn('expiration_date', function (Product $product) {
                $days = $product->days_until_expiration;
                $class = 'text-muted';
                $icon = '';

                if ($product->is_expired) {
                    $class = 'text-danger fw-bold';
                    $icon = '<i class="fas fa-exclamation-triangle"></i> ';
                } elseif ($product->is_expiring_soon) {
                    $class = 'text-warning fw-bold';
                    $icon = '<i class="fas fa-clock"></i> ';
                }

                return '<span class="' . $class . '">' . $icon . $product->expiration_date->format('M d, Y') . '</span>';
            })
            ->editColumn('created_at', function (Product $product) {
                return '<small class="text-muted">' . $product->created_at->format('M d, Y') . '</small>';
            })
            ->filterColumn('status', function ($query, $keyword) {
                // Filter by exact status value (0, 1, 2)
                if (is_numeric($keyword)) {
                    $query->where('status', (int)$keyword);
                }
            })
            ->filterColumn('expiration_date', function ($query, $keyword) {
                // Special filter for expiring products
                if ($keyword === 'expiring') {
                    // Products expiring within 7 days
                    $query->whereDate('expiration_date', '<=', now()->addDays(7))
                          ->whereDate('expiration_date', '>=', now());
                } elseif ($keyword === 'expired') {
                    // Already expired products
                    $query->whereDate('expiration_date', '<', now());
                }
            })
            ->rawColumns(['name', 'initial_stock', 'remaining_stock', 'unit_of_measurement', 'status', 'expiration_date', 'created_at', 'actions']);
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<Product>
     */
    public function query(Product $model): QueryBuilder
    {
        return $model->newQuery()
            ->select('products.*');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('product_dataTable')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(0, 'asc')
            ->responsive(true)
            ->autoWidth(false)
            ->parameters([
                'dom' => '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>
                         <"row"<"col-sm-12"tr>>
                         <"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                'language' => [
                    'search' => '_INPUT_',
                    'searchPlaceholder' => 'Search products...',
                    'lengthMenu' => 'Show _MENU_ entries',
                    'info' => 'Showing _START_ to _END_ of _TOTAL_ products',
                    'infoEmpty' => 'No products available',
                    'infoFiltered' => '(filtered from _MAX_ total products)',
                    'zeroRecords' => 'No matching products found',
                    'emptyTable' => 'No products in inventory',
                    'paginate' => [
                        'first' => '<i class="fas fa-angle-double-left"></i>',
                        'last' => '<i class="fas fa-angle-double-right"></i>',
                        'next' => '<i class="fas fa-angle-right"></i>',
                        'previous' => '<i class="fas fa-angle-left"></i>',
                    ],
                ],
                'pageLength' => 10,
                'lengthMenu' => [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
                'processing' => true,
                'serverSide' => true,
            ])
            ->buttons([
                Button::make('create')
                    ->text('<i class="fas fa-plus-circle"></i> Add Product')
                    ->addClass('btn btn-success btn-sm'),
                Button::make('excel')
                    ->text('<i class="fas fa-file-excel"></i> Excel')
                    ->addClass('btn btn-primary btn-sm'),
                Button::make('csv')
                    ->text('<i class="fas fa-file-csv"></i> CSV')
                    ->addClass('btn btn-info btn-sm'),
                Button::make('pdf')
                    ->text('<i class="fas fa-file-pdf"></i> PDF')
                    ->addClass('btn btn-danger btn-sm'),
                Button::make('print')
                    ->text('<i class="fas fa-print"></i> Print')
                    ->addClass('btn btn-secondary btn-sm'),
                Button::make('reset')
                    ->text('<i class="fas fa-redo"></i> Reset')
                    ->addClass('btn btn-warning btn-sm'),
                Button::make('reload')
                    ->text('<i class="fas fa-sync"></i> Reload')
                    ->addClass('btn btn-light btn-sm'),
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('name')
                ->title('Product Name')
                ->addClass('align-middle')
                ->width(200),
            Column::make('initial_stock')
                ->title('Initial Stock')
                ->addClass('text-center align-middle')
                ->width(100),
            Column::make('remaining_stock')
                ->title('Current Stock')
                ->addClass('text-center align-middle')
                ->width(120),
            Column::make('unit_of_measurement')
                ->title('Unit')
                ->addClass('text-center align-middle')
                ->width(80),
            Column::make('status')
                ->title('Status')
                ->addClass('text-center align-middle')
                ->width(120),
            Column::make('expiration_date')
                ->title('Expiration Date')
                ->addClass('text-center align-middle')
                ->width(130),
            Column::make('created_at')
                ->title('Date Added')
                ->addClass('text-center align-middle')
                ->width(100),
            Column::computed('actions')
                ->title('Actions')
                ->addClass('text-center align-middle')
                ->exportable(false)
                ->printable(false)
                ->width(100),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Products_' . date('YmdHis');
    }
}