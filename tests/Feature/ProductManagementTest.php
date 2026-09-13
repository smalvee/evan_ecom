<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\NewProduct;
use App\Models\ProductVariant;
use App\Models\SubCategory;
use App\Models\Unit;
use App\Models\User;
use App\Models\Variation;
use App\Models\VariationValues;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProductManagementTest extends TestCase
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

    /**
     * @return array{0: Unit, 1: Brand, 2: Category, 3: SubCategory}
     */
    protected function makeSupport(): array
    {
        $unit = new Unit();
        $unit->name = 'Pieces';
        $unit->s_name = 'pc';
        $unit->save();

        $brand = new Brand();
        $brand->name = 'Acme';
        $brand->slug = 'acme-' . uniqid();
        $brand->status = 1;
        $brand->save();

        $category = new Category();
        $category->name = 'Apparel';
        $category->slug = 'apparel-' . uniqid();
        $category->image = '';
        $category->status = 1;
        $category->save();

        $sub = new SubCategory();
        $sub->name = 'T-Shirts';
        $sub->slug = 't-shirts-' . uniqid();
        $sub->status = 1;
        $sub->category_id = $category->id;
        $sub->save();

        return [$unit, $brand, $category, $sub];
    }

    protected function makeVariation(string $name, array $values): array
    {
        $variation = new Variation();
        $variation->variations = $name;
        $variation->values = '';
        $variation->save();

        $ids = [];

        foreach ($values as $value) {
            $row = new VariationValues();
            $row->variation_id = $variation->id;
            $row->value = $value;
            $row->save();
            $ids[] = $row->id;
        }

        return [$variation->id, $ids];
    }

    protected function encodeValues(array $pairs): string
    {
        return urlencode(json_encode($pairs));
    }

    protected function basePayload(array $support, array $overrides = []): array
    {
        [$unit, $brand, $category, $sub] = $support;

        return array_merge([
            'name' => 'Test Product',
            'slug' => 'test-' . uniqid(),
            'product_sku' => 'PSKU-' . uniqid(),
            'units_id' => $unit->id,
            'brand_id' => $brand->id,
            'category' => $category->id,
            'sub_category' => $sub->id,
            'product_type' => '0',
            'description' => '',
            'status' => '1',
            'hot_products' => '0',
        ], $overrides);
    }

    /* ------------------------------------------------------------------ */

    public function test_create_single_product_creates_default_variant(): void
    {
        $admin = $this->makeAdmin();
        $support = $this->makeSupport();
        $payload = $this->basePayload($support);

        $this->actingAs($admin, 'admin')
            ->post(route('new_product.store'), $payload)
            ->assertOk()
            ->assertJson(['status' => true]);

        $product = NewProduct::first();
        $this->assertNotNull($product);

        $variant = ProductVariant::where('product_id', $product->id)->first();
        $this->assertNotNull($variant);
        $this->assertSame($payload['product_sku'], $variant->sku);
        $this->assertSame(0, (int) $variant->is_variant);
    }

    public function test_create_variable_product_creates_variants(): void
    {
        $admin = $this->makeAdmin();
        $support = $this->makeSupport();
        [$variationId, $valueIds] = $this->makeVariation('Color', ['Red', 'Blue']);

        $payload = $this->basePayload($support, [
            'product_type' => '1',
            'variation_sku' => ['VAR-RED', 'VAR-BLUE'],
            'variation_values' => [
                $this->encodeValues([['variation_id' => $variationId, 'value_id' => $valueIds[0]]]),
                $this->encodeValues([['variation_id' => $variationId, 'value_id' => $valueIds[1]]]),
            ],
            'allow_pre_order' => ['1', '0'],
        ]);

        $this->actingAs($admin, 'admin')
            ->post(route('new_product.store'), $payload)
            ->assertOk()
            ->assertJson(['status' => true]);

        $product = NewProduct::first();
        $variants = ProductVariant::where('product_id', $product->id)->orderBy('id')->get();

        $this->assertCount(2, $variants);
        $this->assertSame('VAR-RED', $variants[0]->sku);
        $this->assertSame(1, (int) $variants[0]->is_variant);
        $this->assertTrue((bool) $variants[0]->allow_pre_order);
        $this->assertSame('VAR-BLUE', $variants[1]->sku);
        $this->assertFalse((bool) $variants[1]->allow_pre_order);
    }

    public function test_duplicate_variant_sku_in_submission_is_rejected(): void
    {
        $admin = $this->makeAdmin();
        $support = $this->makeSupport();
        [$variationId, $valueIds] = $this->makeVariation('Size', ['S', 'M']);

        $payload = $this->basePayload($support, [
            'product_type' => '1',
            'variation_sku' => ['DUP-SKU', 'DUP-SKU'],
            'variation_values' => [
                $this->encodeValues([['variation_id' => $variationId, 'value_id' => $valueIds[0]]]),
                $this->encodeValues([['variation_id' => $variationId, 'value_id' => $valueIds[1]]]),
            ],
        ]);

        $response = $this->actingAs($admin, 'admin')->post(route('new_product.store'), $payload);

        $response->assertOk()->assertJson(['status' => false]);
        $errors = $response->json('errors');
        $this->assertArrayHasKey('variation_sku.0', $errors);
        $this->assertSame('This SKU is already used by another variant.', $errors['variation_sku.0'][0]);

        $this->assertSame(0, NewProduct::count());
    }

    public function test_variant_sku_conflict_with_database_is_rejected(): void
    {
        $admin = $this->makeAdmin();
        $support = $this->makeSupport();
        [$variationId, $valueIds] = $this->makeVariation('Size', ['S']);

        // Existing variant with the same SKU on another product.
        $other = $this->basePayload($support, ['product_type' => '0', 'product_sku' => 'TAKEN-SKU']);
        $this->actingAs($admin, 'admin')->post(route('new_product.store'), $other)->assertJson(['status' => true]);

        $payload = $this->basePayload($support, [
            'product_type' => '1',
            'variation_sku' => ['TAKEN-SKU'],
            'variation_values' => [
                $this->encodeValues([['variation_id' => $variationId, 'value_id' => $valueIds[0]]]),
            ],
        ]);

        $response = $this->actingAs($admin, 'admin')->post(route('new_product.store'), $payload);

        $response->assertOk()->assertJson(['status' => false]);
        $errors = $response->json('errors');
        $this->assertArrayHasKey('variation_sku.0', $errors);

        $this->assertSame(1, NewProduct::count());
    }

    public function test_update_renames_variant_sku_using_variant_id(): void
    {
        $admin = $this->makeAdmin();
        $support = $this->makeSupport();
        [$variationId, $valueIds] = $this->makeVariation('Color', ['Red']);

        $create = $this->basePayload($support, [
            'product_type' => '1',
            'variation_sku' => ['OLD-SKU'],
            'variation_values' => [
                $this->encodeValues([['variation_id' => $variationId, 'value_id' => $valueIds[0]]]),
            ],
        ]);
        $this->actingAs($admin, 'admin')->post(route('new_product.store'), $create)->assertJson(['status' => true]);

        $product = NewProduct::first();
        $variant = ProductVariant::where('product_id', $product->id)->first();

        $update = $this->basePayload($support, [
            'product_type' => '1',
            'variation_sku' => ['NEW-SKU'],
            'variation_values' => [
                $this->encodeValues([['variation_id' => $variationId, 'value_id' => $valueIds[0]]]),
            ],
            'variant_id' => [$variant->id],
        ]);

        $this->actingAs($admin, 'admin')
            ->post(route('update_product.update', $product->id), $update)
            ->assertOk()
            ->assertJson(['status' => true]);

        $this->assertSame(1, ProductVariant::where('product_id', $product->id)->count());
        $this->assertSame('NEW-SKU', $variant->fresh()->sku);
    }

    public function test_update_deletes_safe_removed_variant(): void
    {
        $admin = $this->makeAdmin();
        $support = $this->makeSupport();
        [$variationId, $valueIds] = $this->makeVariation('Color', ['Red', 'Blue']);

        $create = $this->basePayload($support, [
            'product_type' => '1',
            'variation_sku' => ['KEEP-SKU', 'DROP-SKU'],
            'variation_values' => [
                $this->encodeValues([['variation_id' => $variationId, 'value_id' => $valueIds[0]]]),
                $this->encodeValues([['variation_id' => $variationId, 'value_id' => $valueIds[1]]]),
            ],
        ]);
        $this->actingAs($admin, 'admin')->post(route('new_product.store'), $create)->assertJson(['status' => true]);

        $product = NewProduct::first();
        $keep = ProductVariant::where('sku', 'KEEP-SKU')->first();

        $update = $this->basePayload($support, [
            'product_type' => '1',
            'variation_sku' => ['KEEP-SKU'],
            'variation_values' => [
                $this->encodeValues([['variation_id' => $variationId, 'value_id' => $valueIds[0]]]),
            ],
            'variant_id' => [$keep->id],
        ]);

        $this->actingAs($admin, 'admin')
            ->post(route('update_product.update', $product->id), $update)
            ->assertJson(['status' => true]);

        $this->assertSame(1, ProductVariant::where('product_id', $product->id)->count());
        $this->assertNull(ProductVariant::where('sku', 'DROP-SKU')->first());
    }

    public function test_update_blocks_removing_variant_with_stock(): void
    {
        $admin = $this->makeAdmin();
        $support = $this->makeSupport();
        [$variationId, $valueIds] = $this->makeVariation('Color', ['Red', 'Blue']);

        $create = $this->basePayload($support, [
            'product_type' => '1',
            'variation_sku' => ['KEEP-SKU', 'STOCKED-SKU'],
            'variation_values' => [
                $this->encodeValues([['variation_id' => $variationId, 'value_id' => $valueIds[0]]]),
                $this->encodeValues([['variation_id' => $variationId, 'value_id' => $valueIds[1]]]),
            ],
        ]);
        $this->actingAs($admin, 'admin')->post(route('new_product.store'), $create)->assertJson(['status' => true]);

        $product = NewProduct::first();
        $keep = ProductVariant::where('sku', 'KEEP-SKU')->first();
        $stocked = ProductVariant::where('sku', 'STOCKED-SKU')->first();
        $stocked->qty = 5;
        $stocked->save();

        $update = $this->basePayload($support, [
            'product_type' => '1',
            'variation_sku' => ['KEEP-SKU'],
            'variation_values' => [
                $this->encodeValues([['variation_id' => $variationId, 'value_id' => $valueIds[0]]]),
            ],
            'variant_id' => [$keep->id],
        ]);

        $this->actingAs($admin, 'admin')
            ->post(route('update_product.update', $product->id), $update)
            ->assertOk()
            ->assertJson(['status' => false]);

        // Stocked variant is preserved.
        $this->assertNotNull(ProductVariant::where('sku', 'STOCKED-SKU')->first());
    }

    public function test_variable_to_single_blocked_when_variant_has_stock(): void
    {
        $admin = $this->makeAdmin();
        $support = $this->makeSupport();
        [$variationId, $valueIds] = $this->makeVariation('Color', ['Red']);

        $create = $this->basePayload($support, [
            'product_type' => '1',
            'variation_sku' => ['RED-SKU'],
            'variation_values' => [
                $this->encodeValues([['variation_id' => $variationId, 'value_id' => $valueIds[0]]]),
            ],
        ]);
        $this->actingAs($admin, 'admin')->post(route('new_product.store'), $create)->assertJson(['status' => true]);

        $product = NewProduct::first();
        $variant = ProductVariant::where('product_id', $product->id)->first();
        $variant->qty = 3;
        $variant->save();

        $update = $this->basePayload($support, ['product_type' => '0']);

        $this->actingAs($admin, 'admin')
            ->post(route('update_product.update', $product->id), $update)
            ->assertOk()
            ->assertJson(['status' => false]);

        $this->assertSame(1, ProductVariant::where('product_id', $product->id)->count());
        $this->assertSame(3, (int) $variant->fresh()->qty);
    }

    public function test_variable_to_single_reconciles_when_no_stock(): void
    {
        $admin = $this->makeAdmin();
        $support = $this->makeSupport();
        [$variationId, $valueIds] = $this->makeVariation('Color', ['Red']);

        $create = $this->basePayload($support, [
            'product_type' => '1',
            'variation_sku' => ['RED-SKU'],
            'variation_values' => [
                $this->encodeValues([['variation_id' => $variationId, 'value_id' => $valueIds[0]]]),
            ],
        ]);
        $this->actingAs($admin, 'admin')->post(route('new_product.store'), $create)->assertJson(['status' => true]);

        $product = NewProduct::first();
        $update = $this->basePayload($support, [
            'product_type' => '0',
            'product_sku' => 'SINGLE-SKU',
        ]);

        $this->actingAs($admin, 'admin')
            ->post(route('update_product.update', $product->id), $update)
            ->assertJson(['status' => true]);

        $variants = ProductVariant::where('product_id', $product->id)->get();
        $this->assertCount(1, $variants);
        $this->assertSame('SINGLE-SKU', $variants->first()->sku);
        $this->assertSame(0, (int) $variants->first()->is_variant);
    }

    public function test_update_rejects_variant_from_another_product(): void
    {
        $admin = $this->makeAdmin();
        $support = $this->makeSupport();
        [$variationId, $valueIds] = $this->makeVariation('Color', ['Red']);

        // Product A (variable).
        $payloadA = $this->basePayload($support, [
            'product_type' => '1',
            'variation_sku' => ['A-SKU'],
            'variation_values' => [
                $this->encodeValues([['variation_id' => $variationId, 'value_id' => $valueIds[0]]]),
            ],
        ]);
        $this->actingAs($admin, 'admin')->post(route('new_product.store'), $payloadA)->assertJson(['status' => true]);
        $productA = NewProduct::first();
        $variantA = ProductVariant::where('product_id', $productA->id)->first();

        // Product B (variable).
        $payloadB = $this->basePayload($support, [
            'product_type' => '1',
            'variation_sku' => ['B-SKU'],
            'variation_values' => [
                $this->encodeValues([['variation_id' => $variationId, 'value_id' => $valueIds[0]]]),
            ],
        ]);
        $this->actingAs($admin, 'admin')->post(route('new_product.store'), $payloadB)->assertJson(['status' => true]);
        $productB = NewProduct::where('id', '!=', $productA->id)->first();

        // Try to submit Product A's variant while updating Product B.
        $update = $this->basePayload($support, [
            'product_type' => '1',
            'variation_sku' => ['B-SKU'],
            'variation_values' => [
                $this->encodeValues([['variation_id' => $variationId, 'value_id' => $valueIds[0]]]),
            ],
            'variant_id' => [$variantA->id],
        ]);

        $this->actingAs($admin, 'admin')
            ->post(route('update_product.update', $productB->id), $update)
            ->assertOk()
            ->assertJson(['status' => false]);

        $this->assertSame('B-SKU', ProductVariant::where('product_id', $productB->id)->first()->sku);
    }

    public function test_product_image_store_saves_without_image_driver(): void
    {
        $admin = $this->makeAdmin();
        $support = $this->makeSupport();

        $this->actingAs($admin, 'admin')
            ->post(route('new_product.store'), $this->basePayload($support))
            ->assertJson(['status' => true]);

        $variant = ProductVariant::first();

        $tempDir = public_path('temp');
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0777, true);
        }

        $name = 'test-' . uniqid() . '.png';
        // 1x1 transparent PNG.
        file_put_contents(
            $tempDir . '/' . $name,
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==')
        );

        $temp = new \App\Models\TempImage();
        $temp->name = $name;
        $temp->save();

        $created = [];

        try {
            $this->actingAs($admin, 'admin')
                ->post(route('image.store'), [
                    'variant_id' => $variant->id,
                    'gallery_image_ids' => [$temp->id],
                ])
                ->assertOk()
                ->assertJson(['status' => true]);

            $image = \App\Models\ProductImage::where('product_id', $variant->id)->where('is_thumb', 0)->first();
            $this->assertNotNull($image);

            $created[] = public_path('uploads/products/large/' . $image->image);
            $created[] = public_path('uploads/products/small/' . $image->image);

            $this->assertFileExists($created[0]);
            $this->assertFileExists($created[1]);
        } finally {
            foreach ($created as $file) {
                if (file_exists($file)) {
                    @unlink($file);
                }
            }
            @unlink($tempDir . '/' . $name);
        }
    }

    public function test_create_and_edit_pages_render(): void
    {
        $admin = $this->makeAdmin();
        $support = $this->makeSupport();

        $this->actingAs($admin, 'admin')
            ->get(route('products.create'))
            ->assertOk()
            ->assertSee('ProductForm', false)
            ->assertSee('name="product_type"', false);

        $this->actingAs($admin, 'admin')
            ->post(route('new_product.store'), $this->basePayload($support))
            ->assertJson(['status' => true]);

        $product = NewProduct::first();

        $this->actingAs($admin, 'admin')
            ->get(route('new_products.edit', $product->id))
            ->assertOk()
            ->assertSee('ProductForm', false)
            ->assertSee('Generated Variants');
    }
}
