<?php

namespace App\DataTables;

use App\Models\Menu;
use App\Enums\MenuType;
use App\Enums\MenuStatus;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class MenuDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<Menu> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->setRowId('id')
            ->addColumn('actions', function (Menu $menu) {
                return view('admin.menu.components.action', compact('menu'));
            })
            ->editColumn('dish_picture', function (Menu $menu) {
                if ($menu->dish_picture) {
                    return '<img src="' . $menu->dish_picture_url . '" alt="' . e($menu->name) . '" class="dish-thumbnail" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">';
                }
                return '<div class="text-center text-muted" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; background: #f8f9fa; border-radius: 8px;">
                    <i class="fas fa-image fa-2x"></i>
                </div>';
            })
            ->editColumn('name', function (Menu $menu) {
                return '<div class="menu-name">
                    <strong>' . e($menu->name) . '</strong>
                    <br><small class="text-muted">' . e(substr($menu->description, 0, 50)) . '...</small>
                </div>';
            })
            ->editColumn('price', function (Menu $menu) {
                return '<span class="badge bg-success fs-6">' . $menu->formatted_price . '</span>';
            })
            ->editColumn('category', function (Menu $menu) {
                $icon = MenuType::getIcon($menu->category->value);
                $badgeClass = MenuType::getBadgeClass($menu->category->value);
                
                return '<span class="badge bg-' . $badgeClass . '"><i class="' . $icon . '"></i> ' . $menu->category->description . '</span>';
            })
            ->editColumn('status', function (Menu $menu) {
                $icon = MenuStatus::getIcon($menu->status->value);
                $badgeClass = MenuStatus::getBadgeClass($menu->status->value);
                
                return '<span class="badge bg-' . $badgeClass . '"><i class="' . $icon . '"></i> ' . $menu->status->description . '</span>';
            })
            ->editColumn('created_at', function (Menu $menu) {
                return '<small class="text-muted">
                    <i class="fas fa-calendar-alt"></i> ' . $menu->created_at->format('M d, Y') . '
                    <br><i class="fas fa-clock"></i> ' . $menu->created_at->diffForHumans() . '
                </small>';
            })
            ->filterColumn('category', function ($query, $keyword) {
                // Filter by exact category value (0, 1, 2, 3, 4)
                if (is_numeric($keyword)) {
                    $query->where('category', (int)$keyword);
                }
            })
            ->filterColumn('status', function ($query, $keyword) {
                // Filter by exact status value (0, 1)
                if (is_numeric($keyword)) {
                    $query->where('status', (int)$keyword);
                }
            })
            ->rawColumns(['dish_picture', 'name', 'price', 'category', 'status', 'created_at', 'actions']);
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<Menu>
     */
    public function query(Menu $model): QueryBuilder
    {
        return $model->newQuery()
            ->select('menus.*');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('menu_dataTable')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(5, 'desc')
            ->responsive(true)
            ->autoWidth(false)
            ->parameters([
                'dom' => '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>
                         <"row"<"col-sm-12"tr>>
                         <"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                'language' => [
                    'search' => '_INPUT_',
                    'searchPlaceholder' => 'Search menu items...',
                    'lengthMenu' => 'Show _MENU_ entries',
                    'info' => 'Showing _START_ to _END_ of _TOTAL_ menu items',
                    'infoEmpty' => 'No menu items available',
                    'infoFiltered' => '(filtered from _MAX_ total items)',
                    'zeroRecords' => 'No matching menu items found',
                    'emptyTable' => 'No menu items in the system',
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
                    ->text('<i class="fas fa-plus-circle"></i> Add Menu Item')
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
            Column::make('dish_picture')
                ->title('Image')
                ->addClass('text-center align-middle')
                ->orderable(false)
                ->searchable(false)
                ->width(80),
            Column::make('name')
                ->title('Menu Item')
                ->addClass('align-middle')
                ->width(250),
            Column::make('price')
                ->title('Price')
                ->addClass('text-center align-middle')
                ->width(100),
            Column::make('category')
                ->title('Category')
                ->addClass('text-center align-middle')
                ->width(150),
            Column::make('status')
                ->title('Status')
                ->addClass('text-center align-middle')
                ->width(130),
            Column::make('created_at')
                ->title('Date Added')
                ->addClass('text-center align-middle')
                ->width(150),
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
        return 'Menu_' . date('YmdHis');
    }
}