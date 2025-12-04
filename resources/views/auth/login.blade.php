<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form id="formAuthentication" class="mb-4" method="POST" action="{{ route('login') }}">
        @csrf
        <div class="row">
            <div class="col-md-6">
                <div class="mb-6 form-control-validation">
                    <label for="email" class="form-label">Email</label>
                    <input type="text" class="form-control" id="email" name="email"
                        placeholder="Enter your email" autofocus />
                    @error('email')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-6 form-password-toggle form-control-validation">
                    <label class="form-label" for="password">Password</label>
                    <div class="input-group input-group-merge">
                        <input type="password" id="password" class="form-control" name="password"
                            placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                            aria-describedby="password" />
                        <span class="input-group-text cursor-pointer"><i class="icon-base ti tabler-eye-off"></i></span>
                    </div>
                    @error('password')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>
                <div class="my-8">
                    <div class="d-flex justify-content-between">
                        <div class="form-check mb-0 ms-2">
                            <input class="form-check-input" type="checkbox" id="remember-me" />
                            <label class="form-check-label" for="remember-me"> Remember Me </label>
                        </div>
                        <a href="{{ route('password.request') }}">
                            <p class="mb-0">Forgot Password?</p>
                        </a>
                    </div>
                </div>
                <div class="mb-6">
                    <button class="btn btn-primary d-grid w-100" type="submit">Login</button>
                </div>
    </form>

    <p class="text-center">
        <span>Not Signed Up Yet?</span>
        <a href="{{ route('register') }}">
            <span>Create an account</span>
        </a>
    </p>
    </div>
    <div class="col-md-6">
        <div class="login-box"
            style="background:#f0e9fe;  text-align:center; height:auto; padding:40px 20px 50px; color:#000;">
            @php
                $nextConference = null;
                if (!empty($society) && $society->conferences) {
                    $upcoming = $society->conferences
                        ->filter(function ($c) {
                            return \Carbon\Carbon::parse($c->start_date)->isToday() ||
                                \Carbon\Carbon::parse($c->start_date)->isFuture();
                        })
                        ->sortBy('start_date');

                    $nextConference = $upcoming->first();

                    if (!$nextConference) {
                        $nextConference = $society->conferences->sortByDesc('start_date')->first();
                    }
                }
            @endphp

            @if (!empty($nextConference))
                @if (!empty($nextConference->conference_logo))
                    <a href="#" class="app-brand-link justify-content-center">
                        <img src="{{ asset('storage/conference/conference/logo/' . $nextConference->conference_logo) }}"
                            style="height:50px;">
                    </a>
                @else
                    <div class="text-center">
                        <h3>{{ $nextConference->conference_name ?? 'Upcoming Conference' }}</h3>
                    </div>
                @endif

                <br />
                <div class="mt-2" style="font-style:italic">{{ $nextConference->conference_theme ?? '' }}</div>
                <br /><br />
                <p><strong>Time & Venue </strong><br />
                    {{ \Carbon\Carbon::parse($nextConference->start_date)->format('j M, Y') }}
                    @if (!empty($nextConference->end_date))
                        - {{ \Carbon\Carbon::parse($nextConference->end_date)->format('j M, Y') }}
                    @endif
                    <br />
                    @if (!empty($nextConference->ConferenceVenueDetail))
                        {{ $nextConference->ConferenceVenueDetail->venue_name ?? '' }}
                        @if (!empty($nextConference->ConferenceVenueDetail->venue_address))
                            , {{ $nextConference->ConferenceVenueDetail->venue_address }}
                        @endif
                    @else
                        Venue details coming soon
                    @endif
                </p>
            @else
                {{-- <h3>Medcon Alert</h3> --}}
                <div class="text-center">
                    <img src="{{ asset('frontend/assets/img/MEDCON-LOGO-blue.png') }}" alt="Medcon Alert">
                </div>
            @endif

            <!-- Guidelines Section -->
            @php
                $conferenceSetting = $nextConference->conferenceSetting ?? null;
                $hasGuidelines = false;
                if ($conferenceSetting) {
                    $hasGuidelines = 
                        $conferenceSetting->registration_guideline ||
                        $conferenceSetting->registration_guideline_youtube;
                }
            @endphp

            @if ($hasGuidelines)
                <div class="mt-4">
                    <h6 class="mb-3"><strong>📚 Guidelines & Resources</strong></h6>

                    <!-- PDF Guideline Button -->
                    @if ($conferenceSetting->registration_guideline)
                        <div class="mb-2">
                            <a href="{{ asset('storage/conference/registration-guideline/' . $conferenceSetting->registration_guideline) }}"
                                target="_blank" class="btn btn-outline-primary btn-sm w-100">
                                <i class="ti ti-file-text me-1"></i> Download PDF Guidelines
                            </a>
                        </div>
                    @endif

                    <!-- Video Guidelines Buttons -->
                    @if ($conferenceSetting->registration_guideline_youtube)
                        <div class="mb-2">
                            <button type="button" class="btn btn-outline-danger btn-sm w-100"
                                onclick="showVideoModal('registration', '{{ $conferenceSetting->registration_guideline_youtube }}')">
                                <i class="ti ti-video me-1"></i> Registration Video Guide
                            </button>
                        </div>
                    @endif
                </div>

            @endif
        </div>
    </div>
    </div>

    <!-- Video Modal -->
    <div class="modal fade" id="videoGuidelineModal" tabindex="-1" aria-labelledby="videoGuidelineModalLabel"
        aria-hidden="true" data-bs-backdrop="true" data-bs-keyboard="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header mb-3">
                    <h5 class="modal-title" id="videoGuidelineModalLabel">Video Guidelines</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        onclick="closeVideoModal()"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="ratio ratio-16x9">
                        <iframe id="videoGuidelineFrame" src="" frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen>
                        </iframe>
                    </div>
                </div>
                <div class="modal-footer mt-3">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                        onclick="closeVideoModal()">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let videoModalInstance = null;

        function openGuidelines() {
            window.open("{{ asset('backend/guideline/Steps_for_Registering_in_SAFOGCON.pdf') }}", '_blank');
        }

        function showVideoModal(type, url) {
            // Convert YouTube URL to embed format
            let embedUrl = convertToEmbedUrl(url);

            // Update modal title based on type
            let titles = {
                'registration': 'Registration Video Guidelines',
                'submission': 'Submission Video Guidelines',
                'expert': 'Expert Video Guidelines'
            };

            document.getElementById('videoGuidelineModalLabel').textContent = titles[type] || 'Video Guidelines';

            // Set iframe source
            document.getElementById('videoGuidelineFrame').src = embedUrl;

            // Show modal
            if (videoModalInstance) {
                videoModalInstance.dispose();
            }
            videoModalInstance = new bootstrap.Modal(document.getElementById('videoGuidelineModal'), {
                backdrop: true,
                keyboard: true,
                focus: true
            });
            videoModalInstance.show();
        }

        function closeVideoModal() {
            // Clear iframe to stop video
            document.getElementById('videoGuidelineFrame').src = '';

            // Hide modal
            if (videoModalInstance) {
                videoModalInstance.hide();
            }
        }

        function convertToEmbedUrl(url) {
            // Handle various YouTube URL formats
            let videoId = '';

            // Standard watch URL: https://www.youtube.com/watch?v=VIDEO_ID
            if (url.includes('youtube.com/watch?v=')) {
                videoId = url.split('watch?v=')[1].split('&')[0];
            }
            // Short URL: https://youtu.be/VIDEO_ID
            else if (url.includes('youtu.be/')) {
                videoId = url.split('youtu.be/')[1].split('?')[0];
            }
            // Already embed URL
            else if (url.includes('youtube.com/embed/')) {
                return url;
            }

            return `https://www.youtube.com/embed/${videoId}?autoplay=1&rel=0`;
        }

        // Clear iframe when modal is closed to stop video
        document.addEventListener('DOMContentLoaded', function() {
            var videoModal = document.getElementById('videoGuidelineModal');
            if (videoModal) {
                videoModal.addEventListener('hidden.bs.modal', function() {
                    document.getElementById('videoGuidelineFrame').src = '';
                    if (videoModalInstance) {
                        videoModalInstance.dispose();
                        videoModalInstance = null;
                    }
                });

                // Allow clicking backdrop to close
                videoModal.addEventListener('click', function(e) {
                    if (e.target === videoModal) {
                        closeVideoModal();
                    }
                });
            }
        });
    </script>
</x-guest-layout>
