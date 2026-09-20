<?php

namespace Tests\Feature;

use App\Models\CourierSetting;
use App\Models\CourierShipment;
use App\Models\NewProduct;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Models\User;
use App\Services\Courier\CourierManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CourierTest extends TestCase
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

    protected function makeOrder(User $customer, array $attrs = []): Order
    {
        $product = NewProduct::create([
            'name' => 'Courier Product ' . uniqid(),
            'slug' => 'courier-product-' . uniqid(),
            'sku' => 'CP-' . uniqid(),
            'status' => 1,
        ]);

        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'CPV-' . uniqid(),
            'is_variant' => 0,
            'qty' => 10,
            'selling_price' => 1250,
            'compare_price' => 1400,
            'purchase_price' => 800,
            'allow_pre_order' => false,
        ]);

        $order = new Order();
        $order->user_id = $customer->id;
        $order->subtotal = 1250;
        $order->shipping = 60;
        $order->discount = 50;
        $order->grand_total = 1260;
        $order->name = 'Recipient Name';
        $order->phone = '01711111111';
        $order->address = '123 Test Street, Dhaka';
        $order->notes = '';
        $order->status = 'confirm';
        $order->payment_status = 0;
        $order->stock_deducted = true;

        foreach ($attrs as $key => $value) {
            $order->{$key} = $value;
        }

        $order->save();
        $order->order_id = date('Y') . str_pad((string) $order->id, 4, '0', STR_PAD_LEFT);
        $order->save();

        $item = new OrderItem();
        $item->order_id = $order->id;
        $item->product_id = $variant->id;
        $item->name = $variant->sku;
        $item->qty = 2;
        $item->price = 625;
        $item->cost_price = 400;
        $item->discount = 0;
        $item->total = 1250;
        $item->free_delivery = false;
        $item->is_pre_order = false;
        $item->save();

        return $order;
    }

    protected function liveSetting(array $attrs = []): CourierSetting
    {
        return CourierSetting::create(array_merge([
            'provider' => 'steadfast',
            'mode' => 'live',
            'api_key' => 'live-api-key',
            'secret_key' => 'live-secret-key',
            'is_active' => true,
        ], $attrs));
    }

    /* ------------------------------------------------------------------ */
    /* Settings                                                           */
    /* ------------------------------------------------------------------ */

    public function test_admin_can_save_test_mode_settings_without_credentials(): void
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin, 'admin')->put(route('admin.courier.settings.update'), [
            'provider' => 'steadfast',
            'mode' => 'test',
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.courier.settings'));

        $setting = CourierSetting::current();
        $this->assertSame('test', $setting->mode);
        $this->assertFalse($setting->hasCredentials());
    }

    public function test_credentials_are_encrypted_at_rest(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin, 'admin')->put(route('admin.courier.settings.update'), [
            'provider' => 'steadfast',
            'mode' => 'test',
            'api_key' => 'plain-api-key',
            'secret_key' => 'plain-secret-key',
            'is_active' => '1',
        ])->assertRedirect(route('admin.courier.settings'));

        $rawApiKey = DB::table('courier_settings')->where('provider', 'steadfast')->value('api_key');
        $rawSecret = DB::table('courier_settings')->where('provider', 'steadfast')->value('secret_key');

        $this->assertNotSame('plain-api-key', $rawApiKey);
        $this->assertNotSame('plain-secret-key', $rawSecret);

        $setting = CourierSetting::current();
        $this->assertSame('plain-api-key', $setting->api_key);
        $this->assertSame('plain-secret-key', $setting->secret_key);
    }

    public function test_live_mode_requires_credentials(): void
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin, 'admin')
            ->from(route('admin.courier.settings'))
            ->put(route('admin.courier.settings.update'), [
                'provider' => 'steadfast',
                'mode' => 'live',
                'is_active' => '1',
            ]);

        $response->assertSessionHasErrors('mode');
        $this->assertSame(0, CourierSetting::where('mode', 'live')->count());
    }

    public function test_live_mode_can_be_saved_with_credentials(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin, 'admin')->put(route('admin.courier.settings.update'), [
            'provider' => 'steadfast',
            'mode' => 'live',
            'api_key' => 'k',
            'secret_key' => 's',
            'is_active' => '1',
        ])->assertRedirect(route('admin.courier.settings'));

        $setting = CourierSetting::current();
        $this->assertTrue($setting->isLive());
        $this->assertTrue($setting->hasCredentials());
    }

    public function test_blank_credentials_do_not_overwrite_existing(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin, 'admin')->put(route('admin.courier.settings.update'), [
            'provider' => 'steadfast',
            'mode' => 'test',
            'api_key' => 'keep-me',
            'secret_key' => 'keep-me-too',
            'is_active' => '1',
        ]);

        $this->actingAs($admin, 'admin')->put(route('admin.courier.settings.update'), [
            'provider' => 'steadfast',
            'mode' => 'test',
            'is_active' => '1',
        ]);

        $setting = CourierSetting::current();
        $this->assertSame('keep-me', $setting->api_key);
        $this->assertSame('keep-me-too', $setting->secret_key);
    }

    public function test_admin_can_disable_courier_integration(): void
    {
        $admin = $this->makeAdmin();
        $order = $this->makeOrder($this->makeCustomer());

        $this->actingAs($admin, 'admin')->put(route('admin.courier.settings.update'), [
            'provider' => 'steadfast',
            'mode' => 'test',
            'is_active' => '0',
        ])->assertRedirect(route('admin.courier.settings'));

        $this->assertFalse(CourierSetting::current()->is_active);

        // Disabled integration must block new shipments.
        $this->actingAs($admin, 'admin')
            ->postJson(route('admin.orders.courier.create', $order->id))
            ->assertOk()
            ->assertJson(['success' => false]);

        $this->assertSame(0, CourierShipment::where('order_id', $order->id)->count());
    }

    public function test_settings_page_never_exposes_full_credentials(): void
    {
        $admin = $this->makeAdmin();
        $this->liveSetting(['api_key' => 'supersecretvalue', 'secret_key' => 'anothersecretvalue']);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.courier.settings'));

        $response->assertOk();
        $response->assertDontSee('supersecretvalue');
        $response->assertDontSee('anothersecretvalue');
        $response->assertSee('••••');
    }

    /* ------------------------------------------------------------------ */
    /* Test / Mock mode end-to-end                                        */
    /* ------------------------------------------------------------------ */

    public function test_send_order_to_courier_creates_shipment_and_history(): void
    {
        $admin = $this->makeAdmin();
        $order = $this->makeOrder($this->makeCustomer());

        $response = $this->actingAs($admin, 'admin')
            ->postJson(route('admin.orders.courier.create', $order->id));

        $response->assertOk()->assertJson(['success' => true]);

        $shipment = CourierShipment::where('order_id', $order->id)->first();
        $this->assertNotNull($shipment);
        $this->assertStringStartsWith('TEST-', $shipment->consignment_id);
        $this->assertStringStartsWith('TEST-TRK-', $shipment->tracking_code);
        $this->assertSame('in_review', $shipment->status);
        $this->assertSame(1, (int) $shipment->active);
        $this->assertSame(1, $shipment->statusHistories()->count());
    }

    public function test_cod_amount_is_calculated_from_the_database(): void
    {
        $admin = $this->makeAdmin();

        $unpaid = $this->makeOrder($this->makeCustomer(), ['payment_status' => 0, 'grand_total' => 1250]);
        $paid = $this->makeOrder($this->makeCustomer(), ['payment_status' => 1, 'grand_total' => 1250]);

        $this->actingAs($admin, 'admin')->postJson(route('admin.orders.courier.create', $unpaid->id))->assertJson(['success' => true]);
        $this->actingAs($admin, 'admin')->postJson(route('admin.orders.courier.create', $paid->id))->assertJson(['success' => true]);

        $this->assertSame(1250.0, (float) CourierShipment::where('order_id', $unpaid->id)->value('cod_amount'));
        $this->assertSame(0.0, (float) CourierShipment::where('order_id', $paid->id)->value('cod_amount'));
    }

    public function test_duplicate_shipment_is_blocked(): void
    {
        $admin = $this->makeAdmin();
        $order = $this->makeOrder($this->makeCustomer());

        $this->actingAs($admin, 'admin')->postJson(route('admin.orders.courier.create', $order->id))->assertJson(['success' => true]);

        $second = $this->actingAs($admin, 'admin')
            ->postJson(route('admin.orders.courier.create', $order->id));

        $second->assertOk()->assertJson(['success' => false]);
        $this->assertStringContainsString('already', $second->json('message'));

        $this->assertSame(1, CourierShipment::where('order_id', $order->id)->where('active', 1)->count());
    }

    public function test_invalid_order_cannot_be_sent(): void
    {
        $admin = $this->makeAdmin();
        $order = $this->makeOrder($this->makeCustomer(), ['phone' => '']);

        $response = $this->actingAs($admin, 'admin')
            ->postJson(route('admin.orders.courier.create', $order->id));

        $response->assertOk()->assertJson(['success' => false]);
        $this->assertSame(0, CourierShipment::where('order_id', $order->id)->count());
    }

    /* ------------------------------------------------------------------ */
    /* Only Confirmed orders may be sent to the courier                   */
    /* ------------------------------------------------------------------ */

    public function test_send_button_disabled_for_unconfirmed_orders(): void
    {
        $admin = $this->makeAdmin();

        foreach (['pending', 'cancell', 'shipped'] as $status) {
            $order = $this->makeOrder($this->makeCustomer(), ['status' => $status]);

            $response = $this->actingAs($admin, 'admin')->get(route('orders.details', $order->id));

            $response->assertOk();
            $this->assertMatchesRegularExpression(
                '/<button[^>]*id="courierSendBtn"[^>]*\bdisabled\b/s',
                $response->getContent(),
                "Send button must be disabled for status [{$status}]"
            );
            $response->assertSee('Only confirmed orders can be sent to the courier', false);
        }
    }

    public function test_send_button_enabled_for_confirmed_order(): void
    {
        $admin = $this->makeAdmin();
        $order = $this->makeOrder($this->makeCustomer(), ['status' => 'confirm']);

        $response = $this->actingAs($admin, 'admin')->get(route('orders.details', $order->id));

        $response->assertOk();
        $this->assertDoesNotMatchRegularExpression(
            '/<button[^>]*id="courierSendBtn"[^>]*\bdisabled\b/s',
            $response->getContent(),
            'Send button must be enabled for a confirmed order'
        );
    }

    public function test_backend_blocks_courier_for_unconfirmed_order(): void
    {
        $admin = $this->makeAdmin();

        foreach (['pending', 'cancell', 'shipped'] as $status) {
            $order = $this->makeOrder($this->makeCustomer(), ['status' => $status]);

            Http::fake();

            $response = $this->actingAs($admin, 'admin')
                ->postJson(route('admin.orders.courier.create', $order->id));

            $response->assertOk()->assertJson(['success' => false]);
            $this->assertStringContainsString('confirmed', strtolower((string) $response->json('message')));

            // No shipment created and no courier API call made.
            $this->assertSame(0, CourierShipment::where('order_id', $order->id)->count());
            Http::assertNothingSent();
        }
    }

    public function test_refresh_status_returns_current_status(): void
    {
        $admin = $this->makeAdmin();
        $order = $this->makeOrder($this->makeCustomer());

        $this->actingAs($admin, 'admin')->postJson(route('admin.orders.courier.create', $order->id));

        $response = $this->actingAs($admin, 'admin')
            ->postJson(route('admin.orders.courier.status', $order->id));

        $response->assertOk()->assertJson(['success' => true]);
    }

    public function test_simulate_status_updates_shipment_and_history(): void
    {
        $admin = $this->makeAdmin();
        $order = $this->makeOrder($this->makeCustomer());

        $this->actingAs($admin, 'admin')->postJson(route('admin.orders.courier.create', $order->id));

        $this->actingAs($admin, 'admin')
            ->postJson(route('admin.orders.courier.simulate', $order->id), ['status' => 'delivered'])
            ->assertOk()
            ->assertJson(['success' => true]);

        $shipment = CourierShipment::where('order_id', $order->id)->first();
        $this->assertSame('delivered', $shipment->status);
        $this->assertSame(2, $shipment->statusHistories()->count());
    }

    public function test_cancel_shipment_releases_slot_and_allows_retry(): void
    {
        $admin = $this->makeAdmin();
        $order = $this->makeOrder($this->makeCustomer());

        $this->actingAs($admin, 'admin')->postJson(route('admin.orders.courier.create', $order->id));

        $this->actingAs($admin, 'admin')
            ->postJson(route('admin.orders.courier.cancel', $order->id))
            ->assertOk()
            ->assertJson(['success' => true]);

        $shipment = CourierShipment::where('order_id', $order->id)->first();
        $this->assertSame('cancelled', $shipment->status);
        $this->assertNull($shipment->active);

        // A cancelled shipment frees the active slot, so a retry is allowed.
        $this->actingAs($admin, 'admin')
            ->postJson(route('admin.orders.courier.create', $order->id))
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertSame(2, CourierShipment::where('order_id', $order->id)->count());
        $this->assertSame(1, CourierShipment::where('order_id', $order->id)->where('active', 1)->count());
    }

    public function test_release_shipment_locally_allows_resend(): void
    {
        $admin = $this->makeAdmin();
        $order = $this->makeOrder($this->makeCustomer());

        $this->actingAs($admin, 'admin')->postJson(route('admin.orders.courier.create', $order->id))->assertJson(['success' => true]);

        // Release locally (no provider call).
        $this->actingAs($admin, 'admin')
            ->postJson(route('admin.orders.courier.release', $order->id))
            ->assertOk()
            ->assertJson(['success' => true]);

        $shipment = CourierShipment::where('order_id', $order->id)->first();
        $this->assertSame('cancelled', $shipment->status);
        $this->assertNull($shipment->active);

        // The order can be sent again.
        $this->actingAs($admin, 'admin')
            ->postJson(route('admin.orders.courier.create', $order->id))
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertSame(1, CourierShipment::where('order_id', $order->id)->where('active', 1)->count());
    }

    public function test_order_details_shows_send_button_after_release(): void
    {
        $admin = $this->makeAdmin();
        $order = $this->makeOrder($this->makeCustomer());

        $this->actingAs($admin, 'admin')->postJson(route('admin.orders.courier.create', $order->id));
        $this->actingAs($admin, 'admin')->postJson(route('admin.orders.courier.release', $order->id));

        $response = $this->actingAs($admin, 'admin')->get(route('orders.details', $order->id));

        $response->assertOk()->assertSee('Send to Courier');
    }

    public function test_order_details_hides_send_button_when_active(): void
    {
        $admin = $this->makeAdmin();
        $order = $this->makeOrder($this->makeCustomer());

        $this->actingAs($admin, 'admin')->postJson(route('admin.orders.courier.create', $order->id));

        $this->actingAs($admin, 'admin')
            ->get(route('orders.details', $order->id))
            ->assertOk()
            ->assertDontSee('Send to Courier');
    }

    public function test_test_connection_in_test_mode_succeeds(): void
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin, 'admin')
            ->postJson(route('admin.courier.settings.test'), ['mode' => 'test']);

        $response->assertOk()->assertJson(['success' => true]);
        $this->assertStringContainsString('Mock', $response->json('message'));
    }

    public function test_test_connection_in_live_mode_requires_credentials(): void
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin, 'admin')
            ->postJson(route('admin.courier.settings.test'), ['mode' => 'live']);

        $response->assertOk()->assertJson(['success' => false]);
        $this->assertStringContainsString('credentials', $response->json('message'));
    }

    /* ------------------------------------------------------------------ */
    /* UI rendering                                                       */
    /* ------------------------------------------------------------------ */

    public function test_courier_settings_page_renders(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin, 'admin')
            ->get(route('admin.courier.settings'))
            ->assertOk()
            ->assertSee('Courier Settings')
            ->assertSee('TEST MODE');
    }

    public function test_order_details_page_renders_courier_panel(): void
    {
        $admin = $this->makeAdmin();
        $order = $this->makeOrder($this->makeCustomer());

        $this->actingAs($admin, 'admin')
            ->get(route('orders.details', $order->id))
            ->assertOk()
            ->assertSee('Send to Courier')
            ->assertSee('Not Submitted');
    }

    public function test_order_details_shows_shipment_after_send(): void
    {
        $admin = $this->makeAdmin();
        $order = $this->makeOrder($this->makeCustomer());

        $this->actingAs($admin, 'admin')->postJson(route('admin.orders.courier.create', $order->id));

        $response = $this->actingAs($admin, 'admin')->get(route('orders.details', $order->id));

        $response->assertOk()
            ->assertSee('Status History')
            ->assertSee('TEST-TRK-')
            ->assertSee('In Review');
    }

    public function test_courier_section_is_hidden_when_integration_is_disabled(): void
    {
        $admin = $this->makeAdmin();
        $order = $this->makeOrder($this->makeCustomer());

        CourierSetting::create([
            'provider' => 'steadfast',
            'mode' => 'test',
            'is_active' => false,
        ]);

        $response = $this->actingAs($admin, 'admin')->get(route('orders.details', $order->id));

        $response->assertOk()
            ->assertDontSee('Send to Courier')
            ->assertDontSee('courierCard');
    }

    /* ------------------------------------------------------------------ */
    /* Authorization                                                      */
    /* ------------------------------------------------------------------ */

    public function test_guest_cannot_access_courier_admin(): void
    {
        $order = $this->makeOrder($this->makeCustomer());

        $this->get(route('admin.courier.settings'))->assertRedirect(route('admin.login'));
        $this->postJson(route('admin.orders.courier.create', $order->id))->assertUnauthorized();
    }

    public function test_customer_cannot_manage_courier(): void
    {
        $customer = $this->makeCustomer();
        $order = $this->makeOrder($customer);

        // A logged-in storefront customer is not an admin.
        $this->actingAs($customer)
            ->postJson(route('admin.orders.courier.create', $order->id))
            ->assertUnauthorized();

        $this->actingAs($customer)
            ->get(route('admin.courier.settings'))
            ->assertRedirect(route('admin.login'));
    }

    /* ------------------------------------------------------------------ */
    /* Live adapter (Http::fake — no real API calls)                      */
    /* ------------------------------------------------------------------ */

    public function test_live_create_success(): void
    {
        $this->liveSetting();
        $order = $this->makeOrder($this->makeCustomer());

        Http::fake([
            'portal.steadfast.com.bd/*' => Http::response([
                'status' => 200,
                'message' => 'Consignment has been created successfully.',
                'consignment' => [
                    'consignment_id' => 987654,
                    'invoice' => (string) $order->order_id,
                    'tracking_code' => 'TRK987654',
                    'status' => 'in_review',
                ],
            ], 200),
        ]);

        $response = CourierManager::make()->createShipment($order);

        $this->assertTrue($response->success);
        $this->assertSame('987654', $response->consignmentId());

        $shipment = CourierShipment::where('order_id', $order->id)->first();
        $this->assertSame('987654', $shipment->consignment_id);
        $this->assertSame('TRK987654', $shipment->tracking_code);
        $this->assertSame('in_review', $shipment->status);
    }

    public function test_live_payload_uses_integer_delivery_type_and_numeric_cod(): void
    {
        $this->liveSetting();
        $order = $this->makeOrder($this->makeCustomer());

        Http::fake([
            'portal.steadfast.com.bd/*' => Http::response([
                'status' => 200,
                'message' => 'Consignment has been created successfully.',
                'consignment' => [
                    'consignment_id' => 111,
                    'invoice' => '1',
                    'tracking_code' => 'TRK',
                    'status' => 'in_review',
                ],
            ], 200),
        ]);

        CourierManager::make()->createShipment($order);

        Http::assertSent(function ($request) {
            $data = $request->data();

            return array_key_exists('delivery_type', $data)
                && is_int($data['delivery_type'])
                && $data['delivery_type'] === 0
                && is_numeric($data['cod_amount']);
        });
    }

    public function test_live_create_401_returns_credential_error(): void
    {
        $this->assertLiveFailure(
            Http::response(['status' => 401, 'message' => 'Unauthenticated.'], 401),
            'credentials'
        );
    }

    public function test_live_create_422_returns_validation_error(): void
    {
        $this->assertLiveFailure(
            Http::response(['status' => 422, 'message' => 'The given data was invalid.', 'errors' => ['recipient_phone' => ['Invalid phone.']]], 422),
            'invalid'
        );
    }

    public function test_live_create_500_returns_server_error(): void
    {
        $this->assertLiveFailure(
            Http::response(['status' => 500, 'message' => 'Server error'], 500),
            'unavailable'
        );
    }

    public function test_live_create_timeout_is_handled(): void
    {
        $this->liveSetting();
        $order = $this->makeOrder($this->makeCustomer());

        Http::fake(function () {
            throw new ConnectionException('cURL error 28: Operation timed out');
        });

        $response = CourierManager::make()->createShipment($order);

        $this->assertFalse($response->success);
        $this->assertStringContainsString('Unable to connect', $response->message);
        // The reservation is released so the order can be retried.
        $this->assertSame(0, CourierShipment::where('order_id', $order->id)->count());
    }

    public function test_live_create_malformed_response_is_handled(): void
    {
        $this->assertLiveFailure(
            Http::response('<html>gateway error</html>', 200, ['Content-Type' => 'text/html']),
            'invalid response'
        );
    }

    public function test_live_mode_without_credentials_makes_no_request(): void
    {
        // A live row with no credentials (bypassing the settings form guard).
        CourierSetting::create([
            'provider' => 'steadfast',
            'mode' => 'live',
            'is_active' => true,
        ]);

        $order = $this->makeOrder($this->makeCustomer());

        Http::fake();

        $response = CourierManager::make()->createShipment($order);

        $this->assertFalse($response->success);
        $this->assertStringContainsString('credentials', $response->message);
        Http::assertNothingSent();
        $this->assertSame(0, CourierShipment::where('order_id', $order->id)->count());
    }

    public function test_admin_can_save_custom_base_url(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin, 'admin')->put(route('admin.courier.settings.update'), [
            'provider' => 'steadfast',
            'mode' => 'test',
            'base_url' => 'https://custom-steadfast.test/api/v1',
            'is_active' => '1',
        ])->assertRedirect(route('admin.courier.settings'));

        $this->assertSame('https://custom-steadfast.test/api/v1', CourierSetting::current()->base_url);
    }

    public function test_blank_base_url_clears_the_override(): void
    {
        $admin = $this->makeAdmin();
        $this->liveSetting(['base_url' => 'https://custom-steadfast.test/api/v1']);

        $this->actingAs($admin, 'admin')->put(route('admin.courier.settings.update'), [
            'provider' => 'steadfast',
            'mode' => 'test',
            'base_url' => '',
            'is_active' => '1',
        ]);

        $this->assertNull(CourierSetting::current()->base_url);
    }

    public function test_custom_base_url_is_used_for_live_requests(): void
    {
        $this->liveSetting(['base_url' => 'https://custom-steadfast.test/api/v1']);
        $order = $this->makeOrder($this->makeCustomer());

        Http::fake([
            '*' => Http::response([
                'status' => 200,
                'message' => 'Consignment has been created successfully.',
                'consignment' => [
                    'consignment_id' => 555,
                    'invoice' => '1',
                    'tracking_code' => 'CUSTOMTRK',
                    'status' => 'in_review',
                ],
            ], 200),
        ]);

        $response = CourierManager::make()->createShipment($order);

        $this->assertTrue($response->success);
        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'custom-steadfast.test/api/v1/create_order');
        });
    }

    public function test_live_test_connection_success(): void
    {
        $this->liveSetting();

        Http::fake([
            'portal.steadfast.com.bd/*' => Http::response(['status' => 200, 'current_balance' => 1000], 200),
        ]);

        $response = CourierManager::make()->testConnection();

        $this->assertTrue($response->success);
        $this->assertStringContainsString('Live', $response->message);
    }

    protected function assertLiveFailure($fakeResponse, string $expectedMessageFragment): void
    {
        $this->liveSetting();
        $order = $this->makeOrder($this->makeCustomer());

        Http::fake(['portal.steadfast.com.bd/*' => $fakeResponse]);

        $response = CourierManager::make()->createShipment($order);

        $this->assertFalse($response->success);
        $this->assertStringContainsString($expectedMessageFragment, $response->message);
        // Failed creations must not leave an active shipment behind.
        $this->assertSame(0, CourierShipment::where('order_id', $order->id)->where('active', 1)->count());
    }
}
