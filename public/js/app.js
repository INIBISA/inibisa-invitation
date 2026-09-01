(function () {
    "use strict";

    var body = document.body;
    var sidebar = document.querySelector("[data-sidebar]");
    var sidebarOverlay = document.querySelector("[data-sidebar-overlay]");
    var sidebarTrigger = document.querySelector("[data-sidebar-open]");
    var profileTrigger = document.querySelector("[data-profile-trigger]");
    var profileDropdown = document.querySelector("[data-profile-dropdown]");

    function openSidebar() {
        if (!sidebar) {
            return;
        }
        sidebar.classList.add("open");
        sidebarOverlay.classList.add("open");
        sidebarTrigger.setAttribute("aria-expanded", "true");
        body.classList.add("sidebar-open");
    }

    function closeSidebar() {
        if (!sidebar) {
            return;
        }
        sidebar.classList.remove("open");
        sidebarOverlay.classList.remove("open");
        sidebarTrigger.setAttribute("aria-expanded", "false");
        body.classList.remove("sidebar-open");
    }

    if (sidebarTrigger) {
        sidebarTrigger.addEventListener("click", openSidebar);
        sidebarOverlay.addEventListener("click", closeSidebar);
        document.querySelector("[data-sidebar-close]").addEventListener("click", closeSidebar);
    }

    if (profileTrigger) {
        profileTrigger.addEventListener("click", function () {
            var isOpen = profileDropdown.classList.toggle("open");
            profileTrigger.setAttribute("aria-expanded", String(isOpen));
        });
    }

    var toast;
    var toastTimer;
    function showToast(message) {
        if (!toast) {
            toast = document.createElement("div");
            toast.className = "app-toast";
            toast.setAttribute("role", "status");
            body.appendChild(toast);
        }
        toast.textContent = message;
        toast.classList.add("show");
        window.clearTimeout(toastTimer);
        toastTimer = window.setTimeout(function () {
            toast.classList.remove("show");
        }, 2400);
    }

    function copyText(value) {
        if (navigator.clipboard && window.isSecureContext) {
            return navigator.clipboard.writeText(value);
        }
        var input = document.createElement("textarea");
        input.value = value;
        input.style.position = "fixed";
        input.style.opacity = "0";
        body.appendChild(input);
        input.select();
        document.execCommand("copy");
        input.remove();
        return Promise.resolve();
    }

    document.addEventListener("click", function (event) {
        if (profileDropdown && !event.target.closest(".profile-menu")) {
            profileDropdown.classList.remove("open");
            profileTrigger.setAttribute("aria-expanded", "false");
        }

        var add = event.target.closest("[data-add-repeater]");
        if (add) {
            var root = add.closest("[data-repeater]");
            var list = root.querySelector("[data-repeater-list]");
            var template = root.querySelector("template").innerHTML;
            var index = Number(root.dataset.nextIndex || list.children.length);
            list.insertAdjacentHTML("beforeend", template.replaceAll("__INDEX__", String(index)));
            root.dataset.nextIndex = String(index + 1);
        }

        var remove = event.target.closest("[data-remove-repeater]");
        if (remove) {
            remove.closest(".repeater-item").remove();
        }

        var copy = event.target.closest("[data-copy-link]");
        if (copy) {
            copyText(copy.dataset.copyLink).then(function () {
                copy.setAttribute("title", "Link copied");
                showToast("Invitation link copied");
            });
        }

        if (event.target.closest("[data-auth-help]")) {
            showToast("Please contact the administrator to reset your password.");
        }

        if (event.target.closest(".notification-button")) {
            showToast("You're all caught up.");
        }
    });

    document.querySelectorAll("[data-password-toggle]").forEach(function (button) {
        button.addEventListener("click", function () {
            var input = document.getElementById(button.dataset.passwordToggle);
            var willShow = input.type === "password";
            input.type = willShow ? "text" : "password";
            button.classList.toggle("showing", willShow);
            button.setAttribute("aria-label", willShow ? "Hide password" : "Show password");
        });
    });

    var search = document.querySelector("[data-dashboard-search]");
    if (search) {
        search.addEventListener("input", function () {
            var query = search.value.trim().toLowerCase();
            document.querySelectorAll("[data-searchable]").forEach(function (item) {
                item.classList.toggle("search-hidden", query !== "" && !item.dataset.searchable.includes(query));
            });
        });
    }

    document.addEventListener("input", function (event) {
        if (event.target.matches("[data-title-source]")) {
            var slug = document.querySelector("[data-slug-target]");
            if (slug && !slug.dataset.touched) {
                slug.value = event.target.value
                    .toLowerCase()
                    .normalize("NFD")
                    .replace(/[\u0300-\u036f]/g, "")
                    .replace(/[^a-z0-9]+/g, "-")
                    .replace(/^-|-$/g, "");
            }
        }
        if (event.target.matches("[data-slug-target]")) {
            event.target.dataset.touched = "true";
        }
    });

    document.addEventListener("keydown", function (event) {
        if ((event.metaKey || event.ctrlKey) && event.key.toLowerCase() === "k" && search) {
            event.preventDefault();
            search.focus();
        }
        if (event.key === "Escape") {
            closeSidebar();
            if (profileDropdown) {
                profileDropdown.classList.remove("open");
                profileTrigger.setAttribute("aria-expanded", "false");
            }
        }
    });
})();
