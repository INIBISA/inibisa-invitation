(function () {
    "use strict";

    var html = document.documentElement;
    var gate = document.querySelector("[data-invitation-gate]");
    var openButton = document.querySelector("[data-open-invitation]");
    var audio = document.getElementById("wedding-music");
    var musicButton = document.querySelector(".music-toggle");
    var reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

    function openInvitation() {
        gate.classList.add("is-open");
        html.classList.remove("invitation-locked");
        document.dispatchEvent(new CustomEvent("invitation:opened"));
        if (audio) {
            var start = Math.max(0, parseInt(audio.dataset.startSeconds || "0", 10) || 0);
            audio.addEventListener("loadedmetadata", function () { if (start < audio.duration) { audio.currentTime = start; } }, { once: true });
            audio.play().catch(function () {});
            musicButton.hidden = false;
        }
    }

    openButton?.addEventListener("click", function () {
        if (window.WeddingMusic?.whenReady) { window.WeddingMusic.whenReady(3000).then(openInvitation, openInvitation); } else { openInvitation(); }
    });
    musicButton?.addEventListener("click", function () {
        if (audio.paused) { audio.play(); musicButton.classList.remove("paused"); } else { audio.pause(); musicButton.classList.add("paused"); }
    });

    document.querySelectorAll("[data-countdown]").forEach(function (countdown) {
        var target = new Date(countdown.dataset.countdown).getTime();
        function update() {
            var remaining = Math.max(0, target - Date.now());
            var values = { days: Math.floor(remaining / 86400000), hours: Math.floor(remaining / 3600000) % 24, minutes: Math.floor(remaining / 60000) % 60, seconds: Math.floor(remaining / 1000) % 60 };
            Object.entries(values).forEach(function (entry) { countdown.querySelector("[data-" + entry[0] + "]").textContent = String(entry[1]).padStart(2, "0"); });
        }
        update(); window.setInterval(update, 1000);
    });

    var reveals = document.querySelectorAll(".rf-reveal,.mm-reveal");
    if (!reducedMotion && "IntersectionObserver" in window) {
        var observer = new IntersectionObserver(function (entries) { entries.forEach(function (entry) { if (entry.isIntersecting) { entry.target.classList.add("visible"); observer.unobserve(entry.target); } }); }, { rootMargin: "0px 0px -8%" });
        reveals.forEach(function (item) { observer.observe(item); });
    } else { reveals.forEach(function (item) { item.classList.add("visible"); }); }

    var links = document.querySelectorAll("[data-nav-link]");
    if (links.length && "IntersectionObserver" in window) {
        var navObserver = new IntersectionObserver(function (entries) { entries.forEach(function (entry) { if (entry.isIntersecting) { links.forEach(function (link) { link.classList.toggle("active", link.dataset.navLink === entry.target.dataset.navSection); }); } }); }, { rootMargin: "-30% 0px -55%" });
        document.querySelectorAll("[data-nav-section]").forEach(function (section) { navObserver.observe(section); });
    }

    document.querySelectorAll("#attendance").forEach(function (attendance) {
        attendance.addEventListener("change", function () { var field = attendance.form.querySelector("[data-guest-count]"); field.hidden = attendance.value !== "attending"; field.querySelector("input").disabled = field.hidden; });
    });

    function copy(value) { return navigator.clipboard && window.isSecureContext ? navigator.clipboard.writeText(value) : Promise.resolve(); }
    document.addEventListener("click", function (event) { var button = event.target.closest("[data-account]"); if (button) { copy(button.dataset.account).then(function () { button.textContent = "Tersalin"; }); } });

    var images = document.querySelectorAll("[data-gallery] img");
    if (images.length) {
        var lightbox = document.createElement("div"); lightbox.className = "editorial-lightbox"; lightbox.setAttribute("role", "dialog"); lightbox.setAttribute("aria-modal", "true"); lightbox.innerHTML = '<button type="button" aria-label="Tutup">×</button><img alt="">'; document.body.appendChild(lightbox);
        images.forEach(function (image) { image.closest("figure").addEventListener("click", function () { lightbox.querySelector("img").src = image.currentSrc || image.src; lightbox.querySelector("img").alt = image.alt; lightbox.classList.add("open"); }); });
        lightbox.addEventListener("click", function (event) { if (event.target === lightbox || event.target.closest("button")) { lightbox.classList.remove("open"); } });
    }
})();
