(function () {
    "use strict";

    document.documentElement.classList.add("js");

    var header = document.querySelector("[data-landing-header]");
    var menuButton = document.querySelector("[data-landing-menu-open]");
    var mobileMenu = document.querySelector("[data-landing-mobile]");

    function isMobileMenuOpen() {
        return mobileMenu && !mobileMenu.hasAttribute("hidden");
    }

    function openMobileMenu() {
        if (!mobileMenu || !menuButton) {
            return;
        }
        mobileMenu.removeAttribute("hidden");
        menuButton.setAttribute("aria-expanded", "true");
    }

    function closeMobileMenu() {
        if (!mobileMenu || !menuButton) {
            return;
        }
        mobileMenu.setAttribute("hidden", "");
        menuButton.setAttribute("aria-expanded", "false");
    }

    if (menuButton && mobileMenu) {
        menuButton.addEventListener("click", function () {
            if (isMobileMenuOpen()) {
                closeMobileMenu();
            } else {
                openMobileMenu();
            }
        });

        mobileMenu.querySelectorAll("a").forEach(function (link) {
            link.addEventListener("click", closeMobileMenu);
        });

        document.addEventListener("click", function (event) {
            if (!isMobileMenuOpen()) {
                return;
            }
            if (event.target.closest("[data-landing-mobile]") || event.target.closest("[data-landing-menu-open]")) {
                return;
            }
            closeMobileMenu();
        });

        document.addEventListener("keydown", function (event) {
            if (event.key === "Escape") {
                closeMobileMenu();
            }
        });

        window.addEventListener("resize", function () {
            if (window.innerWidth > 760) {
                closeMobileMenu();
            }
        });
    }

    document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
        anchor.addEventListener("click", function (event) {
            var targetId = anchor.getAttribute("href");
            if (!targetId || targetId === "#") {
                return;
            }
            var target = document.querySelector(targetId);
            if (!target) {
                return;
            }
            event.preventDefault();
            var top = target.getBoundingClientRect().top + window.scrollY - (header ? header.offsetHeight + 10 : 0);
            window.scrollTo({ top: top, behavior: "smooth" });
        });
    });

    var prefersReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

    if (!prefersReducedMotion && "IntersectionObserver" in window) {
        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add("in");
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.14 });

        document.querySelectorAll("[data-reveal]").forEach(function (element) {
            observer.observe(element);
        });
    } else {
        document.querySelectorAll("[data-reveal]").forEach(function (element) {
            element.classList.add("in");
        });
    }

    var themeModal = document.querySelector("[data-theme-modal]");
    var themeModalTitle = document.querySelector("[data-theme-modal-title]");
    var themeModalImage = document.querySelector("[data-theme-modal-image]");
    var lastThemeTrigger = null;

    function openDemoModal(title, imageUrl) {
        if (!themeModal || !themeModalTitle || !themeModalImage) {
            return;
        }

        themeModalTitle.textContent = title;
        themeModalImage.src = imageUrl;
        themeModalImage.alt = title + " preview";
        themeModal.removeAttribute("hidden");
        document.documentElement.style.overflow = "hidden";

        var closeButton = themeModal.querySelector("[data-theme-modal-close]");
        if (closeButton) {
            closeButton.focus();
        }
    }

    function closeDemoModal() {
        if (!themeModal || themeModal.hasAttribute("hidden")) {
            return;
        }

        themeModal.setAttribute("hidden", "");
        document.documentElement.style.removeProperty("overflow");

        if (themeModalImage) {
            themeModalImage.removeAttribute("src");
            themeModalImage.removeAttribute("alt");
        }

        if (lastThemeTrigger) {
            lastThemeTrigger.focus();
        }
    }

    window.openDemoModal = openDemoModal;

    document.querySelectorAll("[data-demo-title][data-demo-image]").forEach(function (button) {
        button.addEventListener("click", function () {
            lastThemeTrigger = button;
            openDemoModal(button.dataset.demoTitle, button.dataset.demoImage);
        });
    });

    document.querySelectorAll("[data-theme-modal-close]").forEach(function (closeTarget) {
        closeTarget.addEventListener("click", closeDemoModal);
    });

    document.addEventListener("keydown", function (event) {
        if (event.key === "Escape") {
            closeDemoModal();
        }
    });

    if (!prefersReducedMotion) {
        var parallaxItems = document.querySelectorAll("[data-parallax]");
        var ticking = false;

        function updateParallax() {
            var scrollY = window.scrollY;
            parallaxItems.forEach(function (item) {
                var speed = Number(item.getAttribute("data-parallax") || 0);
                item.style.transform = "translateY(" + String(scrollY * speed * 0.18) + "px)";
            });
            ticking = false;
        }

        window.addEventListener("scroll", function () {
            if (!ticking) {
                ticking = true;
                window.requestAnimationFrame(updateParallax);
            }
        }, { passive: true });
        updateParallax();
    }
})();
