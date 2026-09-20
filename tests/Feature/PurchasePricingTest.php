<?php

namespace Tests\Feature;

use App\Models\NewProduct;
use App\Models\ProductVariant;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Verifies the separation between purchase cost and customer pricing:
 * creating a purchase must never change selling_price / compare_price.
 */
class PurchasePricingTest extends TestCase
{
    use RefreshDatabase;

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

    protected function makeSupplier(): Supplier
    {
        $supplier = new Supplier();
        $supplier->name = 'Supplier ' . uniqid();
        $supplier->email = 'supplier_' . uniqid() . '@example.com';
        $supplier->phone = '019' . random_int(10000000, 99999999);
        $supplier->address = 'Test Address';
        $supplier->save();

        return $supplier;
    }

    protected function makeVariant(array $attrs = []): ProductVariant
    {
        $product = NewProduct::create([
            'name' => 'Purchase Product ' . uniqid(),
            'slug' => 'purchase-product-' . uniqid(),
            'sku' => 'PP-' . uniqid(),
            'status' => 1,
        ]);

        return ProductVariant::create(array_merge([
            'product_id' => $product->id,
            'sku' => 'PPV-' . uniqid(),
            'is_variant' => 0,
            'qty' => 0,
            'selling_price' => null,
            'compare_price' => null,
            'purchase_price' => null,
            'average_cost' => null,
            'price_manually_managed' => false,
            'allow_pre_order' => false,
        ], $attrs));
    }

    protected function purchasePayload(Supplier $supplier, ProductVariant $variant, float $unitCost, int $qty, float $profit = 0, float $discount = 0): array
    {
        return [
            'supplier_id' => $supplier->id,
            'date' => now()->toDateString(),
            'p_name' => ['Product'],
            'variant_id' => [$variant->id],
            'qty' => [$qty],
            'unit_cost' => [$unitCost],
            'profit_amount' => [$profit],
            'discount' => [$discount],
        ];
    }

    protected function createPurchase(User $admin, Supplier $supplier, ProductVariant $variant, float $unitCost, int $qty, float $profit = 0, float $discount = 0)
    {
        return $this->actingAs($admin, 'admin')->postJson(
            route('purchase.store'),
            $this->purchasePayload($supplier, $variant, $unitCost, $qty, $profit, $discount)
        );
    }

    /* ------------------------------------------------------------------ */
    /* Test 1 — existing selling price / MRP remain unchanged             */
    /* ------------------------------------------------------------------ */

    public function test_purchase_creation_does_not_change_existing_pricing(): void
    {
        $admin = $this->makeAdmin();
        $supplier = $this->makeSupplier();
        $variant = $this->makeVariant([
            'qty' => 10,
            'purchase_price' => 100,
            'average_cost' => 100,
            'selling_price' => 150,
            'compare_price' => 180,
            'price_manually_managed' => false,
        ]);

        $this->createPurchase($admin, $supplier, $variant, 120, 10)->assertJson(['status' => true]);

        $v = $variant->fresh();
        $this->assertSame(150.0, (float) $v->selling_price, 'selling price must not change');
        $this->assertSame(180.0, (float) $v->compare_price, 'MRP must not change');
        $this->assertSame(120.0, (float) $v->purchase_price, 'latest cost must update');
        $this->assertSame(20, (int) $v->qty, 'stock must increase');
        $this->assertSame(110.0, (float) $v->average_cost, 'weighted average must be recalculated');
        $this->assertFalse((bool) $v->price_manually_managed, 'manual flag must not be set');
    }

    /* ------------------------------------------------------------------ */
    /* Test 2 — zero profit must not set selling price to cost            */
    /* ------------------------------------------------------------------ */

    public function test_purchase_creation_does_not_establish_price_from_cost(): void
    {
        $admin = $this->makeAdmin();
        $supplier = $this->makeSupplier();
        $variant = $this->makeVariant([
            'qty' => 0,
            'selling_price' => null,
            'compare_price' => null,
            'purchase_price' => null,
            'average_cost' => null,
        ]);

        $this->createPurchase($admin, $supplier, $variant, 100, 5)->assertJson(['status' => true]);

        $v = $variant->fresh();
        $this->assertNull($v->selling_price, 'selling price must stay NULL (not become the cost)');
        $this->assertNull($v->compare_price, 'MRP must stay NULL');
        $this->assertSame(100.0, (float) $v->purchase_price);
        $this->assertSame(5, (int) $v->qty);
    }

    /* ------------------------------------------------------------------ */
    /* Test 3 — manually managed price stays protected                    */
    /* ------------------------------------------------------------------ */

