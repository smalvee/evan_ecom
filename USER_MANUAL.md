# Softco - Ecommerce manual

A simple, step-by-step guide for running your online store.

This manual covers two areas:

- **Part 1 — Admin Panel:** the private area where you manage products, orders, stock and settings.
- **Part 2 — Customer Store:** what your shoppers see and do on the website.

> Tip: Keep this file handy. Every instruction uses the exact menu names you will see on screen.

---

# Table of Contents

**Part 1 — Admin Panel**
1. Getting Started (Logging In)
2. The Dashboard
3. Setting Up Your Shop Basics
4. Adding and Managing Products
5. Product Images
6. Pricing
7. Stock Adjustment
8. Suppliers
9. Purchases (Buying Stock)
10. Purchase Return
11. Orders
12. Pre Orders
13. Coupons
14. Shipping Charges
15. Banners
16. Advertisement
17. Customers (Users)
18. Content Pages
19. Reports
20. Courier (Delivery Company)
21. Settings
22. Logging Out

**Part 2 — Customer Store**
23. What Your Customers See
24. Register and Log In
25. Browsing and Searching
26. Product Page
27. Cart and Coupons
28. Checkout and Payment
29. After Ordering (Thank You, Invoice, Track Order)
30. Customer Account Area

**Part 3 — Good Practices**
31. Daily and Weekly Routine
32. Important Rules to Remember
33. Common Problems and Fixes

---

# PART 1 — ADMIN PANEL

## 1. Getting Started (Logging In)

1. Open your web browser.
2. Go to your website address and add **/admin/login** at the end.
   - Example: `www.yourstore.com/admin/login`
3. Type your **email** and **password**.
4. Click **Log In**.

You are now inside the **Admin Panel**. The left side shows the main menu. Everything you manage is grouped there:

- **Main** → Dashboard
- **Catalog** → Categories, Sub Categories, Brands, Products, Units, Variations, Pricing, Stock Adjustment
- **Sales** → Orders, Pre Orders, Coupons, Shipping
- **Purchasing** → Suppliers, Purchase, Purchase Return
- **Marketing** → Banners, Advertisement
- **Customers** → Users
- **Content** → Pages
- **Reports** → Reports Dashboard, Reports
- **System** → Courier, Settings

> To keep your account safe, always click **Logout** when you finish (see Section 22).

---

## 2. The Dashboard

**Menu path:** Main → Dashboard

This is your home screen. It gives you a quick snapshot of your business:

- Total sales and total orders.
- Number of customers and products.
- How many products are in stock, low in stock, or out of stock.
- A chart of sales over time.
- Best-selling products and top categories.
- Recent customers.
- A count of **Pending Pre Orders** waiting for you.

Use this screen every morning to see how the shop is doing.

---

## 3. Setting Up Your Shop Basics

Do these steps **once**, before adding products. It keeps your products organised.

### 3.1 Categories
**Menu path:** Catalog → Categories

1. Click **Categories**.
2. Click the button to add a new category (for example, "Home Appliances").
3. Type the category **name** and any short description.
4. Save it.
5. To change a category later, click **Edit** beside it.
6. To remove one, click **Delete**.

### 3.2 Sub Categories
**Menu path:** Catalog → Sub Categories

1. Click **Sub Categories**.
2. Add a sub category and choose which **main category** it belongs to (for example, "Air Fryers" under "Home Appliances").
3. Save it.

### 3.3 Brands
**Menu path:** Catalog → Brands

1. Click **Brands**.
2. Add the brand name (for example, "Philips").
3. Save it.

### 3.4 Units
**Menu path:** Catalog → Units

Units describe how a product is measured or counted (for example, "Piece", "Kg", "Box").

1. Click **Units**.
2. Add a unit name.
3. Save it.

### 3.5 Variations
**Menu path:** Catalog → Variations

Variations are the choices a product can have, such as **Size** or **Colour**.

