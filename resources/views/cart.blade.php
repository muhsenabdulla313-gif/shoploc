@extends('layout.master')

@section('body')
  <div class="cart-page">
    <div class="container cart-container">

      <div class="cart-title">My Cart</div>

      <div class="cart-layout" id="cartContainer">

        <div class="cart-left">
          <div class="cart-card">
            <div class="cart-card-head">
              <div class="cart-card-head-title" id="cartCountTitle">My Cart (0)</div>
            </div>

            <div class="cart-items" id="cartItems">
              <div class="cart-empty">
                <div class="cart-empty-icon"><i class="fa-solid fa-cart-shopping"></i></div>
                <h4>Your cart is empty</h4>
                <p>Looks like you haven't added anything yet.</p>
                <a href="/" class="btn btn-dark">Continue Shopping</a>
              </div>
            </div>
          </div>
        </div>
        <div class="sum-row" style="margin-bottom:10px;">
          <input type="text" id="couponInput" class="form-control" placeholder="Enter referral code (optional)">

          <button id="applyCouponBtn" class="btn btn-dark" style="margin-left:10px;">
            Apply
          </button>
        </div>

        <div style="margin-top:8px;">
          <label style="font-size:13px;">
            <input type="checkbox" id="noReferralCheck">
            Continue without referral
          </label>
        </div>

        <div id="couponMessage" style="font-size:13px;"></div>


        <div class="cart-right">
          <div class="cart-card">
            <div class="cart-card-head">
              <div class="cart-card-head-title">Your Order</div>
            </div>

            <div class="summary-body">
              <div class="sum-row">
                <span>Subtotal</span>
                <strong id="cartSubtotal">₹0.00</strong>
              </div>

              <div class="sum-row sum-row-muted">
                <span>Delivery</span>
                <strong id="cartShipping">Free</strong>
              </div>

              <div class="sum-row sum-row-muted">
                <span>Tax</span>
                <strong id="cartTax">₹0.00</strong>
              </div>



              <div class="sum-divider"></div>

              <div class="sum-total">
                <span>Total Payable</span>
                <strong id="cartTotal">₹0.00</strong>
              </div>

              <button class="checkout-btn" id="checkoutBtn" disabled>
                PROCEED TO CHECKOUT
              </button>

              <div class="sum-footnote">
                Shipping calculated at checkout
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

  @include('footer')

  <script>
    document.addEventListener('DOMContentLoaded', function () {

      const cartItemsContainer = document.getElementById('cartItems');
      const cartCountTitle = document.getElementById('cartCountTitle');

      const elSubtotal = document.getElementById('cartSubtotal');
      const elShipping = document.getElementById('cartShipping');
      const elTax = document.getElementById('cartTax');
      const elTotal = document.getElementById('cartTotal');

      const discountRow = document.getElementById('discountRow');
      const elDiscount = document.getElementById('cartDiscount');

      const checkoutBtn = document.getElementById('checkoutBtn');
      checkoutBtn.disabled = true;
      let appliedDiscount = 0;

      function safeParse(json) {
        try { return JSON.parse(json); } catch (e) { return null; }
      }
      let appliedCoupon = null;

      async function applyCoupon(code) {
        const messageEl = document.getElementById('couponMessage');

        code = (code || '').trim();

        if (!code) {
          messageEl.innerHTML = "<span style='color:red;'>Enter code</span>";
          return;
        }

        try {
          const res = await fetch('/apply-referral', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ code: code })
          });

          const data = await res.json();

          if (data.success) {
            appliedCoupon = data.staff_id;

            messageEl.innerHTML = "<span style='color:green;'>Referral applied</span>";
            checkoutBtn.disabled = false;

            // ❗ uncheck "no referral"
            document.getElementById('noReferralCheck').checked = false;

          } else {
            appliedCoupon = null;

            messageEl.innerHTML = "<span style='color:red;'>Invalid code</span>";
            checkoutBtn.disabled = true;
          }

        } catch (e) {
          messageEl.innerHTML = "<span style='color:red;'>Error</span>";
        }
      }
      document.getElementById('noReferralCheck').addEventListener('change', function () {
        const messageEl = document.getElementById('couponMessage');

        if (this.checked) {
          appliedCoupon = "NO_REFERRAL"; // ✅ internal flag

          messageEl.innerHTML = "<span style='color:gray;'>Continuing without referral</span>";
          checkoutBtn.disabled = false;

          // ❗ clear input
          document.getElementById('couponInput').value = "";
        } else {
          appliedCoupon = null;
          checkoutBtn.disabled = true;
        }
      });

      document.getElementById('applyCouponBtn').addEventListener('click', function () {
        const code = document.getElementById('couponInput').value;
        applyCoupon(code);
      });
      // ✅ cart read: supports "cart", "cartItems", and {items:[]}
      function readCart() {
        let raw = localStorage.getItem('cart');
        let data = safeParse(raw);

        if (!data) {
          data = safeParse(raw);
        }

        if (!data) return [];

        // if stored as {items:[...]}
        if (data && !Array.isArray(data) && Array.isArray(data.items)) return data.items;

        return Array.isArray(data) ? data : [];
      }

      function writeCart(cart) {
        localStorage.setItem('cart', JSON.stringify(cart));
        window.dispatchEvent(new CustomEvent('cartUpdated'));
      }
      function normalizeImg(item) {
        if (!item || !item.image) {
          return 'https://placehold.co/200x200?text=No+Image';
        }

        let img = String(item.image).trim();

        // ✅ if already full URL → use directly
        if (img.startsWith('http')) return img;

        // ✅ if already correct storage path
        if (img.startsWith('/storage')) return img;

        // ✅ otherwise add storage
        return '/storage/' + img;
      }

      function calcSummary(cart) {
        let subtotal = 0;

        cart.forEach(i => {
          subtotal += Number(i.price || 0) * Number(i.qty || 1);
        });

        const shipping = 0;
        const tax = 0;

        const total = subtotal + shipping + tax;

        return { subtotal, shipping, tax, total };
      }

    function updateSummary(cart){
  const { subtotal, shipping, tax, total } = calcSummary(cart);

  elSubtotal.textContent = '₹' + subtotal.toFixed(2);
  elShipping.textContent = (shipping === 0 && subtotal > 0) ? 'Free' : '₹' + shipping.toFixed(2);
  elTax.textContent = '₹' + tax.toFixed(2);
  elTotal.textContent = '₹' + total.toFixed(2);

  // ✅ correct condition
  if (cart.length === 0) {
    checkoutBtn.disabled = true;
  } else if (!appliedCoupon) {
    checkoutBtn.disabled = true;
  } else {
    checkoutBtn.disabled = false;
  }
}

      function renderCart() {
        const cart = readCart();
        cartCountTitle.textContent = `My Cart (${cart.length})`;

        if (cart.length === 0) {
          cartItemsContainer.innerHTML = `
              <div class="cart-empty">
                <div class="cart-empty-icon"><i class="fa-solid fa-cart-shopping"></i></div>
                <h4>Your cart is empty</h4>
                <p>Looks like you haven't added anything yet.</p>
                <a href="/" class="btn btn-dark">Continue Shopping</a>
              </div>
            `;
          appliedDiscount = 0;
          updateSummary(cart);
          return;
        }

        let html = '';
        cart.forEach((item, index) => {
          const imgSrc = normalizeImg(item);
          const qty = Number(item.qty || 1);
          const price = Number(item.price || 0);
          const shipping = Number(item.shipping_charge || 0);
          const lineTotal = price * qty;
          const lineShipping = shipping * qty;

          html += `
              <div class="cart-item">
                <div class="item-img">
                  <a href="/product/${item.id}">
                    <img src="${imgSrc}" alt="${(item.name || 'Product')}"
                      onerror="this.onerror=null;this.src='https://placehold.co/200x200?text=No+Image';">
                  </a>
                </div>

                <div class="item-info">
                  <div class="item-name">
                    <a href="/product/${item.id}" style="text-decoration:none;color:inherit;">
                      ${item.name || 'Product'}
                    </a>
                  </div>

                  <div class="item-meta">
                    ${item.size ? `<span class="item-size">Size: ${item.size}</span>` : ``}
                    ${item.color ? `<span class="item-color">Color: ${item.color}</span>` : ``}
                  </div>

                  <div class="item-price-line">
                    <div class="item-price">₹${lineTotal.toFixed(2)}</div>
                    ${shipping > 0 ? `<div class="item-shipping">Shipping: ₹${lineShipping.toFixed(2)}</div>` : ``}
                  </div>
                </div>

                <div class="item-controls">
                  <div class="qty-box">
                    <button class="qty-btn" data-action="dec" data-index="${index}" type="button">-</button>
                    <div class="qty-val">${qty}</div>
                    <button class="qty-btn" data-action="inc" data-index="${index}" type="button">+</button>
                  </div>

                  <button class="remove-link" data-action="remove" data-index="${index}" type="button">
                    ✕ Remove
                  </button>
                </div>
              </div>
            `;
        });

        cartItemsContainer.innerHTML = html;

        cartItemsContainer.querySelectorAll('button[data-action]').forEach(btn => {
          btn.addEventListener('click', function () {
            const action = this.dataset.action;
            const index = parseInt(this.dataset.index, 10);

            let cart = readCart();
            if (!cart[index]) return;

            if (action === 'remove') {
              cart.splice(index, 1);
              writeCart(cart);
              renderCart();
              return;
            }

            let q = Number(cart[index].qty || 1);
            if (action === 'inc') {
              if (q < (cart[index].stock || 0)) {
                q += 1;
              } else {
                showToast(`Only ${cart[index].stock} items available`, 'warning');
              }
            }
            if (action === 'dec') q -= 1;

            if (q <= 0) cart.splice(index, 1);
            else cart[index].qty = q;

            writeCart(cart);
            renderCart();
          });
        });

        updateSummary(cart);
      }


      renderCart();
      window.addEventListener('cartUpdated', renderCart);


      checkoutBtn.addEventListener('click', async function () {
        const cart = readCart();

        if (!cart.length) {
          alert("Cart is empty");
          return;
        }

        // ✅ send referral to backend session
        await fetch('/save-referral', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          body: JSON.stringify({
            staff_id: appliedCoupon || "NO_REFERRAL"
          })
        });

        // ✅ now go to checkout
        window.location.href = "/checkout";
      });

    });

  </script>
@endsection