  <footer class="footer">
      <div class="container">
          <div class="row align-items-start justify-content-center footer-row ">
              <div class="col-lg-3 mb-lg-0 text-center text-lg-start">
                  <img src="{{ asset('frontend/assets/img/MEDCON-LOGO.png') }}" alt="MedConAlert Logo"
                      class="footer-logo mb-3" style="max-height: 20px !important;">
                  <p class="footer-tagline mt-4">
                      Smart, Human-Centered Solutions for Scientific Events
                  </p>
              </div>

              <div class="col-lg-3 col-md-6 mb-md-0 d-flex justify-content-center">
                  <ul class="list-unstyled footer-links text-center text-lg-start">
                      <ul class="list-unstyled footer-links text-center text-lg-start">
                          <li><a href="{{ route('about-us') }}" class="footer-link">About Us</a></li>
                          <li><a href="{{ route('solution') }}" class="footer-link">Solutions</a></li>
                          <li><a href="{{ route('our-client') }}" class="footer-link">Our Clients</a></li>
                          <li><a href="" class="footer-link">Conferences</a></li>
                          <li><a href="{{ route('blog') }}" class="footer-link">Blogs</a></li>
                      </ul>
                  </ul>
              </div>

              <div class="col-lg-3 col-md-6 mb-md-0 d-flex justify-content-center">
                  <ul class="list-unstyled footer-links text-center text-lg-start">
                      <li><a href="{{ route('contact-us') }}" class="footer-link">Contact</a></li>
                      <li><a href="#" class="footer-link">Request a Quote</a></li>
                      <li><a href="{{ route('login') }}" class="footer-link">Login</a></li>
                  </ul>
              </div>

              <div class="col-lg-3 col-md-6 mb-md-0 d-flex justify-content-center">
                  <ul class="list-unstyled footer-links text-center text-lg-start">
                      <li><a href="#" class="footer-link me-3">Terms of Use</a></li>
                      <li><a href="#" class="footer-link">Privacy</a></li>
                  </ul>
              </div>
          </div>
          <div class="social-links d-flex  mb-4">
              <a href="#" class="footer-link mx-3">
                  <i class="fa-brands fa-linkedin"></i>
              </a>
              <a href="#" class="footer-link mx-3">
                  <i class="fa-brands fa-facebook"></i>
              </a>
          </div>

          <div
              class="footer-bottom d-flex flex-column flex-md-row justify-content-between align-items-center pt-3 border-top">
              <div>© Copyright MedConAlert {{ date('Y') }}</div>
              <div>Developed by / <a href="https://omwaytechnologies.com/" target="blank" class="strong">Omway Technology</strong></a>
              </div>
          </div>
      </div>
  </footer>