1. Click **Variations**.
2. Add a variation name (for example, "Colour").
3. Add its values (for example, "Black", "White", "Blue").
4. Save it.

> Example: A t-shirt may have Variation "Size" with values "S, M, L, XL".

---

## 4. Adding and Managing Products

**Menu path:** Catalog → Products

Click **Products** to open the product menu:

- **List Products** — see all your products.
- **Add Product** — create a new product.
- **Product Images** — upload photos for a product.

### 4.1 Add a Single Product (one price, one stock)

1. Go to **Catalog → Products → Add Product**.
2. Fill in the details:
   - Product **name**
   - **Category**, **Sub Category**, **Brand**, **Unit**
   - **Description** (use the text editor for bold, lists, images, tables)
   - **Product type:** choose **Single** (simple product).
3. If the product should never charge delivery, turn on **Free Delivery**.
4. Choose whether the product is **Active** (visible in the shop).
5. Save the product.

> Note: Price and stock are **not** set on this page. You set price in **Pricing** (Section 6), stock in **Stock Adjustment** (Section 7) or by recording a **Purchase** (Section 9). This is normal.

### 4.2 Add a Variable Product (options like size or colour)

1. Go to **Catalog → Products → Add Product**.
2. Choose **Product type: Variable**.
3. Use the **variation builder** to pick the variations and values (for example, Size: S, M, L).
4. The system creates one row for each combination. Each row has its own **SKU** (item code).
5. Save the product.

### 4.3 Edit or Delete a Product

**Menu path:** Catalog → Products → List Products

1. Find the product in the list.
2. Click **Edit** to change it.
3. Click **Delete** to remove it.
   - If a product option is still in stock or has been used in orders/purchases, the system will **not** delete it and will show a message. This protects your records.

### 4.4 SKU (Item Code) — a quick note

Every product option has an **SKU** — a short code you create (for example, `AF-V3-BLK`). Use clear, unique codes. The SKU is what you and your staff will search by.

---

## 5. Product Images

**Menu path:** Catalog → Products → Product Images

1. Click **Product Images**.
2. Choose the product/option you want to add photos to.
3. Upload one or more images.
4. Mark one image as the **thumbnail** (the main picture shown in lists).
5. Save.

> Tip: Use clear photos on a plain background. The system creates large, small and thumbnail versions automatically.

---

## 6. Pricing

**Menu path:** Catalog → Pricing

This is where you set the **selling price** and **MRP (compare price)** for each product option.

1. Click **Pricing**.
2. Search for the product/option by name or SKU.
3. Enter:
   - **MRP** (the "before discount" price, optional)
   - **Selling Price** (the price customers pay)
4. Save.

**Important:** Once you edit a price here, it becomes **manually managed**. This means future purchases will **not** automatically overwrite your price. You stay in control.

You can also view the **price history** to see past changes.

---

## 7. Stock Adjustment

**Menu path:** Catalog → Stock Adjustment

Use this when you need to add stock, remove stock, or correct your stock to match the real physical count.

There are **three types**:

### 7.1 Increase Stock
Use when you receive extra items.

1. Click **Stock Adjustment**, then **New Adjustment**.
2. Search and select the product/option.
3. Choose **Increase Stock**.
4. Enter the **quantity** to add (for example, 5).
5. Choose a **reason** (for example, "Found Stock").
6. Add a **note** if you wish.
7. Click **Save Adjustment**.

Result example: Current 20 + 5 = **25**.

### 7.2 Decrease Stock
Use when items are damaged, lost, or removed.

1. Select the product/option.
2. Choose **Decrease Stock**.
3. Enter the **quantity** to remove.
4. Choose a reason (for example, "Damaged").
5. Save.

Result example: Current 20 − 5 = **15**.

> You cannot reduce below zero. If you try, the system will warn you and nothing will change.

### 7.3 Stock Correction
Use this after a physical stock count, when the system number does not match reality. **You only type the real number you counted** — the system does the maths.

