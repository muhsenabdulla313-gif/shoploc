@extends('layout.master')

@section('body')
<div class="wrap">
  <div class="grid">

    <!-- LEFT -->
    <div class="col-left">
      <div class="card card-flat">
        <div class="pay-wrap">
          <div class="section-title">Select Payment Method</div>

          <!-- COD -->
          <div class="pay-card pay-card-flat selected" data-method="cod" id="cardCod">
            <div class="left-price">
              <div class="price-big" id="codTotal">₹0</div>
            </div>

            <div class="pay-main">
              <div class="pay-row">
                <div class="pay-title">
                  Cash on Delivery <span class="icon-pill">💵</span>
                </div>
                <div class="radio" aria-hidden="true"></div>
              </div>
            </div>
          </div>

          <!-- ONLINE -->
          <div class="pay-card pay-card-flat" data-method="online" id="cardOnline">
            <div class="left-price">
              <div class="price-stack" id="onlinePriceStack">
                <span class="strike">₹0</span>
                <span class="offer">₹0</span>
                <span class="save">Save ₹0</span>
              </div>
            </div>

            <div class="pay-main">
              <div class="pay-row">
                <div class="pay-title">Pay Online</div>
                <div class="radio" aria-hidden="true"></div>
              </div>

              <div class="sub-note">
                <span class="leaf">✳</span>
                <span>Extra discount with bank offers</span>
              </div>

              <div class="expand">
                <div class="pay-group pay-group-flat">

                  <div class="group open" id="groupUpi">
                    <div class="group-head" data-toggle="groupUpi">
                      <b>UPI</b>
                      <div class="right">
                        <span class="offers">Offers Available</span>
                        <span class="chev"></span>
                      </div>
                    </div>

                    <div class="group-body">
                      <div class="qr-box qr-box-flat">
                        <div class="qr" aria-label="QR"></div>
                        <button class="btn-flat btn-flat--sm" type="button">View QR Code</button>
                      </div>

                      <div class="qr-caption">Click to view and scan QR Code with any UPI App</div>
                      <div class="add-upi">
                        <span style="letter-spacing:.4px;">ADD UPI ID</span>
                        <span class="plus">+</span>
                      </div>
                    </div>
                  </div>

                  <div class="group" id="groupCard">
                    <div class="group-head" data-toggle="groupCard">
                      <b>Debit/Credit Cards</b>
                      <div class="right">
                        <span class="offers">Offers Available</span>
                        <span class="chev"></span>
                      </div>
                    </div>

                    <div class="group-body">
                      <div style="display:flex;flex-direction:column;gap:10px;">
                        <input type="text" placeholder="Card Number" class="mini-input">
                        <div style="display:flex;gap:10px;">
                          <input type="text" placeholder="MM/YY" class="mini-input" style="flex:1;">
                          <input type="text" placeholder="CVV" class="mini-input" style="flex:1;">
                        </div>
                      </div>
                    </div>
                  </div>

                </div>
              </div>

            </div>
          </div>

        </div>
      </div>
    </div>

    <!-- RIGHT -->
    <div class="col-right">
      <div class="card card-flat">
        <div class="price-head">Product Details</div>
        <div id="paymentProductsContainer" style="padding-bottom:12px;"></div>

        <button class="btn-flat btn-flat--primary" type="button" id="placeOrderBtn">
          Confirm Order
        </button>
      </div>
    </div>

  </div>
</div>

<!-- Success Modal (CSS style.css il already und enn assume) -->
<div id="successModal" class="success-overlay" style="display:none;">
  <div class="success-card">
    <div class="success-checkmark">
      <div class="check-icon">
        <span class="check-line line-tip"></span>
        <span class="check-line line-long"></span>
        <div class="check-circle"></div>
        <div class="check-fix"></div>
      </div>
    </div>

    <h2 class="success-title">Order Confirmed!</h2>
    <p class="success-sub">Your order has been placed successfully.</p>
  </div>
