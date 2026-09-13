<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Models\AboutUs;
use App\Models\Advertisement;
use App\Models\CustomerAddress;
use App\Models\NewProduct;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\SubCategory;
use App\Models\Variation;
use App\Models\VariationValues;
use Illuminate\Http\Request;
use App\Models\BannerPhoto;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use Gloudemans\Shoppingcart\Facades\Cart;
use App\Models\Product;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use App\Models\ShippingCharge;

class FrontController extends Controller
{
    public function index()
    {
        // $products = NewProduct::latest('id')->where('status', 1)->where('qty', '>=', 1)->with('product_image')->get();

        $advertise = Advertisement::first();

        $categories = Category::get();
        $cartContent = Cart::content();
        $banner = BannerPhoto::where('status', 1)->first();

        $data = [];
        // $data['products'] = $products;
        $data['categories'] = $categories;
        $data['cartContent'] = $cartContent;
        $data['banner'] = $banner;
        $data['advertise'] = $advertise;

        return view('front.pages.home', $data);
    }

    public function Product_details_page($slug)
    {
        // $product_image = ProductImage::where('product_id', $product_variant_info->id)->get();
        $get_category_id = NewProduct::where('slug', $slug)->first();

        $related_product = NewProduct::where('cat_id', $get_category_id->cat_id)->get();

        $product_info = NewProduct::where('slug', $slug)->first();

        // Get all variants for this product
        $variants = ProductVariant::where('product_id', $product_info->id)->get();
        $defaultVariant = $variants->first();
        $advertise = Advertisement::first();

        $variantMap = [];
        $groupedVariations = [];

        foreach ($variants as $variant) {
            $variationValues = json_decode($variant->variation_values, true);

            if (empty($variationValues)) {
                continue;
            }

            $keyParts = [];

            foreach ($variationValues as $item) {
                $variation = Variation::find($item['variation_id']);
                $value = VariationValues::find($item['value_id']);

                if ($variation && $value) {
                    // For frontend buttons (Color, Size, etc.)
                    $groupedVariations[$variation->variations][] = $value->value;

                    // For variant key
                    $keyParts[] = $value->value;
                }
            }

            $key = implode('-', $keyParts);

            // Variant images
            $images = ProductImage::where('product_id', $variant->id)->where('is_thumb', 0)->pluck('image')->toArray();

            $variantMap[$key] = [
                'id' => $variant->id,
                'price' => $variant->selling_price,
                'compare_price' => $variant->compare_price,
                'sku' => $variant->sku ?? 'N/A',
                'stock' => $variant->stock(),
                'allow_pre_order' => (bool) $variant->allow_pre_order,
                'images' => $images,
            ];
        }

        // remove duplicates
        if (!empty($variationValues)) {
            foreach ($groupedVariations as $name => $values) {
                $groupedVariations[$name] = array_values(array_unique($values));
            }
        }

        $categories = Category::get();
        $cartContent = Cart::content();
        $banner = BannerPhoto::where('status', 1)->first();

        $data = [];
        $data['product_info'] = $product_info;
        $data['categories'] = $categories;
        $data['cartContent'] = $cartContent;
        $data['banner'] = $banner;
        $data['advertise'] = $advertise;
        $data['related_product'] = $related_product;
        $data['defaultVariant'] = $defaultVariant;

        if (!empty($variantMap)) {
            $data['variantMap'] = $variantMap;
        }
        // $data['groupedVariations'] = $groupedVariations;
        $data['variants'] = $variants;
        // $data['product_variant_info'] = $product_variant_info;
        // $data['product_image'] = $product_image;

        if (!empty($groupedVariations)) {
            $data['groupedVariations'] = $groupedVariations;
        }

        return view('front.pages.product_details', $data);
    }

