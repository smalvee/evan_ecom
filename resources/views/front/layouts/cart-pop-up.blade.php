  <?php
  
  use App\Models\ProductImage;
  use App\Models\ProductVariant;
  use App\Models\NewProduct;
  use Gloudemans\Shoppingcart\Facades\Cart;
  
  ?>

  <ul class="items-image">
      @if (!empty($cartContent) && count($cartContent) > 0)
          {{-- @foreach ($cartContent as $item)
              @php
                  $product_image = ProductImage::where('product_id', $item->id)->first();
                  $get_product_id = ProductVariant::where('id', $item->id)->first();
                  $get_product_info = NewProduct::where('id', $get_product_id->product_id)->first();
              @endphp
              <li>
                
                  @if (!empty($product_image))
                      <img src="{{ asset('uploads/products/small/' . $product_image->image) }}" class="blur-up lazyload"
                          alt="">
                  @endif
              </li>
          @endforeach --}}
      @endif

  </ul>
  {{-- <button onclick="location.href = '{{ route('front.cart') }}';" class="btn item-button btn-sm fw-bold">৳
      {{ Cart::subtotal() }}</button> --}}