    public function test_manual_price_is_preserved_on_purchase_creation(): void
    {
        $admin = $this->makeAdmin();
        $supplier = $this->makeSupplier();
        $variant = $this->makeVariant([
            'qty' => 0,
            'purchase_price' => 50,
            'average_cost' => 50,
            'selling_price' => 240,
            'compare_price' => 250,
            'price_manually_managed' => true,
        ]);

        $this->createPurchase($admin, $supplier, $variant, 80, 3)->assertJson(['status' => true]);

        $v = $variant->fresh();
        $this->assertSame(240.0, (float) $v->selling_price);
        $this->assertSame(250.0, (float) $v->compare_price);
        $this->assertTrue((bool) $v->price_manually_managed);
        $this->assertSame(80.0, (float) $v->purchase_price);
    }

    /* ------------------------------------------------------------------ */
    /* Test 4 — stock, average cost and historical cost still work        */
    /* ------------------------------------------------------------------ */

    public function test_stock_average_and_historical_cost_are_preserved(): void
    {
        $admin = $this->makeAdmin();
        $supplier = $this->makeSupplier();
        $variant = $this->makeVariant(['qty' => 0]);

        $this->createPurchase($admin, $supplier, $variant, 100, 10)->assertJson(['status' => true]);
        $this->createPurchase($admin, $supplier, $variant, 120, 10)->assertJson(['status' => true]);

        $v = $variant->fresh();
        $this->assertSame(20, (int) $v->qty);
        $this->assertSame(120.0, (float) $v->purchase_price, 'latest cost wins');
        $this->assertSame(110.0, (float) $v->average_cost);

        // purchase_items.unit_cost is historical and must never be overwritten.
        $items = PurchaseItem::where('variant_id', $variant->id)->orderBy('id')->get();
        $this->assertCount(2, $items);
        $this->assertSame(100.0, (float) $items[0]->unit_cost);
        $this->assertSame(120.0, (float) $items[1]->unit_cost);

        // The purchase record still stores its own reference selling price.
        $this->assertNotNull($items[0]->selling_price);
    }

    /* ------------------------------------------------------------------ */
    /* Test 5 — purchase EDIT behaviour is unchanged                      */
    /* ------------------------------------------------------------------ */

    public function test_purchase_edit_only_changes_price_when_opted_in(): void
    {
        $admin = $this->makeAdmin();
        $supplier = $this->makeSupplier();
        $variant = $this->makeVariant([
            'qty' => 0,
            'selling_price' => 150,
            'compare_price' => 180,
            'price_manually_managed' => false,
        ]);

        $this->createPurchase($admin, $supplier, $variant, 100, 5)->assertJson(['status' => true]);

        $purchase = Purchase::latest('id')->first();
        $item = PurchaseItem::where('purchase_id', $purchase->id)->first();

        // Edit WITHOUT update_price -> selling price preserved, cost updated.
        $this->actingAs($admin, 'admin')->postJson(route('purchase.update', $purchase->id), [
            'supplier_id' => $supplier->id,
            'date' => now()->toDateString(),
            'item_id' => [$item->id],
            'variant_id' => [$variant->id],
            'unit_cost' => [130],
            'profit_amount' => [0],
            'discount' => [0],
        ])->assertJson(['status' => true]);

        $v = $variant->fresh();
        $this->assertSame(130.0, (float) $v->purchase_price);
        $this->assertSame(150.0, (float) $v->selling_price, 'edit without opt-in must not change price');
        $this->assertSame(180.0, (float) $v->compare_price);

        // Edit WITH update_price -> price recalculated from cost + profit - discount.
        $this->actingAs($admin, 'admin')->postJson(route('purchase.update', $purchase->id), [
            'supplier_id' => $supplier->id,
            'date' => now()->toDateString(),
            'item_id' => [$item->id],
            'variant_id' => [$variant->id],
            'unit_cost' => [130],
            'profit_amount' => [50],
            'discount' => [10],
            'update_price' => 1,
        ])->assertJson(['status' => true]);

        $v = $variant->fresh();
        $this->assertSame(170.0, (float) $v->selling_price, 'opt-in must recalculate selling price');
        $this->assertSame(180.0, (float) $v->compare_price);
    }

    /* ------------------------------------------------------------------ */
    /* Manual pricing feature still works (Catalog -> Pricing)            */
    /* ------------------------------------------------------------------ */

    public function test_manual_pricing_still_sets_the_manual_flag(): void
    {
        $admin = $this->makeAdmin();
        $variant = $this->makeVariant(['qty' => 5, 'selling_price' => 100, 'compare_price' => 120]);

        $this->actingAs($admin, 'admin')->postJson(route('admin.pricing.update', $variant->id), [
            'compare_price' => 200,
            'selling_price' => 175,
            'reason' => 'Manual price set',
        ])->assertJson(['status' => true]);

        $v = $variant->fresh();
        $this->assertSame(175.0, (float) $v->selling_price);
        $this->assertSame(200.0, (float) $v->compare_price);
        $this->assertTrue((bool) $v->price_manually_managed);
    }
}
