# AGENTS.md — Evan E-commerce (Laravel)

Handoff/context file for opencode sessions. Read this first.

## Project

- **Path:** `D:\Project\Evan` (open this folder as the workspace; it is the real project).
- **Stack:** Laravel 10, PHP 8.1, MySQL (`evan_ecom`), Bootstrap + jQuery + AdminLTE-style templates, Vite.
- **Packages:** `hardevine/shoppingcart`, `intervention/image`, `maatwebsite/excel`, `laravel/sanctum`.
- **Two surfaces:** admin panel (`/admin/*`) and storefront (front routes).
- **Auth:** `web` guard = customers; `admin` guard = admins (`users.role == 2`). Same `User` model for both.
- All work is currently **uncommitted** unless a later commit exists. `git status` to confirm.

## Common commands

```bash
php artisan migrate --force        # run migrations
php artisan route:list             # routes
php artisan view:cache / view:clear
php -l path/to/file.php            # syntax check
php artisan tinker --execute="..." # quick checks
```
No test suite is configured (only default stubs). Verification is done via ad-hoc scripts
(create temp `_xxx.php`, boot Laravel, exercise code, then delete). Do NOT leave temp files.

## Key architecture

### Order lifecycle & statuses
- Statuses (magic strings, note the typo): `pending`, `confirm`, `shipped`, `cancell`.
- `ReportService::EXCLUDED_STATUS = 'cancell'`; sales metrics exclude cancelled.
- **Stock rule:** stock is NOT reduced at order placement. It is deducted when the order
  becomes `confirm`/`shipped` (once, guarded by `orders.stock_deducted` flag) and restored when
  a stock-deducted order is cancelled. See `OrderController::applyStockForStockChange` /
  `adjustVariantStock`.
- `order_items.product_id` actually references **`product_variants.id`** (not `products`).
- `order_items.price` and `order_items.cost_price` are **snapshots** at order time; never recompute.

### Pricing vs. costing (important separation)
- `product_variants.purchase_price` = current/latest inventory cost.
- `product_variants.average_cost` = current **weighted-average** inventory cost (WAC).
- `product_variants.compare_price` = current MRP.
- `product_variants.selling_price` = current customer selling price.
- `product_variants.price_manually_managed` = true once an admin edits price; purchases then
  will NOT overwrite `compare_price`/`selling_price`.
- `purchase_items.unit_cost` = immutable historical cost of that purchase.
- Services:
  - `App\Services\ProductVariantPricingService` — manual price edits + purchase-driven pricing + price history.
  - `App\Services\InventoryCostingService` — WAC (`recordPurchase`, `applyReturn`, `recalculateAverageCost`).
- Price history table: `product_variant_price_histories`.
- **Purchase edit** defaults to NOT changing current price; only recalculates when the
  `update_price` checkbox is submitted (see `PurchaseController::update`).
- COGS in reports uses `COALESCE(order_items.cost_price, product_variants.purchase_price)`.
- WAC `recalculateAverageCost` uses net-purchased qty (ignores sales) — documented assumption.

### Product systems
- Active system: `new_products` + `product_variants` (admin: `NewProductController`,
  `ProductController::index/create`, `ImageController`, `ProductPricingController`).
- **Legacy `products` table + `Product` model were removed** (migration
  `2026_09_11_000009_drop_legacy_products_table`). `product_images` is shared with the new
  system and was intentionally kept.
- `pruducts-sub-category.index` + `ProductSubCategoryController` are still used by the new
  product create/edit forms (do not remove).

### Settings & global data
- `settings` table (key/value) + `App\Models\Setting` with caching (`site_settings`).
- View composer in `AppServiceProvider` shares `$settings` / `$socialLinks` with all views.

### Reports
- `App\Services\ReportService` + `admin/ReportController` + `resources/views/admin/reports/*`.
- Revenue/discount model: `grand_total = subtotal + shipping - discount`; `additional_discount`
  is a memo of already-netted item discounts (do not subtract again).

## Conventions

