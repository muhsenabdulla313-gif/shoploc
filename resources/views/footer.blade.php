<!-- FOOTER START -->
<footer class="footer">
  <div class="container footer-container">
    <div class="row footer-row">

      <!-- Column 1 -->
      <div class="col-lg-3 col-md-6 col-sm-12 footer-col">
        <div class="footer__about">
          <div class="footer__about__logo">
            <a href="/">Textails</a>
          </div>
          <ul>
            <li>📍 Deli Kalanad(PO) Kasaragod-671317</li>
            <li>📞 8848748469</li>
            <li>✉️ razicdeli@gmail.com</li>
          </ul>
        </div>
      </div>

      <!-- Column 2 -->
      <div class="col-lg-2 col-md-3 col-sm-6 footer-col">
        <div class="footer__widget">
          <h6>Shop</h6>
          <ul>
            <li><a href="/">New Arrivals</a></li>
            <li><a href="/women">Women's Fashion</a></li>
            <!-- <li><a href="/men">Men's Fashion</a></li> -->
            <!-- <li><a href="/kids">Kids Collection</a></li> -->
            <!-- <li><a href="/blog">Fashion Blog</a></li> -->
          </ul>
        </div>
      </div>

      <!-- Column 3 -->
      <div class="col-lg-2 col-md-3 col-sm-6 footer-col">
        <div class="footer__widget">
          <h6>Support</h6>
          <ul>
            <li><a href="/contact">Contact Us</a></li>
            <li><a href="/privacy-policy">Privacy Policy</a></li>
            <li><a href="/terms-conditions">Terms & Conditions</a></li>
          </ul>
        </div>
      </div>

      <!-- Column 4 -->
      <div class="col-lg-4 col-md-8 col-sm-12 footer-col footer-col-social">
        <div class="footer__widget">
          <h6>Stay Connected</h6>
          <div class="footer__newslatter">
            <div class="footer__social">
              <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
              <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
              <!-- <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a> -->
              <!-- <a href="#" aria-label="Pinterest"><i class="fab fa-pinterest-p"></i></a> -->
              <!-- <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a> -->
            </div>
          </div>
        </div>
      </div>

    </div>

    <!-- Copyright -->
    <div class="footer__copyright">
      <p>
        &copy; <span id="year"></span>
        <a href="https://www.astrasoftwaresolutions.com" target="_blank" rel="noopener noreferrer">
          Astra Software Solutions
        </a>. All rights reserved.
      </p>
    </div>

  </div>
</footer>
<!-- FOOTER END -->

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const yearSpan = document.getElementById('year');
    if (yearSpan) yearSpan.textContent = new Date().getFullYear();
  });
</script>
