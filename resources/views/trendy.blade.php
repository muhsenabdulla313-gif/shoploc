<style>
.tt-triple{
  padding: 40px 0 30px;
  background: #fff;
}

.tt-wrap{
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 18px;
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 90px; /* screenshot-like spacing */
}

/* Title */
.tt-title{
  font-size: 16px;
  font-weight: 800;
  letter-spacing: .4px;
  margin: 0 0 26px;
  text-transform: uppercase;
  position: relative;
}
.tt-title::after{
  content:"";
  display:block;
  width: 48px;
  height: 2px;
  background: #e11;
  margin-top: 8px;
}

/* Item row */
.tt-item{
  display: flex;
  align-items: center;
  gap: 20px;
  text-decoration: none;
  color: inherit;
  padding: 12px 0;
}

/* Thumb */
.tt-thumb{
  width: 92px;
  height: 92px;
  background: #f2f2f2;
  overflow: hidden;
  flex-shrink: 0;
}
.tt-thumb img{
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

/* Text */
.tt-info{ min-width: 0; }
.tt-name{
  font-size: 15px;
  font-weight: 500;
  margin: 0 0 8px;
  line-height: 1.35;
  max-width: 220px; /* wrap like screenshot */
}

/* Stars */
.tt-stars{ margin-bottom: 8px; }
.tt-star{
  font-size: 12px;
  color: #cfcfcf;
  letter-spacing: 1px;
}
.tt-star-fill{ color: #f4b400; } /* gold */

/* Price */
.tt-price{
  font-size: 16px;
  font-weight: 800;
}

/* Hover */
.tt-item:hover .tt-name{
  text-decoration: underline;
}

/* Empty */
.tt-empty{
  margin: 16px 0 0;
  font-weight: 700;
  opacity: .7;
}

/* Responsive */
@media (max-width: 992px){
  .tt-wrap{ grid-template-columns: 1fr 1fr; gap: 50px; }
}
@media (max-width: 600px){
  .tt-wrap{ grid-template-columns: 1fr; gap: 28px; }
  .tt-name{ max-width: 100%; }
}

</style>


<!-- =========================
   TRIPLE TREND (Screenshot Style)
========================= -->
<section class="tt-triple">
  <div class="tt-wrap">

    <!-- HOT TREND -->
    <div class="tt-col">
      <h3 class="tt-title">HOT TREND</h3>

      @forelse($trendyProducts->where('trend_type','hot-trend')->take(3) as $product)
        <a href="{{ route('product.show', $product->slug ?? $product->id) }}" class="tt-item">
          <div class="tt-thumb">
            <img
              src="{{ $product->image ? asset('storage/'.$product->image) : 'https://placehold.co/160x160?text=No+Image' }}"
              alt="{{ $product->name }}"
              loading="lazy"
            >
          </div>

          <div class="tt-info">
            <p class="tt-name">{{ $product->name }}</p>

            <div class="tt-stars">
              @php $rate = (int) ($product->rating ?? 5); @endphp
              @for($i=1; $i<=5; $i++)
                <span class="{{ $i <= $rate ? 'tt-star tt-star-fill' : 'tt-star' }}">★</span>
              @endfor
            </div>

            <div class="tt-price">
              ₹{{ number_format($product->price ?? 0, 2) }}
            </div>
          </div>
        </a>
      @empty
        <p class="tt-empty">No hot trend products.</p>
      @endforelse
    </div>

    <!-- BEST SELLER -->
    <div class="tt-col">
      <h3 class="tt-title">BEST SELLER</h3>

      @forelse($trendyProducts->where('trend_type','best-seller')->take(3) as $product)
        <a href="{{ route('product.show', $product->slug ?? $product->id) }}" class="tt-item">
          <div class="tt-thumb">
            <img
              src="{{ $product->image ? asset('storage/'.$product->image) : 'https://placehold.co/160x160?text=No+Image' }}"
              alt="{{ $product->name }}"
              loading="lazy"
            >
          </div>

          <div class="tt-info">
            <p class="tt-name">{{ $product->name }}</p>

            <div class="tt-stars">
              @php $rate = (int) ($product->rating ?? 5); @endphp
              @for($i=1; $i<=5; $i++)
                <span class="{{ $i <= $rate ? 'tt-star tt-star-fill' : 'tt-star' }}">★</span>
              @endfor
            </div>

            <div class="tt-price">
              ₹{{ number_format($product->price ?? 0, 2) }}
            </div>
          </div>
        </a>
      @empty
        <p class="tt-empty">No best seller products.</p>
      @endforelse
    </div>

    <!-- FEATURE -->
    <div class="tt-col">
      <h3 class="tt-title">FEATURE</h3>

      {{-- IMPORTANT: DB trend_type should match. If you use "featured", keep featured --}}
      @forelse($trendyProducts->where('trend_type','featured')->take(3) as $product)
        <a href="{{ route('product.show', $product->slug ?? $product->id) }}" class="tt-item">
          <div class="tt-thumb">
            <img
              src="{{ $product->image ? asset('storage/'.$product->image) : 'https://placehold.co/160x160?text=No+Image' }}"
              alt="{{ $product->name }}"
              loading="lazy"
            >
          </div>

          <div class="tt-info">
            <p class="tt-name">{{ $product->name }}</p>

            <div class="tt-stars">
              @php $rate = (int) ($product->rating ?? 5); @endphp
              @for($i=1; $i<=5; $i++)
                <span class="{{ $i <= $rate ? 'tt-star tt-star-fill' : 'tt-star' }}">★</span>
              @endfor
            </div>

            <div class="tt-price">
              ₹{{ number_format($product->price ?? 0, 2) }}
            </div>
          </div>
        </a>
      @empty
        <p class="tt-empty">No featured products.</p>
      @endforelse
    </div>

  </div>
</section>