- **Admin design system:** `public/new-admin-assets/css/admin.css` (loaded after the template CSS).
  Use classes: `.a-page-head`, `.a-card`/`.a-card-head`/`.a-card-body`/`.a-card-footer`,
  `.a-badge`/`.a-badge-*`, `.a-actions-cell`/`.a-action-btn`, `.a-stat-card`, `.a-empty`,
  `.a-toolbar`, `.btn-theme`, `.form-control`, `.form-label`, `.theme-table`.
- **Admin layout:** `resources/views/admin/layouts/new_app.blade.php` (+ `new_sidebar`, `new_page_header`).
- **Storefront:** single layout `resources/views/front/layouts/new_app.blade.php`
  (the legacy `front.layouts.app` and all legacy views were deleted).
- **Icons:** remixicon only (verify an icon exists in `public/new-admin-assets/css/remixicon.css`
  before using it).
- **Routes:** preserve existing route names/input names; admin routes are under the `admin.auth`
  middleware group in `routes/web.php`.
- Blade views may not run DB queries if avoidable; prefer controllers/services + caching.
- Money columns are partly `VARCHAR` (product_variants prices/qty) — cast numerically; don't rely
  on lexical comparison.

## What was done in the previous session (high level)

1. **Admin UI/UX redesign (45 pages)** — global `admin.css` design system, sidebar grouping,
   header, dashboard.
2. **Reporting system** — 13 reports, filters, charts (ApexCharts), Excel/CSV export, print.
3. **Settings page** — DB-driven branding/contact/social, dynamic footer, logo upload.
4. **Order/inventory logic** — deduct on confirm, restore on cancel (once), block deleting
   confirmed orders, unified single-transaction order edit (status/address/items/cancel/add).
5. **Purchase module fixes** — relationships, transactions, atomic stock, return validation.
6. **Pricing separation** — manual MRP/selling price with history; purchase edit opt-in.
7. **WAC inventory costing** — `average_cost`, order-time `cost_price` snapshots, COGS from snapshots.
8. **Advertisement active/inactive** per slot.
9. **Security fixes** — single-checkout server-side pricing, upload validation + `.htaccess` in
   `public/temp` & `public/uploads`, random guest passwords + guest-account claim on register,
   removed `app/info.php`, order-tracking requires matching phone, login throttling.
10. **Frontend consolidation** — removed the legacy second layout, migrated remaining pages to
    `new_app`, removed the orphaned legacy `Product` CRUD/model/table.
11. **Performance** — cached front search index (`front_search_products`), removed N+1 and dead queries.

## Remaining / known issues

- **VARCHAR money/qty** on `product_variants` — columns are still strings. Mitigated in code:
  `ProductVariant` casts `qty` to int and money fields to float; SQL filters/ordering use
  `CAST(... AS SIGNED/DECIMAL)` (`HomeController` dashboard, `ReportService`); front views use
  explicit `(float)` casts on price comparisons. Schema was intentionally not altered.
- **WAC recalculation** now replays the full history (purchases, supplier returns and
  stock-deducted sales) chronologically in `InventoryCostingService::recalculateAverageCost`.
  Sales remove stock at the running average (so they don't change the average, but they
  correctly weight later purchases); the result is anchored to current on-hand qty. Sale
  timing uses `orders.created_at` and only stock-deducted, non-cancelled orders count.
- **Schema drift** — reconciled. The create migrations now include the live-only columns the
  code uses (`product_images.is_thumb`/`product_variant_id`, `orders.order_id`/`status`/
  `admin_note`/`payment_status`/`additional_discount`, `order_items.discount`,
  `new_products.status`/`hot_products`, `product_variants.variation_sku`/`variation_values`).
  The `product_variants` -> `new_products` FK is now created in the `new_products` migration
  (its table is created later), so a fresh `migrate` succeeds. Verified against a scratch DB.
- Legacy `front-assets/` was removed (unreferenced). `admin-assets/` was pruned from ~82 MB to
  ~8 MB: only `plugins/dropzone`, `plugins/summernote`, `css/datetimepicker.css`,
  `js/datetimepicker.js` and `img/default-150x150.png` are used; AdminLTE theme/plugin bundles
  were deleted.

## Gotchas

- Never run `migrate:fresh`/`refresh` on production (drops data).
- Migrations may be edited to match code — verify against the live DB.
- On production (cPanel), the root `.htaccess` rewrites to `public/`.
- Do not commit unless the user asks.
