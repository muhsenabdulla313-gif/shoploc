@extends('layout.master')

@section('body')
  <div class="checkout-page">
    <div class="checkout-container">
      <div class="checkout-grid">

        <div class="checkout-left">

          <!-- ✅ SAVED ADDRESS UI -->
        
     @if($addresses->count())
  @foreach($addresses as $addr)
    <div style="margin-bottom:8px;">
      <label>
        <input type="radio" name="address_id"
          value="{{ $addr->id }}"
          data-first_name="{{ $addr->first_name }}"
          data-last_name="{{ $addr->last_name }}"
          data-phone="{{ $addr->phone }}"
          data-address="{{ $addr->address }}"
          data-city="{{ $addr->city }}"
          data-zip="{{ $addr->zip }}">

        {{ $addr->address }}, {{ $addr->city }}
      </label>
    </div>
  @endforeach
@else
  <p>No saved addresses. Please add a new one.</p>
@endif
          <!-- ✅ ADDRESS FORM -->
          <div id="addressFormContainer">
            <form id="checkoutForm">

              <div class="section">
                <h3 class="section-title">Customer Details</h3>

                <div class="row-2">
                  <div class="field">
                    <label>First Name <span>*</span></label>
                    <input class="input" type="text" name="first_name" required>
                  </div>
                  <div class="field">
                    <label>Last Name <span>*</span></label>
                    <input class="input" type="text" name="last_name" required>
                  </div>
                </div>

                <div class="row-2">
                  <div class="field">
                    <label>Phone Number <span>*</span></label>
                    <div class="phone-wrap">
                      <select class="input phone-code" name="phone_code">
                        <option value="+91">IN +91</option>
                      </select>
                      <input class="input phone-num" type="tel" name="phone" required>
                    </div>
                  </div>

                  <div class="field">
                    <label>Alternate Phone</label>
                    <input class="input" type="tel" name="alt_phone">
                  </div>
                </div>
              </div>

              <div class="section">
                <h3 class="section-title">Shipping Details</h3>

                <div class="field">
                  <label>Address <span>*</span></label>
                  <input class="input" type="text" name="address" required>
                </div>

                <div class="row-3">
                  <div class="field">
                    <label>Postal Code</label>
                    <input class="input" type="text" name="zip" required>
                  </div>
                  <div class="field">
                    <label>City</label>
                    <input class="input" type="text" name="city" required>
                  </div>
                </div>
              </div>

          

            </form>
          </div>

        </div>
<div class="section">
  <h3 class="section-title">Payment Method</h3>

  <label>
    <input type="radio" name="payment_method" value="cod" checked>
    Cash on Delivery
  </label>

  <br>

  <label>
    <input type="radio" name="payment_method" value="online">
    Online Payment
  </label>
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
document.addEventListener('DOMContentLoaded', () => {

  renderSummary();

  const form = document.getElementById('checkoutForm');

  // ✅ AUTO FILL WHEN ADDRESS SELECTED
  document.querySelectorAll('input[name="address_id"]').forEach(radio => {
    radio.addEventListener('change', function () {

      form.querySelector('[name="first_name"]').value = this.dataset.first_name || '';
      form.querySelector('[name="last_name"]').value = this.dataset.last_name || '';
      form.querySelector('[name="phone"]').value = this.dataset.phone || '';
      form.querySelector('[name="address"]').value = this.dataset.address || '';
      form.querySelector('[name="city"]').value = this.dataset.city || '';
      form.querySelector('[name="zip"]').value = this.dataset.zip || '';

      // 🔒 make readonly
      form.querySelectorAll('input[type="text"], input[type="tel"]').forEach(input => {
        input.readOnly = true;
      });

    });
  });

  // ✅ ENABLE INPUTS IF USER WANTS NEW ADDRESS
  form.addEventListener('reset', () => {
    form.querySelectorAll('input').forEach(input => {
      input.readOnly = false;
      input.value = '';
    });

    document.querySelectorAll('input[name="address_id"]').forEach(r => r.checked = false);
  });

  form.addEventListener('submit', handlePlaceOrder);
});


// 💰 MONEY FORMAT
function money(n) {
  return '₹' + Number(n).toFixed(2);
}


// 🛒 GET CART
function getCart() {
  try {
    return JSON.parse(localStorage.getItem('cart') || '[]');
  } catch {
    return [];
  }
}


// 🧾 RENDER SUMMARY
function renderSummary() {
  const cart = getCart();
  const wrap = document.getElementById('summaryItems');

  if (!cart.length) {
    window.location.href = '/cart';
    return;
  }

  let subtotal = 0;

  wrap.innerHTML = cart.map(item => {
    const qty = Number(item.qty || 1);
    const price = Number(item.price || 0);

    subtotal += price * qty;

   const imgSrc = item.image
          ? (String(item.image).includes('://') ? item.image : (String(item.image).startsWith('/') ? item.image : '/storage/' + item.image))
          : 'https://placehold.co/56x56?text=IMG';

    return `
      <div class="item">
        <img src="${imgSrc}" />
        <div>
          <p class="item-title">${item.name}</p>
          <small>Qty: ${qty}</small>
        </div>
          <div class="item-price">${money(price)}</div>
      </div>
    `;
  }).join('');

  updateTotals(subtotal);
}


// 🧮 TOTAL
function updateTotals(subtotal) {
  const total = subtotal;

  document.getElementById('subtotalVal').textContent = money(subtotal);
  document.getElementById('shippingVal').textContent = 'Free';
  document.getElementById('taxVal').textContent = money(0);
  document.getElementById('totalVal').textContent = money(total);
}


// 🚀 PLACE ORDER
function handlePlaceOrder(e) {
  e.preventDefault();

  const cart = getCart();
  if (!cart.length) return alert("Cart empty");

  const formData = new FormData(e.target);
  const addressId = formData.get('address_id');
  const paymentMethod = formData.get('payment_method');

  const payload = {
    cart: cart,
    payment_method: paymentMethod,
    address_id: addressId,
    address: Object.fromEntries(formData.entries()),
    discount: 0
  };

  fetch('/checkout', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    },
    body: JSON.stringify(payload)
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      alert('Order placed successfully');
      localStorage.removeItem('cart');
      window.location.href = "/success";
    } else {
      alert(data.message);
    }
  })
  .catch(() => alert('Something went wrong'));
}




  @if(session('referral_code'))
    
      document.addEventListener('DOMContentLoaded', function () {
        localStorage.setItem('referral_code', '{{ session('referral_code') }}');
      });
    
  @endif
</script>
@endpush