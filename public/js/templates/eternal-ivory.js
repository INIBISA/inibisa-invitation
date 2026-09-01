(function () {
    "use strict";
    var html = document.documentElement;
    var body = document.body;
    var opening = document.querySelector(".opening");
    var openButton = document.getElementById("open-invitation");
    var audio = document.getElementById("wedding-music");
    var musicButton = document.querySelector(".music-toggle");
    function openInvitation() {
        opening.classList.add("is-open");
        html.classList.remove("invitation-locked");
        body.classList.add("invitation-opened");
        if (audio) {
            audio
                .play()
                .then(function () {
                    musicButton.hidden = false;
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
            } else {
                audio.pause();
                musicButton.classList.add("paused");
            }
        });
    }
    var reveals = document.querySelectorAll(".reveal");
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
                                link.dataset.navLink ===
                                    entry.target.dataset.navSection,
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
        var update = function () {
            var distance = Math.max(0, target - Date.now());
            fields.days.textContent = String(
                Math.floor(distance / 86400000),
            ).padStart(2, "0");
            fields.hours.textContent = String(
                Math.floor(distance / 3600000) % 24,
            ).padStart(2, "0");
            fields.minutes.textContent = String(
                Math.floor(distance / 60000) % 60,
            ).padStart(2, "0");
            fields.seconds.textContent = String(
                Math.floor(distance / 1000) % 60,
            ).padStart(2, "0");
        };
        update();
        setInterval(update, 1000);
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
    document.addEventListener("click", function (event) {
        var button = event.target.closest("[data-account]");
        if (button) {
            navigator.clipboard
                .writeText(button.dataset.account)
                .then(function () {
                    button.textContent = "Tersalin";
                });
        }
    });
})();
