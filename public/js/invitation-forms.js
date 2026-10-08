(function () {
    "use strict";

    document.querySelectorAll("[data-async-form]").forEach(function (form) {
        form.addEventListener("submit", async function (event) {
            event.preventDefault();
            var button = form.querySelector('[type="submit"]');
            var status = form.parentElement.querySelector("[data-form-status]");
            form.querySelectorAll("[data-field-error]").forEach(function (error) {
                error.textContent = "";
                var field = form.querySelector('[name="' + error.dataset.fieldError + '"]');
                if (field) { field.removeAttribute("aria-invalid"); }
            });
            button.disabled = true;
            status.hidden = true;

            try {
                var response = await fetch(form.action, {
                    method: "POST",
                    headers: { "Accept": "application/json", "X-Requested-With": "XMLHttpRequest" },
                    body: new FormData(form)
                });
                var isJson = (response.headers.get("content-type") || "").includes("application/json");
                var payload = isJson ? await response.json() : {};

                if (!response.ok) {
                    Object.entries(payload.errors || {}).forEach(function (entry) {
                        var error = form.querySelector('[data-field-error="' + entry[0] + '"]');
                        var field = form.querySelector('[name="' + entry[0] + '"]');
                        if (error) { error.textContent = entry[1][0]; }
                        if (field) { field.setAttribute("aria-invalid", "true"); }
                    });
                    var message = response.status === 419
                        ? "Sesi telah berakhir. Muat ulang halaman lalu coba lagi."
                        : response.status === 429
                            ? "Terlalu banyak percobaan. Tunggu sebentar lalu coba lagi."
                            : payload.message || "Data belum dapat dikirim.";
                    throw new Error(message);
                }

                status.textContent = payload.message;
                status.hidden = false;
                if (form.dataset.asyncForm === "wish") {
                    var empty = document.querySelector("[data-wish-list] .mn-empty");
                    if (empty) { empty.remove(); }
                    var article = document.createElement("article");
                    var name = document.createElement("strong");
                    var message = document.createElement("p");
                    var time = document.createElement("time");
                    name.textContent = payload.data.guest_name;
                    message.textContent = payload.data.message;
                    time.textContent = payload.data.created_at;
                    article.append(name, message, time);
                    document.querySelector("[data-wish-list]").prepend(article);
                    form.querySelector('[name="message"]').value = "";
                }
            } catch (error) {
                status.textContent = error.message || "Koneksi bermasalah. Silakan coba lagi.";
                status.hidden = false;
            } finally {
                button.disabled = false;
            }
        });
    });
})();
