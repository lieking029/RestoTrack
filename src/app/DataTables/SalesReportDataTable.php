<?php

namespace App\DataTables;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class SalesReportDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<Order> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->setRowId('id')
            ->addColumn('actions', function (Order $order) {
                return view('admin.sales-report.components.action', compact('order'));
            })
            ->editColumn('created_at', function (Order $order) {
                return '<small class="text-muted">
                    <i class="fas fa-calendar-alt"></i> ' . $order->created_at->format('M d, Y') . '
                    <br><i class="fas fa-clock"></i> ' . $order->created_at->format('h:i A') . '
                </small>';
            })
            ->editColumn('total', function (Order $order) {
                return '<span class="badge bg-success fs-6">₱' . number_format($order->total, 2) . '</span>';
            })
            ->editColumn('subtotal', function (Order $order) {
                return '<span class="text-muted">₱' . number_format($order->subtotal, 2) . '</span>';
            })
            ->editColumn('tax', function (Order $order) {
                return '<span class="text-info">₱' . number_format($order->tax, 2) . '</span>';
            })
            ->editColumn('status', function (Order $order) {
                $badgeClass = match ($order->status->value) {
                    OrderStatus::COMPLETED => 'success',
                    OrderStatus::PENDING => 'warning',
                    OrderStatus::CONFIRMED => 'info',
                    OrderStatus::INPREPARATION => 'primary',
                    OrderStatus::READY => 'secondary',
                    OrderStatus::CANCELLED => 'danger',
                    default => 'secondary'
                };

                $icon = match ($order->status->value) {
                    OrderStatus::COMPLETED => '<i class="fas fa-check-circle"></i>',
                    OrderStatus::PENDING => '<i class="fas fa-clock"></i>',
                    OrderStatus::CONFIRMED => '<i class="fas fa-thumbs-up"></i>',
                    OrderStatus::INPREPARATION => '<i class="fas fa-fire"></i>',
                    OrderStatus::READY => '<i class="fas fa-bell"></i>',
                    OrderStatus::CANCELLED => '<i class="fas fa-times-circle"></i>',
                    default => ''
                };

                $statusLabel = match ($order->status->value) {
                    OrderStatus::COMPLETED => 'Completed',
                    OrderStatus::PENDING => 'Pending',
                    OrderStatus::CONFIRMED => 'Confirmed',
                    OrderStatus::INPREPARATION => 'In Preparation',
                    OrderStatus::READY => 'Ready',
                    OrderStatus::CANCELLED => 'Cancelled',
                    default => 'Unknown'
                };

                return '<span class="badge bg-' . $badgeClass . '">' . $icon . ' ' . $statusLabel . '</span>';
            })
            ->addColumn('cashier_name', function (Order $order) {
                if ($order->cashier) {
                    return '<div class="user-name">
                        <strong>' . e($order->cashier->full_name) . '</strong>
                    </div>';
                }
                return '<span class="text-muted">-</span>';
            })
            ->addColumn('items_count', function (Order $order) {
                return '<span class="badge bg-secondary">' . $order->items->count() . ' items</span>';
            })
            ->filterColumn('status', function ($query, $keyword) {
                if (is_numeric($keyword)) {
                    $query->where('status', (int) $keyword);
                }
            })
            ->filterColumn('created_at', function ($query, $keyword) {
                // Support date filtering
                if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $keyword)) {
                    $query->whereDate('created_at', $keyword);
                }
            })
            ->rawColumns(['created_at', 'total', 'subtotal', 'tax', 'status', 'cashier_name', 'items_count', 'actions']);
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<Order>
     */
    public function query(Order $model): QueryBuilder
    {
        return $model->newQuery()
            ->with(['cashier', 'items'])
            ->select('orders.*');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('sales_report_dataTable')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(0, 'desc')
            ->responsive(true)
            ->autoWidth(false)
            ->parameters([
                'dom' => '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>
                         <"row"<"col-sm-12"tr>>
                         <"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                'language' => [
                    'search' => '_INPUT_',
                    'searchPlaceholder' => 'Search orders...',
                    'lengthMenu' => 'Show _MENU_ entries',
                    'info' => 'Showing _START_ to _END_ of _TOTAL_ orders',
                    'infoEmpty' => 'No orders available',
                    'infoFiltered' => '(filtered from _MAX_ total orders)',
                    'zeroRecords' => 'No matching orders found',
                    'emptyTable' => 'No orders in the system',
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
            Column::make('created_at')
                ->title('Date & Time')
                ->addClass('align-middle')
                ->width(140),
            Column::computed('items_count')
                ->title('Items')
                ->addClass('text-center align-middle')
                ->width(80),
            Column::make('subtotal')
                ->title('Subtotal')
                ->addClass('text-end align-middle')
                ->width(100),
            Column::make('tax')
                ->title('Tax')
                ->addClass('text-end align-middle')
                ->width(80),
            Column::make('total')
                ->title('Total')
                ->addClass('text-end align-middle')
                ->width(120),
            Column::make('status')
                ->title('Status')
                ->addClass('text-center align-middle')
                ->width(120),
            Column::computed('cashier_name')
                ->title('Cashier')
                ->addClass('align-middle')
                ->width(150),
            Column::computed('actions')
                ->title('Actions')
                ->addClass('text-center align-middle')
                ->exportable(false)
                ->printable(false)
                ->width(80),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'SalesReport_' . date('YmdHis');
    }
}
