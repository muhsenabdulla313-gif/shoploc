@extends('layout.master')

@section('body')

@php
    $gallery = $product->gallery_images ?? [];
    if (is_string($gallery)) $gallery = json_decode($gallery, true) ?: [];
    if (!is_array($gallery)) $gallery = [];

    $sizePricesJson = json_encode($sizePrices ?: []);

    // ✅ helpers
    $imgMain = $product->image ? asset('storage/' . $product->image) : 'https://placehold.co/900x1200?text=No+Image';
    $price = (float)($product->price ?? 0);
    $oprice = (float)($product->original_price ?? 0);
    $hasDiscount = ($oprice > 0 && $oprice > $price);

    $stock = (int)($product->stock ?? $product->quantity ?? $product->qty ?? 0);
@endphp

<div class="pd-wrap">

  <a class="pd-back" href="javascript:history.back()">← Back</a>

  <div class="pd-top">
    {{-- LEFT IMAGE AREA --}}
    <div class="pd-left">
      <div class="pd-gallery">
        <div class="pd-thumbs">
          @if($product->image)
            <img src="{{ asset('storage/' . $product->image) }}"
                 class="pd-thumb active"
                 alt="thumb"
                 onclick="changeMainImage(this)">
          @endif

          @foreach($gallery as $img)
            <img src="{{ asset('storage/' . $img) }}"
                 class="pd-thumb"
                 alt="thumb"
                 onclick="changeMainImage(this)">
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
      <div class="pd-sub">{{ $product->category ?? 'PRODUCT' }}</div>

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
        <label class="pd-color" data-type="color">
          <input type="radio" name="productColor" value="Black">
          <span class="dot" style="background:#000"></span><span>Black</span>
        </label>
        <label class="pd-color" data-type="color">
          <input type="radio" name="productColor" value="White">
          <span class="dot" style="background:#fff"></span><span>White</span>
        </label>
        <label class="pd-color" data-type="color">
          <input type="radio" name="productColor" value="Blue">
          <span class="dot" style="background:#2b6cff"></span><span>Blue</span>
        </label>
        <label class="pd-color" data-type="color">
          <input type="radio" name="productColor" value="Red">
          <span class="dot" style="background:#dc3545"></span><span>Red</span>
        </label>
      </div>

      {{-- SIZE --}}
      <h4 class="pd-block-title">Size</h4>
      <div class="pd-sizes">
        @if($product->sizes && is_array($product->sizes) && count($product->sizes) > 0)
          @foreach($product->sizes as $size)
            <label class="pd-size" data-type="size">
              <input type="radio" name="productSize" value="{{ $size }}">{{ $size }}
            </label>
          @endforeach
        @else
          <label class="pd-size" data-type="size"><input type="radio" name="productSize" value="S">S</label>
          <label class="pd-size" data-type="size"><input type="radio" name="productSize" value="M">M</label>
          <label class="pd-size" data-type="size"><input type="radio" name="productSize" value="L">L</label>
          <label class="pd-size" data-type="size"><input type="radio" name="productSize" value="XL">XL</label>
        @endif
      </div>

      {{-- QUANTITY --}}
      <div class="pd-qty">
        <div class="pd-qty-row">
          <h4 class="pd-block-title" style="margin:0;">Quantity</h4>
          <div class="pd-stock">
            @if($stock > 0)
              <!-- In stock: <strong>{{ $stock }}</strong> -->
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
        <button class="pd-btn primary" id="addCartBtn" type="button" onclick="addToCart(false)" @if($stock <= 0) disabled @endif>
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
  const productId = {{ $product->id }};
  const productName = {!! json_encode($product->name) !!} || '';
  const productPrice = {{ $product->price }};
  const productShippingCharge = {{ $product->shipping_charge ?? 0 }};
  const productOriginalPrice = {{ $product->original_price ?? 0 }};
  const productImage = {!! json_encode($imgMain) !!} || '';
  const productCategory = {!! json_encode($product->category) !!} || '';
  const productStock = {{ $stock ?? 0 }};
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

  document.addEventListener('DOMContentLoaded', function () {

    const colors = document.querySelectorAll('.pd-color');
    colors.forEach(lbl => {
      lbl.addEventListener('click', function(){
        colors.forEach(x => x.classList.remove('active'));
        this.classList.add('active');
        const r = this.querySelector('input[type="radio"]');
        if (r) r.checked = true;
      });
    });

    const sizes = document.querySelectorAll('.pd-size');
    sizes.forEach(lbl => {
      lbl.addEventListener('click', function(){
        sizes.forEach(x => x.classList.remove('active'));
        this.classList.add('active');
        const r = this.querySelector('input[type="radio"]');
        if (r) r.checked = true;

        const selectedSize = r.value;
        if (window.sizePricesData && window.sizePricesData[selectedSize]) {
          if (window.sizePricesData[selectedSize].price !== undefined) {
            const sizePrice = parseFloat(window.sizePricesData[selectedSize].price);
            const sizeOriginalPrice = window.sizePricesData[selectedSize].original_price !== undefined
              ? parseFloat(window.sizePricesData[selectedSize].original_price) : null;
            updateProductPrice(sizePrice, sizeOriginalPrice);
          } else {
            updateProductPrice(parseFloat(window.sizePricesData[selectedSize]));
          }
        } else {
          updateProductPrice(productPrice);
        }
      });
    });

    const mainWishBtn = document.getElementById('pdWishBtn');
    if (mainWishBtn && isWishlisted(productId)) {
      mainWishBtn.classList.add('active');
      mainWishBtn.querySelector('.icon').textContent = '♥';
    }

    window.addEventListener('wishlistUpdated', function() {
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

  function setupQuantity(){
    const minus = document.getElementById('qtyMinus');
    const plus  = document.getElementById('qtyPlus');
    const input = document.getElementById('qtyInput');
    const note  = document.getElementById('qtyNote');

    function clamp(){
      let v = parseInt(input.value || '1', 10);
      if (isNaN(v) || v < 1) v = 1;

      if (productStock > 0 && v > productStock) {
        v = productStock;
        note.style.display = '';
        note.textContent = `Max available quantity is ${productStock}.`;
      } else {
        note.style.display = 'none';
        note.textContent = '';
      }

      input.value = v;
      minus.disabled = (v <= 1);
      if (productStock > 0) plus.disabled = (v >= productStock);
      else plus.disabled = false;
    }

    if (!minus || !plus || !input) return;

    minus.addEventListener('click', () => {
      input.value = (parseInt(input.value || '1', 10) || 1) - 1;
      clamp();
    });

    plus.addEventListener('click', () => {
      input.value = (parseInt(input.value || '1', 10) || 1) + 1;
      clamp();
    });

    input.addEventListener('input', clamp);
    input.addEventListener('blur', clamp);

    if (productStock <= 0) {
      input.value = 1;
      input.disabled = true;
      minus.disabled = true;
      plus.disabled = true;
      if (note) {
        note.style.display = '';
        note.textContent = 'This item is currently out of stock.';
      }
      return;
    }
    clamp();
  }

  function getSelectedQty(){
    const input = document.getElementById('qtyInput');
    let qty = parseInt(input?.value || '1', 10);
    if (isNaN(qty) || qty < 1) qty = 1;
    if (productStock > 0 && qty > productStock) qty = productStock;
    return qty;
  }

  function addToCart(redirect = false) {
    // Check if referral code is required and present
    if (redirect) { // Only check for referral code when redirecting to checkout
      const hasReferralCode = localStorage.getItem('referral_code') || document.cookie.includes('referral_code');
      if (!hasReferralCode) {
        // Redirect to WhatsApp to get referral code since it's required for purchase
        const whatsappNumber = '918848748469';
        const message = encodeURIComponent(
          "Hello 👋\n" +
          "I need a referral code.\n" +
          "I'm trying to make a purchase on the website.\n" +
          "Please share a valid referral code."
        );
        window.open(`https://wa.me/${whatsappNumber}?text=${message}`, '_blank');
        return;
      }
    }

    const colorInput = document.querySelector('input[name="productColor"]:checked');
    const sizeInput  = document.querySelector('input[name="productSize"]:checked');

    const selectedColor = colorInput ? colorInput.value : null;
    const selectedSize  = sizeInput ? sizeInput.value : null;

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

    const qtyToAdd = getSelectedQty();

    const product = {
      id: productId,
      name: productName,
      price: selectedPrice,
      original_price: selectedOriginalPrice,
      shipping_charge: productShippingCharge || 0,
      image: productImage,
      category: productCategory,
      color: selectedColor,
      size: selectedSize
    };

    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    const idx = cart.findIndex(item => String(item.id) === String(product.id));

    if (idx > -1) cart[idx].qty = (parseInt(cart[idx].qty || 0, 10) || 0) + qtyToAdd;
    else cart.push({ ...product, qty: qtyToAdd });

    if (productStock > 0) {
      cart = cart.map(it => {
        if (String(it.id) === String(productId)) it.qty = Math.min(parseInt(it.qty || 1, 10) || 1, productStock);
        return it;
      });
    }

    localStorage.setItem('cart', JSON.stringify(cart));
    window.dispatchEvent(new CustomEvent('cartUpdated'));

    // Show custom verification popup instead of alert
    showCartVerificationPopup(product.name, qtyToAdd);
    
    if (redirect) window.location.href = '/checkout';
  }

  // Custom verification popup function
  function showCartVerificationPopup(productName, quantity) {
    // Remove existing popup if any
    const existingPopup = document.getElementById('cartVerificationPopup');
    if (existingPopup) existingPopup.remove();

    // Create popup HTML
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

    // Add popup to DOM
    document.body.insertAdjacentHTML('beforeend', popupHtml);

    // Auto close after 3 seconds
    setTimeout(() => {
      closeCartVerificationPopup();
    }, 3000);
  }

  function closeCartVerificationPopup() {
    const popup = document.getElementById('cartVerificationPopup');
    if (popup) {
      popup.style.opacity = '0';
      popup.style.transform = 'scale(0.9)';
      setTimeout(() => {
        if (popup) popup.remove();
      }, 300);
    }
  }

  function viewCartAndClose() {
    closeCartVerificationPopup();
    window.location.href = '/cart';
  }

  function buyNow(){ 
    // Check if user is logged in
    if (!{!! json_encode($isLoggedIn ?? false) !!}) {
      // Redirect to registration page if not logged in
      window.location.href = '/register';
      return;
    }
    
    // If logged in, proceed with adding to cart and redirecting to checkout
    addToCart(true); 
  }

  function toggleMainWishlist(btn){
    const product = { id: productId, name: productName, price: productPrice, image: productImage, category: productCategory };
    let wishlist = JSON.parse(localStorage.getItem("wishlist")) || [];
    const idx = wishlist.findIndex(item => String(item.id) === String(product.id));

    if (idx > -1){
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

  // ✅✅ Change here: 12 -> 15
  function addRecentlyViewed() {
    const currentProduct = { id: productId, name: productName, price: productPrice, image: productImage, category: productCategory };
    let recentlyViewed = JSON.parse(localStorage.getItem('recentlyViewed')) || [];
    recentlyViewed = recentlyViewed.filter(item => String(item.id) !== String(currentProduct.id));
    recentlyViewed.unshift(currentProduct);

    // ✅ keep max 15
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

  function railCardHTML(p){
    const id = p.id;
    const name = (p.name || '').replace(/'/g, "\\'");
    const price = Number(p.price || 0).toFixed(2);
    const image = p.image || 'https://placehold.co/600x800?text=No+Image';
    const category = (p.category || '').replace(/'/g, "\\'");
    const active = isWishlisted(id) ? 'active' : '';
    const heart = isWishlisted(id) ? '♥' : '♡';

    return `
      <div class="pd-card">
        <div class="img">
          <a href="/product/${id}">
            <img src="${image}" alt="${name}"
              onerror="this.onerror=null;this.src='https://placehold.co/600x800?text=No+Image';">
          </a>
          <button class="pd-wbtn ${active}" type="button"
            onclick="event.preventDefault(); event.stopPropagation(); toggleCardWishlist(this,'${id}','${name}','${price}','${image}','${category}')">${heart}</button>
        </div>
        <div class="meta">
          <a href="/product/${id}">
            <p class="name" title="${name}">${name}</p>
          </a>
          <div class="p">₹${price}</div>
        </div>
      </div>
    `;
  }

  function displayRecentlyViewed() {
    // ✅ safety: show only 15
    const recentlyViewed = (JSON.parse(localStorage.getItem('recentlyViewed')) || []).slice(0, 15);
    const container = document.getElementById('recentlyViewedProducts');
    if (!container) return;

    if (recentlyViewed.length === 0) {
      container.innerHTML = '<p style="color:#777;">No recently viewed products.</p>';
      return;
    }

    container.innerHTML = recentlyViewed.map(railCardHTML).join('');
  }

  function loadRelatedProducts() {
    const cat = encodeURIComponent(productCategory);
    const pid = encodeURIComponent(productId);

    fetch(`/api/products/related/${cat}/${pid}`)
      .then(r => r.json())
      .then(data => {
        const container = document.getElementById('relatedProducts');
        if (!container) return;

        if (data.success && data.products && data.products.length > 0) {
          container.innerHTML = data.products.map(railCardHTML).join('');
        } else {
          container.innerHTML = '<p style="color:#777;">No related products found.</p>';
        }
      })
      .catch(() => {
        const container = document.getElementById('relatedProducts');
        if (container) container.innerHTML = '<p style="color:#777;">Error loading related products.</p>';
      });
  }
</script>

@include('footer')
@endsection

@push('styles')
<style>
/* Custom Cart Verification Popup Styles */
.cart-verification-popup {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  backdrop-filter: blur(5px);
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 1;
  transition: opacity 0.3s ease;
}

.cart-verification-content {
  background: white;
  border-radius: 16px;
  padding: 30px;
  text-align: center;
  max-width: 400px;
  width: 90%;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
  transform: scale(1);
  transition: transform 0.3s ease;
  /* Remove conflicting positioning - let flexbox handle centering */
}

.cart-verification-icon {
  width: 60px;
  height: 60px;
  background: #4CAF50;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 20px;
  font-size: 30px;
  color: white;
  font-weight: bold;
}

.cart-verification-title {
  font-size: 24px;
  font-weight: 600;
  color: #333;
  margin: 0 0 10px;
}

.cart-verification-message {
  font-size: 16px;
  color: #666;
  margin: 0 0 25px;
  line-height: 1.5;
}

.cart-verification-actions {
  display: flex;
  gap: 12px;
  justify-content: center;
}

.cart-verification-btn {
  padding: 12px 24px;
  border: none;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
}

.cart-verification-btn.continue {
  background: #f5f5f5;
  color: #333;
}

.cart-verification-btn.continue:hover {
  background: #e0e0e0;
}

.cart-verification-btn.view-cart {
  background: #007bff;
  color: white;
}

.cart-verification-btn.view-cart:hover {
  background: #0056b3;
}

/* Responsive styles */
@media (max-width: 480px) {
  .cart-verification-content {
    padding: 20px;
    margin: 20px;
  }
  
  .cart-verification-title {
    font-size: 20px;
  }
  
  .cart-verification-message {
    font-size: 14px;
  }
  
  .cart-verification-actions {
    flex-direction: column;
  }
  
  .cart-verification-btn {
    width: 100%;
  }
}
</style>
@endpush
