(function () {
    "use strict";

    document.querySelectorAll("[data-async-form]").forEach(function (form) {
        form.addEventListener("submit", async function (event) {
            event.preventDefault();
            var button = form.querySelector('[type="submit"]');
            var status = form.parentElement.querySelector("[data-form-status]");
            form.querySelectorAll("[data-field-error]").forEach(function (error) { error.textContent = ""; });
            button.disabled = true;
            status.hidden = true;

            try {
                var response = await fetch(form.action, {
                    method: "POST",
                    headers: { "Accept": "application/json", "X-Requested-With": "XMLHttpRequest" },
                    body: new FormData(form)
                });
                var payload = await response.json();

                if (!response.ok) {
                    Object.entries(payload.errors || {}).forEach(function (entry) {
                        var error = form.querySelector('[data-field-error="' + entry[0] + '"]');
                        if (error) { error.textContent = entry[1][0]; }
                    });
                    throw new Error(response.status === 429 ? "Terlalu banyak percobaan. Tunggu sebentar lalu coba lagi." : payload.message || "Data belum dapat dikirim.");
                }

                status.textContent = payload.message;
                status.hidden = false;
                if (form.dataset.asyncForm === "wish") {
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
