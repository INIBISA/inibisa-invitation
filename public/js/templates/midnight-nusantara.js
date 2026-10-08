(function () {
    "use strict";

    var html = document.documentElement;
    var body = document.body;
    var gate = document.querySelector(".mn-gate");
    var main = document.querySelector("main");
    var navigation = document.querySelector(".mn-nav");
    var shell = document.querySelector(".mn-shell");
    var openButton = document.getElementById("open-invitation");
    var audio = document.getElementById("wedding-music");
    var musicButton = document.querySelector(".music-toggle");
    var reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

    html.classList.add("mn-enhanced", "mn-locked");
    main.inert = true;
    navigation.inert = true;
    openButton.focus({ preventScroll: true });

    if (audio) {
        var audioStartSeconds = Math.max(0, parseInt(audio.dataset.startSeconds || "0", 10) || 0);
        if (audioStartSeconds > 0) {
            audio.addEventListener("loadedmetadata", function () {
                if (!Number.isFinite(audio.duration) || audioStartSeconds < audio.duration) {
                    audio.currentTime = audioStartSeconds;
                }
            });
        }
    }

    function revealInvitation() {
        gate.classList.add("is-open");
        html.classList.remove("mn-locked");
        main.inert = false;
        navigation.inert = false;
        main.focus({ preventScroll: true });
        document.dispatchEvent(new CustomEvent("invitation:opened"));

        if (audio) {
            audio.play().then(function () {
                musicButton.hidden = false;
                musicButton.setAttribute("aria-label", "Jeda musik");
            }).catch(function () {
                musicButton.hidden = false;
                musicButton.classList.add("paused");
            });
        }
    }

    if (openButton) {
        openButton.addEventListener("click", function () {
            if (gate.classList.contains("is-open")) {
                return;
            }

            var label = openButton.textContent;
            openButton.disabled = true;
            openButton.textContent = "Menyiapkan musik...";

            function proceed() {
                openButton.disabled = false;
                openButton.textContent = label;
                revealInvitation();
            }

            if (window.WeddingMusic && typeof window.WeddingMusic.whenReady === "function") {
                window.WeddingMusic.whenReady(3000).then(proceed, proceed);
            } else {
                proceed();
            }
        });
    }

    if (musicButton && audio) {
        musicButton.addEventListener("click", function () {
            if (audio.paused) {
                audio.play().then(function () {
                    musicButton.classList.remove("paused");
                    musicButton.setAttribute("aria-label", "Jeda musik");
                }).catch(function () {
                    musicButton.classList.add("paused");
                    musicButton.setAttribute("aria-label", "Putar musik");
                });
            } else {
                audio.pause();
                musicButton.classList.add("paused");
                musicButton.setAttribute("aria-label", "Putar musik");
            }
        });
    }

    var reveals = document.querySelectorAll(".mn-reveal");
    if (!reducedMotion && "IntersectionObserver" in window) {
        var revealObserver = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add("is-visible");
                    revealObserver.unobserve(entry.target);
                }
            });
        }, { rootMargin: "0px 0px -8%" });
        reveals.forEach(function (item) { revealObserver.observe(item); });
    } else {
        reveals.forEach(function (item) { item.classList.add("is-visible"); });
    }

    var countdown = document.querySelector("[data-countdown]");
    if (countdown) {
        var target = new Date(countdown.dataset.countdown).getTime();
        var countdownFields = {
            days: countdown.querySelector("[data-days]"),
            hours: countdown.querySelector("[data-hours]"),
            minutes: countdown.querySelector("[data-minutes]"),
            seconds: countdown.querySelector("[data-seconds]"),
        };
        var updateCountdown = function () {
            var distance = Math.max(0, target - Date.now());
            countdownFields.days.textContent = String(Math.floor(distance / 86400000)).padStart(2, "0");
            countdownFields.hours.textContent = String(Math.floor(distance / 3600000) % 24).padStart(2, "0");
            countdownFields.minutes.textContent = String(Math.floor(distance / 60000) % 60).padStart(2, "0");
            countdownFields.seconds.textContent = String(Math.floor(distance / 1000) % 60).padStart(2, "0");
        };
        updateCountdown();
        window.setInterval(updateCountdown, 1000);
    }

    var attendance = document.getElementById("attendance");
    var guestCount = document.getElementById("guest_count");
    var guestCountField = document.querySelector("[data-guest-count-field]");
    if (attendance && guestCount && guestCountField) {
        var syncGuestCount = function () {
            var attending = attendance.value === "attending";
            guestCount.disabled = !attending;
            guestCountField.hidden = !attending;
        };
        attendance.addEventListener("change", syncGuestCount);
        syncGuestCount();
    }

    var navigationLinks = document.querySelectorAll("[data-nav-link]");
    var navigationSections = Array.from(navigationLinks).map(function (link) {
        return document.querySelector('[data-mn-section="' + link.dataset.navLink + '"]');
    }).filter(Boolean);
    if ("IntersectionObserver" in window) {
        var navigationObserver = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    navigationLinks.forEach(function (link) {
                        link.classList.toggle("active", link.dataset.navLink === entry.target.dataset.mnSection);
                    });
                }
            });
        }, { rootMargin: "-30% 0px -58%" });
        navigationSections.forEach(function (section) { navigationObserver.observe(section); });
    }

    var progress = document.createElement("div");
    progress.className = "mn-progress";
    progress.setAttribute("aria-hidden", "true");
    progress.innerHTML = "<span></span>";
    body.appendChild(progress);
    var progressBar = progress.firstElementChild;
    var scrollScheduled = false;
    function updateProgress() {
        var height = document.documentElement.scrollHeight - window.innerHeight;
        var ratio = height > 0 ? window.scrollY / height : 0;
        progressBar.style.transform = "scaleX(" + Math.min(1, Math.max(0, ratio)) + ")";
        scrollScheduled = false;
    }
    window.addEventListener("scroll", function () {
        if (!scrollScheduled) {
            scrollScheduled = true;
            requestAnimationFrame(updateProgress);
        }
    }, { passive: true });
    updateProgress();

    var lightbox = document.createElement("div");
    lightbox.className = "mn-lightbox";
    lightbox.setAttribute("role", "dialog");
    lightbox.setAttribute("aria-modal", "true");
    lightbox.setAttribute("aria-label", "Tampilan foto");
    lightbox.innerHTML = '<button type="button" aria-label="Tutup galeri">×</button><img alt="">';
    body.appendChild(lightbox);
    var lightboxImage = lightbox.querySelector("img");
    var lightboxClose = lightbox.querySelector("button");
    var lastGalleryTrigger = null;

    function closeLightbox() {
        lightbox.classList.remove("is-open");
        html.style.removeProperty("overflow");
        shell.inert = false;
        if (lastGalleryTrigger) {
            lastGalleryTrigger.focus();
        }
    }

    document.querySelectorAll("[data-gallery-item]").forEach(function (figure) {
        var image = figure.querySelector("img");
        figure.tabIndex = 0;
        figure.setAttribute("role", "button");
        figure.setAttribute("aria-label", "Perbesar " + image.alt);

        function openLightbox() {
            lastGalleryTrigger = figure;
            lightboxImage.src = image.currentSrc || image.src;
            lightboxImage.alt = image.alt;
            lightbox.classList.add("is-open");
            html.style.overflow = "hidden";
            shell.inert = true;
            lightboxClose.focus();
        }

        figure.addEventListener("click", openLightbox);
        figure.addEventListener("keydown", function (event) {
            if (event.key === "Enter" || event.key === " ") {
                event.preventDefault();
                openLightbox();
            }
        });
    });
    lightbox.addEventListener("click", function (event) {
        if (event.target === lightbox || event.target === lightboxClose) {
            closeLightbox();
        }
    });
    document.addEventListener("keydown", function (event) {
        if (event.key === "Escape" && lightbox.classList.contains("is-open")) {
            closeLightbox();
        }
        if (event.key === "Tab" && lightbox.classList.contains("is-open")) {
            event.preventDefault();
            lightboxClose.focus();
        }
    });

    var toast = document.createElement("div");
    toast.className = "mn-toast";
    toast.setAttribute("role", "status");
    body.appendChild(toast);
    var toastTimer;
    function showToast(message) {
        toast.textContent = message;
        toast.classList.add("is-visible");
        window.clearTimeout(toastTimer);
        toastTimer = window.setTimeout(function () { toast.classList.remove("is-visible"); }, 2200);
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
        var copied = document.execCommand("copy");
        temporary.remove();
        return copied ? Promise.resolve() : Promise.reject(new Error("Nomor belum dapat disalin."));
    }

    document.addEventListener("click", function (event) {
        var accountButton = event.target.closest("[data-account]");
        if (!accountButton) {
            return;
        }
        copyText(accountButton.dataset.account).then(function () {
            var originalLabel = accountButton.textContent;
            accountButton.textContent = "Tersalin";
            showToast("Nomor rekening tersalin");
            window.setTimeout(function () { accountButton.textContent = originalLabel; }, 2200);
        }).catch(function (error) {
            showToast(error.message || "Nomor belum dapat disalin.");
        });
    });
})();