    public function shop_page($slug)
    {
        $categories = Category::get();
        $cartContent = Cart::content();
        $banner = BannerPhoto::where('status', 1)->first();

        $cat_id = Category::where('slug', $slug)->first();

        if ($slug == 'hot-products') {
            $all_products = NewProduct::where('status', 1)->where('hot_products', 1)->latest()->get();
        } else {
            $all_products = NewProduct::where('cat_id', $cat_id->id)->latest()->get();
        }

        $data = [];
        // $data['products'] = $products;
        $data['categories'] = $categories;
        $data['cartContent'] = $cartContent;
        $data['banner'] = $banner;
        $data['all_products'] = $all_products;
        $data['url_slug'] = $slug;

        return view('front.pages.shop', $data);
    }

    public function ViewAboutUs()
    {
        $categories = Category::get();
        $cartContent = Cart::content();
        $banner = BannerPhoto::where('status', 1)->first();
        $about_us = AboutUs::firstOrNew();

        $data = [];
        // $data['products'] = $products;
        $data['categories'] = $categories;
        $data['cartContent'] = $cartContent;
        $data['banner'] = $banner;
        $data['about_us'] = $about_us;

        return view('front.pages.about_us', $data);
    }

    public function cart()
    {
        $categories = Category::get();
        $cartContent = Cart::content();
        $banner = BannerPhoto::where('status', 1)->first();
        $about_us = AboutUs::firstOrNew();

        $user = Auth::user();
        $customerAddress = null; // default value
        if ($user) {
            $customerAddress = CustomerAddress::where('user_id', $user->id)->first();

            // dd($customerAddress); // only for debugging
        }
        $shippingCharge = ShippingCharge::all();

        $data = [];
        // $data['products'] = $products;
        $data['categories'] = $categories;
        $data['cartContent'] = $cartContent;
        $data['banner'] = $banner;
        $data['about_us'] = $about_us;

        $data['customerAddress'] = $customerAddress;
        $data['shippingCharge'] = $shippingCharge;
        $data['districtAmounts'] = ShippingCharge::ratesByDistrict();
        $data['allFreeDelivery'] = Order::isFreeDeliveryCart(Cart::content());

        return view('front.pages.view_cart', $data);
    }

    public function ViewReturnPolicy()
    {
        $categories = Category::get();
        $cartContent = Cart::content();
        $banner = BannerPhoto::where('status', 1)->first();
        $about_us = AboutUs::firstOrNew();

        $data = [];
        // $data['products'] = $products;
        $data['categories'] = $categories;
        $data['cartContent'] = $cartContent;
        $data['banner'] = $banner;
        $data['about_us'] = $about_us;

        return view('front.pages.return_policy', $data);
    }

    public function thankyou($id)
    {
        $categories = Category::get();
        $cartContent = Cart::content();
        $banner = BannerPhoto::where('status', 1)->first();
        $about_us = AboutUs::firstOrNew();

        $data = [];
        // $data['products'] = $products;
        $data['categories'] = $categories;
        $data['cartContent'] = $cartContent;
        $data['banner'] = $banner;
        $data['id'] = $id;
        $data['about_us'] = $about_us;

        return view('front.pages.order_success', $data);
    }

    public function invoice($id)
    {
        $categories = Category::get();
        $cartContent = Cart::content();
        $banner = BannerPhoto::where('status', 1)->first();
        $about_us = AboutUs::firstOrNew();

        $data = [];
        // $data['products'] = $products;
        $data['categories'] = $categories;
        $data['cartContent'] = $cartContent;
        $data['banner'] = $banner;
        $data['id'] = $id;
        $data['about_us'] = $about_us;

        return view('front.pages.invoice', $data);
    }

    public function shop_page_sub_cat($slug)
    {
        $categories = Category::get();
        $cartContent = Cart::content();
        $banner = BannerPhoto::where('status', 1)->first();

        $sub_cat_id = SubCategory::where('slug', $slug)->first();

        $all_products = NewProduct::where('sub_cat_id', $sub_cat_id->id)->latest()->get();

        $data = [];
        // $data['products'] = $products;
        $data['categories'] = $categories;
        $data['cartContent'] = $cartContent;
        $data['banner'] = $banner;
        $data['all_products'] = $all_products;
        $data['url_slug'] = $slug;

        return view('front.pages.product_shop_sub_cat', $data);
    }
}
