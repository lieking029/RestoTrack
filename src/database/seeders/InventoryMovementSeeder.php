<?php

namespace Database\Seeders;

use App\Models\InventoryItem;
use App\Models\InventoryMovement;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class InventoryMovementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Allowed reasons: SALE, CANCEL, ADJUSTMENT, WASTE, RECEIVING
     */
    public function run(): void
    {
        $inventoryItems = InventoryItem::with('product')->get();
        $employees = User::whereHas('roles')->get();

        if ($employees->isEmpty()) {
            $this->command->warn('No employees found. Skipping inventory movements.');
            return;
        }

        $totalMovements = 0;

        foreach ($inventoryItems as $item) {
            $performer = $employees->random();

            // 1. Initial receiving movement (historical setup - use RECEIVING)
            InventoryMovement::create([
                'id' => Str::uuid()->toString(),
                'inventory_item_id' => $item->id,
                'order_id' => null,
                'performed_by' => $performer->id,
                'type' => 'CREDIT',
                'reason' => 'RECEIVING',
                'quantity' => $item->stock_quantity + fake()->numberBetween(50, 150),
                'note' => 'Initial inventory setup for ' . $item->product->name,
                'created_at' => fake()->dateTimeBetween('-60 days', '-45 days'),
                'updated_at' => now(),
            ]);
            $totalMovements++;

            // 2. Receiving movements (2-5 restocking events)
            $receiveCount = fake()->numberBetween(2, 5);
            for ($i = 0; $i < $receiveCount; $i++) {
                $suppliers = ['Metro Wholesale', 'S&R Membership', 'Puregold', 'Local Market', 'Direct Supplier'];
                $notes = ['Regular weekly delivery', 'Emergency restock', 'Monthly bulk order', 'Fresh produce delivery'];

                InventoryMovement::create([
                    'id' => Str::uuid()->toString(),
                    'inventory_item_id' => $item->id,
                    'order_id' => null,
                    'performed_by' => $employees->random()->id,
                    'type' => 'CREDIT',
                    'reason' => 'RECEIVING',
                    'quantity' => fake()->numberBetween(20, 100),
                    'note' => fake()->randomElement($notes) . ' from ' . fake()->randomElement($suppliers),
                    'created_at' => fake()->dateTimeBetween('-40 days', '-5 days'),
                    'updated_at' => now(),
                ]);
                $totalMovements++;
            }

            // 3. Waste movements (0-3 spoilage/damage events)
            $wasteCount = fake()->numberBetween(0, 3);
            for ($i = 0; $i < $wasteCount; $i++) {
                $wasteReasons = [
                    'Expired - past best before date',
                    'Spoiled - improper storage',
                    'Damaged during handling',
                    'Quality check failed',
                    'Contamination detected',
                ];

                InventoryMovement::create([
                    'id' => Str::uuid()->toString(),
                    'inventory_item_id' => $item->id,
                    'order_id' => null,
                    'performed_by' => $employees->random()->id,
                    'type' => 'DEBIT',
                    'reason' => 'WASTE',
                    'quantity' => fake()->numberBetween(1, 15),
                    'note' => fake()->randomElement($wasteReasons),
                    'created_at' => fake()->dateTimeBetween('-30 days', '-2 days'),
                    'updated_at' => now(),
                ]);
                $totalMovements++;
            }

            // 4. Adjustment movements (0-2 corrections)
            $adjustCount = fake()->numberBetween(0, 2);
            for ($i = 0; $i < $adjustCount; $i++) {
                $isPositive = fake()->boolean(40);
                $adjustmentNotes = [
                    'Physical count adjustment',
                    'Inventory audit correction',
                    'System discrepancy fix',
                    'Stock take variance',
                ];

                InventoryMovement::create([
                    'id' => Str::uuid()->toString(),
                    'inventory_item_id' => $item->id,
                    'order_id' => null,
                    'performed_by' => $employees->random()->id,
                    'type' => $isPositive ? 'CREDIT' : 'DEBIT',
                    'reason' => 'ADJUSTMENT',
                    'quantity' => fake()->numberBetween(1, 10),
                    'note' => fake()->randomElement($adjustmentNotes),
                    'created_at' => fake()->dateTimeBetween('-20 days', '-1 day'),
                    'updated_at' => now(),
                ]);
                $totalMovements++;
            }
        }

        $this->command->info("✓ Created {$totalMovements} inventory movements");
        $this->command->info('  - RECEIVING: ' . InventoryMovement::where('reason', 'RECEIVING')->count());
        $this->command->info('  - WASTE: ' . InventoryMovement::where('reason', 'WASTE')->count());
        $this->command->info('  - ADJUSTMENT: ' . InventoryMovement::where('reason', 'ADJUSTMENT')->count());
    }
}