</div>
@endsection


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const cardCod = document.getElementById('cardCod');
  const cardOnline = document.getElementById('cardOnline');
  const placeBtn = document.getElementById('placeOrderBtn');
  const successModal = document.getElementById('successModal');

  const cart = JSON.parse(localStorage.getItem('cart')) || [];
  const container = document.getElementById('paymentProductsContainer');

  if (!cart.length) { window.location.href = '/cart'; return; }

  let subtotal = 0, totalDiscount = 0, html = '';

  cart.forEach(item => {
    const qty = item.qty || 1;
    const price = parseFloat(item.price || 0);
    const originalPrice = parseFloat(item.original_price || item.price || 0);

    subtotal += price * qty;
    totalDiscount += (originalPrice > price) ? (originalPrice - price) * qty : 0;

    const imgSrc = item.image
      ? (String(item.image).includes('://') ? item.image : (String(item.image).startsWith('/') ? item.image : '/storage/' + item.image))
      : 'https://placehold.co/90x90?text=IMG';

    html += `
      <div style="border-bottom:1px solid #e5e7eb; padding: 12px 16px;">
        <div style="display:flex; gap:12px; align-items:flex-start;">
          <img src="${imgSrc}" alt="${(item.name||'Product').replace(/"/g,'&quot;')}"
               style="width:54px;height:54px;border-radius:0;background:#f3f4f6;border:1px solid #e5e7eb;object-fit:cover;flex:0 0 54px;" />
          <div style="flex:1;">
            <p style="font-size:14px;font-weight:700;margin:0 0 6px;color:#333;">${item.name || 'Product'}</p>
            <div style="display:flex;align-items:baseline;gap:8px;margin:0 0 4px;font-size:13px;">
              <span style="font-weight:800;color:#111;">₹${price.toFixed(0)}</span>
              ${originalPrice > price ? `<span style="color:#6b7280;text-decoration:line-through;font-size:12px;">₹${originalPrice.toFixed(0)}</span>` : ''}
            </div>
            <div style="color:#6b7280;font-size:13px;">Qty: <b style="color:#111827">${qty}</b></div>
          </div>
        </div>
      </div>
    `;
  });

  if (container) container.innerHTML = html;

  const grossTotal = subtotal + totalDiscount;
  const total = subtotal;

  document.getElementById('codTotal').textContent = '₹' + total.toFixed(0);

  const onlineStack = document.getElementById('onlinePriceStack');
  if (onlineStack) {
    onlineStack.innerHTML = (totalDiscount > 0)
      ? `<span class="strike">₹${grossTotal.toFixed(0)}</span>
         <span class="offer">₹${total.toFixed(0)}</span>
         <span class="save">Save ₹${totalDiscount.toFixed(0)}</span>`
      : `<span class="offer">₹${total.toFixed(0)}</span>`;
  }

  function selectMethod(method){
    cardCod.classList.remove('selected');
    cardOnline.classList.remove('selected');
    if(method === 'cod') cardCod.classList.add('selected');
    if(method === 'online') cardOnline.classList.add('selected');
  }
  cardCod.addEventListener('click', () => selectMethod('cod'));
  cardOnline.addEventListener('click', () => selectMethod('online'));

  document.querySelectorAll('[data-toggle]').forEach(head => {
    head.addEventListener('click', (e) => {
      e.stopPropagation();
      const id = head.getAttribute('data-toggle');
      const group = document.getElementById(id);
      if (group) group.classList.toggle('open');
    });
  });

  placeBtn.addEventListener('click', async () => {
    const originalText = placeBtn.textContent;
    placeBtn.disabled = true;
    placeBtn.textContent = 'Processing...';

    try {
      const cartNow = JSON.parse(localStorage.getItem('cart')) || [];
      if (!cartNow.length) {
        placeBtn.disabled = false;
        placeBtn.textContent = originalText;
        return;
      }

      const address = JSON.parse(localStorage.getItem('checkoutAddress')) || {};
      const selected = document.querySelector('.pay-card.selected');
      const paymentMethod = selected ? selected.getAttribute('data-method') : 'cod';

      let sub = 0, disc = 0;
      cartNow.forEach(item => {
        const qty = item.qty || 1;
        const price = parseFloat(item.price || 0);
        const originalPrice = parseFloat(item.original_price || item.price || 0);
        sub += price * qty;
        disc += (originalPrice > price) ? (originalPrice - price) * qty : 0;
      });

      const response = await fetch('/checkout', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
          cart: cartNow,
          address,
          payment_method: paymentMethod,
          subtotal: sub.toFixed(2),
          discount: disc.toFixed(2),
          total: sub.toFixed(2)
        })
      });

      // ⚠️ if server returns HTML (not JSON) this will fail.
      const result = await response.json();

      if (result.success) {
        if (successModal) successModal.style.display = 'flex';

        localStorage.removeItem('cart');
        window.dispatchEvent(new CustomEvent('cartUpdated'));

        setTimeout(() => {
          window.location.href = '/';
        }, 2500);
      } else {
        alert(result.message || 'Order failed. Please try again.');
        placeBtn.disabled = false;
        placeBtn.textContent = originalText;
      }
    } catch (err) {
      console.error(err);
      alert('Error! Console check cheyyu.');
      placeBtn.disabled = false;
      placeBtn.textContent = originalText;
    }
  });

});
</script>
@endpush