1. Select the product/option.
2. Choose **Stock Correction**.
3. In **Actual Physical Stock**, type the number you actually counted (for example, 0).
4. Save.

The system shows:
- **System Stock** (what the system had)
- **Actual Stock** (what you counted)
- **Adjustment** (the difference, added automatically)
- **Final Stock** (the corrected number)

Example: System Stock was **−3**, you counted **0**, so the system adds **+3** and the final stock becomes **0**.

> This is very useful when stock has gone negative by mistake.

### 7.4 Viewing Adjustment History
**Menu path:** Catalog → Stock Adjustment

The list shows every adjustment with its number (for example, `ADJ-000001`), date, product, type, the change (+ or −), before and after stock, reason and who made it. Click **View** to see full details.

> Adjustments are permanent records. They cannot be edited or deleted. This keeps your history honest.

---

## 8. Suppliers

**Menu path:** Purchasing → Suppliers

A supplier is the person or company you buy stock from.

1. Click **Suppliers**.
2. Click **Add Supplier**.
3. Fill in the supplier's name and contact details.
4. Save.
5. Use **Edit** to update details later.

---

## 9. Purchases (Buying Stock)

**Menu path:** Purchasing → Purchase

Recording a purchase does two things at once: it **adds stock** and it records your **cost**.

1. Click **Purchase → Add Purchase**.
2. Choose the **supplier**.
3. Add each item you bought:
   - Search the product/option.
   - Enter **quantity** and **unit cost** (what you paid per item).
4. Check the totals.
5. Save the purchase.

**About prices:** By default, saving a purchase will **not** change your selling price. If you *do* want the selling price to update from this purchase, tick the **update price** option before saving.

**About cost:** The system keeps a running **average cost** for each item. This is used later in profit reports, so your profit numbers stay accurate.

### 9.1 List and Edit Purchases
**Menu path:** Purchasing → Purchase → List Purchase

1. Open **List Purchase** to see all purchases.
2. Click a purchase to **Edit** it if you need to correct something.

---

## 10. Purchase Return

**Menu path:** Purchasing → Purchase Return

Use this when you return items to a supplier.

1. Go to **Purchase Return → New Return**.
2. Select the purchase and the items you are returning.
3. Enter the returned quantity.
4. Save. Stock and cost are adjusted automatically.

Use **Return List** to see all past returns.

---

## 11. Orders

**Menu path:** Sales → Orders

This is where you manage every customer order.

### 11.1 Order List
**Menu path:** Sales → Orders → Order List

1. See all orders with order number, customer, total and status.
2. Use the **search box** to find by customer name, phone, email or order number.
3. Use the **status filter** to show only Pending, Confirmed, Shipped or Cancelled.

### 11.2 Create an Order (for phone or walk-in customers)
**Menu path:** Sales → Orders → Create Order

1. Search and add products to the order.
2. Enter the customer's **name**, **phone** and **address**.
3. Choose the **shipping** option.
4. Review the totals.
5. Save the order.

### 11.3 Order Details and Status
Open any order from the list to see everything in one place: customer, products, payment, delivery and courier.

**Order statuses:**
- **Pending** — just placed, not confirmed yet.
- **Confirmed** — you accepted the order.
- **Shipped** — the order has been sent out.
- **Cancelled** — the order was cancelled.

**Very important — how stock works with orders:**
- Stock is **not** reduced when an order is placed.
- Stock is reduced **once** when you set the order to **Confirmed** or **Shipped**.
- Stock is **returned** when you set the order to **Cancelled**.
- This happens automatically — you do not need to change stock by hand.

**Editing an order:** On the order details page you can change the status, update the address, change quantities, remove items, or add items, then click **Update Order**. All changes are saved together.

**Deleting orders:** Confirmed or Shipped orders **cannot** be deleted. If you must remove one, set it to **Cancelled** first.

