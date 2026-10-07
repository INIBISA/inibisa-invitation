(function () {
    "use strict";

    var root = document.querySelector("[data-youtube-music]");
    if (!root || !/^[A-Za-z0-9_-]{11}$/.test(root.dataset.youtubeMusic)) {
        return;
    }

    var toggle = root.querySelector("[data-youtube-toggle]");
    var frame = root.querySelector("[data-youtube-frame]");
    var player = null;
    var invitationOpened = false;

    function updateToggle(playing) {
        toggle.hidden = false;
        toggle.classList.toggle("paused", !playing);
        toggle.setAttribute("aria-label", playing ? "Jeda musik" : "Lanjutkan musik");
    }

    function createPlayer() {
        if (!window.YT || !window.YT.Player || player) {
            return;
        }

        player = new window.YT.Player(frame, {
            width: 1,
            height: 1,
            videoId: root.dataset.youtubeMusic,
            playerVars: { controls: 0, playsinline: 1, rel: 0, origin: window.location.origin },
            events: {
                onReady: function (event) {
                    if (invitationOpened) {
                        event.target.playVideo();
                    }
                },
                onStateChange: function (event) {
                    updateToggle(event.data === window.YT.PlayerState.PLAYING);
                }
            }
        });
    }

    window.onYouTubeIframeAPIReady = createPlayer;
    var script = document.createElement("script");
    script.src = "https://www.youtube.com/iframe_api";
    document.head.appendChild(script);

    document.addEventListener("invitation:opened", function () {
        invitationOpened = true;
        if (player && typeof player.playVideo === "function") {
            player.playVideo();
        }
    });

    toggle.addEventListener("click", function () {
        if (!player || typeof player.getPlayerState !== "function") {
            return;
        }
        if (player.getPlayerState() === window.YT.PlayerState.PLAYING) {
            player.pauseVideo();
        } else {
            player.playVideo();
        }
    });
})();
