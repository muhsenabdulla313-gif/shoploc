@extends('layout.master')

@section('body')
<div class="od-wrap">
  <div class="od-inner">

    {{-- Header --}}
    <div class="od-header">
      <h2 class="od-title">Orders</h2>

      {{-- Filter --}}
      <div class="od-filter">
        <select class="od-select" id="statusFilter" onchange="filterOrders()">
          <option value="">All Statuses</option>
          <option value="pending">Pending</option>
          <option value="confirmed">Confirmed</option>
          <option value="shipped">Shipped</option>
          <option value="delivered">Delivered</option>
          <option value="cancelled">Cancelled</option>
        </select>
      </div>
    </div>

    @if($orders->count() > 0)

      <div id="ordersWrap" class="od-list">

        @foreach($orders as $order)
          @php
            $status = $order->status ?? 'pending';

            // Top Row - labels
            $orderPlaced = $order->created_at ? $order->created_at->format('M d, Y') : '-';
            $orderTotal  = $order->total_amount ?? 0;

            // Ship To (adjust to your fields)
            $shipTo = $order->shipping_name
                ?? ($order->user->name ?? 'Customer');

            $shipAddress = $order->shipping_address
                ?? ($order->shipping_city ?? '');

            $shipLine = trim($shipTo . (strlen($shipAddress) ? ', '.$shipAddress : ''));

            // Status date line (right block)
            if($status === 'cancelled') {
              $statusDate = $order->cancelled_at ? \Carbon\Carbon::parse($order->cancelled_at)->format('d.m.y') : $order->updated_at->format('d.m.y');
              $statusTitle = "Cancelled on $statusDate";
              $statusSub = "This order has been cancelled.";
            } elseif($status === 'delivered') {
              $statusDate = $order->delivered_at ? \Carbon\Carbon::parse($order->delivered_at)->format('d.m.y') : $order->updated_at->format('d.m.y');
              $statusTitle = "Delivered on $statusDate";
              $statusSub = "Your order is delivered.";
            } elseif($status === 'shipped') {
              $statusDate = $order->shipped_at ? \Carbon\Carbon::parse($order->shipped_at)->format('d.m.y') : $order->updated_at->format('d.m.y');
              $statusTitle = "Shipped on $statusDate";
              $statusSub = "Your order is shipped.";
            } elseif($status === 'confirmed') {
              $statusDate = $order->confirmed_at ? \Carbon\Carbon::parse($order->confirmed_at)->format('d.m.y') : $order->updated_at->format('d.m.y');
              $statusTitle = "Confirmed on $statusDate";
              $statusSub = "Order confirmed successfully.";
            } else {
              $statusTitle = "Order placed on " . $orderPlaced;
              $statusSub = "We received your order.";
            }
          @endphp

          {{-- each ORDER can have multiple items; show each like screenshot (one row per item) --}}
          @if($order->items && $order->items->count() > 0)
            @foreach($order->items as $item)

              @php
                /**
                 * IMAGE FIX (Corrected)
                 * - file_exists() remove cheythu (athu false aayi image break aakum)
                 * - storage image aanel php artisan storage:link must
                 * - $img path: "storage/..." or full http url
                 */
                $img = null;

                if(isset($item->product) && !empty($item->product->image)) $img = $item->product->image;
                elseif(isset($item->product) && !empty($item->product->thumbnail)) $img = $item->product->thumbnail;
                elseif(!empty($item->product_image)) $img = $item->product_image;
                elseif(!empty($item->image)) $img = $item->image;

                // Default fallback (put this image in: public/images/no-image.png)
                $fallbackImg = asset('images/no-image.png');

                // Build final URL safely
                if($img) {
                  // If already full URL
                  if(\Illuminate\Support\Str::startsWith($img, ['http://', 'https://'])) {
                    $imgUrl = $img;
                  } else {
                    // If user stored like "products/abc.jpg", convert to "storage/products/abc.jpg"
                    // If already starts with "storage/", keep it.
                    $cleanPath = ltrim($img, '/');
                    if(!\Illuminate\Support\Str::startsWith($cleanPath, 'storage/')) {
                      // optional: if your uploads are in storage/app/public
                      // then DB might store "products/abc.jpg"
                      $cleanPath = 'storage/' . $cleanPath;
                    }
                    $imgUrl = asset($cleanPath);
                  }
                } else {
                  $imgUrl = $fallbackImg;
                }

                $itemName = $item->product_name ?? ($item->product->name ?? 'Product');
                $qty = $item->quantity ?? 1;

                $price = $item->price ?? null;
                $lineTotal = $price !== null ? ($price * $qty) : null;

                // Delivery received date
                $receivedDate = $order->delivered_at ? \Carbon\Carbon::parse($order->delivered_at)->format('d.m.y') : null;

                $detailsUrl = route('order.details', $order->id) . '?item=' . $item->id;
              @endphp

              <div class="od-card" data-status="{{ $status }}">

                {{-- TOP BAR (like screenshot) --}}
                <div class="od-top">
                  <div class="od-top-col">
                    <div class="od-top-label">ORDER PLACED</div>
                    <div class="od-top-val">{{ $orderPlaced }}</div>
                  </div>

                  <div class="od-top-col">
                    <div class="od-top-label">TOTAL</div>
                    <div class="od-top-val">₹{{ number_format($orderTotal, 0) }}</div>
                  </div>

                  <div class="od-top-col od-top-grow">
                    <div class="od-top-label">SHIP TO</div>
                    <div class="od-top-val">{{ $shipLine ?: '—' }}</div>
                  </div>

                  <!-- <div class="od-top-right">
                    <div class="od-top-orderno">ORDER #{{ $order->id }}</div>
                    <a class="od-details-link" href="{{ $detailsUrl }}">Order Details</a>
                  </div> -->
                </div>

                {{-- BODY (image + details + qty + actions) --}}
                <div class="od-body">
                  <div class="od-thumb">
                    <img
                      src="{{ $imgUrl }}"
                      alt="Product"
                      loading="lazy"
                      onerror="this.onerror=null;this.src='{{ $fallbackImg }}';"
                    >
                  </div>

                  <div class="od-mid">
                    <div class="od-name">{{ $itemName }}</div>

                    <div class="od-meta">
                      @if(!empty($item->color)) <span>Color: <b>{{ $item->color }}</b></span> @endif
                      @if(!empty($item->size)) <span>Size: <b>{{ $item->size }}</b></span> @endif
                    </div>

                    @if($receivedDate)
                      <div class="od-received">
                        <span class="od-received-pill">Received on {{ $receivedDate }}</span>
                      </div>
                    @endif

                    <div class="od-desc">
                      @if(!empty($item->description))
                        {!! nl2br(e($item->description)) !!}
                      @else
                        <span class="od-muted">Tap “Order Details” to view full item details.</span>
                      @endif
                    </div>
                  </div>

                  <div class="od-qty">
                    <div class="od-qty-label">Qty</div>
                    <div class="od-qty-box">{{ $qty }}</div>

                    <div class="od-lineprice">
                      @if($lineTotal !== null)
                        ₹{{ number_format($lineTotal, 0) }}
                      @endif
                    </div>
                  </div>

                  <div class="od-actions">
                    @if($status === 'delivered')
                      <button class="od-btn od-btn-dark" type="button"
                              onclick="window.location.href='{{ $detailsUrl }}'">
                        ORDER AGAIN
                      </button>
                    @elseif($status === 'pending' || $status === 'confirmed' || $status === 'shipped')
                      <button class="od-btn od-btn-primary" type="button"
                              onclick="window.location.href='{{ $detailsUrl }}'">
                        TRACK / DETAILS
                      </button>
                    @endif

                    @if($status !== 'cancelled')
                      <button class="od-btn od-btn-outline" type="button"
                              onclick="window.location.href='{{ $detailsUrl }}'">
                        ARCHIVE ORDER
                      </button>
                    @else
                      <button class="od-btn od-btn-outline" type="button"
                              onclick="window.location.href='{{ $detailsUrl }}'">
                        VIEW
                      </button>
                    @endif
                  </div>
                </div>

                {{-- STATUS FOOT --}}
                <div class="od-foot">
                  <div class="od-status">
                    <span class="od-dot dot-{{ $status }}"></span>
                    <div class="od-status-text">
                      <div class="od-status-title">{{ $statusTitle }}</div>
                      <div class="od-status-sub">{{ $statusSub }}</div>
                    </div>
                  </div>
                </div>

              </div>
            @endforeach
          @else
            {{-- if no items --}}
            <div class="od-card" data-status="{{ $status }}">
              <div class="od-top">
                <div class="od-top-col">
                  <div class="od-top-label">ORDER PLACED</div>
                  <div class="od-top-val">{{ $orderPlaced }}</div>
                </div>
                <div class="od-top-col">
                  <div class="od-top-label">TOTAL</div>
                  <div class="od-top-val">₹{{ number_format($orderTotal, 0) }}</div>
                </div>
                <div class="od-top-col od-top-grow">
                  <div class="od-top-label">SHIP TO</div>
                  <div class="od-top-val">{{ $shipLine ?: '—' }}</div>
                </div>
                <div class="od-top-right">
                  <div class="od-top-orderno">ORDER #{{ $order->id }}</div>
                  <a class="od-details-link" href="{{ route('order.details', $order->id) }}">Order Details</a>
                </div>
              </div>

              <div class="od-body">
                <div class="od-thumb">
                  <img src="https://via.placeholder.com/130x130?text=Order" alt="Order">
                </div>
                <div class="od-mid">
                  <div class="od-name">Order #{{ $order->id }}</div>
                  <div class="od-muted">No items found for this order.</div>
                </div>
              </div>

              <div class="od-foot">
                <div class="od-status">
                  <span class="od-dot dot-{{ $status }}"></span>
                  <div class="od-status-text">
                    <div class="od-status-title">{{ $statusTitle }}</div>
                    <div class="od-status-sub">{{ $statusSub }}</div>
                  </div>
                </div>
              </div>
            </div>
          @endif

        @endforeach
      </div>

    @else
      <div class="text-center py-5">
        <i class="fas fa-shopping-bag" style="font-size: 3rem; color: #ccc;"></i>
        <p class="mt-3">You haven't placed any orders yet.</p>
        <a href="/" class="btn btn-primary">Start Shopping</a>
      </div>
    @endif
  </div>
</div>

<script>
  function filterOrders() {
    const status = document.getElementById('statusFilter').value;
    const cards = document.querySelectorAll('#ordersWrap .od-card');

    cards.forEach(card => {
      if (status === '' || card.getAttribute('data-status') === status) {
        card.style.display = 'block';
      } else {
        card.style.display = 'none';
      }
    });
  }
</script>

@endsection
