@extends('layout.master')

@section('body')

  @php
    $gallery = $product->gallery_images ?? [];
    if (is_string($gallery))
      $gallery = json_decode($gallery, true) ?: [];
    if (!is_array($gallery))
      $gallery = [];

    $sizePricesJson = json_encode($sizePrices ?? []);

    $imgMain = $product->image ? asset('storage/' . $product->image) : 'https://placehold.co/900x1200?text=No+Image';
    $price = (float) ($product->price ?? 0);
    $oprice = (float) ($product->original_price ?? 0);
    $hasDiscount = ($oprice > 0 && $oprice > $price);

    $stock = (int) ($product->stock ?? $product->quantity ?? $product->qty ?? 0);

    $colorImages = [];

    foreach ($product->colors as $color) {
      $colorImages[$color->id] = $product->images
        ->where('color_id', $color->id)
        ->pluck('image')
        ->map(fn($img) => asset('storage/' . $img))
        ->toArray();
    }
    $variantStock = [];

    foreach ($product->variants as $variant) {
      $variantStock[$variant->color_id][$variant->size] = $variant->stock;
    }
  @endphp



  <div class="pd-wrap">

    <a class="pd-back" href="javascript:history.back()">← Back</a>

    <div class="pd-top">
      {{-- LEFT IMAGE AREA --}}
      <div class="pd-left">
        <div class="pd-gallery">
          <div class="pd-thumbs">
            @if($product->image)
              <img src="{{ asset('storage/' . $product->image) }}" class="pd-thumb active" alt="thumb"
                onclick="changeMainImage(this)">
            @endif

            @foreach($gallery as $img)
              <img src="{{ asset('storage/' . $img) }}" class="pd-thumb" alt="thumb" onclick="changeMainImage(this)">
            @endforeach
          </div>

          <div class="pd-main">
            <img src="{{ $imgMain }}" alt="{{ $product->name }}" id="mainProductImage">
          </div>
        </div>
      </div>

      {{-- RIGHT INFO AREA --}}
      <div class="pd-right">
        <h1 class="pd-title">{{ $product->name }}</h1>

        <div class="pd-price">
          <span class="now">₹{{ number_format($price, 2) }}</span>
          <span class="was" @if(!$hasDiscount) style="display:none;" @endif>₹{{ number_format($oprice, 2) }}</span>
          <span class="off" @if(!$hasDiscount) style="display:none;" @endif>
            @if($oprice > 0 && $oprice > $price)
              {{ number_format((($oprice - $price) / $oprice) * 100, 0) }}% OFF
            @else
              0% OFF
            @endif
          </span>
        </div>

        <hr class="pd-divider">

        {{-- COLOR --}}
        <h4 class="pd-block-title">Color</h4>
        <div class="pd-colors" style="margin-bottom:18px;">
          @foreach($product->colors->unique('id') as $color)
            <label class="pd-color" data-type="color">
              <input type="radio" name="productColor" value="{{ $color->id }}">
              <span class="dot" style="background: {{ $color->code }}"></span>
              <span>{{ $color->name }}</span>
            </label>
          @endforeach
        </div>

        <h4 class="pd-block-title">Size</h4>
        <div class="pd-sizes" id="sizeContainer"></div>

        {{-- QUANTITY --}}
        <div class="pd-qty">
          <div class="pd-qty-row">
            <h4 class="pd-block-title" style="margin:0;">Quantity</h4>
            <div class="pd-stock">
              @if($stock > 0)
                In stock
              @else
                <strong style="color:#b00020;">Out of stock</strong>
              @endif
            </div>
          </div>

          <div style="margin-top:10px; display:flex; align-items:center; justify-content:flex-start;">
            <div class="pd-qty-box" aria-label="Quantity selector">
              <button class="pd-qty-btn" type="button" id="qtyMinus">−</button>
              <input class="pd-qty-input" type="number" id="qtyInput" value="1" min="1" step="1">
              <button class="pd-qty-btn" type="button" id="qtyPlus">+</button>
            </div>
          </div>

          <div class="pd-qty-note" id="qtyNote" style="display:none;"></div>
        </div>

        <div class="pd-cta">
          <button class="pd-btn primary" id="addCartBtn" type="button" onclick="addToCart(false)" @if($stock <= 0) disabled
          @endif>
            ADD TO CART
          </button>
          <button class="pd-btn ghost" id="buyNowBtn" type="button" onclick="buyNow()" @if($stock <= 0) disabled @endif>
            BUY NOW
          </button>
        </div>

        <div class="pd-mini-actions">
          <button class="pd-mini" id="pdWishBtn" type="button" onclick="toggleMainWishlist(this)">
            <span class="icon">♡</span> <span>ADD TO WISHLIST</span>
          </button>
        </div>

        <hr class="pd-divider">

        @if($product->description)
          <div class="pd-desc">
            <h4>Description</h4>
            <p>{{ $product->description }}</p>
          </div>
        @endif
      </div>
    </div>

    {{-- Related Products --}}
    <div class="pd-rail" style="margin-top:60px;">
      <h3 class="pd-rail-title">Related Products</h3>
      <div id="relatedProducts" class="pd-grid">
        <p class="text-muted">Loading...</p>
      </div>
    </div>

    {{-- Recently Viewed --}}
    <div class="pd-rail" style="margin-top:34px;">
      <h3 class="pd-rail-title">Recently Viewed</h3>
      <div id="recentlyViewedProducts" class="pd-grid"></div>
    </div>

  </div>

  <script>
    window.colorImagesData = @json($colorImages);

    const productId = {{ $product->id }};
    const productName = {!! json_encode($product->name) !!} || '';
    const productPrice = {{ $product->price }};
    const productShippingCharge = {{ $product->shipping_charge ?? 0 }};
    const productOriginalPrice = {{ $product->original_price ?? 0 }};
    const productImage = {!! json_encode($imgMain) !!} || '';
    const productCategory = {{ $product->category_id }};
    window.currentStock = {{ $stock ?? 0 }};
    window.sizePricesData = {!! $sizePricesJson !!};


    function updateProductPrice(newPrice, newOriginalPrice = null) {
      const priceElement = document.querySelector('.pd-price .now');
      const originalPriceElement = document.querySelector('.pd-price .was');
      const discountElement = document.querySelector('.pd-price .off');

      if (priceElement) priceElement.textContent = '₹' + parseFloat(newPrice).toFixed(2);

      if (newOriginalPrice && newOriginalPrice > newPrice && newOriginalPrice > 0) {
        if (originalPriceElement) {
          originalPriceElement.textContent = '₹' + parseFloat(newOriginalPrice).toFixed(2);
          originalPriceElement.style.display = 'inline';
        }
        if (discountElement) {
          const discountPercent = ((newOriginalPrice - newPrice) / newOriginalPrice) * 100;
          discountElement.textContent = Math.round(discountPercent) + '% OFF';
          discountElement.style.display = 'inline';
        }
      } else if (newOriginalPrice !== null) {
        if (originalPriceElement) originalPriceElement.style.display = 'none';
        if (discountElement) discountElement.style.display = 'none';
      } else {
        if (productOriginalPrice > newPrice && productOriginalPrice > 0) {
          if (originalPriceElement) {
            originalPriceElement.textContent = '₹' + parseFloat(productOriginalPrice).toFixed(2);
            originalPriceElement.style.display = 'inline';
          }
          if (discountElement) {
            const discountPercent = ((productOriginalPrice - newPrice) / productOriginalPrice) * 100;
            discountElement.textContent = Math.round(discountPercent) + '% OFF';
            discountElement.style.display = 'inline';
          }
        } else {
          if (originalPriceElement) originalPriceElement.style.display = 'none';
          if (discountElement) discountElement.style.display = 'none';
        }
      }
    }

    function changeMainImage(thumb) {
      document.getElementById('mainProductImage').src = thumb.src;
      document.querySelectorAll('.pd-thumb').forEach(t => t.classList.remove('active'));
      thumb.classList.add('active');
    }

    function updateStockUI() {
      const color = document.querySelector('input[name="productColor"]:checked')?.value;
      const size = document.querySelector('input[name="productSize"]:checked')?.value;
      const stockText = document.querySelector('.pd-stock');

      if (!color || !size) {
        stockText.innerHTML = '<span>Select color & size</span>';
        window.currentStock = 0;
        return;
      }

      const variant = window.variantData.find(v => v.color == color && v.size == size);
      let stock = variant ? variant.stock : 0;

      let cartQty = getCartQty(productId, color, size);
      let availableStock = stock - cartQty;

      if (availableStock < 0) availableStock = 0;

      window.currentStock = availableStock;

      document.getElementById('addCartBtn').disabled = !(availableStock > 0);
      document.getElementById('buyNowBtn').disabled = !(availableStock > 0);

      if (availableStock > 0) {
        stockText.innerHTML = `<strong style="color:green;">In Stock (${availableStock})</strong>`;
      } else {
        stockText.innerHTML = `<strong style="color:red;">Out of stock</strong>`;
      }

      document.getElementById('qtyInput').value = 1;

      setupQuantity();
    }

    window.currentStock = 0;
    document.addEventListener('DOMContentLoaded', function () {
      const colors = document.querySelectorAll('.pd-color');
      colors.forEach(lbl => {
        lbl.addEventListener('click', function () {
          colors.forEach(x => x.classList.remove('active'));
          this.classList.add('active');

          const r = this.querySelector('input[type="radio"]');
          if (r) r.checked = true;

          const colorId = r.value;
          renderSizesByColor(colorId);
          window.currentStock = 0;
          updateStockUI();

          if (window.colorImagesData && window.colorImagesData[colorId]) {
            const images = window.colorImagesData[colorId];
            const thumbContainer = document.querySelector('.pd-thumbs');
            const mainImage = document.getElementById('mainProductImage');

            if (images.length > 0) {
              mainImage.src = images[0];
              thumbContainer.innerHTML = images.map((img, index) => `
                                                      <img src="${img}" class="pd-thumb ${index === 0 ? 'active' : ''}" onclick="changeMainImage(this)">
                                                    `).join('');
            }
            else {
              mainImage.src = productImage;
              thumbContainer.innerHTML = `
                            <img src="${productImage}" class="pd-thumb active" onclick="changeMainImage(this)">
                          `;
            }
          }
        });
      });

      window.variantData = @json(
        $product->variants->map(function ($v) {
          return ['color' => $v->color_id, 'size' => $v->size, 'stock' => $v->stock];
        })
      );

      const firstColor = document.querySelector('input[name="productColor"]');
      if (firstColor) {
        firstColor.checked = true;
        const parentLabel = firstColor.closest('.pd-color');
        if (parentLabel) parentLabel.classList.add('active');

        const colorId = firstColor.value;

        if (window.colorImagesData && window.colorImagesData[colorId]) {
          const images = window.colorImagesData[colorId];
          const thumbContainer = document.querySelector('.pd-thumbs');
          const mainImage = document.getElementById('mainProductImage');

          if (images.length > 0) {
            mainImage.src = images[0];
            thumbContainer.innerHTML = images.map((img, index) => `
                                                    <img src="${img}" class="pd-thumb ${index === 0 ? 'active' : ''}" onclick="changeMainImage(this)">
                                                  `).join('');
          }
          else {
            mainImage.src = productImage;
            thumbContainer.innerHTML = `
                            <img src="${productImage}" class="pd-thumb active" onclick="changeMainImage(this)">
                          `;
          }
        }
        renderSizesByColor(colorId);
        selectFirstAvailableSize();
      }



      const mainWishBtn = document.getElementById('pdWishBtn');
      if (mainWishBtn && isWishlisted(productId)) {
        mainWishBtn.classList.add('active');
        mainWishBtn.querySelector('.icon').textContent = '♥';
      }

      window.addEventListener('wishlistUpdated', function () {
        setTimeout(() => {
          const updatedWishBtn = document.getElementById('pdWishBtn');
          if (updatedWishBtn) {
            if (isWishlisted(productId)) {
              updatedWishBtn.classList.add('active');
              updatedWishBtn.querySelector('.icon').textContent = '♥';
            } else {
              updatedWishBtn.classList.remove('active');
              updatedWishBtn.querySelector('.icon').textContent = '♡';
            }
          }
        }, 100);
      });

      setupQuantity();
      addRecentlyViewed();
      displayRecentlyViewed();
      loadRelatedProducts();
    });
    function selectFirstAvailableSize() {
      const sizes = document.querySelectorAll('.pd-size:not(.disabled)');
      if (sizes.length > 0) {
        const first = sizes[0];
        first.classList.add('active');
        const input = first.querySelector('input');
        if (input) input.checked = true;
        updateStockUI();
      }
    }
    function attachSizeEvents() {
      const sizes = document.querySelectorAll('.pd-size');
      sizes.forEach(lbl => {
        lbl.addEventListener('click', function () {
          if (this.classList.contains('disabled')) return;
          sizes.forEach(x => x.classList.remove('active'));
          this.classList.add('active');
          const r = this.querySelector('input');
          r.checked = true;
          updateStockUI();
          document.getElementById('qtyInput').value = 1;
        });
      });
    }

    function renderSizesByColor(colorId) {
      const container = document.getElementById('sizeContainer');
      container.innerHTML = '';

      const sizes = [...new Map(
        window.variantData
          .filter(v => v.color == colorId)
          .map(v => [v.size, v])
      ).values()];

      if (sizes.length === 0) {
        container.innerHTML = '<span>No sizes available</span>';
        return;
      }

      sizes.forEach(v => {
        const disabled = v.stock <= 0 ? 'disabled' : '';
        container.innerHTML += `
                                                <label class="pd-size ${disabled ? 'disabled' : ''}">
                                                  <input type="radio" name="productSize" value="${v.size}" ${disabled}>
                                                  ${v.size}
                                                </label>
                                              `;
      });

      attachSizeEvents();
    }

    function setupQuantity() {
      const minus = document.getElementById('qtyMinus');
      const plus = document.getElementById('qtyPlus');
      const input = document.getElementById('qtyInput');
      const note = document.getElementById('qtyNote');

      if (!minus || !plus || !input) return;

      const newMinus = minus.cloneNode(true);
      const newPlus = plus.cloneNode(true);
      minus.parentNode.replaceChild(newMinus, minus);
      plus.parentNode.replaceChild(newPlus, plus);

      function clamp() {
        let v = parseInt(input.value || '1', 10);
        if (isNaN(v) || v < 1) v = 1;

        let availableStock = window.currentStock; // already adjusted!

        if (v > availableStock) {
          v = availableStock;

          showToast(`Only ${availableStock} items available`, 'warning');
        }

        input.value = v;

        newMinus.disabled = (v <= 1);
        newPlus.disabled = (v >= availableStock);
      }

      newMinus.addEventListener('click', () => { input.value = (parseInt(input.value || '1', 10) || 1) - 1; clamp(); });
      newPlus.addEventListener('click', () => { input.value = (parseInt(input.value || '1', 10) || 1) + 1; clamp(); });
      input.addEventListener('input', clamp);
      input.addEventListener('blur', clamp);
      clamp();
    }

    function getSelectedQty() {
      const input = document.getElementById('qtyInput');
      let qty = parseInt(input?.value || '1', 10);
      if (isNaN(qty) || qty < 1) qty = 1;
      if (window.currentStock && qty > window.currentStock) qty = window.currentStock;
      return qty;
    }

    function addToCart(redirect = false) {

      const colorInput = document.querySelector('input[name="productColor"]:checked');
      const sizeInput = document.querySelector('input[name="productSize"]:checked');

      const selectedColor = colorInput ? colorInput.value : null;
      const selectedSize = sizeInput ? sizeInput.value : null;
      const selectedImage = document.getElementById('mainProductImage').src.replace(window.location.origin, ''); if (!selectedColor || !selectedSize) {
        document.getElementById('addCartBtn').disabled = true;
        showToast('Please select color and size', 'error');
        return;
      }

      let selectedPrice = productPrice;
      let selectedOriginalPrice = productOriginalPrice;

      if (selectedSize && window.sizePricesData && window.sizePricesData[selectedSize]) {
        if (typeof window.sizePricesData[selectedSize] === 'object') {
          selectedPrice = parseFloat(window.sizePricesData[selectedSize].price);
          if (window.sizePricesData[selectedSize].original_price !== undefined) {
            selectedOriginalPrice = parseFloat(window.sizePricesData[selectedSize].original_price);
          }
        } else {
          selectedPrice = parseFloat(window.sizePricesData[selectedSize]);
        }
      }
      const selectedColorName = document.querySelector('input[name="productColor"]:checked')
        ?.closest('.pd-color')
        ?.querySelector('span:last-child')?.innerText;
      const qtyToAdd = getSelectedQty();

      const product = {
        id: productId,
        name: productName,
        price: selectedPrice,
        shipping_charge: productShippingCharge || 0,
        image: selectedImage,
        category: productCategory,
        color: selectedColorName,
        color_id: selectedColor,
        size: selectedSize,
        qty: qtyToAdd,
        stock: window.currentStock

      };

      let cart = JSON.parse(localStorage.getItem('cart')) || [];

      cart = cart.filter(item => item && item.id);

      const idx = cart.findIndex(item =>
        item.id == product.id &&
        item.color_id == selectedColor &&
        item.size == selectedSize
      );

      if (idx > -1) {
        let currentQty = parseInt(cart[idx].qty || 0);

        let newQty = currentQty + qtyToAdd;

        if (newQty > window.currentStock) {
          newQty = window.currentStock;

          showToast('Stock limit reached', 'warning');


        }

        cart[idx].qty = newQty;

      } else {
        let cartQty = getCartQty(productId, selectedColor, selectedSize);
        let availableStock = window.currentStock - cartQty;

        if (qtyToAdd > availableStock) {
          showToast('Only limited quantity available', 'warning');
          return;
        }

        cart.push(product);
      }

      const isLoggedIn = {!! json_encode(auth()->check()) !!};

      if (isLoggedIn) {
        fetch('/cart/add', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          },
          body: JSON.stringify(product)
        });

      } else {
        localStorage.setItem('cart', JSON.stringify(cart));
      }




      window.dispatchEvent(new CustomEvent('cartUpdated'));

      showToast(product.name + ' added to cart', 'success');
      if (redirect) window.location.href = '/checkout';
    }
    function getCartQty(productId, colorId, size) {
      let cart = JSON.parse(localStorage.getItem('cart')) || [];

      const item = cart.find(i =>
        i.id == productId &&
        i.color_id == colorId &&
        i.size == size
      );

      return item ? parseInt(item.qty || 0) : 0;
    }
    function showCartVerificationPopup(productName, quantity) {
      const existingPopup = document.getElementById('cartVerificationPopup');
      if (existingPopup) existingPopup.remove();

      const popupHtml = `
                                              <div id="cartVerificationPopup" class="cart-verification-popup">
                                                <div class="cart-verification-content">
                                                  <div class="cart-verification-icon">✓</div>
                                                  <h3 class="cart-verification-title">Added to Cart!</h3>
                                                  <p class="cart-verification-message">${productName} (${quantity}) has been added to your cart successfully.</p>
                                                  <div class="cart-verification-actions">
                                                    <button class="cart-verification-btn continue" onclick="closeCartVerificationPopup()">Continue Shopping</button>
                                                    <button class="cart-verification-btn view-cart" onclick="viewCartAndClose()">View Cart</button>
                                                  </div>
                                                </div>
                                              </div>
                                            `;

      document.body.insertAdjacentHTML('beforeend', popupHtml);
      setTimeout(() => { closeCartVerificationPopup(); }, 3000);
    }

    function closeCartVerificationPopup() {
      const popup = document.getElementById('cartVerificationPopup');
      if (popup) {
        popup.style.opacity = '0';
        popup.style.transform = 'scale(0.9)';
        setTimeout(() => { if (popup) popup.remove(); }, 300);
      }
    }

    function viewCartAndClose() {
      closeCartVerificationPopup();
      window.location.href = '/cart';
    }

    function buyNow() {
      if (!{!! json_encode($isLoggedIn ?? false) !!}) {
        window.location.href = '/register';
        return;
      }
      addToCart(true);
    }

    function toggleMainWishlist(btn) {
      const product = { id: productId, name: productName, price: productPrice, image: productImage, category: productCategory };
      let wishlist = JSON.parse(localStorage.getItem("wishlist")) || [];
      const idx = wishlist.findIndex(item => String(item.id) === String(product.id));

      if (idx > -1) {
        wishlist.splice(idx, 1);
        btn.classList.remove('active');
        btn.querySelector('.icon').textContent = '♡';
      } else {
        wishlist.push(product);
        btn.classList.add('active');
        btn.querySelector('.icon').textContent = '♥';
      }
      localStorage.setItem("wishlist", JSON.stringify(wishlist));
      window.dispatchEvent(new CustomEvent("wishlistUpdated"));
    }

    function addRecentlyViewed() {
      const currentProduct = { id: productId, name: productName, price: productPrice, image: productImage, category: productCategory };
      let recentlyViewed = JSON.parse(localStorage.getItem('recentlyViewed')) || [];
      recentlyViewed = recentlyViewed.filter(item => String(item.id) !== String(currentProduct.id));
      recentlyViewed.unshift(currentProduct);
      if (recentlyViewed.length > 15) recentlyViewed = recentlyViewed.slice(0, 15);
      localStorage.setItem('recentlyViewed', JSON.stringify(recentlyViewed));
    }

    function isWishlisted(id) {
      const wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];
      return wishlist.some(item => String(item.id) === String(id));
    }

    function toggleCardWishlist(btn, id, name, price, image, category) {
      let wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];
      const idx = wishlist.findIndex(item => String(item.id) === String(id));
      if (idx > -1) {
        wishlist.splice(idx, 1);
        btn.classList.remove('active');
        btn.textContent = '♡';
      } else {
        wishlist.push({ id, name, price: parseFloat(price), image, category });
        btn.classList.add('active');
        btn.textContent = '♥';
      }
      localStorage.setItem('wishlist', JSON.stringify(wishlist));
      window.dispatchEvent(new CustomEvent('wishlistUpdated'));
    }

    function railCardHTML(p) {
      const id = p.id;
      const name = (p.name || '').replace(/'/g, "\\'");
      const price = Number(p.price || 0).toFixed(2);
      const image = p.image || 'https://placehold.co/600x800?text=No+Image';
      const category = String(p.category || '').replace(/'/g, "\\'");
      const active = isWishlisted(id) ? 'active' : '';
      const heart = isWishlisted(id) ? '♥' : '♡';

      return `
                                              <div class="pd-card">
                                                <div class="img">
                                                  <a href="/product/${id}">
                                                    <img src="${image}" alt="${name}" onerror="this.onerror=null;this.src='https://placehold.co/600x800?text=No+Image';">
                                                  </a>
                                                  <button class="pd-wbtn ${active}" type="button"
                                                    onclick="event.preventDefault(); event.stopPropagation(); toggleCardWishlist(this,'${id}','${name}','${price}','${image}','${category}')">${heart}</button>
                                                </div>
                                                <div class="meta">
                                                  <a href="/product/${id}"><p class="name" title="${name}">${name}</p></a>
                                                  <div class="p">₹${price}</div>
                                                </div>
                                              </div>
                                            `;
    }

    function displayRecentlyViewed() {
      const recentlyViewed = (JSON.parse(localStorage.getItem('recentlyViewed')) || []).slice(0, 15);
      const container = document.getElementById('recentlyViewedProducts');
      if (!container) return;
      if (recentlyViewed.length === 0) {
        container.innerHTML = '<p style="color:#777;">No recently viewed products.</p>';
        return;
      }
      container.innerHTML = recentlyViewed.filter(p => p && p.id).map(railCardHTML);
    }

    function loadRelatedProducts() {
      console.log("🔥 Related products function called");

      const cat = encodeURIComponent(productCategory);
      const pid = encodeURIComponent(productId);

      console.log("Category:", cat, "Product:", pid);

      fetch(`/products/related/${cat}/${pid}`)
        .then(res => res.json())
        .then(data => {
          console.log("API Response:", data);

          const container = document.getElementById('relatedProducts');

          if (!container) return;

          if (data.success && data.products.length > 0) {
            container.innerHTML = data.products.map(railCardHTML).join('');
          } else {
            container.innerHTML = '<p style="color:#777;">No related products found.</p>';
          }
        })
        .catch(err => {
          console.error("Related Products Error:", err);
          document.getElementById('relatedProducts').innerHTML =
            '<p style="color:red;">Error loading related products</p>';
        });
    }
  </script>

  @include('footer')
@endsection