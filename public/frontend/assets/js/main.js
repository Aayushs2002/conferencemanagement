(function ($) {
    "use strict";

    /*--------------------------------------------------------------
    1. Preloader
  --------------------------------------------------------------*/
    function preloader() {
        $(".td_preloader").fadeOut();
        $("td_preloader_in").delay(150).fadeOut("slow");
    }

    /*--------------------------------------------------------------
    2. Mobile Menu
  --------------------------------------------------------------*/
    function mainNav() {
        $(".td_nav").append(
            '<span class="td_menu_toggle"><span></span></span>'
        );
        $(".menu-item-has-children").append(
            '<span class="td_munu_dropdown_toggle"><span></span></span>'
        );
        $(".td_menu_toggle").on("click", function () {
            $(this)
                .toggleClass("td_toggle_active")
                .siblings(".td_nav_list_wrap")
                .toggleClass("td_active");
        });
        $(".td_munu_dropdown_toggle").on("click", function () {
            $(this).toggleClass("active").siblings("ul").slideToggle();
            $(this).parent().toggleClass("active");
        });

        $(".td_header_dropdown_btn").on("click", function () {
            $(this).toggleClass("active");
        });
        /* Search Toggle */
        $(".td_search_tobble_btn").on("click", function () {
            $(".td_header_search_wrap").toggleClass("active");
        });
        /* Side Nav */
        $(".td_hamburger_btn").on("click", function () {
            $(".td_side_header").addClass("active");
            $("html").addClass("td_hamburger_active");
        });
        $(".td_close, .td_side_header_overlay").on("click", function () {
            $(".td_side_header").removeClass("active");
            $("html").removeClass("td_hamburger_active");
        });
    }

    /*--------------------------------------------------------------
    3. Sticky Header
  --------------------------------------------------------------*/
    function stickyHeader() {
        var $window = $(window);
        var lastScrollTop = 0;
        var $header = $(".td_sticky_header");
        var headerHeight = $header.outerHeight() + 20;

        $window.scroll(function () {
            var windowTop = $window.scrolldown();

            if (windowTop >= headerHeight) {
                $header.addClass("td_gescout_sticky");
            } else {
                $header.removeClass("td_gescout_sticky");
                $header.removeClass("td_gescout_show");
            }

            if ($header.hasClass("td_gescout_sticky")) {
                if (windowTop < lastScrollTop) {
                    $header.addClass("td_gescout_show");
                } else {
                    $header.removeClass("td_gescout_show");
                }
            }
            lastScrollTop = windowTop;
        });
    }

    window.addEventListener("scroll", function () {
        const header = document.querySelector(".td_site_header");
        if (window.scrollY > 50) {
            header.classList.add("sticky");
        } else {
            header.classList.remove("sticky");
        }
    });

    /*--------------------------------------------------------------
    7. Scroll Up
  --------------------------------------------------------------*/
    function scrollUp() {
        $(".td_scrollup").on("click", function (e) {
            e.preventDefault();
            $("html,body").animate(
                {
                    scrollTop: 0,
                },
                0
            );
        });
    }
    /* For Scroll Up */
    function showScrollUp() {
        let scroll = $(window).scrollTop();
        if (scroll >= 350) {
            $(".td_scrollup").addClass("td_scrollup_show");
        } else {
            $(".td_scrollup").removeClass("td_scrollup_show");
        }
    }

    /*--------------------------------------------------------------
    filter Speaker
  --------------------------------------------------------------*/
    const filterButtons = document.querySelectorAll("#safogTabs .nav-link");
    const speakers = document.querySelectorAll("#speakersGrid .speaker-card");
    const spanText = document.querySelector(".span-text");

    filterButtons.forEach((btn) => {
        btn.addEventListener("click", () => {
            filterButtons.forEach((b) => b.classList.remove("active"));
            btn.classList.add("active");

            const filter = btn.getAttribute("data-filter");

            switch (filter) {
                case "all":
                    spanText.textContent = "All Speakers";
                    break;
                case "national":
                    spanText.textContent = "National Speakers";
                    break;
                case "faculty":
                    spanText.textContent = "Faculty Speakers";
                    break;
                case "international":
                    spanText.textContent = "International Speakers";
                    break;
                default:
                    spanText.textContent = "";
            }

            speakers.forEach((speaker) => {
                if (filter === "all") {
                    speaker.style.display = "block";
                } else if (filter === "national") {
                    if (speaker.querySelector(".country-flag.flag-nepal")) {
                        speaker.style.display = "block";
                    } else {
                        speaker.style.display = "none";
                    }
                } else if (filter === "faculty") {
                    if (speaker.dataset.category.includes("faculty")) {
                        speaker.style.display = "block";
                    } else {
                        speaker.style.display = "none";
                    }
                } else if (filter === "international") {
                    if (speaker.dataset.category.includes("international")) {
                        speaker.style.display = "block";
                    } else {
                        speaker.style.display = "none";
                    }
                }
            });
        });
    });

    /*--------------------------------------------------------------
Testimonial
  --------------------------------------------------------------*/

    document.addEventListener("DOMContentLoaded", function () {
        const slider = document.querySelector(".testimonial-slider");
        const cards = Array.from(
            document.querySelectorAll(".testimonial-card")
        );
        const container = document.querySelector(".testimonial-container");

        let currentIndex = 0;
        let slideInterval;

        function getVisibleCount() {
            if (window.innerWidth <= 767) return 1;
            if (window.innerWidth <= 991) return 2;
            return 3;
        }

        function cloneCards() {
            const visibleCount = getVisibleCount();
            for (let i = 0; i < visibleCount; i++) {
                const clone = cards[i].cloneNode(true);
                slider.appendChild(clone);
            }
        }

        function updateCardWidth() {
            const visibleCount = getVisibleCount();
            const cardWidth = container.offsetWidth / visibleCount - 20;
            const allCards = slider.querySelectorAll(".testimonial-card");
            allCards.forEach((card) => {
                card.style.flex = `0 0 ${cardWidth}px`;
                card.style.maxWidth = `${cardWidth}px`;
            });
            updateSlider();
        }

        function updateSlider() {
            const cardWidth =
                slider.querySelector(".testimonial-card").offsetWidth + 20;
            slider.style.transition = "transform 0.5s ease";
            slider.style.transform = `translateX(${
                -currentIndex * cardWidth
            }px)`;
        }

        function nextSlide() {
            const visibleCount = getVisibleCount();
            const cardWidth =
                slider.querySelector(".testimonial-card").offsetWidth + 20;
            currentIndex++;
            slider.style.transition = "transform 0.5s ease";
            slider.style.transform = `translateX(${
                -currentIndex * cardWidth
            }px)`;

            if (currentIndex >= cards.length) {
                setTimeout(() => {
                    slider.style.transition = "none";
                    currentIndex = 0;
                    slider.style.transform = `translateX(0px)`;
                }, 500);
            }
        }

        function startAutoSlide() {
            slideInterval = setInterval(nextSlide, 3000);
        }

        function stopAutoSlide() {
            clearInterval(slideInterval);
        }

        slider.addEventListener("mouseenter", stopAutoSlide);
        slider.addEventListener("mouseleave", startAutoSlide);

        window.addEventListener("resize", () => {
            updateCardWidth();
        });

        cloneCards();
        updateCardWidth();
        startAutoSlide();
    });

    /*--------------------------------------------------------------
Confrence time countsdown
  --------------------------------------------------------------*/

    const startDate = new Date("November 8, 2025 00:00:00").getTime();
    const endDate = new Date("November 9, 2025 23:59:59").getTime();

    const timer = setInterval(() => {
        const now = new Date().getTime();
        const distance = startDate - now;

        if (distance > 0) {
            updateClock(distance);
        } else if (now <= endDate) {
            document.querySelector(".countdown-box").innerHTML =
                "<div class='time-box'>Conference In Progress</div>";
        } else {
            clearInterval(timer);
            document.querySelector(".countdown-box").innerHTML =
                "<div class='time-box'>Conference Ended</div>";
        }
    }, 1000);

    function updateClock(dist) {
        const days = Math.floor(dist / (1000 * 60 * 60 * 24));
        const hours = Math.floor(
            (dist % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)
        );
        const minutes = Math.floor((dist % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((dist % (1000 * 60)) / 1000);

        document.getElementById("days").textContent = days;
        document.getElementById("hours").textContent = hours
            .toString()
            .padStart(2, "0");
        document.getElementById("minutes").textContent = minutes
            .toString()
            .padStart(2, "0");
        document.getElementById("seconds").textContent = seconds
            .toString()
            .padStart(2, "0");
    }

    /*--------------------------------------------------------------
Navbar toggler
  --------------------------------------------------------------*/
    window.addEventListener("scroll", function () {
        const navbar = document.querySelector(".navbar");
        if (window.scrollY > 50) {
            navbar.classList.add("scrolled");
        } else {
            navbar.classList.remove("scrolled");
        }
    });

    /*--------------------------------------------------------------
conference menu
  --------------------------------------------------------------*/
    document.addEventListener("DOMContentLoaded", function () {
        const mobileMenuBtn = document.getElementById("mobileMenuBtn");
        const mobileSidebar = document.getElementById("mobileSidebar");
        const closeSidebar = document.getElementById("closeSidebar");
        const sidebarOverlay = document.getElementById("sidebarOverlay");
        const mobileTabLinks = document.querySelectorAll(".mobile-tab-link");
        function toggleSidebar() {
            mobileSidebar.classList.toggle("open");
            sidebarOverlay.classList.toggle("open");
        }

        mobileMenuBtn.addEventListener("click", toggleSidebar);
        closeSidebar.addEventListener("click", toggleSidebar);
        sidebarOverlay.addEventListener("click", toggleSidebar);

        mobileTabLinks.forEach((link) => {
            link.addEventListener("click", function () {
                const target = this.getAttribute("data-bs-target");

                const tabTrigger = new bootstrap.Tab(
                    document.querySelector(`[data-bs-target="${target}"]`)
                );
                tabTrigger.show();

                toggleSidebar();
            });
        });
    });

    /*--------------------------------------------------------------
PASSWORD SHOW
  --------------------------------------------------------------*/
    document.querySelectorAll(".toggle-password").forEach((el) => {
        el.addEventListener("click", function () {
            const input = this.previousElementSibling;
            if (input.type === "password") {
                input.type = "text";
                this.querySelector("i").classList.replace(
                    "fa-eye",
                    "fa-eye-slash"
                );
            } else {
                input.type = "password";
                this.querySelector("i").classList.replace(
                    "fa-eye-slash",
                    "fa-eye"
                );
            }
        });
    });
    /*--------------------------------------------------------------
 News and Notices modal
  --------------------------------------------------------------*/
    document.addEventListener("DOMContentLoaded", function () {
        const modalImg = document.getElementById("modalImage");
        const imgModal = new bootstrap.Modal(
            document.getElementById("imgModal")
        );

        document.querySelectorAll(".default-btn").forEach((btn) => {
            btn.addEventListener("click", function (e) {
                e.preventDefault();

                const imgSrc = this.closest(".card")
                    .querySelector("img")
                    .getAttribute("src");
                modalImg.src = imgSrc;
                imgModal.show();
            });
        });
    });
})(jQuery); // End of use strict
