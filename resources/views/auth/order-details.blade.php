@php
    use Illuminate\Support\Str;
@endphp

@extends('layout.master')

@section('body')
<div class="od-wrap">
    <div class="od-inner">

        {{-- Breadcrumb --}}
        <div class="od-breadcrumb">
            <a href="/">Home</a>
            <span>›</span>
            <a href="{{ route('user.orders') }}">My Orders</a>
            <span>›</span>
            <span class="od-muted">#{{ $order->id }}</span>
        </div>

        <div class="od-grid">
            {{-- LEFT --}}
            <div class="od-left">

                {{-- Pay bar --}}
                @if(($order->payment_method ?? '') !== 'cod' && ($order->payment_status ?? '') !== 'paid')
                    <div class="od-paybar">
                        <div class="od-paybar-text">Pay online for a smooth doorstep experience</div>
                        <a href="{{ route('payment.pay', $order->id) }}" class="od-pay-btn">
                            Pay ₹{{ number_format($order->total_amount, 0) }}
                        </a>
                    </div>
                @endif

                @php
                    $highlightedItemId = request('item');

                    $itemsToShow = $order->items;
                    if ($highlightedItemId) {
                        $itemsToShow = $order->items->filter(fn($it) => (string)$it->id === (string)$highlightedItemId);
                    }
                @endphp

                @if($highlightedItemId && $itemsToShow->count() === 0)
                    <div class="od-card" style="padding:16px;">
                        <b>Item not found in this order.</b>
                        <div class="od-muted" style="margin-top:6px;">Please go back and open from My Orders again.</div>
                    </div>
                @endif

                @foreach($itemsToShow as $item)
                    @php
                        $img = null;
                        if(isset($item->product) && !empty($item->product->image)) $img = $item->product->image;
                        elseif(isset($item->product) && !empty($item->product->thumbnail)) $img = $item->product->thumbnail;
                        elseif(!empty($item->product_image)) $img = $item->product_image;
                        elseif(!empty($item->image)) $img = $item->image;

                        $imgUrl = $img
                            ? (Str::startsWith($img, ['http://','https://']) ? $img : (file_exists(public_path($img)) ? asset($img) : 'https://via.placeholder.com/96x96?text=Product'))
                            : 'https://via.placeholder.com/96x96?text=Product';

                        $name   = $item->product->name ?? $item->product_name ?? 'Product';
                        $seller = $item->seller_name ?? ($item->product->seller_name ?? null);
                        $qty    = $item->quantity ?? 1;

                        $lineTotal = ($item->price ?? 0) * $qty;

                        $status = $order->status ?? 'pending';
                        $step = 1;
                        if($status === 'pending') $step = 1;
                        if($status === 'confirmed') $step = 1;
                        if($status === 'shipped') $step = 2;
                        if($status === 'out_for_delivery') $step = 3;
                        if($status === 'delivered') $step = 4;

                        // Demo updates for modal (you can replace with DB)
                        $updates = [];
                        $updates[] = [
                            'time'  => optional($order->created_at)->format('D, d M Y - h:ia') ?? 'Today',
                            'title' => 'Order Confirmed',
                            'lines' => [
                                'Your order has been placed.',
                                'Seller is processing your order.',
                                'Item waiting to be picked up by delivery partner.',
                            ]
                        ];
                        $updates[] = [
                            'time'  => 'Expected By Thu 15th Jan',
                            'title' => 'Shipped',
                            'lines' => [
                                'Item yet to be shipped.',
                                'Item yet to reach hub nearest to you.',
                            ]
                        ];
                        $updates[] = [
                            'time'  => '—',
                            'title' => 'Out For Delivery',
                            'lines' => [
                                'Item yet to be delivered.',
                            ]
                        ];
                        $updates[] = [
                            'time'  => 'Expected By Sat 24th Jan',
                            'title' => 'Delivery',
                            'lines' => [
                                'Item yet to be delivered.',
                            ]
                        ];

                        $modalId = "odModal-{$order->id}-{$item->id}";
                    @endphp

                    <div class="od-card {{ $highlightedItemId && (string)$item->id === (string)$highlightedItemId ? 'od-highlighted' : '' }}">
                        <div class="od-item">
                            <div class="od-item-info">
                                <div class="od-item-title">{{ $name }}</div>
                                @if($seller)
                                    <div class="od-muted od-seller">Seller: {{ $seller }}</div>
                                @endif

                                <div class="od-price-row">
                                    <div class="od-price">₹{{ number_format($lineTotal, 0) }}</div>
                                </div>

                                <div class="od-variant">
                                    @if(!empty($item->color)) <span>Color: <b>{{ $item->color }}</b></span> @endif
                                    @if(!empty($item->size)) <span class="ms-2">Size: <b>{{ $item->size }}</b></span> @endif
                                    <span class="ms-2">Qty: <b>{{ $qty }}</b></span>
                                </div>
                            </div>

                            <div class="od-item-thumb">
                                <img src="{{ $imgUrl }}" alt="Product" loading="lazy"
                                     onerror="this.src='https://via.placeholder.com/96x96?text=Product';">
                            </div>
                        </div>

                        {{-- Tracking --}}
                        <div class="od-track">
                            @if($status === 'cancelled')
                                <div class="od-cancel-box">
                                    <div class="od-cancel-title">Order Cancelled</div>
                                    <div class="od-muted">This order has been cancelled.</div>
                                </div>
                            @else
                                <div class="od-step @if($step>=1) active @endif">
                                    <div class="dot"></div>
                                    <div class="content">
                                        <div class="title">Order Confirmed</div>
                                        <div class="sub od-muted">Your order has been placed.</div>
                                    </div>
                                </div>

                                <div class="od-step @if($step>=2) active @endif">
                                    <div class="dot"></div>
                                    <div class="content">
                                        <div class="title">Shipped</div>
                                        <div class="sub od-muted">Expected soon</div>
                                    </div>
                                </div>

                                <div class="od-step @if($step>=3) active @endif">
                                    <div class="dot"></div>
                                    <div class="content">
                                        <div class="title">Out For Delivery</div>
                                        <div class="sub od-muted">Arriving shortly</div>
                                    </div>
                                </div>

                                <div class="od-step @if($step>=4) active @endif">
                                    <div class="dot"></div>
                                    <div class="content">
                                        <div class="title">Delivery</div>
                                        <div class="sub od-muted">Delivered</div>
                                    </div>
                                </div>

                                {{-- ✅ Open Modal --}}
                                <button type="button" class="od-updates-link" data-od-open="{{ $modalId }}">
                                    See All Updates →
                                </button>

                                {{-- ✅ Modal (fixed close + click outside + esc) --}}
                                <div id="{{ $modalId }}" class="od-modal" aria-hidden="true">
                                    <div class="od-modal-backdrop" data-od-close="{{ $modalId }}"></div>

                                    <div class="od-modal-dialog" role="dialog" aria-modal="true">
                                        <button type="button" class="od-modal-close" data-od-close="{{ $modalId }}">✕</button>

                                        <div class="od-modal-body">
                                            <div class="od-modal-timeline">
                                                @foreach($updates as $i => $u)
                                                    <div class="od-mstep {{ $i === 0 ? 'active' : '' }}">
                                                        <div class="od-mdot"></div>
                                                        <div class="od-mcontent">
                                                            <div class="od-mtitle">
                                                                {{ $u['title'] ?? '' }}
                                                                @if(!empty($u['time']))
                                                                    <span class="od-mtime">{{ $u['time'] }}</span>
                                                                @endif
                                                            </div>

                                                            @if(!empty($u['lines']) && is_array($u['lines']))
                                                                @foreach($u['lines'] as $line)
                                                                    <div class="od-mlines">{{ $line }}</div>
                                                                @endforeach
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Cancel button --}}
                            @if(in_array($order->status, ['pending','confirmed']))
                                <div class="od-actions">
                                    <button class="od-cancel-btn" onclick="cancelOrder('{{ $order->id }}')">Cancel Order</button>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach

            </div>

            {{-- RIGHT --}}
            <div class="od-right">
                {{-- Delivery details --}}
                <div class="od-sidecard">
                    <div class="od-side-title">Delivery details</div>

                    <div class="od-delivery-row">
                        <div class="od-ico">📍</div>
                        <div class="od-delivery-text">
                            <div class="od-delivery-main">
                                {{ $order->address ?? 'Address not available' }}<br>
                                @if(!empty($order->city)){{ $order->city }}, @endif
                                @if(!empty($order->state)){{ $order->state }}, @endif
                                @if(!empty($order->zip)){{ $order->zip }} @endif
                            </div>
                            <div class="od-muted">{{ $order->country ?? 'India' }}</div>
                        </div>
                        <div class="od-arrow">›</div>
                    </div>

                    <div class="od-delivery-row">
                        <div class="od-ico">👤</div>
                        <div class="od-delivery-text">
                            <div class="od-delivery-main">{{ $order->first_name ?? 'Customer' }} {{ $order->last_name ?? '' }}</div>
                            <div class="od-muted">{{ $order->phone ?? '' }}</div>
                        </div>
                        <div class="od-arrow">›</div>
                    </div>
                </div>

                {{-- Price details --}}
                @php
                    $itemsTotal = 0;
                    foreach($order->items as $it){
                        $itemsTotal += (($it->price ?? 0) * ($it->quantity ?? 1));
                    }

                    $listingPrice = $order->listing_price ?? $itemsTotal;
                    $sellingPrice = $order->selling_price ?? $itemsTotal;
                    $discount     = $order->discount_amount ?? max(0, ($listingPrice - $sellingPrice));
                    $totalAmount  = $order->total_amount ?? $sellingPrice;
                    $paymentMethodText = ($order->payment_method ?? 'cod') === 'cod' ? 'Cash On Delivery' : 'Online Payment';
                @endphp

                <div class="od-sidecard">
                    <div class="od-side-title">Price details</div>

                    <div class="od-pricebox">
                        <div class="od-row">
                            <span>Listing price</span>
                            <span>₹{{ number_format($listingPrice, 0) }}</span>
                        </div>
                        <div class="od-row">
                            <span>Selling price</span>
                            <span>₹{{ number_format($sellingPrice, 0) }}</span>
                        </div>
                        <div class="od-row">
                            <span>Other discount</span>
                            <span class="od-green">-₹{{ number_format($discount, 0) }}</span>
                        </div>

                        <div class="od-divider"></div>

                        <div class="od-row od-total">
                            <span>Total amount</span>
                            <span>₹{{ number_format($totalAmount, 0) }}</span>
                        </div>

                        <div class="od-payment">
                            <div class="od-row">
                                <span>Payment method</span>
                                <span class="od-paymethod">{{ $paymentMethodText }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <a href="{{ route('user.orders') }}" class="od-back">← Back to Orders</a>
            </div>
        </div>
    </div>
</div>

<script>
    function cancelOrder(orderId) {
        // Show the cancellation modal
        const cancelModal = document.getElementById('cancelOrderModal');
        if(cancelModal) {
            cancelModal.style.display = 'block';
            document.body.classList.add('od-modal-open');
            
            // Set the order ID in the form
            document.getElementById('cancelOrderId').value = orderId;
        }
    }
    
    function closeCancelModal() {
        const cancelModal = document.getElementById('cancelOrderModal');
        if(cancelModal) {
            cancelModal.style.display = 'none';
            document.body.classList.remove('od-modal-open');
        }
    }
    
    function submitCancellation() {
        const reason = document.querySelector('input[name="cancel_reason"]:checked');
        const orderId = document.getElementById('cancelOrderId').value;
        const otherReason = document.getElementById('otherReason').value;
        
        if (!reason && !otherReason.trim()) {
            alert('Please select a reason or provide other reason');
            return;
        }
        
        let cancelReason = otherReason.trim();
        if (reason) {
            cancelReason = reason.value;
        }
        
        fetch(`/my-orders/${orderId}/cancel`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                reason: cancelReason
            })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Unable to cancel order.');
            }
        })
        .catch(() => {
            alert('Something went wrong.');
        });
    }
    
    function toggleOtherReason(checkbox) {
        const otherInput = document.getElementById('otherReason');
        if (checkbox.checked) {
            otherInput.style.display = 'block';
            otherInput.focus();
        } else {
            otherInput.style.display = 'none';
            otherInput.value = '';
        }
    }

    // ✅ Modal open/close (FIXED)
    function odOpenModal(id){
        const modal = document.getElementById(id);
        if(!modal) return;

        modal.classList.add('show');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('od-modal-open');
    }

    function odCloseModal(id){
        const modal = document.getElementById(id);
        if(!modal) return;

        modal.classList.remove('show');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('od-modal-open');
    }

    document.addEventListener('click', function(e){
        const openBtn  = e.target.closest('[data-od-open]');
        const closeBtn = e.target.closest('[data-od-close]');

        if(openBtn){
            odOpenModal(openBtn.getAttribute('data-od-open'));
            return;
        }

        if(closeBtn){
            odCloseModal(closeBtn.getAttribute('data-od-close'));
            return;
        }
    });

    document.addEventListener('keydown', function(e){
        if(e.key === 'Escape'){
            document.querySelectorAll('.od-modal.show').forEach(m => {
                m.classList.remove('show');
                m.setAttribute('aria-hidden', 'true');
            });
            document.body.classList.remove('od-modal-open');
        }
    });
