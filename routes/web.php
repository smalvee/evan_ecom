<?php

use App\Http\Controllers\admin\AdminLoginController;
use App\Http\Controllers\admin\AdvertisementController;
use App\Http\Controllers\admin\BannerController;
use App\Http\Controllers\admin\BrandController;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\CourierSettingsController;
use App\Http\Controllers\admin\CourierShipmentController;
use App\Http\Controllers\admin\HomeController;
use App\Http\Controllers\admin\ImageController;
use App\Http\Controllers\admin\NewProductController;
use App\Http\Controllers\admin\OrderController;
use App\Http\Controllers\admin\PageInfoController;
use App\Http\Controllers\admin\PreOrderController;
use App\Http\Controllers\admin\ProductController;
use App\Http\Controllers\admin\ProductAdjustmentController;
use App\Http\Controllers\admin\ProductPricingController;
use App\Http\Controllers\admin\ProductSubCategoryController;
use App\Http\Controllers\admin\PurchaseController;
use App\Http\Controllers\admin\SettingController;
use App\Http\Controllers\admin\Report;
use App\Http\Controllers\admin\ReportController;
use App\Http\Controllers\admin\ShippingController;
use App\Http\Controllers\admin\SubCategoryController;
use App\Http\Controllers\admin\SupplierController;
use App\Http\Controllers\admin\TempImagesController;
use App\Http\Controllers\admin\DiscontCodeController;
use App\Http\Controllers\admin\UniteController;
use App\Http\Controllers\admin\UserController;
use App\Http\Controllers\admin\VariationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\front\FrontController as NewFrontController;
use App\Http\Controllers\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

// new front

Route::get('/', [NewFrontController::class, 'index'])->name('front.home');
// Route::get('/', [AdminLoginController::class, 'index'])->name('admin.login');

// Route::get('/', [FrontController::class, 'index'])->name('front.home');
// Route::get('/prodict-details/{sku}', [FrontController::class, 'Product_details'])->name('Product_details.home');
Route::get('/prodict-details/{sku}', [NewFrontController::class, 'Product_details_page'])->name('Product_details.home');
// Route::get('/product-shop/{category_id}', [FrontController::class, 'shop_page'])->name('product_shop.home');
Route::get('/product-shop/offer/{is_offered}', [FrontController::class, 'shop_page_offerZone'])->name('product_shop_offer.home');

Route::get('/product-shop/{slug}', [NewFrontController::class, 'shop_page'])->name('product_shop.home');
Route::get('/product-shop-inv/{slug}', [NewFrontController::class, 'shop_page_sub_cat'])->name('product_shop_sub_cat.home');

//search
Route::get('/search-products', [NewProductController::class, 'search'])->name('search.products');

// cart
Route::get('/cart', [NewFrontController::class, 'cart'])->name('front.cart');
Route::post('/add-to-cart', [CartController::class, 'addToCart'])->name('front.addToCart');
Route::post('/update-cart', [CartController::class, 'updateCart'])->name('front.updateCart');
Route::post('/delete-item-cart', [CartController::class, 'deleteItem'])->name('front.deleteItem.cart');
Route::post('/process-checkout', [CartController::class, 'processCheckout'])->name('front.checkout');
Route::get('/thanks/{order_id}', [NewFrontController::class, 'thankyou'])->name('front.thankyou');
Route::get('/invoice/{order_id}', [NewFrontController::class, 'invoice'])->name('front.invoice');

Route::post('/apply-coupon', [CartController::class, 'applyCoupon'])->name('coupon.apply');
Route::post('/delete-item-side-cart', [CartController::class, 'deleteItemFromSideCart'])->name('front.deleteItem.sidecart');
// Route::get('/singel-product/{slug}', [CartController::class, 'singleCart'])->name('front.sing.checkout');
Route::get('/singel-product/{id}', [CartController::class, 'singleCart'])->name('front.sing.checkout');
Route::post('/singel-product-checkout/{slug}', [CartController::class, 'singleCheckout'])->name('front.singCheckout');

