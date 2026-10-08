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

    function youtubeVideoIdFromUrl(value) {
        try {
            var url = new URL(value);
            if (url.protocol !== "https:" && url.protocol !== "http:") {
                return null;
            }
            var host = url.hostname.toLowerCase();
            var path = url.pathname.split("/").filter(Boolean);
            var videoId = null;
            if (host === "youtu.be" || host === "www.youtu.be") {
                videoId = path[0];
            } else if (["youtube.com", "www.youtube.com", "m.youtube.com", "www.youtube-nocookie.com"].includes(host)) {
                if (["embed", "shorts", "live"].includes(path[0])) {
                    videoId = path[1];
                } else if (path[0] === "watch") {
                    videoId = url.searchParams.get("v");
                }
            }
            return /^[A-Za-z0-9_-]{11}$/.test(videoId || "") ? videoId : null;
        } catch (error) {
            return null;
        }
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
            enhanceUploads();
            updateRepeater(root);
            list.lastElementChild.querySelector("input, textarea, select")?.focus();
        }

        var remove = event.target.closest("[data-remove-repeater]");
        if (remove) {
            var repeater = remove.closest("[data-repeater]");
            remove.closest(".repeater-item").remove();
            updateRepeater(repeater);
        }

        var copy = event.target.closest("[data-copy-link]");
        if (copy) {
            copyText(copy.dataset.copyLink).then(function () {
                copy.setAttribute("title", "Tautan disalin");
                showToast("Tautan undangan disalin");
            });
        }

        var copyTextButton = event.target.closest("[data-copy-text]");
        if (copyTextButton) {
            copyText(copyTextButton.dataset.copyText).then(function () {
                showToast(copyTextButton.dataset.copyMessage || "Berhasil disalin");
            });
        }

        if (event.target.closest("[data-auth-help]")) {
            showToast("Hubungi administrator untuk mengatur ulang kata sandi.");
        }

        if (event.target.closest(".notification-button")) {
            showToast("Tidak ada pemberitahuan baru.");
        }
    });

    document.querySelectorAll("[data-password-toggle]").forEach(function (button) {
        button.addEventListener("click", function () {
            var input = document.getElementById(button.dataset.passwordToggle);
            var willShow = input.type === "password";
            input.type = willShow ? "text" : "password";
            button.classList.toggle("showing", willShow);
            button.setAttribute("aria-label", willShow ? "Sembunyikan kata sandi" : "Tampilkan kata sandi");
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
        if (event.target.matches('[name="youtube_url"]') && event.target.value.trim()) {
            var noCatalogMusic = event.target.form?.querySelector('[name="wedding_music_id"][value=""]');
            if (noCatalogMusic) {
                noCatalogMusic.checked = true;
            }
        }
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

    document.addEventListener("change", function (event) {
        if (event.target.matches('[name="wedding_music_id"]') && event.target.value) {
            var customMusicUrl = event.target.form?.querySelector('[name="youtube_url"]');
            if (customMusicUrl) {
                customMusicUrl.value = "";
            }
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

    var confirmDialog = document.querySelector("[data-confirm-dialog]");
    var pendingForm = null;
    document.addEventListener("submit", function (event) {
        var form = event.target;
        if (form.dataset.confirm && !form.dataset.confirmed) {
            event.preventDefault();
            pendingForm = form;
            confirmDialog.querySelector("[data-confirm-message]").textContent = form.dataset.confirm;
            confirmDialog.showModal();
            return;
        }
        form.classList.add("is-submitting");
    });
    if (confirmDialog) {
        confirmDialog.addEventListener("close", function () {
            if (confirmDialog.returnValue === "confirm" && pendingForm) {
                pendingForm.dataset.confirmed = "true";
                pendingForm.requestSubmit();
            }
            pendingForm = null;
        });
    }

    document.addEventListener("click", function (event) {
        var reject = event.target.closest("[data-reject-payment]");
        if (reject) {
            document.querySelector('[data-reject-dialog="' + reject.dataset.rejectPayment + '"]').showModal();
        }
        var imagePreview = event.target.closest("[data-image-preview]");
        if (imagePreview) {
            var imageDialog = document.querySelector("[data-image-dialog]");
            imageDialog.querySelector("[data-image-target]").src = imagePreview.dataset.imagePreview;
            imageDialog.showModal();
        }
        var videoPreview = event.target.closest("[data-youtube-preview], [data-youtube-url-preview]");
        if (videoPreview) {
            var videoDialog = document.querySelector("[data-video-dialog]");
            var previewForm = videoPreview.closest("form");
            var urlInput = previewForm && previewForm.querySelector('[name="youtube_url"]');
            var isCustomUrl = videoPreview.hasAttribute("data-youtube-url-preview");
            var videoId = isCustomUrl ? youtubeVideoIdFromUrl(urlInput ? urlInput.value : "") : videoPreview.dataset.youtubePreview;
            if (!videoDialog || !/^[A-Za-z0-9_-]{11}$/.test(videoId || "")) {
                if (isCustomUrl) {
                    showToast("Masukkan tautan YouTube yang valid untuk diputar.");
                    urlInput?.focus();
                }
                return;
            }
            var startInput = previewForm && previewForm.querySelector('[name="music_start_seconds"]');
            var startSeconds = startInput ? Math.min(43200, Math.max(0, parseInt(startInput.value, 10) || 0)) : 0;
            var startParameter = startSeconds > 0 ? "&start=" + startSeconds : "";
            videoDialog.querySelector("[data-video-frame]").innerHTML = '<iframe title="Pratinjau musik YouTube" src="https://www.youtube.com/embed/' + videoId + '?autoplay=1' + startParameter + '" referrerpolicy="strict-origin-when-cross-origin" allow="autoplay; encrypted-media" allowfullscreen></iframe>';
            videoDialog.showModal();
        }
        if (event.target.closest("[data-dialog-close]")) {
            event.target.closest("dialog").close();
        }
        var midtrans = event.target.closest("[data-midtrans-token]");
        if (midtrans && window.snap) {
            window.snap.pay(midtrans.dataset.midtransToken, {
                onSuccess: function () { window.location.reload(); },
                onPending: function () { window.location.reload(); },
                onError: function () { window.location.reload(); },
                onClose: function () { window.location.reload(); }
            });
        }
        var quotePreset = event.target.closest("[data-quote-value]");
        if (quotePreset) {
            var quoteField = document.getElementById("quote");
            quoteField.value = quotePreset.dataset.quoteValue;
            document.querySelectorAll("[data-quote-value]").forEach(function (button) {
                button.classList.toggle("is-selected", button === quotePreset);
            });
        }
        if (event.target.closest("[data-quote-custom]")) {
            var customQuote = document.getElementById("quote");
            customQuote.value = "";
            document.querySelectorAll("[data-quote-value]").forEach(function (button) {
                button.classList.remove("is-selected");
            });
            customQuote.focus();
        }
        var share = event.target.closest("[data-share-whatsapp]");
        if (share) {
            var number = share.dataset.shareWhatsapp.replace(/\D/g, "");
            if (number.startsWith("0")) { number = "62" + number.slice(1); }
            var templateElement = document.querySelector("[data-whatsapp-message-template]");
            var message = JSON.parse(templateElement.textContent)
                .replaceAll("{nama_tamu}", share.dataset.guestName)
                .replaceAll("{nama_mempelai}", share.dataset.coupleName)
                .replaceAll("{tautan_undangan}", share.dataset.personalLink);
            window.open("https://wa.me/" + number + "?text=" + encodeURIComponent(message), "_blank", "noopener");
            if (share.dataset.deliveryEndpoint) {
                fetch(share.dataset.deliveryEndpoint, {
                    method: "PATCH",
                    headers: {
                        "Accept": "application/json",
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ sent: true })
                }).then(function (response) {
                    if (response.ok) { window.location.reload(); }
                });
            }
        }

        var placeholder = event.target.closest("[data-insert-placeholder]");
        if (placeholder) {
            var templateInput = document.querySelector("[data-whatsapp-template-input]");
            var start = templateInput.selectionStart;
            var token = placeholder.dataset.insertPlaceholder;
            templateInput.setRangeText(token, start, templateInput.selectionEnd, "end");
            templateInput.focus();
        }
    });
    document.querySelectorAll("[data-video-dialog]").forEach(function (dialog) {
        dialog.addEventListener("close", function () {
            dialog.querySelector("[data-video-frame]").innerHTML = "";
        });
    });

    function enhanceUploads() {
        document.querySelectorAll('input[type="file"]:not([data-upload-ready])').forEach(function (input) {
            input.dataset.uploadReady = "true";
            var zone = input.closest("[data-upload-zone]");
            if (!zone) {
                zone = document.createElement("div");
                zone.className = "upload-zone";
                zone.dataset.uploadZone = "";
                input.parentNode.insertBefore(zone, input);
                zone.appendChild(input);
                zone.insertAdjacentHTML("beforeend", '<span class="upload-icon">↑</span><strong>Tarik file ke sini atau klik untuk memilih</strong><small>JPG, PNG, atau WebP</small><span data-upload-info></span><button class="button small secondary" type="button" data-upload-remove hidden>Hapus file</button>');
            }
            var info = zone.querySelector("[data-upload-info]");
            var removeButton = zone.querySelector("[data-upload-remove]");
            var preview = zone.querySelector("[data-upload-preview]");
            var previews = zone.querySelector(".upload-previews");
            if (!previews && !preview) {
                previews = document.createElement("div");
                previews.className = "upload-previews";
                zone.insertBefore(previews, info);
            }
            var error = document.createElement("span");
            error.dataset.uploadError = "";
            zone.appendChild(error);

            function renderFiles() {
                var files = Array.from(input.files || []);
                var maxMb = Number(input.dataset.maxMb || 5);
                var maxFiles = Number(input.dataset.maxFiles || (input.multiple ? 12 : 1));
                error.textContent = "";
                if (files.length > maxFiles) {
                    error.textContent = "Maksimal " + maxFiles + " file.";
                    input.value = "";
                    files = [];
                } else if (files.some(function (file) { return file.size > maxMb * 1024 * 1024; })) {
                    error.textContent = "Ukuran gambar maksimal " + maxMb + " MB.";
                    input.value = "";
                    files = [];
                } else if (files.some(function (file) { return input.accept && !input.accept.split(",").map(function (type) { return type.trim(); }).includes(file.type); })) {
                    error.textContent = "Format file tidak sesuai.";
                    input.value = "";
                    files = [];
                }
                if (previews) {
                    previews.innerHTML = "";
                }
                if (preview) {
                    preview.hidden = true;
                    preview.removeAttribute("src");
                }
                files.forEach(function (file, index) {
                    if (!file.type.startsWith("image/")) { return; }
                    var image = index === 0 && preview ? preview : document.createElement("img");
                    image.src = URL.createObjectURL(file);
                    image.onload = function () { URL.revokeObjectURL(image.src); };
                    image.hidden = false;
                    if (image !== preview) { previews.appendChild(image); }
                });
                info.textContent = files.map(function (file) { return file.name + " (" + (file.size / 1024 / 1024).toFixed(1) + " MB)"; }).join(", ");
                removeButton.hidden = files.length === 0;
            }
            input.addEventListener("change", renderFiles);
            removeButton.addEventListener("click", function () { input.value = ""; renderFiles(); });
            zone.addEventListener("dragover", function (event) { event.preventDefault(); zone.classList.add("is-dragging"); });
            zone.addEventListener("dragleave", function () { zone.classList.remove("is-dragging"); });
            zone.addEventListener("drop", function (event) {
                event.preventDefault();
                zone.classList.remove("is-dragging");
                if (event.dataTransfer && event.dataTransfer.files) {
                    input.files = event.dataTransfer.files;
                    renderFiles();
                }
            });
        });
    }

    function updateRepeater(root) {
        if (!root) {
            return;
        }
        var items = Array.from(root.querySelectorAll("[data-repeater-list] > .repeater-item"));
        var empty = root.querySelector("[data-repeater-empty]");
        var name = root.dataset.repeaterName || "Butir";
        items.forEach(function (item, index) {
            var title = item.querySelector("[data-repeater-title]");
            if (title) {
                title.textContent = name + " " + (index + 1);
            }
        });
        if (empty) {
            empty.hidden = items.length > 0;
        }
        updateInvitationSummary();
    }

    var invitationForm = document.querySelector("[data-invitation-form]");
    var invitationWizard = document.querySelector("[data-invitation-wizard]");
    var currentInvitationStep = 1;
    var invitationStepDirty = false;
    var invitationStepUpdateUrl = invitationForm?.dataset.stepUpdateUrl || "";

    function invitationSteps() {
        return invitationWizard ? Array.from(invitationWizard.querySelectorAll("[data-wizard-step]")) : [];
    }

    function showInvitationStep(step) {
        if (!invitationWizard) {
            return;
        }
        var steps = invitationSteps();
        currentInvitationStep = Math.max(1, Math.min(step, steps.length));
        steps.forEach(function (panel) {
            panel.hidden = Number(panel.dataset.wizardStep) !== currentInvitationStep;
        });
        invitationWizard.querySelectorAll("[data-wizard-tab]").forEach(function (tab) {
            var tabStep = Number(tab.dataset.wizardTab);
            tab.toggleAttribute("aria-current", tabStep === currentInvitationStep);
            tab.classList.toggle("is-complete", tabStep < currentInvitationStep);
        });
        invitationWizard.querySelector("[data-wizard-progress]").style.width = (currentInvitationStep / steps.length * 100) + "%";
        invitationWizard.querySelector("[data-wizard-status]").textContent = "Langkah " + currentInvitationStep + " dari " + steps.length;
        invitationWizard.querySelector("[data-wizard-previous]").hidden = currentInvitationStep === 1;
        invitationWizard.querySelector("[data-wizard-next]").hidden = currentInvitationStep === steps.length;
        invitationWizard.querySelector("[data-wizard-submit]").hidden = currentInvitationStep !== steps.length;
        updateInvitationSummary();
    }

    function firstInvalidInStep(step) {
        return invitationWizard.querySelector('[data-wizard-step="' + step + '"]')?.querySelector(":invalid");
    }

    function validateInvitationStep(step) {
        var invalid = firstInvalidInStep(step);
        if (!invalid) {
            return true;
        }
        invalid.reportValidity();
        invalid.focus();
        return false;
    }

    function summaryValue(name) {
        return invitationForm?.querySelector('[data-summary-source="' + name + '"]')?.value.trim() || "";
    }

    function updateInvitationSummary() {
        if (!invitationWizard || !invitationForm) {
            return;
        }
        var template = invitationForm.querySelector("#template_id");
        var date = summaryValue("date");
        var eventCount = invitationForm.querySelectorAll('[data-repeater-name="Acara"] [data-repeater-list] > .repeater-item').length;
        var values = {
            template: template?.selectedOptions[0]?.value ? template.selectedOptions[0].textContent : "Belum dipilih",
            title: summaryValue("title") || "Belum diisi",
            slug: summaryValue("slug") ? window.location.origin + "/" + summaryValue("slug") : "Belum diisi",
            couple: [summaryValue("groom"), summaryValue("bride")].filter(Boolean).join(" & ") || "Belum diisi",
            date: date ? new Intl.DateTimeFormat("id-ID", { dateStyle: "long" }).format(new Date(date + "T00:00:00")) : "Belum diisi",
            events: eventCount + " acara"
        };
        Object.keys(values).forEach(function (key) {
            var target = invitationWizard.querySelector('[data-summary="' + key + '"]');
            if (target) {
                target.textContent = values[key];
            }
        });
    }

    if (invitationWizard && invitationForm) {
        invitationWizard.querySelectorAll("[data-repeater]").forEach(updateRepeater);
        showInvitationStep(Number(invitationWizard.dataset.initialStep || 1));

        invitationWizard.querySelector("[data-wizard-next]").addEventListener("click", function () {
            if (!invitationWizard.hasAttribute("data-step-save") && validateInvitationStep(currentInvitationStep)) {
                showInvitationStep(currentInvitationStep + 1);
                invitationWizard.scrollIntoView({ behavior: "smooth", block: "start" });
            }
        });
        invitationWizard.querySelector("[data-wizard-previous]").addEventListener("click", function () {
            showInvitationStep(currentInvitationStep - 1);
            invitationWizard.scrollIntoView({ behavior: "smooth", block: "start" });
        });
        invitationWizard.querySelectorAll("[data-wizard-tab]").forEach(function (tab) {
            tab.addEventListener("click", function () {
                var targetStep = Number(tab.dataset.wizardTab);
                if (invitationStepDirty && !window.confirm("Perubahan pada tahap ini belum disimpan. Tetap pindah?")) { return; }
                invitationStepDirty = false;
                showInvitationStep(targetStep);
            });
        });
        invitationForm.addEventListener("input", function () { invitationStepDirty = true; updateInvitationSummary(); });
        invitationForm.addEventListener("change", function () { invitationStepDirty = true; updateInvitationSummary(); });

        invitationWizard.querySelectorAll("[data-save-step]").forEach(function (button) {
            button.addEventListener("click", async function () {
                if (!validateInvitationStep(currentInvitationStep)) { return; }
                if (!invitationStepUpdateUrl && currentInvitationStep !== 1) {
                    invitationWizard.querySelector("[data-wizard-status]").textContent = "Simpan langkah 1 terlebih dahulu untuk membuat draft.";
                    return;
                }

                var panel = invitationWizard.querySelector('[data-wizard-step="' + currentInvitationStep + '"]');
                var data = new FormData();
                panel.querySelectorAll("input, select, textarea").forEach(function (field) {
                    if (!field.name || field.disabled || ((field.type === "checkbox" || field.type === "radio") && !field.checked)) { return; }
                    if (field.type === "file") {
                        Array.from(field.files || []).forEach(function (file) { data.append(field.name, file); });
                    } else {
                        data.append(field.name, field.value);
                    }
                });
                data.append("step", String(currentInvitationStep));
                data.append("_token", invitationForm.querySelector('[name="_token"]').value);
                if (invitationStepUpdateUrl) { data.append("_method", "PATCH"); }

                var status = invitationWizard.querySelector("[data-wizard-status]");
                var defaultLabel = button.textContent;
                button.disabled = true;
                button.textContent = "Menyimpan...";
                status.textContent = "Menyimpan langkah " + currentInvitationStep + "...";

                try {
                    var response = await fetch(invitationStepUpdateUrl.replace("__STEP__", String(currentInvitationStep)) || invitationForm.dataset.stepStoreUrl, {
                        method: "POST",
                        headers: { "Accept": "application/json", "X-Requested-With": "XMLHttpRequest" },
                        body: data
                    });
                    var payload = await response.json();
                    if (!response.ok) {
                        var errors = Object.values(payload.errors || {}).flat();
                        throw new Error(errors[0] || payload.message || "Langkah belum dapat disimpan.");
                    }

                    invitationStepUpdateUrl = payload.step_url;
                    invitationForm.dataset.stepUpdateUrl = payload.step_url;
                    window.history.replaceState({}, "", payload.edit_url);
                    invitationStepDirty = false;
                    status.textContent = payload.message;
                    if (currentInvitationStep < invitationSteps().length) {
                        showInvitationStep(currentInvitationStep + 1);
                        invitationWizard.scrollIntoView({ behavior: "smooth", block: "start" });
                    }
                } catch (error) {
                    status.textContent = error.message || "Koneksi bermasalah. Silakan coba lagi.";
                } finally {
                    button.disabled = false;
                    button.textContent = defaultLabel;
                }
            });
        });
        invitationForm.addEventListener("submit", function (event) {
            for (var step = 1; step <= invitationSteps().length; step += 1) {
                if (firstInvalidInStep(step)) {
                    event.preventDefault();
                    event.stopImmediatePropagation();
                    showInvitationStep(step);
                    window.setTimeout(function () { validateInvitationStep(currentInvitationStep); }, 0);
                    return;
                }
            }
        }, true);
    }

    document.querySelectorAll("[data-music-time-picker]").forEach(function (picker) {
        var minutesInput = picker.querySelector("[data-music-minutes]");
        var secondsInput = picker.querySelector("[data-music-seconds]");
        var startInput = picker.querySelector("[data-music-start-value]");
        var summary = picker.querySelector("[data-music-start-summary]");
        var presets = picker.querySelectorAll("[data-music-start-preset]");

        function updateMusicStart(normalizeFields) {
            var minutes = Math.max(0, Math.min(720, parseInt(minutesInput.value, 10) || 0));
            var seconds = Math.max(0, Math.min(59, parseInt(secondsInput.value, 10) || 0));
            var total = Math.min(43200, minutes * 60 + seconds);

            startInput.value = String(total);
            summary.textContent = "Musik mulai dari " + String(Math.floor(total / 60)).padStart(2, "0") + ":" + String(total % 60).padStart(2, "0") + ". Tombol putar mengikuti waktu ini.";
            presets.forEach(function (preset) {
                preset.setAttribute("aria-pressed", String(Number(preset.dataset.musicStartPreset) === total));
            });

            if (normalizeFields) {
                minutesInput.value = String(Math.floor(total / 60));
                secondsInput.value = String(total % 60);
            }
        }

        [minutesInput, secondsInput].forEach(function (input) {
            input.addEventListener("input", function () { updateMusicStart(false); });
            input.addEventListener("change", function () { updateMusicStart(true); });
        });
        presets.forEach(function (preset) {
            preset.addEventListener("click", function () {
                var total = Number(preset.dataset.musicStartPreset);
                minutesInput.value = String(Math.floor(total / 60));
                secondsInput.value = String(total % 60);
                updateMusicStart(true);
            });
        });
        updateMusicStart(true);
    });

    document.querySelectorAll("[data-youtube-search]").forEach(function (search) {
        var queryInput = search.querySelector("[data-youtube-search-query]");
        var searchButton = search.querySelector("[data-youtube-search-submit]");
        var status = search.querySelector("[data-youtube-search-status]");
        var results = search.querySelector("[data-youtube-search-results]");
        var form = search.closest("form");

        function renderResults(items) {
            results.replaceChildren();
            results.hidden = items.length === 0;

            items.forEach(function (item) {
                if (!/^[A-Za-z0-9_-]{11}$/.test(item.id || "")) {
                    return;
                }

                var card = document.createElement("article");
                card.className = "youtube-search-result";
                var image = document.createElement("img");
                image.src = "https://i.ytimg.com/vi/" + item.id + "/hqdefault.jpg";
                image.alt = "";
                image.loading = "lazy";
                var details = document.createElement("div");
                details.className = "youtube-search-result-details";
                var title = document.createElement("strong");
                title.textContent = item.title || "Video YouTube";
                var channel = document.createElement("small");
                channel.textContent = item.channel || "YouTube";
                details.append(title, channel);
                var actions = document.createElement("div");
                actions.className = "youtube-search-result-actions";
                var previewButton = document.createElement("button");
                previewButton.type = "button";
                previewButton.className = "button secondary small";
                previewButton.dataset.youtubePreview = item.id;
                previewButton.textContent = "Putar";
                var chooseButton = document.createElement("button");
                chooseButton.type = "button";
                chooseButton.className = "button gold small";
                chooseButton.textContent = "Pilih";
                chooseButton.addEventListener("click", function () {
                    var urlInput = form.querySelector('[name="youtube_url"]');
                    urlInput.value = "https://www.youtube.com/watch?v=" + item.id;
                    urlInput.dispatchEvent(new Event("input", { bubbles: true }));

                    if (search.hasAttribute("data-youtube-search-fill-title")) {
                        var titleInput = form.querySelector('[name="title"]');
                        if (titleInput && !titleInput.value.trim()) {
                            titleInput.value = item.title || "";
                        }
                    }

                    results.querySelectorAll(".youtube-search-result").forEach(function (result) {
                        result.classList.toggle("is-selected", result === card);
                    });
                    status.textContent = "Dipilih: " + (item.title || "Video YouTube") + ". Simpan formulir untuk menggunakan musik ini.";
                });
                actions.append(previewButton, chooseButton);
                card.append(image, details, actions);
                results.appendChild(card);
            });
        }

        async function runSearch() {
            var query = queryInput.value.trim();
            if (query.length < 2) {
                status.textContent = "Masukkan minimal 2 karakter untuk mencari musik.";
                queryInput.focus();
                return;
            }

            searchButton.disabled = true;
            status.textContent = "Mencari musik di YouTube...";
            results.hidden = true;

            try {
                var response = await fetch(search.dataset.searchEndpoint + "?q=" + encodeURIComponent(query), {
                    headers: { Accept: "application/json" },
                    credentials: "same-origin"
                });
                var data = await response.json();
                if (!response.ok) {
                    throw new Error(data.message || "Pencarian gagal. Coba lagi.");
                }
                var items = Array.isArray(data.results) ? data.results : [];
                renderResults(items);
                status.textContent = items.length ? "Pilih musik dari hasil pencarian." : "Musik tidak ditemukan. Coba kata kunci lain.";
            } catch (error) {
                status.textContent = error.message || "Pencarian gagal. Coba lagi.";
            } finally {
                searchButton.disabled = false;
            }
        }

        searchButton.addEventListener("click", runSearch);
        queryInput.addEventListener("keydown", function (event) {
            if (event.key === "Enter") {
                event.preventDefault();
                runSearch();
            }
        });
    });

    enhanceUploads();
})();
