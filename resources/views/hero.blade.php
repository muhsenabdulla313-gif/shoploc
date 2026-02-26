<div class="right-panel">

  <div class="two-col-slider">

    <!-- LEFT COLUMN SLIDER -->
    <div class="hero-img-slider" id="heroImgSliderLeft">
      <div class="img-slide active">
        <img src="{{ asset('assets/images/banner/shoploc-banner3.png') }}" alt="Left Banner 1">
      </div>
      <div class="img-slide">
        <img src="{{ asset('assets/images/banner/shoploc-banner.png') }}" alt="Left Banner 2">
      </div>
      <div class="img-slide">
        <img src="{{ asset('assets/images/banner/shoploc-banner2.png') }}" alt="Left Banner 3">
      </div>
    </div>

    <!-- RIGHT COLUMN SLIDER -->
    <div class="hero-img-slider" id="heroImgSliderRight">
      <div class="img-slide active">
        <img src="{{ asset('assets/images/banner/shoploc-banner4.png') }}" alt="Right Banner 1">
      </div>
      <div class="img-slide">
        <img src="{{ asset('assets/images/banner/shoploc-banner1.png') }}" alt="Right Banner 2">
      </div>
      <div class="img-slide">
        <img src="{{ asset('assets/images/banner/shoploc-banner5.png') }}" alt="Right Banner 3">
      </div>
    </div>

  </div>

</div>



<script>
document.addEventListener("DOMContentLoaded", function () {

  function startSlider(sliderId, interval = 3500){
    const slider = document.getElementById(sliderId);
    if (!slider) return;

    const slides = slider.querySelectorAll(".img-slide");
    if (!slides.length) return;

    let current = 0;
    slides.forEach((s,i)=> s.classList.toggle("active", i === 0));

    setInterval(() => {
      slides[current].classList.remove("active");
      current = (current + 1) % slides.length;
      slides[current].classList.add("active");
    }, interval);
  }

  startSlider("heroImgSliderLeft", 3500);
  startSlider("heroImgSliderRight", 4000);
});
</script>