// page info
// Route::get('/about-us', [PageInfoController::class, 'ViewAboutUs'])->name('front.aboutus');
Route::get('/about-us', [NewFrontController::class, 'ViewAboutUs'])->name('front.aboutus');
// Route::get('/return-policy', [PageInfoController::class, 'ViewReturnPolicy'])->name('front.return');
Route::get('/return-policy', [NewFrontController::class, 'ViewReturnPolicy'])->name('front.return');
Route::get('/refund-policy', [PageInfoController::class, 'ViewRefundPolicy'])->name('front.refund');

//track order
Route::get('/track-order', [FrontController::class, 'trackOrderPage'])->name('front.trackOrderPage');
Route::post('/track-order', [FrontController::class, 'trackOrder'])->name('track.order');

// user group
Route::group(['prefix' => 'account'], function () {
    Route::group(['middleware' => 'guest'], function () {
        Route::get('/register', [AuthController::class, 'register'])->name('account.register');
        Route::post('/process-register', [AuthController::class, 'processRegister'])->name('account.processRegister');
        Route::get('/login', [AuthController::class, 'login'])->name('account.userLogin');
        Route::post('/login', [AuthController::class, 'authenticate'])->name('account.authenticate')->middleware('throttle:10,1');
    });
    Route::group(['middleware' => 'auth'], function () {
        Route::get('/user-dashboard', [AuthController::class, 'dashboard'])->name('account.userDashboard');
        Route::get('/orders', [AuthController::class, 'orders'])->name('account.orders');
        Route::get('/user-logout', [AuthController::class, 'logout'])->name('account.logout');
        Route::get('/order-details/{order_id}', [AuthController::class, 'orderDetails'])->name('account.orderDetails');
        Route::get('/user-profile', [UserProfile::class, 'index'])->name('account.profile');
        Route::put('/user-profile-update', [UserProfile::class, 'update'])->name('account.profileUpdate');
        Route::put('/user-password-update', [UserProfile::class, 'updatePassword'])->name('account.updatePassword');
    });
});