**Payment:** Orders are **Cash on Delivery**. After you receive the money, click the payment button to mark the order **Paid** (and again to mark it **Unpaid** if needed).

---

## 12. Pre Orders

**Menu path:** Sales → Pre Orders

Pre-order lets a customer buy an item that is currently out of stock.

### 12.1 Turn On Pre-Order for a Product Option
1. Go to **Catalog → Products → List Products** and edit the product.
2. For the specific option (variant), turn on **Allow Pre Order**.
3. Save.

### 12.2 Manage Pre Orders
1. Go to **Sales → Pre Orders**.
2. Use the filters: Pending, Stock Available, Processing, Completed, Cancelled.
3. When stock arrives, open the pre-order and click **Process Pre Order**.
   - This reduces stock **once**, confirms the order, and moves it into your normal order flow.

> A pre-order always stays a pre-order in your records, even if you later turn the setting off.

---

## 13. Coupons

**Menu path:** Sales → Coupons

Coupons give customers a discount.

### 13.1 Create a Coupon
**Menu path:** Sales → Coupons → Create Coupon

1. Enter the **coupon code** (for example, `SAVE10`).
2. Give it a **name** and short **description**.
3. Choose the **discount type**: **Fixed amount** or **Percentage**.
4. Enter the **discount amount**.
5. Set a **minimum order amount** (optional).
6. Set **maximum total uses** and **maximum uses per customer** (optional; 0 = unlimited).
7. Save.

### 13.2 Manage Coupons
**Menu path:** Sales → Coupons → Coupon List

- View all coupons, **Edit** them, or **Delete** them.

---

## 14. Shipping Charges

**Menu path:** Sales → Shipping

Set the delivery charge for each district/area.

1. Click **Shipping**.
2. Add a shipping option with its **name** and **charge** for a district.
3. Save.
4. Edit any charge later from the list.

> If a product is marked **Free Delivery**, the customer is charged no shipping for it.

---

## 15. Banners

**Menu path:** Marketing → Banners

Banners are the promotional images on the home page.

1. Click **Banners**.
2. Upload a banner image and add a title/link if needed.
3. Save.
4. Use the **toggle** to make a banner **Active** (shown) or **Inactive** (hidden).
5. Delete banners you no longer need.

---

## 16. Advertisement

**Menu path:** Marketing → Advertisement

Advertisements are placed in fixed spots on the store (slots).

1. Click **Advertisement**.
2. For each slot, upload the image and set its link.
3. Save.
4. Use the **toggle** to switch a slot **Active** or **Inactive**.

---

## 17. Customers (Users)

**Menu path:** Customers → Users

This is your list of customers.

1. Click **Users** to see everyone who registered.
2. Use **Add User** to create an account for a customer if needed.
3. Use **Edit** to update their details.

> Customers can also create their own account from the website (see Section 24).

---

## 18. Content Pages

**Menu path:** Content → Pages

Edit the text pages of your store:

- **About Us**
- **Return Policy**
- **Refund Policy**

1. Click the page you want to edit.
2. Type or update the content using the editor (bold, lists, links, images).
3. Save.

---

## 19. Reports

**Menu path:** Reports

Use reports to understand your business. Every report can be filtered by date, and most can be **exported to Excel/CSV** or **printed**.

- **Reports Dashboard** — an overview of everything.
- **Sales Report** — total sales over time.
- **Product Sales** — sales per product.
- **Product Performance** — best and worst performers.
- **Profit & Loss** — profit after cost.
- **Order Report** — orders and their statuses.
- **Customer Report** — who buys the most.
- **Payment Report** — paid vs unpaid.
- **Inventory Report** — current stock levels.
- **Purchase Report** — what you bought.
- **Purchase Return** — returned items.
- **Coupon Report** — coupon usage.
- **Shipping Report** — delivery charges.

> Cancelled orders are excluded from sales and profit numbers, so your totals stay correct.

---

## 20. Courier (Delivery Company)

