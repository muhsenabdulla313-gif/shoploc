@extends('layout.master')

@section('body')
  <div class="checkout-page">
    <div class="checkout-container">
      <div class="checkout-grid">

        <div class="checkout-left">

          <!-- ✅ SAVED ADDRESS UI -->
          <div id="savedAddressContainer" style="display:none;">
            <div class="saved-address-box">
              <p id="savedAddressText"></p>

              <button class="btn-flat btn-flat--primary" id="useSavedBtn">
                Use This Address
              </button>

              <button class="btn-flat" id="addNewAddressBtn" style="margin-left:10px;">
                Add New Address
              </button>
            </div>
          </div>
       @if($addresses->count())
    @foreach($addresses as $addr)
        <div>
            <input type="radio" name="address_id" value="{{ $addr->id }}">
            {{ $addr->address }}, {{ $addr->city }}
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

              <button class="btn-flat btn-flat--primary" type="submit">
                Confirm Order
              </button>

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
    let TAX = 00;

    document.addEventListener('DOMContentLoaded', () => {
      loadSavedAddress();
      renderSummary();

      const form = document.getElementById('checkoutForm');
      if (form) {
        form.addEventListener('submit', handlePlaceOrder);
      }
    });

 

    // ✅ money helper
    function money(n) { return '$' + Number(n).toFixed(2); }

    // ✅ shipping label helper (0 => Free)
    function shippingLabel(amount) {
      const a = Number(amount || 0);
      if (a <= 0) return 'Free';
      return money(a);
    }
    function loadSavedAddress() {
      const saved = localStorage.getItem('checkoutAddress');

      const formContainer = document.getElementById('addressFormContainer');
      const savedContainer = document.getElementById('savedAddressContainer');
      const savedText = document.getElementById('savedAddressText');

      if (!saved) {
        formContainer.style.display = 'block';
        savedContainer.style.display = 'none';
        return;
      }

      const data = JSON.parse(saved);

      formContainer.style.display = 'none';
      savedContainer.style.display = 'block';

      savedText.innerHTML = `
      <strong>${data.first_name} ${data.last_name}</strong><br>
      ${data.phone}<br>
      ${data.address}, ${data.city} - ${data.zip}
    `;

      document.getElementById('useSavedBtn').onclick = function () {
        formContainer.style.display = 'block';
        savedContainer.style.display = 'none';

        const form = document.getElementById('checkoutForm');
        Object.keys(data).forEach(key => {
          const input = form.querySelector(`[name="${key}"]`);
          if (input) input.value = data[key];
        });
      };

      document.getElementById('addNewAddressBtn').onclick = function () {
        formContainer.style.display = 'block';
        savedContainer.style.display = 'none';

        document.getElementById('checkoutForm').reset();
      };
    }
    function getCart() {
      try {
        const c = JSON.parse(localStorage.getItem('cart') || '[]');
        return Array.isArray(c) ? c : [];
      } catch (e) {
        return [];
      }
    }

    function setCart(cart) {
      localStorage.setItem('cart', JSON.stringify(cart));
      window.dispatchEvent(new CustomEvent('cartUpdated'));
    }

    function renderSummary() {
      const cart = getCart();
      const wrap = document.getElementById('summaryItems');

      if (!cart.length) {
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
          <img src="${imgSrc}" alt="${(item.name || 'Item').replace(/"/g, '&quot;')}" />
          <div>
            <p class="item-title">${item.name || 'Product'}</p>



            <div class="qty">
    <span>Quantity: ${qty}</span>
  </div>
          </div>
          <div class="item-price">${money(price)}</div>
        </div>
      `;
      }).join('');

      updateTotals(subtotal);
    }

    function updateTotals(subtotal) {

      const totalShipping = 0;
      const TAX = 0;

      const total = subtotal + totalShipping + TAX;

      document.getElementById('subtotalVal').textContent = money(subtotal);

      document.getElementById('shippingVal').textContent = 'Free'; // ✅ always free
      document.getElementById('taxVal').textContent = money(0);
      document.getElementById('totalVal').textContent = money(total);
    }

    function changeQty(index, delta) {
      const cart = getCart();
      if (!cart[index]) return;

      cart[index].qty = (Number(cart[index].qty || 1)) + delta;
      if (cart[index].qty <= 0) cart.splice(index, 1);

      setCart(cart);
      renderSummary();
    }

    function removeItem(index) {
      const cart = getCart();
      cart.splice(index, 1);
      setCart(cart);
      renderSummary();
    }

    function handlePlaceOrder(e) {
      e.preventDefault();

      const cart = getCart();
      if (!cart.length) {
        alert("Cart empty");
        return;
      }
      const formData = new FormData(e.target);
      const addressId = formData.get('address_id');

      const address = Object.fromEntries(formData.entries()); 
const paymentMethod = formData.get('payment_method'); // ✅ ADD

      const staffId = localStorage.getItem('staff_id');
      fetch('/checkout', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
          cart: cart,
          address: address,
          address_id: addressId,
          staff_id: staffId,
              payment_method: paymentMethod,

          discount: 0
        })
      })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            alert('Order placed successfully');

            localStorage.removeItem('cart');
            localStorage.removeItem('staff_id');

            window.location.href = "/success";
          } else {
            alert(data.message);
          }
        })
        .catch(() => alert('Something went wrong'));
    }

  </script>

  {{-- Store referral code in localStorage when checkout page loads --}}
  @if(session('referral_code'))
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        localStorage.setItem('referral_code', '{{ session('referral_code') }}');
      });
    </script>
  @endif

@endpush