</script>

<style>
    .od-wrap{ background: #f7f7f7; padding: 22px 14px 60px; }
    .od-inner{ max-width: 1180px; margin: 0 auto; }
    .od-breadcrumb{ font-size: 0.9rem; color: #6c757d; margin-bottom: 14px; display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }
    .od-breadcrumb a{ color: #6c757d; text-decoration: none; }
    .od-breadcrumb a:hover{ text-decoration: underline; }
    .od-muted{ color: #6c757d; }

    .od-grid{ display: grid; grid-template-columns: 1fr 360px; gap: 18px; align-items: start; }

    .od-card, .od-sidecard{
        background: #fff;
        border: 1px solid rgba(0,0,0,0.06);
        border-radius: 14px;
        box-shadow: 0 10px 22px rgba(0,0,0,0.06);
    }

    .od-paybar{
        background: #fff;
        border: 1px solid rgba(0,0,0,0.06);
        border-radius: 14px;
        padding: 14px 16px;
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        box-shadow: 0 10px 22px rgba(0,0,0,0.06);
    }
    .od-paybar-text{ font-weight: 600; color: #111; font-size: 0.95rem; }
    .od-pay-btn{
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 10px 16px;
        border-radius: 12px;
        border: 1px solid #0d6efd;
        color: #0d6efd;
        background: #fff;
        font-weight: 800;
        text-decoration: none;
        white-space: nowrap;
    }
    .od-pay-btn:hover{ background: #0d6efd; color: #fff; }

    .od-item{ display: flex; justify-content: space-between; gap: 14px; padding: 18px; }
    .od-item-info{ min-width: 0; flex: 1; }
    .od-item-title{ font-size: 1.08rem; font-weight: 800; line-height: 1.35rem; color: #111; }
    .od-seller{ margin-top: 6px; font-size: 0.9rem; }

    .od-price-row{ display: flex; align-items: baseline; gap: 12px; margin-top: 10px; }
    .od-price{ font-size: 1.25rem; font-weight: 900; color: #111; }
    .od-offers{ color: #198754; font-weight: 800; font-size: 0.92rem; }

    .od-variant{ margin-top: 10px; font-size: 0.92rem; color: #6c757d; }

    .od-item-thumb{
        width: 96px;
        height: 96px;
        border-radius: 14px;
        overflow: hidden;
        background: #f3f4f6;
        border: 1px solid rgba(0,0,0,0.06);
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 96px;
    }
    .od-item-thumb img{ width: 100%; height: 100%; object-fit: cover; }

    .od-track{ border-top: 1px solid rgba(0,0,0,0.06); padding: 14px 18px 18px; }
    .od-step{ display: grid; grid-template-columns: 18px 1fr; gap: 10px; padding: 10px 0; position: relative; }
    .od-step .dot{
        width: 12px; height: 12px; border-radius: 50%;
        margin-top: 4px; background: #cfd4da; border: 2px solid #cfd4da;
        position: relative; z-index: 2;
    }
    .od-step:before{
        content: "";
        position: absolute;
        left: 5px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e9ecef;
        z-index: 1;
    }
    .od-step:first-child:before{ top: 14px; }
    .od-step:last-child:before{ bottom: 14px; }
    .od-step.active .dot{
        background: #198754;
        border-color: #198754;
        box-shadow: 0 0 0 6px rgba(25,135,84,0.12);
    }
    .od-step .title{ font-weight: 800; color: #111; font-size: 0.95rem; }
    .od-step .sub{ margin-top: 2px; font-size: 0.86rem; }

    /* Open link */
    .od-updates-link{
        margin-top: 10px;
        border: none;
        background: transparent;
        padding: 0;
        font-weight: 900;
        color: #0d6efd;
        cursor: pointer;
    }
    .od-updates-link:hover{ text-decoration: underline; }

    /* ✅ MODAL: ensure close button clickable (your issue was z-index/position) */
    .od-modal{ display: none; }
    .od-modal.show{ display: block; }

    .od-modal-backdrop{
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.55);
        z-index: 9998;
    }
    .od-modal-dialog{
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: min(820px, calc(100vw - 28px));
        height: min(560px, calc(100vh - 60px));
        background: #fff;
        border-radius: 14px;
        border: 1px solid rgba(0,0,0,0.10);
        box-shadow: 0 18px 50px rgba(0,0,0,0.25);
        z-index: 9999;
        overflow: hidden;
    }

    .od-modal-close{
        position: absolute;
        top: 10px;
        right: 10px;
        width: 42px;
        height: 42px;
        border: none;
        border-radius: 12px;
        background: rgba(0,0,0,0.06);
        font-size: 22px;
        line-height: 1;
        cursor: pointer;
        z-index: 10000; /* ✅ important */
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .od-modal-close:hover{ background: rgba(0,0,0,0.12); }

    .od-modal-body{
        height: 100%;
        padding: 18px;
        padding-top: 56px; /* ✅ space for close button (fix empty area issue) */
        overflow: auto;
    }

    /* timeline */
    .od-modal-timeline{
        position: relative;
        padding-left: 26px;
    }
    .od-modal-timeline:before{
        content: "";
        position: absolute;
        left: 9px;
        top: 6px;
        bottom: 6px;
        width: 2px;
        background: #e9ecef;
    }
    .od-mstep{ position: relative; padding: 8px 0 16px; }
    .od-mdot{
        position: absolute;
        left: -26px;
        top: 14px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #cfd4da;
    }
    .od-mstep.active .od-mdot{
        background: #198754;
        box-shadow: 0 0 0 6px rgba(25,135,84,0.14);
    }
    .od-mtitle{
        font-weight: 900;
        color: #111;
        font-size: 0.98rem;
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: baseline;
    }
    .od-mtime{
        font-weight: 700;
        color: #6c757d;
        font-size: 0.88rem;
    }
    .od-mlines{
        margin-top: 6px;
        color: #111;
        font-size: 0.92rem;
        line-height: 1.25rem;
    }

    .od-actions{ margin-top: 14px; }
    .od-cancel-btn{
        width: 100%;
        height: 48px;
        border-radius: 14px;
        background: #7d1b7e;
        border: 1px solid #7d1b7e;
        color: #fff;
        font-weight: 900;
        letter-spacing: 0.2px;
    }
    .od-cancel-btn:hover{ background: #6c176d; border-color: #6c176d; }

    .od-cancel-box{
        background: #fff4f4;
        border: 1px solid rgba(220,53,69,0.25);
        border-radius: 12px;
        padding: 12px 12px;
        margin-top: 8px;
    }
    .od-cancel-title{ font-weight: 900; color: #dc3545; }

    .od-highlighted{
        border: 2px solid #0d6efd;
        box-shadow: 0 0 0 3px rgba(13,110,253,0.25);
    }

    .od-sidecard{ padding: 16px; }
    .od-side-title{ font-size: 1.05rem; font-weight: 900; color: #111; margin-bottom: 12px; }

    .od-delivery-row{
        display: grid;
        grid-template-columns: 26px 1fr 18px;
        gap: 10px;
        padding: 10px 0;
        align-items: center;
        border-bottom: 1px solid rgba(0,0,0,0.06);
    }
    .od-delivery-row:last-child{ border-bottom: none; }
    .od-ico{ font-size: 18px; }
    .od-arrow{ color: #adb5bd; font-size: 18px; }
    .od-delivery-main{ font-weight: 700; color: #111; font-size: 0.92rem; line-height: 1.2rem; }

    .od-pricebox{ margin-top: 6px; }
    .od-row{ display: flex; align-items: center; justify-content: space-between; padding: 8px 0; font-size: 0.95rem; color: #111; }
    .od-green{ color: #198754; font-weight: 900; }
    .od-divider{ height: 1px; background: rgba(0,0,0,0.08); margin: 10px 0; }
    .od-total{ font-weight: 900; font-size: 1.02rem; }
    .od-payment{ margin-top: 10px; padding-top: 10px; border-top: 1px dashed rgba(0,0,0,0.12); }
    .od-paymethod{ font-weight: 900; }

    .od-back{
        display: inline-block;
        margin-top: 14px;
        text-decoration: none;
        font-weight: 900;
        color: #6c757d;
    }
    .od-back:hover{ text-decoration: underline; }

    @media (max-width: 992px){
        .od-grid{ grid-template-columns: 1fr; }
    }

    body.od-modal-open{ overflow: hidden; }
</style>

{{-- Cancel Order Modal --}}
<div id="cancelOrderModal" class="od-modal" aria-hidden="true" style="display:none;">
    <div class="od-modal-backdrop" onclick="closeCancelModal()"></div>
    
    <div class="od-modal-dialog" role="dialog" aria-modal="true">
        <button type="button" class="od-modal-close" onclick="closeCancelModal()">✕</button>
        
        <div class="od-modal-body">
            <h3 style="margin-top:0;margin-bottom:16px;font-weight:900;">Cancel Order</h3>
            
            <p style="margin-bottom:16px;">Please select a reason for cancelling your order:</p>
            
            <form id="cancelOrderForm">
                <input type="hidden" id="cancelOrderId" name="order_id">
                
                <div style="margin-bottom:16px;">
                    <label style="display:block;margin-bottom:8px;font-weight:700;">Select Reason:</label>
                    
                    <div style="margin-bottom:12px;">
                        <input type="radio" id="reason1" name="cancel_reason" value="Changed my mind">
                        <label for="reason1" style="margin-left:8px;">Changed my mind</label>
                    </div>
                    
                    <div style="margin-bottom:12px;">
                        <input type="radio" id="reason2" name="cancel_reason" value="Found cheaper price elsewhere">
                        <label for="reason2" style="margin-left:8px;">Found cheaper price elsewhere</label>
                    </div>
                    
                    <div style="margin-bottom:12px;">
                        <input type="radio" id="reason3" name="cancel_reason" value="Delivery time too long">
                        <label for="reason3" style="margin-left:8px;">Delivery time too long</label>
                    </div>
                    
                    <div style="margin-bottom:12px;">
                        <input type="radio" id="reason4" name="cancel_reason" value="Ordered by mistake">
                        <label for="reason4" style="margin-left:8px;">Ordered by mistake</label>
                    </div>
                    
                    <div style="margin-bottom:12px;">
                        <input type="radio" id="reason5" name="cancel_reason" value="No longer needed">
                        <label for="reason5" style="margin-left:8px;">No longer needed</label>
                    </div>
                    
                    <div style="margin-bottom:12px;">
                        <input type="radio" id="reason_other" name="cancel_reason" value="" onchange="toggleOtherReason(this)">
                        <label for="reason_other" style="margin-left:8px;">Other (please specify):</label>
                        <input type="text" id="otherReason" placeholder="Enter your reason here" style="width:100%;margin-top:8px;padding:8px;border:1px solid #ccc;border-radius:4px;display:none;">
                    </div>
                </div>
                
                <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:20px;">
                    <button type="button" class="od-cancel-btn" style="background:#6c757d;border-color:#6c757d;" onclick="closeCancelModal()">Close</button>
                    <button type="button" class="od-cancel-btn" style="background:#dc3545;border-color:#dc3545;" onclick="submitCancellation()">Confirm Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
