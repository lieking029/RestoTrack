<?php

namespace App\DataTables;

use App\Enums\UserType;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class UserDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<User> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->setRowId('id')
            ->addColumn('actions', function (User $user) {
                return view('admin.user.components.action', compact('user'));
            })
            ->editColumn('full_name', function (User $user) {
                return '<div class="user-name">
                    <strong>' . e($user->full_name) . '</strong>
                    <br><small class="text-muted">' . e($user->email) . '</small>
                </div>';
            })
            ->editColumn('user_type', function (User $user) {
                $badgeClass = match ($user->user_type->value) {
                    UserType::Admin => 'danger',
                    UserType::Manager => 'primary',
                    UserType::Employee => 'success',
                    default => 'secondary'
                };

                $icon = match ($user->user_type->value) {
                    UserType::Admin => '<i class="fas fa-crown"></i>',
                    UserType::Manager => '<i class="fas fa-user-tie"></i>',
                    UserType::Employee => '<i class="fas fa-user"></i>',
                    default => ''
                };

                return '<span class="badge bg-' . $badgeClass . '">' . $icon . ' ' . $user->user_type->description . '</span>';
            })
            ->editColumn('created_at', function (User $user) {
                return '<small class="text-muted">
                    <i class="fas fa-calendar-alt"></i> ' . $user->created_at->format('M d, Y') . '
                    <br><i class="fas fa-clock"></i> ' . $user->created_at->diffForHumans() . '
                </small>';
            })
            ->filterColumn('user_type', function ($query, $keyword) {
                // Filter by exact user_type value (0, 1, 2)
                if (is_numeric($keyword)) {
                    $query->where('user_type', (int) $keyword);
                }
            })
            ->rawColumns(['full_name', 'user_type', 'created_at', 'actions']);
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<User>
     */
    public function query(User $model): QueryBuilder
    {
        return $model->newQuery()
            ->select('users.*');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('user_dataTable')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(2, 'desc')
            ->responsive(true)
            ->autoWidth(false)
            ->parameters([
                'dom' => '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>
                         <"row"<"col-sm-12"tr>>
                         <"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                'language' => [
                    'search' => '_INPUT_',
                    'searchPlaceholder' => 'Search users...',
                    'lengthMenu' => 'Show _MENU_ entries',
                    'info' => 'Showing _START_ to _END_ of _TOTAL_ users',
                    'infoEmpty' => 'No users available',
                    'infoFiltered' => '(filtered from _MAX_ total users)',
                    'zeroRecords' => 'No matching users found',
                    'emptyTable' => 'No users in the system',
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
                    ->text('<i class="fas fa-user-plus"></i> Add User')
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
            Column::make('full_name')
                ->title('User')
                ->addClass('align-middle')
                ->width(250)
                ->orderable(false)
                ->searchable(false),
            Column::make('user_type')
                ->title('Role')
                ->addClass('text-center align-middle')
                ->width(120),
            Column::make('created_at')
                ->title('Joined')
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
        return 'Users_' . date('YmdHis');
    }
}