(function () {
    "use strict";
    var html = document.documentElement;
    var gate = document.querySelector(".vv-gate");
    var app = document.querySelector(".vv-app");
    var main = document.querySelector("main");
    var openButton = document.getElementById("open-invitation");
    var audio = document.getElementById("wedding-music");
    var musicButton = document.querySelector(".music-toggle");
    var reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    html.classList.add("vv-enhanced", "vv-locked");
    app.inert = true;

    if (audio) {
        var start = Math.max(0, parseInt(audio.dataset.startSeconds || "0", 10) || 0);
        audio.addEventListener("loadedmetadata", function () {
            if (start && (!Number.isFinite(audio.duration) || start < audio.duration)) { audio.currentTime = start; }
        });
    }

    function finishOpening() {
        gate.classList.add("is-open");
        html.classList.remove("vv-locked");
        app.inert = false;
        main.focus({ preventScroll: true });
        document.dispatchEvent(new CustomEvent("invitation:opened"));
        if (audio) {
            audio.play().then(function () { musicButton.hidden = false; }).catch(function () { musicButton.hidden = false; musicButton.classList.add("paused"); });
        }
    }
    openButton.addEventListener("click", function () {
        openButton.disabled = true;
        if (window.WeddingMusic && typeof window.WeddingMusic.whenReady === "function") {
            window.WeddingMusic.whenReady(3000).then(finishOpening, finishOpening);
        } else { finishOpening(); }
    });
    if (musicButton && audio) {
        musicButton.addEventListener("click", function () {
            if (audio.paused) { audio.play().then(function () { musicButton.classList.remove("paused"); }); }
            else { audio.pause(); musicButton.classList.add("paused"); }
        });
    }

    var reveals = document.querySelectorAll(".vv-reveal");
    if (!reducedMotion && "IntersectionObserver" in window) {
        var revealObserver = new IntersectionObserver(function (entries) { entries.forEach(function (entry) { if (entry.isIntersecting) { entry.target.classList.add("visible"); revealObserver.unobserve(entry.target); } }); }, { rootMargin: "0px 0px -8%" });
        reveals.forEach(function (item) { revealObserver.observe(item); });
    } else { reveals.forEach(function (item) { item.classList.add("visible"); }); }

    var countdown = document.querySelector("[data-countdown]");
    if (countdown) {
        var target = new Date(countdown.dataset.countdown).getTime();
        var update = function () {
            var distance = Math.max(0, target - Date.now());
            countdown.querySelector("[data-days]").textContent = String(Math.floor(distance / 86400000)).padStart(2, "0");
            countdown.querySelector("[data-hours]").textContent = String(Math.floor(distance / 3600000) % 24).padStart(2, "0");
            countdown.querySelector("[data-minutes]").textContent = String(Math.floor(distance / 60000) % 60).padStart(2, "0");
            countdown.querySelector("[data-seconds]").textContent = String(Math.floor(distance / 1000) % 60).padStart(2, "0");
        };
        update(); window.setInterval(update, 1000);
    }

    var attendance = document.getElementById("attendance");
    var guestCount = document.getElementById("guest_count");
    var guestCountField = document.querySelector("[data-guest-count]");
    if (attendance && guestCount && guestCountField) {
        var sync = function () { var attending = attendance.value === "attending"; guestCount.disabled = !attending; guestCountField.hidden = !attending; };
        attendance.addEventListener("change", sync); sync();
    }

    var links = document.querySelectorAll("[data-vv-nav]");
    if ("IntersectionObserver" in window) {
        var sectionObserver = new IntersectionObserver(function (entries) { entries.forEach(function (entry) { if (entry.isIntersecting) { links.forEach(function (link) { link.classList.toggle("active", link.dataset.vvNav === entry.target.dataset.vvSection); }); } }); }, { rootMargin: "-30% 0px -55%" });
        links.forEach(function (link) { var section = document.querySelector('[data-vv-section="' + link.dataset.vvNav + '"]'); if (section) { sectionObserver.observe(section); } });
    }

    var toast = document.querySelector(".vv-toast");
    var timer;
    function showToast(message) { toast.textContent = message; toast.classList.add("show"); clearTimeout(timer); timer = setTimeout(function () { toast.classList.remove("show"); }, 2200); }
    document.addEventListener("click", function (event) {
        var button = event.target.closest("[data-account]");
        if (!button) { return; }
        var value = button.dataset.account;
        var copy = navigator.clipboard && window.isSecureContext ? navigator.clipboard.writeText(value) : Promise.reject();
        copy.then(function () { showToast("Nomor rekening tersalin"); }).catch(function () { showToast("Salin nomor: " + value); });
    });
})();
