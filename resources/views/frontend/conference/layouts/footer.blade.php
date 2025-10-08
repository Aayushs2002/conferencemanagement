  <footer class="footer pt-5">
      <div class="container">
          <div class="row gy-4">
              <div class="col-lg-3 col-md-6">
                  <h5 class="footer-title mb-3">NESOG</h5>
                  <ul class="list-unstyled footer-links">
                      <li><a href="{{ route('conference.name', $conference->slug) }}" class="footer-link">Conference
                              Overview</a></li>
                      <li><a href="{{ route('conference.scientific-session', $conference->slug) }}"
                              class="footer-link">Scientific Sessions</a></li>
                      <li><a href="{{ route('conference.news-and-notice', $conference->slug) }}"
                              class="footer-link">News
                              & Notices</a></li>
                      <li><a href="{{ route('conference.register', $conference->slug) }}"
                              class="footer-link">Registration Form</a></li>
                  </ul>
              </div>
              <div class="col-lg-3 col-md-6">
                  <h5 class="footer-title mb-3">About Conference</h5>
                  <ul class="list-unstyled footer-links">
                      <li><a href="{{ route('conference.committe', $conference->slug) }}"
                              class="footer-link">Committee</a></li>
                      <li><a href="{{ route('conference.speaker', $conference->slug) }}"
                              class="footer-link">Speakers</a>
                      </li>
                  </ul>
              </div>
              <div class="col-lg-3 col-md-6">
                  <h5 class="footer-title mb-3">Workshops</h5>
                  <ul class="list-unstyled footer-links">
                      @foreach ($workshops as $workshop)
                          <li><a href="{{ route('conference.workshop.singlePage', [$conference->slug, $workshop->slug]) }}"
                                  class="footer-link">{{ $workshop->workshop_title }}</a></li>
                      @endforeach
                  </ul>
              </div>
              <div class="col-lg-3 col-md-6">
                  <ul class="list-unstyled footer-links mt-4 mt-lg-0">
                      <li><a href="#" class="footer-link">Terms & Conditions</a></li>
                      <li><a href="#" class="footer-link">Privacy Policy</a></li>
                  </ul>
              </div>
          </div>
          <div class="footer-bottom mt-4 pt-3">
              <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                  <p class="mb-0 footer-bottom-text">
                      &copy; {{ date('Y') }} MedCon. All rights reserved.
                  </p>
                  <p class="mb-0 footer-bottom-text">
                      Developed by <a href="#"> Omway Technologies</a>
                  </p>
              </div>
          </div>
      </div>
  </footer>
