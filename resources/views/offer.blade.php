<!-- =========================
     OFFER BANNER (SMALL HEIGHT)
========================== -->
<section id="offersSection" class="offer-banner">

  @php
    $offer = $offers->first();   // only one image
  @endphp

  @if($offer && $offer->image)
    <div class="offer-banner-wrap">
      <img
        src="{{ asset('storage/' . $offer->image) }}"
        alt="{{ $offer->alt_text ?? 'Offer Image' }}"
        class="offer-banner-img"
      >
    </div>
  @endif

</section>