// admin group
Route::group(['prefix' => 'admin'], function () {
    Route::group(['middleware' => 'admin.guest'], function () {
        Route::get('/login', [AdminLoginController::class, 'index'])->name('admin.login');
        Route::post('/authenticate', [AdminLoginController::class, 'authenticate'])->name('admin.authenticate')->middleware('throttle:10,1');
    });

    Route::group(['middleware' => 'admin.auth'], function () {
        Route::get('/dashboard', [HomeController::class, 'index'])->name('admin.dashboard');
        Route::get('/logout', [HomeController::class, 'logout'])->name('admin.logout');

        // categories
        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/{category_id}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{category_id}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category_id}', [CategoryController::class, 'distroy'])->name('categories.delete');

        // sub category
        Route::get('/sub-categories/create', [SubCategoryController::class, 'create'])->name('sub-categories.create');
        Route::post('/sub-categories', [SubCategoryController::class, 'store'])->name('sub-categories.store');
        Route::get('/sub-categories', [SubCategoryController::class, 'index'])->name('sub-categories.index');
        Route::get('/sub-categories/{sub_category_id}/edit', [SubCategoryController::class, 'edit'])->name('sub-categories.edit');
        Route::put('/sub-categories/{sub_category_id}', [SubCategoryController::class, 'update'])->name('sub-categories.update');
        Route::delete('/sub-categories/{sub_category_id}', [SubCategoryController::class, 'distroy'])->name('sub-categories.delete');

        //pages
        Route::get('/about-us', [PageInfoController::class, 'displayaboutus'])->name('admin.display.aboutus');
        Route::get('/refund', [PageInfoController::class, 'displayrefund'])->name('admin.display.refund');
        Route::get('/return', [PageInfoController::class, 'displayreturn'])->name('admin.display.return');
        //store or update about us page content
        Route::post('/who-we-are', [PageInfoController::class, 'store_who_we_are'])->name('admin.store_who_we_are');
        Route::post('/mission', [PageInfoController::class, 'store_our_mission'])->name('admin.store_our_mission');
        Route::post('/vission', [PageInfoController::class, 'store_our_vission'])->name('admin.store_our_vission');

        Route::post('/refund', [PageInfoController::class, 'store_refund_policy'])->name('admin.store_refund');
        Route::post('/return', [PageInfoController::class, 'store_return_policy'])->name('admin.store_return');

        // Brands
        Route::get('/brands', [BrandController::class, 'index'])->name('brands.index');
        Route::get('/brands/create', [BrandController::class, 'create'])->name('brands.create');
        Route::post('/brands', [BrandController::class, 'store'])->name('brands.store');
        Route::get('/brands/{brand_id}/edit', [BrandController::class, 'edit'])->name('brands.edit');
        Route::put('/brands/{brand_id}', [BrandController::class, 'update'])->name('brands.update');
        Route::delete('/brands/{brand_id}', [BrandController::class, 'distroy'])->name('brands.delete');

        // shipping
        Route::get('shipping/create', [ShippingController::class, 'create'])->name('shipping.create');
        Route::post('/shipping', [ShippingController::class, 'store'])->name('shipping.store');
        Route::get('/shipping/{id}/edit', [ShippingController::class, 'edit'])->name('shipping.edit');
        Route::put('/shipping/{id}', [ShippingController::class, 'update'])->name('shipping.update');
        Route::delete('/shipping/{id}', [ShippingController::class, 'distroy'])->name('shipping.delete');

        // Coupons
        Route::get('coupon/create', [DiscontCodeController::class, 'create'])->name('coupon.create');
        Route::post('/coupon', [DiscontCodeController::class, 'store'])->name('coupon.store');
        Route::get('/coupons', [DiscontCodeController::class, 'index'])->name('coupon.index');
        Route::get('/coupons/{id}/edit', [DiscontCodeController::class, 'edit'])->name('coupon.edit');
        Route::put('/coupons/{id}', [DiscontCodeController::class, 'update'])->name('coupon.update');
        Route::delete('/coupons/{id}', [DiscontCodeController::class, 'distroy'])->name('coupon.delete');

        // orders
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{id}', [OrderController::class, 'details'])->name('orders.details');
        Route::delete('/orders/{id}', [OrderController::class, 'destroy'])->name('orders.delete');
        Route::post('/orders/change-status/{id}', [OrderController::class, 'ChangeOrderStatus'])->name('orders.changeStatus');
        Route::get('/orders-create', [OrderController::class, 'create_order'])->name('orders.create');
        Route::post('/orders-store', [OrderController::class, 'order_store'])->name('orders.store');
        Route::post('/orders-address-update/{id}', [OrderController::class, 'order_address_update'])->name('orders.address_update');
        Route::post('/orders-update/{id}', [OrderController::class, 'order_update'])->name('orders.order_update');
        Route::post('/orders-full-update/{id}', [OrderController::class, 'updateOrder'])->name('orders.update_full');
        Route::post('/orders/{id}/payment-status', [OrderController::class, 'togglePaymentStatus'])->name('orders.paymentStatus');

        // courier shipments (order-level actions)
        Route::post('/orders/{order}/courier/create', [CourierShipmentController::class, 'create'])->name('admin.orders.courier.create');
        Route::post('/orders/{order}/courier/status', [CourierShipmentController::class, 'refresh'])->name('admin.orders.courier.status');
        Route::post('/orders/{order}/courier/cancel', [CourierShipmentController::class, 'cancel'])->name('admin.orders.courier.cancel');
        Route::post('/orders/{order}/courier/simulate', [CourierShipmentController::class, 'simulate'])->name('admin.orders.courier.simulate');

        // pre-orders
        Route::get('/pre-orders', [PreOrderController::class, 'index'])->name('admin.pre_orders.index');
        Route::get('/pre-orders/{id}', [PreOrderController::class, 'details'])->name('admin.pre_orders.details');
        Route::post('/pre-orders/{id}/process', [PreOrderController::class, 'process'])->name('admin.pre_orders.process');

        // users
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/create-user', [UserController::class, 'create'])->name('users.create');
        Route::post('/store-user', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user_id}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user_id}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{id}', [UserController::class, 'distroy'])->name('users.delete');

        // suppliers
        Route::get('/suppliers', [SupplierController::class, 'index'])->name('supply.index');
        Route::get('/create-supplier', [SupplierController::class, 'create'])->name('supply.create');
        Route::post('/supplier-create', [SupplierController::class, 'store'])->name('supply.store');
        Route::get('/supplier/{supplier_id}/edit', [SupplierController::class, 'edit'])->name('supply.edit');
        Route::put('/supplier/{user_id}/update', [SupplierController::class, 'update'])->name('supply.update');

        // Units
        Route::get('/units', [UniteController::class, 'index'])->name('units.index');
        Route::post('/units-store', [UniteController::class, 'store'])->name('units.store');
        Route::post('update_units/{id}', [UniteController::class, 'update'])->name('units.update');
        Route::delete('/unit/{id}', [UniteController::class, 'distroy'])->name('units.delete');

        // variation
        Route::get('/variations', [VariationController::class, 'index'])->name('variation.index');
        Route::post('/variations-store', [VariationController::class, 'store'])->name('variation.store');
        Route::post('/variations/update/{id}', [VariationController::class, 'update'])->name('variations.update');
        // Route::delete('/variations/{id}', [VariationController::class, 'destroy'])->name('variations.destroy');
        Route::delete('/variations/{id}', [VariationController::class, 'destroy'])->name('variations.destroy');

        Route::get('/get-variation-values/{id}', [VariationController::class, 'getValues']);

        // new product
        Route::post('/new-product-store', [NewProductController::class, 'store'])->name('new_product.store');
        Route::get('/new-products/{product_id}/edit', [NewProductController::class, 'edit'])->name('new_products.edit');
        Route::delete('/product-variant/{id}', [NewProductController::class, 'destroy_variant'])->name('variant.destroy');
        Route::post('/product/update/{id}', [NewProductController::class, 'update'])->name('update_product.update');
        Route::delete('/new-product/delete/{id}', [NewProductController::class, 'distroy'])->name('new_product.destroy');

        // purchase
        Route::get('/purchase', [PurchaseController::class, 'index'])->name('purchase.index');
        Route::get('/create-purchase', [PurchaseController::class, 'create'])->name('purchase.create');
        Route::post('/purchase-store', [PurchaseController::class, 'store'])->name('purchase.store');
        Route::get('/purchase-edit/{id}', [PurchaseController::class, 'edit'])->name('purchase.edit');
        Route::post('/purchase-update/{id}', [PurchaseController::class, 'update'])->name('purchase.update');

        // purchase return
        Route::get('/purchase-return', [PurchaseController::class, 'purchase_return'])->name('return.index');
        Route::get('/purchase/search', [PurchaseController::class, 'search'])->name('purchase.search');
        Route::post('/purchase/return', [PurchaseController::class, 'return_store'])->name('purchase.return_store');
        Route::get('/purchase/return/list', [PurchaseController::class, 'return_list'])->name('purchase.return_list');
        Route::get('/purchase/return/view/{id}', [PurchaseController::class, 'return_view'])->name('purchase.return_view');





        

        // advertise
        Route::get('/advertise', [AdvertisementController::class, 'index'])->name('advertise.index');
        Route::post('/add-store/{slot}', [AdvertisementController::class, 'update'])->name('advertisements.update');
        Route::post('/advertise/toggle/{slot}', [AdvertisementController::class, 'toggleStatus'])->name('advertisements.toggleStatus');

        // product Image
        Route::get('/product-list', [ImageController::class, 'get_product_list'])->name('get_product_list.index');
        Route::get('/product-image/{id}', [ImageController::class, 'edit'])->name('image_edit.edit');
        Route::post('/image-store', [ImageController::class, 'store'])->name('image.store');
        Route::delete('/galery-image/delete/{id}', [ImageController::class, 'distroy'])->name('galery_image.destroy');

        //banner
        Route::get('/banner', [BannerController::class, 'index'])->name('banner.index');
        Route::post('/banner-create', [BannerController::class, 'store'])->name('banner.create');
        Route::post('/banners/toggle-status', [BannerController::class, 'toggleStatus'])->name('banners.toggleStatus');
        Route::delete('/banners/{id}', [BannerController::class, 'destroy'])->name('banner.destroy');

        // report
        Route::get('/sales-report', [ReportController::class, 'sales_index'])->name('sales.index');
        Route::get('/item-wise-sales-report', [ReportController::class, 'item_sales_index'])->name('item_sales.index');
        Route::get('reports/sales/export', [ReportController::class, 'exportExcel'])->name('admin.reports.sales.export');

        // reports dashboard + analytics
        Route::get('/reports', [ReportController::class, 'dashboard'])->name('admin.reports.index');
        Route::get('/reports/product-performance', [ReportController::class, 'product_performance'])->name('admin.reports.product_performance');
        Route::get('/reports/profit-loss', [ReportController::class, 'profit_loss'])->name('admin.reports.profit_loss');
        Route::get('/reports/orders', [ReportController::class, 'orders'])->name('admin.reports.orders');
        Route::get('/reports/customers', [ReportController::class, 'customers'])->name('admin.reports.customers');
        Route::get('/reports/payments', [ReportController::class, 'payments'])->name('admin.reports.payments');
        Route::get('/reports/inventory', [ReportController::class, 'inventory'])->name('admin.reports.inventory');
        Route::get('/reports/purchases', [ReportController::class, 'purchases'])->name('admin.reports.purchases');
        Route::get('/reports/purchase-returns', [ReportController::class, 'purchase_returns'])->name('admin.reports.purchase_returns');
        Route::get('/reports/coupons', [ReportController::class, 'coupons'])->name('admin.reports.coupons');
        Route::get('/reports/shipping', [ReportController::class, 'shipping'])->name('admin.reports.shipping');
        Route::get('/reports/export/{report}', [ReportController::class, 'export'])->name('admin.reports.export');

        // website settings
        Route::get('/settings', [SettingController::class, 'index'])->name('admin.settings');
        Route::put('/settings', [SettingController::class, 'update'])->name('admin.settings.update');

        // courier settings
        Route::get('/settings/courier', [CourierSettingsController::class, 'index'])->name('admin.courier.settings');
        Route::put('/settings/courier', [CourierSettingsController::class, 'update'])->name('admin.courier.settings.update');
        Route::post('/settings/courier/test-connection', [CourierSettingsController::class, 'testConnection'])->name('admin.courier.settings.test');




















        // Products
        Route::get('/products', [ProductController::class, 'index'])->name('products.index');
        Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
        Route::get('/pruducts-sub-category', [ProductSubCategoryController::class, 'index'])->name('pruducts-sub-category.index');

        // Product pricing management (MRP + selling price, separate from purchase cost)
        Route::get('/product-pricing', [ProductPricingController::class, 'index'])->name('admin.pricing.index');
        Route::post('/product-pricing/{variant}', [ProductPricingController::class, 'update'])->name('admin.pricing.update');
        Route::get('/product-pricing/{variant}/history', [ProductPricingController::class, 'history'])->name('admin.pricing.history');

        // Product stock adjustments (increase / decrease / correction)
        Route::get('/product-adjustments', [ProductAdjustmentController::class, 'index'])->name('admin.adjustments.index');
        Route::get('/product-adjustments/create', [ProductAdjustmentController::class, 'create'])->name('admin.adjustments.create');
        Route::post('/product-adjustments', [ProductAdjustmentController::class, 'store'])->name('admin.adjustments.store');
        Route::get('/product-adjustments/{id}', [ProductAdjustmentController::class, 'show'])->name('admin.adjustments.show');

        // temporary image create
        Route::post('/upload-temp-image', [TempImagesController::class, 'create'])->name('temp-images.create');
        Route::delete('/delete-temp-image/{id}', [TempImagesController::class, 'delete'])->name('temp-images-delete.delete');

        Route::get('/getSlug', function (Request $request) {
            $slug = '';
            if (!empty($request->title)) {
                $slug = Str::slug($request->title);
            }
            return response()->json([
                'status' => true,
                'slug' => $slug,
            ]);
        })->name('getSlug');
    });
});
