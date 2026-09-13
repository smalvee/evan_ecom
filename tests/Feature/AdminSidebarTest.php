<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminSidebarTest extends TestCase
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

    protected function sidebarHtml(User $admin, string $route): string
    {
        return $this->actingAs($admin, 'admin')->get(route($route))->assertOk()->getContent();
    }

    /** Assert an <a> with the given href carries the `active` class. */
    protected function assertLinkActive(string $html, string $url, string $message = ''): void
    {
        $pattern = '/<a\b[^>]*class="[^"]*\bactive\b[^"]*"[^>]*href="' . preg_quote($url, '/') . '"/s';
        $this->assertMatchesRegularExpression($pattern, $html, $message ?: ('Active link expected: ' . $url));
    }

    public function test_products_parent_is_open_and_correct_child_active(): void
    {
        $admin = $this->makeAdmin();

        $html = $this->sidebarHtml($admin, 'products.create');
        $this->assertStringContainsString('sidebar-list open active', $html);
        $this->assertLinkActive($html, route('products.create'), 'Add Product should be active');

        $html = $this->sidebarHtml($admin, 'products.index');
        $this->assertStringContainsString('sidebar-list open active', $html);
        $this->assertLinkActive($html, route('products.index'), 'List Products should be active');
    }

    public function test_orders_parent_is_open_and_correct_child_active(): void
    {
        $admin = $this->makeAdmin();

        $html = $this->sidebarHtml($admin, 'orders.create');
        $this->assertStringContainsString('sidebar-list open active', $html);
        $this->assertLinkActive($html, route('orders.create'), 'Create Order should be active');

        $html = $this->sidebarHtml($admin, 'orders.index');
        $this->assertStringContainsString('sidebar-list open active', $html);
        $this->assertLinkActive($html, route('orders.index'), 'Order List should be active');
    }

    public function test_purchase_parent_is_open_and_correct_child_active(): void
    {
        $admin = $this->makeAdmin();

        $html = $this->sidebarHtml($admin, 'purchase.create');
        $this->assertStringContainsString('sidebar-list open active', $html);
        $this->assertLinkActive($html, route('purchase.create'), 'Add Purchase should be active');
    }

    public function test_top_level_links_are_active(): void
    {
        $admin = $this->makeAdmin();

        $this->assertLinkActive($this->sidebarHtml($admin, 'categories.index'), route('categories.index'), 'Categories');
        $this->assertLinkActive($this->sidebarHtml($admin, 'brands.index'), route('brands.index'), 'Brands');
        $this->assertLinkActive($this->sidebarHtml($admin, 'advertise.index'), route('advertise.index'), 'Advertisement');
        $this->assertLinkActive($this->sidebarHtml($admin, 'admin.pre_orders.index'), route('admin.pre_orders.index'), 'Pre Orders');
    }

    public function test_only_the_current_parent_is_open(): void
    {
        $admin = $this->makeAdmin();

        $html = $this->sidebarHtml($admin, 'products.create');

        // Exactly one parent list should be open.
        $this->assertSame(1, substr_count($html, 'sidebar-list open active'), 'Only one parent should be open');
    }
}
