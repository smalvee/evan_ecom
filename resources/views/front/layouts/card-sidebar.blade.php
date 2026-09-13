  <?php
  
  use App\Models\ProductImage;
  use App\Models\ProductVariant;
  use App\Models\NewProduct;
  use Gloudemans\Shoppingcart\Facades\Cart;
  
  ?>
  <ul class="cart-list">

      @if (!empty($cartContent) && count($cartContent) > 0)
          @foreach ($cartContent as $item)
              @php
                  $product_image = ProductImage::where('product_id', $item->id)->first();
                  $get_product_id = ProductVariant::where('id', $item->id)->first();
                  $get_product_info = NewProduct::where('id', $get_product_id->product_id)->first();
              @endphp




              <li class="product-box-contain">

                  <div class="drop-cart">
                      <a href="#" class="drop-image">

                          @if (!empty($product_image))
                              <img src="{{ asset('uploads/products/small/' . $product_image->image) }}" @endif
                              class="blur-up lazyload" alt="">

                      </a>

                      <div class="drop-contain">
                          <a href="#">
                              <h5>{{ $get_product_info->name }}</h5>
                          </a>
                          <h6><span>{{ $item->qty }} x</span>
                              {{ $get_product_id->selling_price }} Tk
                          </h6>
                          @if ($item->options->isPreOrder)
                              <span class="badge bg-warning text-dark" style="font-size:10px;">Pre Order</span>
                          @endif
                          <button class="close-button close_button" data-rowid="{{ $item->rowId }}">
                              <i class="fa-solid fa-xmark"></i>
                          </button>
                      </div>
                  </div>
              </li>
          @endforeach
      @endif
  </ul>

  <div class="price-box">
      <h5>Total :</h5>
      <h4 class="theme-color fw-bold">{{ Cart::subtotal() }} Tk</h4>
  </div>

  <div class="button-group">
      <a href="{{ route('front.cart') }}" class="btn btn-sm cart-button">View Cart</a>
      {{-- <a href="checkout.html"
          class="btn btn-sm cart-button theme-bg-color
                                                    text-white">Checkout</a> --}}
  </div>
