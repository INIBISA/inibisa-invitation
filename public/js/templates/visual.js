(function () {
    "use strict";
    const audio = document.querySelector("#wedding-music");
    const button = document.querySelector(".visual-music-toggle");
    if (!audio || !button) return;
    button.addEventListener("click", async () => {
        if (audio.paused) {
            try {
                if (audio.currentTime === 0) audio.currentTime = Number(audio.dataset.startSeconds || 0);
                await audio.play();
                button.setAttribute("aria-label", "Jeda musik");
                button.classList.add("playing");
            } catch (error) {
                button.setAttribute("aria-label", "Musik tidak dapat diputar");
            }
        } else {
            audio.pause();
            button.setAttribute("aria-label", "Putar musik");
            button.classList.remove("playing");
        }
    });
})();
