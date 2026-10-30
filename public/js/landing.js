(function () {
    "use strict";

    document.documentElement.classList.add("js");

    var header = document.querySelector("[data-landing-header]");
    var menuButton = document.querySelector("[data-landing-menu-open]");
    var mobileMenu = document.querySelector("[data-landing-mobile]");
    var whatsappDialog = document.querySelector("[data-whatsapp-dialog]");

    if (whatsappDialog && typeof whatsappDialog.showModal === "function") {
        var whatsappMessage = whatsappDialog.querySelector("[data-whatsapp-message]");
        var whatsappTrigger = null;

        document.querySelectorAll('a[href^="https://wa.me/"]').forEach(function (link) {
            link.addEventListener("click", function (event) {
                event.preventDefault();
                whatsappTrigger = link;
                whatsappDialog.showModal();
            });
        });

        whatsappDialog.querySelector("[data-whatsapp-close]").addEventListener("click", function () {
            whatsappDialog.close();
        });

        whatsappDialog.addEventListener("click", function (event) {
            if (event.target === whatsappDialog) {
                whatsappDialog.close();
            }
        });

        whatsappDialog.addEventListener("close", function () {
            if (whatsappTrigger) {
                whatsappTrigger.focus();
            }
        });

        whatsappDialog.querySelectorAll("[data-whatsapp-suggestion]").forEach(function (button) {
            button.addEventListener("click", function () {
                whatsappMessage.value = button.getAttribute("data-whatsapp-suggestion");
                whatsappMessage.focus();
            });
        });

        whatsappDialog.querySelector("[data-whatsapp-form]").addEventListener("submit", function (event) {
            whatsappMessage.value = whatsappMessage.value.trim();
            if (!whatsappMessage.value) {
                event.preventDefault();
                whatsappMessage.focus();
                return;
            }
            whatsappDialog.close();
        });
    }

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
