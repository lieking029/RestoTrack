<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::create(['name' => 'manage_users']);
        Permission::create(['name' => 'view_users']);
        Permission::create(['name' => 'create_users']);
        Permission::create(['name' => 'update_users']);
        Permission::create(['name' => 'archive_users']);

        Permission::create(['name' => 'manage_roles']);
        Permission::create(['name' => 'view_roles']);
        Permission::create(['name' => 'assign_roles']);

        Permission::create(['name' => 'view_employees']);
        Permission::create(['name' => 'create_employees']);
        Permission::create(['name' => 'update_employees']);
        Permission::create(['name' => 'archive_employees']);
        Permission::create(['name' => 'update_employee_status']);
        Permission::create(['name' => 'view_employment_history']);

        Permission::create(['name' => 'view_menu']);
        Permission::create(['name' => 'create_menu_items']);
        Permission::create(['name' => 'update_menu_items']);
        Permission::create(['name' => 'archive_menu_items']);
        Permission::create(['name' => 'mark_menu_unavailable']);

        Permission::create(['name' => 'view_inventory']);
        Permission::create(['name' => 'update_inventory']);
        Permission::create(['name' => 'adjust_inventory']);
        Permission::create(['name' => 'view_inventory_movements']);
        Permission::create(['name' => 'manage_inventory_thresholds']);

        Permission::create(['name' => 'create_orders']);
        Permission::create(['name' => 'view_orders']);
        Permission::create(['name' => 'update_orders']);
        Permission::create(['name' => 'cancel_orders']);

        Permission::create(['name' => 'process_payments']);
        Permission::create(['name' => 'issue_refunds']);
        Permission::create(['name' => 'view_transactions']);

        Permission::create(['name' => 'view_sales_reports']);
        Permission::create(['name' => 'view_sales_analytics']);
        Permission::create(['name' => 'export_sales_reports']);
        Permission::create(['name' => 'view_real_time_dashboard']);

        Permission::create(['name' => 'view_kitchen_orders']);
        Permission::create(['name' => 'update_order_status']);
        Permission::create(['name' => 'prioritize_orders']);

        Permission::create(['name' => 'view_waste_reports']);
        Permission::create(['name' => 'manage_waste_records']);
        Permission::create(['name' => 'view_expiry_alerts']);
        Permission::create(['name' => 'manage_expiry_settings']);

        Permission::create(['name' => 'view_system_settings']);
        Permission::create(['name' => 'update_system_settings']);

        Permission::create(['name' => 'access_mobile_app']);
        Permission::create(['name' => 'access_pos_system']);
        Permission::create(['name' => 'access_kds']);
    }
}
