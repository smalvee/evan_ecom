<?php

namespace Tests\Feature;

use App\Models\AboutUs;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RichTextEditorTest extends TestCase
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

    public function test_admin_editor_is_configured(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin, 'admin')
            ->get(route('products.create'))
            ->assertOk()
            ->assertSee('initRichEditors', false)
            ->assertSee('Kalpurush', false)
            ->assertSee('fontNamesIgnoreCheck', false)
            ->assertSee('summernote-lite', false)
            ->assertSee('class="summernote"', false);
    }

    public function test_about_us_content_saves_from_description_field(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin, 'admin')
            ->post(route('admin.store_who_we_are'), ['description' => '<p>About us content</p>'])
            ->assertOk()
            ->assertJson(['status' => true]);

        $this->assertSame('<p>About us content</p>', AboutUs::first()->who_we_are);
    }

    public function test_refund_and_return_policy_save_from_description_field(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin, 'admin')
            ->post(route('admin.store_refund'), ['description' => '<p>Refund content</p>'])
            ->assertJson(['status' => true]);

        $this->actingAs($admin, 'admin')
            ->post(route('admin.store_return'), ['description' => '<p>Return content</p>'])
            ->assertJson(['status' => true]);

        $about = AboutUs::first();
        $this->assertSame('<p>Refund content</p>', $about->refund_policy);
        $this->assertSame('<p>Return content</p>', $about->return_policy);
    }

    public function test_about_us_page_renders_sanitized_rich_content(): void
    {
        $about = new AboutUs();
        $about->who_we_are = '<p style="font-family: Kalpurush; font-size: 20px;">আমাদের সম্পর্কে</p>'
            . '<script>alert(1)</script><img src="x" onerror="alert(2)">';
        $about->save();

        $response = $this->get(route('front.aboutus'));

        $response->assertOk();
        $response->assertSee('rich-content', false);
        $response->assertSee('font-family: Kalpurush', false);
        $response->assertSee('আমাদের সম্পর্কে');
        $response->assertDontSee('<script>alert(1)</script>', false);
        $response->assertDontSee('onerror', false);
    }

    public function test_return_policy_page_renders_sanitized_rich_content(): void
    {
        $about = new AboutUs();
        $about->return_policy = '<h2>Return</h2><script>alert(1)</script>';
        $about->refund_policy = '<p>Refund</p>';
        $about->save();

        $response = $this->get(route('front.return'));

        $response->assertOk();
        $response->assertSee('rich-content', false);
        $response->assertDontSee('<script>alert(1)</script>', false);
    }
}
