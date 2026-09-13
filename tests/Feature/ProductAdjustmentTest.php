<?php

namespace Tests\Feature;

use App\Models\NewProduct;
use App\Models\ProductAdjustment;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProductAdjustmentTest extends TestCase
{
    use RefreshDatabase;

    /* ------------------------------------------------------------------ */
    /* Helpers                                                            */
    /* ------------------------------------------------------------------ */

    protected function makeAdmin(): User
    {
        $user = new User();
        $user->name = 'Admin';
        $user->email = 'admin_' . uniqid() . '@example.com';
        $user->phone = '018' . random_int(10000000, 99999999);
        $user->password = Hash::make('secret');
        $user->role = 2;
        $user->save();

        return $user;
    }

    protected function makeCustomer(): User
    {
        $user = new User();
        $user->name = 'Customer';
        $user->email = 'cust_' . uniqid() . '@example.com';
        $user->phone = '017' . random_int(10000000, 99999999);
        $user->password = Hash::make('secret');
        $user->role = 1;
        $user->save();

        return $user;
    }

    protected function makeVariant(int $qty): ProductVariant
    {
        $product = NewProduct::create([
            'name' => 'Air Fryer V3 ' . uniqid(),
            'slug' => 'air-fryer-' . uniqid(),
            'sku' => 'PROD-' . uniqid(),
            'status' => 1,
        ]);

        return ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'AF-V3-BLK-' . uniqid(),
            'is_variant' => 0,
            'qty' => $qty,
            'selling_price' => 100,
            'compare_price' => 120,
            'purchase_price' => 60,
            'allow_pre_order' => false,
        ]);
    }

    protected function store(User $admin, array $payload)
    {
        return $this->actingAs($admin, 'admin')->post(route('admin.adjustments.store'), $payload);
    }

    /* ------------------------------------------------------------------ */
    /* Test 1 — Increase                                                  */
    /* ------------------------------------------------------------------ */

    public function test_increase_stock(): void
    {
        $admin = $this->makeAdmin();
        $variant = $this->makeVariant(20);

        $response = $this->store($admin, [
            'variant_id' => $variant->id,
            'adjustment_type' => 'increase',
            'quantity' => 5,
            'reason' => 'Found Stock',
        ]);

        $response->assertRedirect();

        $this->assertSame(25, (int) $variant->fresh()->qty);

        $adjustment = ProductAdjustment::first();
        $this->assertSame('increase', $adjustment->adjustment_type);
        $this->assertSame(5, $adjustment->quantity);
        $this->assertSame(20, $adjustment->stock_before);
        $this->assertSame(25, $adjustment->stock_after);
        $this->assertNull($adjustment->actual_stock);
        $this->assertSame($admin->id, $adjustment->created_by);
        $this->assertMatchesRegularExpression('/^ADJ-\d{6}$/', $adjustment->adjustment_no);
    }

    /* ------------------------------------------------------------------ */
    /* Test 2 — Decrease                                                  */
    /* ------------------------------------------------------------------ */

    public function test_decrease_stock(): void
    {
        $admin = $this->makeAdmin();
        $variant = $this->makeVariant(20);

        $this->store($admin, [
            'variant_id' => $variant->id,
            'adjustment_type' => 'decrease',
            'quantity' => 5,
            'reason' => 'Damaged',
        ])->assertRedirect();

        $this->assertSame(15, (int) $variant->fresh()->qty);

        $adjustment = ProductAdjustment::first();
        $this->assertSame(-5, $adjustment->quantity);
        $this->assertSame(20, $adjustment->stock_before);
        $this->assertSame(15, $adjustment->stock_after);
    }

    /* ------------------------------------------------------------------ */
    /* Test 3 — Prevent negative                                          */
    /* ------------------------------------------------------------------ */

    public function test_decrease_cannot_make_stock_negative(): void
    {
        $admin = $this->makeAdmin();
        $variant = $this->makeVariant(3);

        $response = $this->store($admin, [
            'variant_id' => $variant->id,
            'adjustment_type' => 'decrease',
            'quantity' => 5,
        ]);

        $response->assertSessionHasErrors('quantity');

        // Stock unchanged, no history record.
        $this->assertSame(3, (int) $variant->fresh()->qty);
        $this->assertSame(0, ProductAdjustment::count());
    }

    /* ------------------------------------------------------------------ */
    /* Test 4 — Negative stock correction                                 */
    /* ------------------------------------------------------------------ */

    public function test_negative_stock_correction(): void
    {
        $admin = $this->makeAdmin();
        $variant = $this->makeVariant(-3);

        $this->store($admin, [
            'variant_id' => $variant->id,
            'adjustment_type' => 'correction',
            'actual_stock' => 0,
            'reason' => 'Stock Correction',
        ])->assertRedirect();

        $this->assertSame(0, (int) $variant->fresh()->qty);

        $adjustment = ProductAdjustment::first();
        $this->assertSame(-3, $adjustment->stock_before);
        $this->assertSame(3, $adjustment->quantity);
        $this->assertSame(0, $adjustment->stock_after);
        $this->assertSame(0, $adjustment->actual_stock);
    }

    /* ------------------------------------------------------------------ */
    /* Test 5 — Positive stock correction                                 */
    /* ------------------------------------------------------------------ */

    public function test_positive_stock_correction(): void
    {
        $admin = $this->makeAdmin();
        $variant = $this->makeVariant(20);

        $this->store($admin, [
            'variant_id' => $variant->id,
            'adjustment_type' => 'correction',
            'actual_stock' => 12,
            'reason' => 'Stock Correction',
        ])->assertRedirect();

        $this->assertSame(12, (int) $variant->fresh()->qty);

        $adjustment = ProductAdjustment::first();
        $this->assertSame(20, $adjustment->stock_before);
        $this->assertSame(-8, $adjustment->quantity);
        $this->assertSame(12, $adjustment->stock_after);
        $this->assertSame(12, $adjustment->actual_stock);
    }

    /* ------------------------------------------------------------------ */
    /* Test 6 — Server-side protection                                    */
    /* ------------------------------------------------------------------ */

    public function test_server_ignores_fake_frontend_stock_values(): void
    {
        $admin = $this->makeAdmin();
        $variant = $this->makeVariant(20);

        $this->store($admin, [
            'variant_id' => $variant->id,
            'adjustment_type' => 'increase',
            'quantity' => 5,
            // Malicious / stale frontend values that must be ignored.
            'current_stock' => 9999,
            'stock_before' => 9999,
            'stock_after' => 10000,
            'adjustment' => 9999,
        ])->assertRedirect();

        // Stock derived from the database, not the request.
        $this->assertSame(25, (int) $variant->fresh()->qty);

        $adjustment = ProductAdjustment::first();
        $this->assertSame(20, $adjustment->stock_before);
        $this->assertSame(25, $adjustment->stock_after);
        $this->assertSame(5, $adjustment->quantity);
    }

    public function test_correction_uses_database_stock_not_submitted_values(): void
    {
        $admin = $this->makeAdmin();
        $variant = $this->makeVariant(7);

        $this->store($admin, [
            'variant_id' => $variant->id,
            'adjustment_type' => 'correction',
            'actual_stock' => 10,
            'stock_before' => 9999,
            'quantity' => 9999,
        ])->assertRedirect();

        $adjustment = ProductAdjustment::first();
        $this->assertSame(7, $adjustment->stock_before);
        $this->assertSame(3, $adjustment->quantity);
        $this->assertSame(10, $adjustment->stock_after);
    }

    /* ------------------------------------------------------------------ */
    /* Validation                                                         */
    /* ------------------------------------------------------------------ */

    public function test_increase_requires_positive_quantity(): void
    {
        $admin = $this->makeAdmin();
        $variant = $this->makeVariant(20);

        $this->store($admin, [
            'variant_id' => $variant->id,
            'adjustment_type' => 'increase',
            'quantity' => 0,
        ])->assertSessionHasErrors('quantity');

        $this->assertSame(20, (int) $variant->fresh()->qty);
        $this->assertSame(0, ProductAdjustment::count());
    }

    public function test_correction_rejects_negative_actual_stock(): void
    {
        $admin = $this->makeAdmin();
        $variant = $this->makeVariant(20);

        $this->store($admin, [
            'variant_id' => $variant->id,
            'adjustment_type' => 'correction',
            'actual_stock' => -1,
        ])->assertSessionHasErrors('actual_stock');

        $this->assertSame(20, (int) $variant->fresh()->qty);
        $this->assertSame(0, ProductAdjustment::count());
    }

    /* ------------------------------------------------------------------ */
    /* UI                                                                 */
    /* ------------------------------------------------------------------ */

    public function test_create_page_renders(): void
    {
        $admin = $this->makeAdmin();
        $this->makeVariant(20);

        $this->actingAs($admin, 'admin')
            ->get(route('admin.adjustments.create'))
            ->assertOk()
            ->assertSee('Product Adjustment')
            ->assertSee('Actual Physical Stock');
    }

    public function test_index_and_show_pages_render(): void
    {
        $admin = $this->makeAdmin();
        $variant = $this->makeVariant(20);

        $this->store($admin, [
            'variant_id' => $variant->id,
            'adjustment_type' => 'increase',
            'quantity' => 5,
        ]);

        $adjustment = ProductAdjustment::first();

        $this->actingAs($admin, 'admin')
            ->get(route('admin.adjustments.index'))
            ->assertOk()
            ->assertSee($adjustment->adjustment_no);

        $this->actingAs($admin, 'admin')
            ->get(route('admin.adjustments.show', $adjustment->id))
            ->assertOk()
            ->assertSee($adjustment->adjustment_no)
            ->assertSee('Stock Movement');
    }

    /* ------------------------------------------------------------------ */
    /* Authorization                                                      */
    /* ------------------------------------------------------------------ */

    public function test_guest_cannot_access_adjustments(): void
    {
        $this->get(route('admin.adjustments.create'))->assertRedirect(route('admin.login'));
        $this->get(route('admin.adjustments.index'))->assertRedirect(route('admin.login'));
    }

    public function test_customer_cannot_access_adjustments(): void
    {
        $customer = $this->makeCustomer();

        $this->actingAs($customer)
            ->get(route('admin.adjustments.create'))
            ->assertRedirect(route('admin.login'));
    }
}
