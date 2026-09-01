(function () {
    "use strict";

    var html = document.documentElement;
    var body = document.body;
    var opening = document.querySelector(".opening");
    var openButton = document.getElementById("open-invitation");
    var audio = document.getElementById("wedding-music");
    var musicButton = document.querySelector(".music-toggle");
    var reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

    requestAnimationFrame(function () {
        body.classList.add("is-ready");
    });

    var progress = document.createElement("div");
    progress.className = "scroll-progress";
    progress.setAttribute("aria-hidden", "true");
    progress.innerHTML = "<span></span>";
    body.appendChild(progress);
    var progressBar = progress.firstElementChild;

    function releasePetals() {
        if (reducedMotion) {
            return;
        }

        var layer = document.createElement("div");
        layer.className = "sweet-petals";

        for (var index = 0; index < 16; index += 1) {
            var petal = document.createElement("i");
            petal.className = "sweet-petal";
            petal.style.left = 4 + Math.random() * 92 + "%";
            petal.style.setProperty("--petal-delay", Math.random() * 1.8 + "s");
            petal.style.setProperty("--petal-duration", 5.5 + Math.random() * 3 + "s");
            petal.style.setProperty("--petal-drift", -55 + Math.random() * 110 + "px");
            petal.style.setProperty("--petal-rotate", 180 + Math.random() * 480 + "deg");
            layer.appendChild(petal);
        }

        body.appendChild(layer);
        window.setTimeout(function () {
            layer.remove();
        }, 9500);
    }

    function openInvitation() {
        opening.classList.add("is-open");
        html.classList.remove("invitation-locked");
        body.classList.add("invitation-opened");
        releasePetals();

        if (audio) {
            audio
                .play()
                .then(function () {
                    musicButton.hidden = false;
                    musicButton.setAttribute("aria-label", "Jeda musik");
                })
                .catch(function () {
                    musicButton.hidden = false;
                    musicButton.classList.add("paused");
                });
        }
    }

    if (openButton) {
        openButton.addEventListener("click", openInvitation);
    }

    if (musicButton) {
        musicButton.addEventListener("click", function () {
            if (audio.paused) {
                audio.play();
                musicButton.classList.remove("paused");
                musicButton.setAttribute("aria-label", "Jeda musik");
            } else {
                audio.pause();
                musicButton.classList.add("paused");
                musicButton.setAttribute("aria-label", "Putar musik");
            }
        });
    }

    var reveals = document.querySelectorAll(".reveal");
    reveals.forEach(function (item, index) {
        if (item.classList.contains("section-head")) {
            item.classList.add("reveal-scale");
        } else if (index % 3 === 1) {
            item.classList.add("reveal-left");
        } else if (index % 3 === 2) {
            item.classList.add("reveal-right");
        }
    });

    if ("IntersectionObserver" in window) {
        var revealObserver = new IntersectionObserver(
            function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add("visible");
                        revealObserver.unobserve(entry.target);
                    }
                });
            },
            { rootMargin: "0px 0px -8%" },
        );
        reveals.forEach(function (item) {
            revealObserver.observe(item);
        });
    } else {
        reveals.forEach(function (item) {
            item.classList.add("visible");
        });
    }

    var navigationLinks = document.querySelectorAll("[data-nav-link]");
    var navigationSections = document.querySelectorAll("[data-nav-section]");
    if ("IntersectionObserver" in window && navigationSections.length) {
        var navigationObserver = new IntersectionObserver(
            function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        navigationLinks.forEach(function (link) {
                            link.classList.toggle(
                                "active",
                                link.dataset.navLink === entry.target.dataset.navSection,
                            );
                        });
                    }
                });
            },
            { rootMargin: "-30% 0px -55%" },
        );
        navigationSections.forEach(function (section) {
            navigationObserver.observe(section);
        });
    }

    var countdown = document.querySelector("[data-countdown]");
    if (countdown) {
        var target = new Date(countdown.dataset.countdown).getTime();
        var fields = {
            days: countdown.querySelector("[data-days]"),
            hours: countdown.querySelector("[data-hours]"),
            minutes: countdown.querySelector("[data-minutes]"),
            seconds: countdown.querySelector("[data-seconds]"),
        };
        var previousSecond = "";
        var update = function () {
            var distance = Math.max(0, target - Date.now());
            fields.days.textContent = String(Math.floor(distance / 86400000)).padStart(2, "0");
            fields.hours.textContent = String(Math.floor(distance / 3600000) % 24).padStart(2, "0");
            fields.minutes.textContent = String(Math.floor(distance / 60000) % 60).padStart(2, "0");
            var second = String(Math.floor(distance / 1000) % 60).padStart(2, "0");
            fields.seconds.textContent = second;

            if (second !== previousSecond && !reducedMotion) {
                fields.seconds.classList.remove("tick");
                void fields.seconds.offsetWidth;
                fields.seconds.classList.add("tick");
            }
            previousSecond = second;
        };
        update();
        window.setInterval(update, 1000);
    }

    var attendance = document.getElementById("attendance");
    var guestCount = document.getElementById("guest_count");
    if (attendance && guestCount) {
        attendance.addEventListener("change", function () {
            var isAttending = attendance.value === "attending";
            guestCount.disabled = !isAttending;
            guestCount.closest(".ivory-field").hidden = !isAttending;
        });
    }

    if (!reducedMotion && window.matchMedia("(hover: hover)").matches) {
        document.querySelectorAll(".portrait").forEach(function (portrait) {
            portrait.addEventListener("pointermove", function (event) {
                var bounds = portrait.getBoundingClientRect();
                var x = (event.clientX - bounds.left) / bounds.width - 0.5;
                var y = (event.clientY - bounds.top) / bounds.height - 0.5;
                portrait.style.setProperty("--tilt-x", -y * 7 + "deg");
                portrait.style.setProperty("--tilt-y", x * 7 + "deg");
                portrait.classList.add("is-tilting");
            });
            portrait.addEventListener("pointerleave", function () {
                portrait.style.removeProperty("--tilt-x");
                portrait.style.removeProperty("--tilt-y");
                portrait.classList.remove("is-tilting");
            });
        });
    }

    var lightbox = document.createElement("div");
    lightbox.className = "sweet-lightbox";
    lightbox.setAttribute("role", "dialog");
    lightbox.setAttribute("aria-modal", "true");
    lightbox.setAttribute("aria-label", "Tampilan foto");
    lightbox.innerHTML = '<button class="lightbox-close" type="button" aria-label="Tutup">×</button><img alt="">';
    body.appendChild(lightbox);
    var lightboxImage = lightbox.querySelector("img");
    var lastGalleryTrigger = null;

    function closeLightbox() {
        lightbox.classList.remove("open");
        html.style.removeProperty("overflow");
        if (lastGalleryTrigger) {
            lastGalleryTrigger.focus();
        }
    }

    document.querySelectorAll(".gallery-grid img").forEach(function (image) {
        var figure = image.closest("figure");
        figure.tabIndex = 0;
        figure.setAttribute("role", "button");
        figure.setAttribute("aria-label", "Perbesar " + image.alt);

        function openGalleryImage() {
            lastGalleryTrigger = figure;
            lightboxImage.src = image.currentSrc || image.src;
            lightboxImage.alt = image.alt;
            lightbox.classList.add("open");
            html.style.overflow = "hidden";
            lightbox.querySelector("button").focus();
        }

        figure.addEventListener("click", openGalleryImage);
        figure.addEventListener("keydown", function (event) {
            if (event.key === "Enter" || event.key === " ") {
                event.preventDefault();
                openGalleryImage();
            }
        });
    });
    lightbox.addEventListener("click", function (event) {
        if (event.target === lightbox || event.target.closest(".lightbox-close")) {
            closeLightbox();
        }
    });
    document.addEventListener("keydown", function (event) {
        if (event.key === "Escape" && lightbox.classList.contains("open")) {
            closeLightbox();
        }
    });

    var toast = document.createElement("div");
    toast.className = "sweet-toast";
    toast.setAttribute("role", "status");
    body.appendChild(toast);
    var toastTimer;
    function showToast(message) {
        toast.textContent = message;
        toast.classList.add("show");
        window.clearTimeout(toastTimer);
        toastTimer = window.setTimeout(function () {
            toast.classList.remove("show");
        }, 2200);
    }

    function copyText(value) {
        if (navigator.clipboard && window.isSecureContext) {
            return navigator.clipboard.writeText(value);
        }

        var temporary = document.createElement("textarea");
        temporary.value = value;
        temporary.style.position = "fixed";
        temporary.style.opacity = "0";
        body.appendChild(temporary);
        temporary.select();
        document.execCommand("copy");
        temporary.remove();
        return Promise.resolve();
    }

    document.addEventListener("click", function (event) {
        var interactive = event.target.closest(".open-button,.ivory-button,.copy-account,.bottom-navigation a");
        if (interactive && !reducedMotion) {
            var bounds = interactive.getBoundingClientRect();
            var ripple = document.createElement("i");
            ripple.className = "sweet-ripple";
            ripple.style.zIndex = "2";
            ripple.style.left = event.clientX - bounds.left + "px";
            ripple.style.top = event.clientY - bounds.top + "px";
            interactive.appendChild(ripple);
            window.setTimeout(function () {
                ripple.remove();
            }, 700);
        }

        var accountButton = event.target.closest("[data-account]");
        if (accountButton) {
            copyText(accountButton.dataset.account).then(function () {
                accountButton.textContent = "Tersalin";
                showToast("Nomor rekening tersalin ♡");
            });
        }
    });

    var parallaxItems = document.querySelectorAll(".event-accent,.response-floral,.quote-floral");
    var scrollScheduled = false;
    function updateScrollEffects() {
        var documentHeight = document.documentElement.scrollHeight - window.innerHeight;
        var ratio = documentHeight > 0 ? window.scrollY / documentHeight : 0;
        progressBar.style.transform = "scaleX(" + Math.min(1, Math.max(0, ratio)) + ")";

        if (!reducedMotion) {
            parallaxItems.forEach(function (item, index) {
                var bounds = item.getBoundingClientRect();
                var offset = (bounds.top + bounds.height / 2 - window.innerHeight / 2) * (index % 2 ? 0.025 : -0.025);
                item.style.transform = "translate3d(0," + offset + "px,0)";
            });
        }
        scrollScheduled = false;
    }
    window.addEventListener(
        "scroll",
        function () {
            if (!scrollScheduled) {
                scrollScheduled = true;
                requestAnimationFrame(updateScrollEffects);
            }
        },
        { passive: true },
    );
    updateScrollEffects();
})();
