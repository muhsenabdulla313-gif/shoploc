@extends('layout.master')

@section('body')
<div class="checkout-page">
  <div class="checkout-container">
    <div class="checkout-grid">

      <!-- LEFT: CHECKOUT FORM -->
      <div class="checkout-left">

        <form id="checkoutForm">
          <div class="section">
            <h3 class="section-title">Customer Details</h3>

            <div class="row-2">
              <div class="field">
                <label>First Name <span>*</span></label>
                <input class="input" type="text" name="first_name" placeholder="Your first name" required>
              </div>
              <div class="field">
                <label>Last Name <span>*</span></label>
                <input class="input" type="text" name="last_name" placeholder="Your last name" required>
              </div>
            </div>

            <div class="row-2">
              <div class="field">
                <label>Phone Number <span>*</span></label>
                <div class="phone-wrap">
                  <select class="input phone-code" name="phone_code" required>
                    <option value="+91" selected>IN +91</option>
                    <option value="+971">UAE +971</option>
                    <option value="+1">US +1</option>
                  </select>
                  <input class="input phone-num" type="tel" name="phone" placeholder="Enter phone number" required>
                </div>
              </div>

              <div class="field">
                <label>Alternate Phone (optional)</label>
                <input class="input" type="tel" name="alt_phone" placeholder="Alternate phone number (optional)">
              </div>
            </div>
          </div>

          <div class="section">
            <h3 class="section-title">Shipping Details</h3>

            <div class="field">
              <label>Street Address <span>*</span></label>
              <input class="input" type="text" name="address" placeholder="House name / Street / Landmark" required>
            </div>

            <div class="row-3">
              <div class="field">
                <label>Postal Code <span>*</span></label>
                <input class="input" type="text" name="zip" placeholder="Postal code" required>
              </div>
              <div class="field">
                <label>City <span>*</span></label>
                <input class="input" type="text" name="city" placeholder="City" required>
              </div>
            </div>
          </div>

          <button class="btn-flat btn-flat--primary" type="submit" id="confirmOrderBtn">
            Confirm Order
          </button>
        </form>

      </div>

      <!-- RIGHT: ORDER SUMMARY -->
      <div class="checkout-right">
        <div class="summary-card">
          <div class="summary-head">
            <h3>Order Summary</h3>
          </div>

          <div id="summaryItems" class="summary-items"></div>

          <div class="divider"></div>

          <div class="totals">
            <div class="trow"><span>Subtotal</span><span id="subtotalVal">$0.00</span></div>

            <!-- ✅ shipping display should show Free if 0 -->
            <div class="trow"><span>Shipping Charge</span><span id="shippingVal">Free</span></div>

            <div class="trow"><span>Taxes</span><span id="taxVal">$0.00</span></div>
            <div class="trow"><span>Discount</span><span id="discountVal">$0.00</span></div>

            <div class="divider"></div>

            <div class="trow total"><span>Total</span><span id="totalVal">$0.00</span></div>
          </div>

          <button class="btn-flat btn-flat--primary" type="button"
                  onclick="document.getElementById('checkoutForm').requestSubmit()">
            Check Out
          </button>
        </div>
      </div>

    </div>
  </div>
</div>
@endsection


@push('scripts')
<script>
let DISCOUNT_AMOUNT = 0;
let TAX = 22.00;

document.addEventListener('DOMContentLoaded', () => {
  loadSavedAddress();
  renderSummary();

  const form = document.getElementById('checkoutForm');
  if(form){
    form.addEventListener('submit', handlePlaceOrder);
  }
});

function loadSavedAddress() {
  const saved = localStorage.getItem('checkoutAddress');
  if (!saved) return;
  const data = JSON.parse(saved);
  const form = document.getElementById('checkoutForm');
  if(!form) return;
  Object.keys(data).forEach(key => {
    const input = form.querySelector(`[name="${key}"]`);
    if(input) input.value = data[key];
  });
}

// ✅ money helper
function money(n){ return '$' + Number(n).toFixed(2); }