**Menu path:** System → Courier

The store can send orders to your delivery company (**Steadfast**). There are two modes:

- **Test Mode** — a practice mode. It works fully **without** any codes and does **not** send real shipments. Use it to learn.
- **Live Mode** — the real mode. It needs the codes from Steadfast and sends real shipments.

### 20.1 Set Up Courier
1. Go to **System → Courier**.
2. Choose **Courier Provider: Steadfast**.
3. Choose the **Environment**:
   - **Test / Mock** (practice) — recommended while learning.
   - **Live** (real) — only after Steadfast gives you your codes.
4. If you chose **Live**, paste your **API Key** and **Secret Key** (the two codes Steadfast provides). They are stored safely and shown only as dots.
5. Leave **Courier integration active** switched on.
6. Click **Save Settings**.

> If you choose Live but leave the codes empty, the system will **not** save it and will ask for the codes. This prevents mistakes.

### 20.2 Test the Connection
1. On the same page, click **Test Connection**.
2. In Test Mode you will see: *"Test connection successful."*
3. In Live Mode it will check your codes are correct.

### 20.3 Send an Order to the Courier
1. Open the order: **Sales → Orders → Order List**, then click the order.
2. Find the **Courier** section on the order page.
3. Check the **COD** amount (the cash the courier will collect). It is calculated automatically.
4. Click **Send to Courier**.
5. The order now shows a **Consignment ID**, **Tracking Code** and a courier status.

> You cannot send the same order twice. If you try, the system says it is already submitted.

### 20.4 Update, Cancel or Simulate
- **Refresh Status** — get the latest courier status.
- **Cancel Shipment** — cancels the courier booking (not the customer order).
- **Test tools** (Test Mode only) — buttons to **Simulate Delivered**, **Hold**, or **Cancelled** so you can see how it looks.

> The courier status is separate from the order status. Changing one does not change the other.

---

## 21. Settings

**Menu path:** System → Settings

This controls how your store looks and how customers contact you.

1. Click **Settings**.
2. **Website Logo** — upload your logo.
3. **Phone Number**, **Email Address**, **Business Address** — shown in the footer and contact areas.
4. **Social Media** — paste your Facebook, Instagram, YouTube, etc. links. Leave a field empty to hide that icon.
5. Click **Save Changes**.

> Your logo, contact details and social links update across the whole website immediately.

---

## 22. Logging Out

1. Click the **logout** option in the top-right corner (or the logout button).
2. Confirm. You are returned to the login page.

> Always log out when using a shared computer.

---

# PART 2 — CUSTOMER STORE

## 23. What Your Customers See

The public website (the storefront) is where shoppers:

- Browse and search products.
- View product details and photos.
- Add items to the cart.
- Apply coupons.
- Place an order with Cash on Delivery.
- Track their order.
- Create an account and view their order history.

---

## 24. Register and Log In

- **Register:** Click **My Account** (top-right) → **Register**. Enter name, email, phone and password.
- **Log In:** Click **My Account** → **Log In**. Customers can log in with **email or phone** and password.
- After logging in, the top-right shows **"Hello, [name]"** with **Dashboard** and **Logout** options.

> Customers can also check out **without** an account. The system creates a basic account for them using their phone number, which they can claim later by registering with the same phone.

---

## 25. Browsing and Searching

- **Home page** shows featured products, banners and categories.
- **Menu** lets customers open a **category** or **sub category**.
- **Search** (magnifier icon) opens a search box — they can type a product name or code.
- **Offer Zone** shows discounted items.

---

## 26. Product Page

On a product page customers can:

- See photos, price and details.
- Choose an option (for example, size or colour).
- Set the **quantity**.
- Click **Add to Cart**.

**Three situations:**
- **In stock** — they can add to cart.
- **Out of stock** — the button is disabled.
- **Pre Order** — if you allowed pre-order, they can order it and see a "Pre Order" note.

---

## 27. Cart and Coupons

