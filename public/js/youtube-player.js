(function () {
    "use strict";

    var root = document.querySelector("[data-youtube-music]");
    if (!root || !/^[A-Za-z0-9_-]{11}$/.test(root.dataset.youtubeMusic)) {
        return;
    }

    var videoId = root.dataset.youtubeMusic;
    var toggle = root.querySelector("[data-youtube-toggle]");
    var frame = root.querySelector("[data-youtube-frame]");
    var label = root.querySelector("[data-youtube-label]");
    var player = null;
    var playerReady = false;
    var apiFailed = false;
    var invitationOpened = false;

    function setLabel(playing, fallback) {
        if (label) {
            label.textContent = playing ? "Jeda Musik" : (fallback ? "Putar Musik" : "Lanjutkan musik");
        }
        toggle.setAttribute("aria-label", playing ? "Jeda musik" : (fallback ? "Putar musik" : "Lanjutkan musik"));
    }

    function showToggle(playing, fallback) {
        if (!invitationOpened) {
            return;
        }
        toggle.hidden = false;
        toggle.classList.toggle("paused", !playing);
        setLabel(playing, fallback);
    }

    function markReady() {
        if (playerReady) {
            return;
        }
        playerReady = true;
        document.dispatchEvent(new CustomEvent("weddingmusic:ready"));
        if (invitationOpened && player && typeof player.playVideo === "function") {
            player.playVideo();
        }
    }

    function showFallback() {
        showToggle(false, true);
    }

    function createPlayer() {
        if (!window.YT || !window.YT.Player || player) {
            return;
        }

        player = new window.YT.Player(frame, {
            width: 200,
            height: 200,
            videoId: videoId,
            playerVars: { controls: 0, playsinline: 1, rel: 0, loop: 1, playlist: videoId, origin: window.location.origin },
            events: {
                onReady: function (event) {
                    try {
                        event.target.getIframe().setAttribute("allow", "autoplay; encrypted-media");
                    } catch (e) { /* abaikan */ }
                    markReady();
                },
                onStateChange: function (event) {
                    if (event.data === window.YT.PlayerState.PLAYING) {
                        showToggle(true, false);
                    } else if (event.data === window.YT.PlayerState.PAUSED) {
                        showToggle(false, false);
                    }
                },
                onError: function () {
                    showFallback();
                },
                onAutoplayBlocked: function () {
                    showFallback();
                }
            }
        });
    }

    window.onYouTubeIframeAPIReady = createPlayer;
    var script = document.createElement("script");
    script.src = "https://www.youtube.com/iframe_api";
    script.onerror = function () {
        apiFailed = true;
        document.dispatchEvent(new CustomEvent("weddingmusic:failed"));
    };
    document.head.appendChild(script);
    if (window.YT && window.YT.Player) {
        createPlayer();
    }

    document.addEventListener("invitation:opened", function () {
        invitationOpened = true;
        if (playerReady && player && typeof player.playVideo === "function") {
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

    window.WeddingMusic = {
        whenReady: function (timeout) {
            return new Promise(function (resolve) {
                if (playerReady) {
                    resolve(true);
                    return;
                }
                if (apiFailed) {
                    resolve(false);
                    return;
                }
                var timer = window.setTimeout(function () {
                    cleanup();
                    resolve(playerReady);
                }, timeout || 3000);
                function cleanup() {
                    window.clearTimeout(timer);
                    document.removeEventListener("weddingmusic:ready", onReady);
                    document.removeEventListener("weddingmusic:failed", onFailed);
                }
                function onReady() {
                    cleanup();
                    resolve(true);
                }
                function onFailed() {
                    cleanup();
                    resolve(false);
                }
                document.addEventListener("weddingmusic:ready", onReady);
                document.addEventListener("weddingmusic:failed", onFailed);
            });
        }
    };
})();