// ✅ shipping label helper (0 => Free)
function shippingLabel(amount){
  const a = Number(amount || 0);
  if (a <= 0) return 'Free';
  return money(a);
}

function getCart(){
  try{
    const c = JSON.parse(localStorage.getItem('cart') || '[]');
    return Array.isArray(c) ? c : [];
  }catch(e){
    return [];
  }
}

function setCart(cart){
  localStorage.setItem('cart', JSON.stringify(cart));
  window.dispatchEvent(new CustomEvent('cartUpdated'));
}

function renderSummary(){
  const cart = getCart();
  const wrap = document.getElementById('summaryItems');

  if(!cart.length){
    window.location.href = '/cart';
    return;
  }

  let subtotal = 0;

  wrap.innerHTML = cart.map((item, idx) => {
    const qty = Number(item.qty || 1);
    const price = Number(item.price || 0);
    subtotal += price * qty;

    // ✅ shipping charge from DB stored in cart item:
    // support multiple keys just in case:
    const ship = Number(item.shipping_charge ?? item.delivery_charge ?? item.shipping ?? 0);

    const imgSrc = item.image
      ? (String(item.image).includes('://') ? item.image : (String(item.image).startsWith('/') ? item.image : '/storage/' + item.image))
      : 'https://placehold.co/56x56?text=IMG';

    return `
      <div class="item">
        <img src="${imgSrc}" alt="${(item.name||'Item').replace(/"/g,'&quot;')}" />
        <div>
          <p class="item-title">${item.name || 'Product'}</p>

          <!-- ✅ optional: per-item delivery label (free/amount) -->
          <small style="display:block;opacity:.7;margin-top:2px;">
            Delivery: ${shippingLabel(ship)}
          </small>

          <div class="qty">
            <button class="qbtn" type="button" onclick="changeQty(${idx}, -1)">-</button>
            <span class="qval">${qty}</span>
            <button class="qbtn" type="button" onclick="changeQty(${idx}, 1)">+</button>
            <span class="remove" onclick="removeItem(${idx})">×</span>
          </div>
        </div>
        <div class="item-price">${money(price)}</div>
      </div>
    `;
  }).join('');

  updateTotals(subtotal);
}

function updateTotals(subtotal){
  const cart = getCart();

  let totalShipping = 0;

  // ✅ total shipping = sum(item.shipping_charge * qty)
  cart.forEach(item => {
    const ship = Number(item.shipping_charge ?? item.delivery_charge ?? item.shipping ?? 0);
    const qty = Number(item.qty || 1);
    totalShipping += ship * qty;
  });

  const total = subtotal + totalShipping + TAX - DISCOUNT_AMOUNT;

  document.getElementById('subtotalVal').textContent  = money(subtotal);

  // ✅ show Free if shipping 0
  document.getElementById('shippingVal').textContent  = (totalShipping <= 0 ? 'Free' : money(totalShipping));

  document.getElementById('taxVal').textContent       = money(TAX);
  document.getElementById('discountVal').textContent  = money(DISCOUNT_AMOUNT);
  document.getElementById('totalVal').textContent     = money(total);
}

function changeQty(index, delta){
  const cart = getCart();
  if(!cart[index]) return;

  cart[index].qty = (Number(cart[index].qty || 1)) + delta;
  if(cart[index].qty <= 0) cart.splice(index,1);

  setCart(cart);
  renderSummary();
}

function removeItem(index){
  const cart = getCart();
  cart.splice(index,1);
  setCart(cart);
  renderSummary();
}

function handlePlaceOrder(e){
  e.preventDefault();

  const cart = getCart();
  if (!cart.length) return;

  const formData = new FormData(e.target);
  const data = Object.fromEntries(formData.entries());
  localStorage.setItem('checkoutAddress', JSON.stringify(data));

  // ✅ redirect to payment page
  window.location.href = '/payment';
}


</script>

{{-- Store referral code in localStorage when checkout page loads --}}
@if(session('referral_code'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        localStorage.setItem('referral_code', '{{ session('referral_code') }}');
    });
</script>
@endif

@endpush
