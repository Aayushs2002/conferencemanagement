@extends('frontend.conference.layouts.main')
@section('title')
    {{ $conference->society->sub_domain_name }} | Scientific Sessions
@endsection
@section('content')
    <div class="container">
        <div class="mb-2">
            <a href="{{ route('our-client.detail', $conference->society->slug) }}" class="back-btn mb-4">
                <i class="fa-solid fa-arrow-left me-2"></i> Back to {{ $conference->society->abbreviation }}
            </a>
        </div>
    </div>
    <div class="search-panel position-relative mb-5">
        <input type="text" id="workshopsSearch" class="form-control" placeholder="Search Workshops...">
        <i class="fa-solid fa-magnifying-glass search-icon"></i>
        <ul class="list-group position-absolute mt-2" id="suggestions" style="z-index: 1000;"></ul>
    </div>
    <div class="col-10 mx-auto mt-5" style="background-color: #F1F4FC; padding: 50px; border-radius: 30px;">
        <h2 class="section-title">Specialized Workshops</h2>
        {{-- <p class="section-subtitle">Hands-on training sessions with leading experts in Anesthesia specialties</p> --}}
        <div class="row g-4 justify-content-center mb-5 mt-4">
            @foreach ($workshops as $workshop)
                <div class="col-md-4">
                    <div class="workshop-card">
                        <div class="img-container position-relative overflow-hidden {{ !$workshop->image ? 'logo-fallback' : '' }}">
                            <img src="{{ $workshop->image
                                ? Storage::url('workshop/workshop/image/' . $workshop->image)
                                : Storage::url('society/logo/' . $conference->society->logo) }}"
                                class="img-fluid {{ !$workshop->image ? 'logo-img' : '' }}" alt="{{ $workshop->workshop_title }}">
                            <div
                                class="overlay d-flex flex-column justify-content-center align-items-center text-center p-3">
                                <h5 class="workshop-title">{{ $workshop->workshop_title }}</h5>
                                {{-- <p class="mb-3">Advanced diagnostic and therapeutic approaches for fetal care</p> --}}
                                <a href="{{ route('conference.workshop.singlePage', [$conference->slug, $workshop->slug]) }}"
                                    class="btn btn-default">
                                    View Details <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                </a>
                            </div>
                        </div>
                        <div class="workshop-title text-center mt-3">{{ $workshop->workshop_title }}</div>
                    </div>
                </div>
            @endforeach
            {{-- 
            <div class="col-md-4">
                <div class="workshop-card">
                    <div class="img-container position-relative overflow-hidden">
                        <img src="assets/img/genetic.jpg" class="img-fluid" alt="Genetics">
                        <div class="overlay d-flex flex-column justify-content-center align-items-center text-center p-3">
                            <h5 class="workshop-title">Genetics</h5>
                            <p class="mb-3">Cutting-edge genetic testing and counseling techniques.</p>
                            <a href="workshopdetails.html" class="btn btn-default">
                                View Details <i class="fa-solid fa-arrow-up-right-from-square"></i>
                            </a>
                        </div>
                    </div>
                    <div class="workshop-title text-center mt-3">Genetics</div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="workshop-card">
                    <div class="img-container position-relative overflow-hidden">
                        <img src="assets/img/infertility.jpg" class="img-fluid" alt="Infertility">
                        <div class="overlay d-flex flex-column justify-content-center align-items-center text-center p-3">
                            <h5 class="workshop-title">Infertility</h5>
                            <p class="mb-3">Innovative treatments in reproductive medicines.</p>
                            <a href="workshopdetails.html" class="btn btn-default">
                                View Details <i class="fa-solid fa-arrow-up-right-from-square"></i>
                            </a>
                        </div>
                    </div>
                    <div class="workshop-title text-center mt-3">Infertility</div>
                </div>
            </div> --}}

        </div>
        {{-- <div class="row g-4 justify-content-center">
            <div class="col-md-4">
                <div class="workshop-card">
                    <div class="img-container position-relative overflow-hidden">
                        <img src="assets/img/High risk pregnancy.jpg" class="img-fluid" alt="pregnancy">
                        <div class="overlay d-flex flex-column justify-content-center align-items-center text-center p-3">
                            <h5 class="workshop-title">High Risk Pregnancy</h5>
                            <p class="mb-3">The mother or baby could be at risk due to certain medical conditions,
                                lifestyle choices.</p>
                            <a href="#" class="btn btn-default">
                                View Details <i class="fa-solid fa-arrow-up-right-from-square"></i>
                            </a>
                        </div>
                    </div>
                    <div class="workshop-title text-center mt-3">High Risk Pregnancy</div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="workshop-card">
                    <div class="img-container position-relative overflow-hidden">
                        <img src="assets/img/fistula.jpg" class="img-fluid" alt="Fistula">
                        <div class="overlay d-flex flex-column justify-content-center align-items-center text-center p-3">
                            <h5 class="workshop-title">Urogynecology/Obstetric Fistula</h5>
                            <p class="mb-3">Specialized care for pelvic floor and childbirth complications.</p>
                            <a href="#" class="btn btn-default">
                                View Details <i class="fa-solid fa-arrow-up-right-from-square"></i>
                            </a>
                        </div>
                    </div>
                    <div class="workshop-title text-center mt-3">Urogynecology/Obstetric Fistula</div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="workshop-card">
                    <div class="img-container position-relative overflow-hidden">
                        <img src="assets/img/surgery.jpg" class="img-fluid" alt="Minimal Invasive Surgery">
                        <div class="overlay d-flex flex-column justify-content-center align-items-center text-center p-3">
                            <h5 class="workshop-title">Minimal Invasive Surgery</h5>
                            <p class="mb-3">Advanced techniques for precise surgery with faster recovery.</p>
                            <a href="#" class="btn btn-default">
                                View Details <i class="fa-solid fa-arrow-up-right-from-square"></i>
                            </a>
                        </div>
                    </div>
                    <div class="workshop-title text-center mt-3">Minimal Invasive Surgery</div>
                </div>
            </div>

        </div> --}}
    </div>


    <div class="td_height_80 td_height_lg_80"></div>
    <script>
        // Real-time Workshop Search Functionality
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('workshopsSearch');
            const workshopCards = document.querySelectorAll('.workshop-card');
            const workshopContainer = document.querySelector('.row.g-4.justify-content-center.mb-5.mt-4');

            if (!searchInput || workshopCards.length === 0) return;

            // Store original workshop data for filtering
            const workshops = Array.from(workshopCards).map(card => ({
                element: card.closest('.col-md-4'),
                title: card.querySelector('.workshop-title').textContent.trim().toLowerCase(),
                image: card.querySelector('img').alt.toLowerCase()
            }));

            // Search function
            function filterWorkshops(searchTerm) {
                const term = searchTerm.toLowerCase().trim();
                let visibleCount = 0;

                workshops.forEach(workshop => {
                    const matches = workshop.title.includes(term) || workshop.image.includes(term);

                    if (matches || term === '') {
                        workshop.element.style.display = 'block';
                        // Add fade-in animation
                        workshop.element.style.animation = 'fadeIn 0.3s ease-in';
                        visibleCount++;
                    } else {
                        workshop.element.style.display = 'none';
                    }
                });

                // Show "no results" message if needed
                showNoResultsMessage(visibleCount, term);
            }

            // Show/hide no results message
            function showNoResultsMessage(count, term) {
                let noResultsMsg = document.getElementById('noResultsMessage');

                if (count === 0 && term !== '') {
                    if (!noResultsMsg) {
                        noResultsMsg = document.createElement('div');
                        noResultsMsg.id = 'noResultsMessage';
                        noResultsMsg.className = 'col-12 text-center py-5';
                        noResultsMsg.innerHTML = `
                    <i class="fa-solid fa-search fa-3x mb-3" style="color: #ccc;"></i>
                    <h4>No workshops found</h4>
                    <p class="text-muted">Try adjusting your search term</p>
                `;
                        workshopContainer.appendChild(noResultsMsg);
                    }
                    noResultsMsg.style.display = 'block';
                } else if (noResultsMsg) {
                    noResultsMsg.style.display = 'none';
                }
            }

            // Search suggestions functionality
            function updateSuggestions(searchTerm) {
                const suggestionsList = document.getElementById('suggestions');
                const term = searchTerm.toLowerCase().trim();

                if (term === '') {
                    suggestionsList.innerHTML = '';
                    suggestionsList.style.display = 'none';
                    return;
                }

                // Get matching workshops
                const matches = workshops.filter(w =>
                    w.title.includes(term)
                ).slice(0, 5); // Limit to 5 suggestions

                if (matches.length > 0) {
                    suggestionsList.innerHTML = '';

                    matches.forEach((workshop, index) => {
                        const li = document.createElement('li');
                        li.className = 'list-group-item list-group-item-action suggestion-item';
                        li.style.cursor = 'pointer';
                        li.style.borderLeft = '3px solid #007bff';

                        const icon = document.createElement('i');
                        icon.className = 'fa-solid fa-search me-2';

                        const workshopTitle = workshop.element.querySelector('.workshop-title').textContent
                            .trim();
                        const text = document.createTextNode(workshopTitle);

                        li.appendChild(icon);
                        li.appendChild(text);

                        li.addEventListener('click', function() {
                            searchInput.value = workshopTitle;
                            filterWorkshops(workshopTitle);
                            suggestionsList.innerHTML = '';
                            suggestionsList.style.display = 'none';
                        });

                        suggestionsList.appendChild(li);
                    });

                    suggestionsList.style.display = 'block';
                } else {
                    suggestionsList.innerHTML = '';
                    suggestionsList.style.display = 'none';
                }
            }

            // Event listener for search input
            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value;
                filterWorkshops(searchTerm);
                updateSuggestions(searchTerm);
            });

            // Close suggestions when clicking outside
            document.addEventListener('click', function(e) {
                const suggestionsList = document.getElementById('suggestions');
                if (!searchInput.contains(e.target) && !suggestionsList.contains(e.target)) {
                    suggestionsList.innerHTML = '';
                    suggestionsList.style.display = 'none';
                }
            });

            // Clear search on Escape key
            searchInput.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    searchInput.value = '';
                    filterWorkshops('');
                    document.getElementById('suggestions').innerHTML = '';
                    document.getElementById('suggestions').style.display = 'none';
                }
            });
        });

        // Add CSS for smooth animations
        const style = document.createElement('style');
        style.textContent = `
    .img-container {
        height: 300px;
        background-color: #f8f9fa;
    }

    .img-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .logo-fallback {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .logo-img {
        object-fit: contain !important;
        max-height: 100%;
        width: auto !important;
        height: auto !important;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    #suggestions {
        max-width: 100%;
        max-height: 300px;
        overflow-y: auto;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        border-radius: 8px;
    }

    .suggestion-item:hover {
        background-color: #f8f9fa !important;
        border-left-color: #0056b3 !important;
    }

    #workshopsSearch {
        transition: all 0.3s ease;
    }

    #workshopsSearch:focus {
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        border-color: #80bdff;
    }
`;
        document.head.appendChild(style);
    </script>
@endsection