1. Open the **cart** (cart icon at the top).
2. Change quantity or remove items.
3. Enter a **coupon code** and click **Apply** to get the discount.
4. The cart shows **Subtotal**, **Shipping**, **Discount** and **Total**.

---

## 28. Checkout and Payment

1. Click **Checkout**.
2. Enter **name**, **phone**, **address** and choose the **district**.
3. The **shipping charge** is added automatically for that district (free if the product qualifies).
4. Review the **Total**.
5. Click to **place the order**.

**Payment is Cash on Delivery (COD)** — the customer pays the delivery person when the order arrives. There is no online payment.

---

## 29. After Ordering (Thank You, Invoice, Track Order)

- **Thank You page** — shown right after the order is placed.
- **Invoice** — a printable summary of the order.
- **Track Order** — customers can check the status by entering their order details; the phone number must match the order.

---

## 30. Customer Account Area

After logging in, customers use the account menu on the left:

- **Dashboard** — a friendly welcome, order counts (Total, Pending, Confirmed, Shipped) and their latest 5 orders.
- **My Orders** — full order history, filterable by status, with a **View Details** button.
- **Order Details** — products with photos, quantities, prices, totals, delivery address and payment status.
- **Profile** — update name, email and phone, and change password.
- **Logout** — sign out safely.

Statuses are always shown in plain words: **Pending, Confirmed, Shipped, Cancelled**.

---

# PART 3 — GOOD PRACTICES

## 31. Daily and Weekly Routine

**Every day**
1. Open the **Dashboard** — check new orders and pending pre-orders.
2. Open **Orders → Order List** — confirm new orders and update statuses.
3. After dispatch, set orders to **Shipped**.
4. Mark orders **Paid** once cash is received.

**Every week**
1. Check **Reports → Inventory Report** for low or out-of-stock items.
2. Record new **Purchases** for stock you received.
3. Review **Reports → Profit & Loss** and **Sales Report**.
4. Update **Banners** and **Advertisement** for current offers.

**Every month**
1. Do a physical stock count and use **Stock Adjustment → Stock Correction** for any differences.
2. Review **Customer Report** and **Coupon Report**.
3. Back up your work (ask your technical contact if unsure).

---

## 32. Important Rules to Remember

1. **Stock is reduced only when an order is Confirmed or Shipped** — not when placed.
2. **Cancelling an order returns the stock automatically.**
3. **Confirmed or Shipped orders cannot be deleted** — cancel them instead.
4. **Price and stock are set outside the product form** — use **Pricing**, **Stock Adjustment** or **Purchase**.
5. **Stock Correction:** you only type the real counted number; the system calculates the change.
6. **Pre-order stock is deducted once** when you click **Process Pre Order**.
7. **Courier Live mode needs your Steadfast codes.** Test mode needs nothing.
8. **Sending an order to the courier twice is blocked** — one active booking per order.
9. **Adjustments and orders are permanent records** — keep them accurate.
10. **Never share your admin password.**

---

## 33. Common Problems and Fixes

| Problem | What to do |
|---|---|
| A product is not visible in the shop | Check the product is **Active** and has a price and stock. |
| Customer cannot add a product to cart | The option is out of stock. Add stock or turn on **Allow Pre Order**. |
| Selling price changed after a purchase | Open **Pricing** and set the price again, or tick **update price** only when you want it changed. |
| Cannot delete a product option | It still has stock or is used in orders/purchases. Reduce stock to zero or keep it. |
| Cannot delete an order | It is Confirmed or Shipped. Set it to **Cancelled** first. |
| Courier says "already submitted" | This order already has a booking. Open it and click **Refresh Status**. |
| Live courier will not save | Both the **API Key** and **Secret Key** must be entered. |
| Forgot admin password | Contact your technical support to reset it. |
| Stock shows a negative number | Use **Stock Adjustment → Stock Correction** and enter the real counted number. |

---

*End of manual.*
