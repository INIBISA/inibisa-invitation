(function () {
    "use strict";

    const root = document.querySelector("[data-studio]");
    if (!root || !window.templateStudioData) return;

    const draftKey = "template-studio-draft-" + root.dataset.templateId;
    const viewKey = "template-studio-view-" + root.dataset.templateId;
    let design = structuredClone(window.templateStudioData.design);
    let recoveredDraft = false;
    try {
        if (root.dataset.saved === "1") sessionStorage.removeItem(draftKey);
        const cached = sessionStorage.getItem(draftKey);
        if (cached) {
            const parsed = JSON.parse(cached);
            if (parsed.canvas && Array.isArray(parsed.section_order)) {
                design = parsed;
                recoveredDraft = true;
            }
        }
    } catch (error) {
        recoveredDraft = false;
    }
    const names = window.templateStudioData.sections;
    const demo = window.templateStudioData.demo || {};
    const list = root.querySelector("[data-section-list]");
    const canvas = root.querySelector("[data-canvas]");
    const layers = root.querySelector("[data-canvas-layers]");
    const content = root.querySelector("[data-canvas-content]");
    const properties = root.querySelector("[data-element-properties]");
    const imageInput = root.querySelector("[data-image-input]");
    const form = root.querySelector("#studio-form");
    let section = "cover";
    let selectedId = null;
    let dirty = false;
    let persistTimer;
    try {
        const lastSection = sessionStorage.getItem(viewKey);
        if (lastSection && design.canvas[lastSection]) section = lastSection;
    } catch (error) {
        section = "cover";
    }

    function currentElements() { return design.canvas[section].elements; }
    function selectedElement() { return currentElements().find((element) => element.id === selectedId); }
    function persistDraft() {
        try { sessionStorage.setItem(draftKey, JSON.stringify(design)); } catch (error) { /* Storage can be unavailable. */ }
    }
    function markDirty() {
        dirty = true;
        const state = root.querySelector("[data-save-state]");
        state.textContent = "Perubahan belum disimpan";
        state.classList.add("dirty");
        root.querySelector("[data-save-state-mobile]").textContent = "Perubahan belum disimpan";
        clearTimeout(persistTimer);
        persistTimer = setTimeout(persistDraft, 250);
    }
    function rememberSection() {
        try { sessionStorage.setItem(viewKey, section); } catch (error) { /* Storage can be unavailable. */ }
    }
    function selectInspector(name) {
        root.querySelectorAll("[data-inspector-tab]").forEach((tab) => {
            tab.setAttribute("aria-selected", String(tab.dataset.inspectorTab === name));
        });
        root.querySelectorAll("[data-inspector-panel]").forEach((panel) => {
            panel.hidden = panel.dataset.inspectorPanel !== name;
        });
    }
    function closePanel() {
        delete root.dataset.openPanel;
        root.querySelector("[data-panel-close]").hidden = true;
        root.querySelectorAll("[data-panel-toggle]").forEach((button) => button.setAttribute("aria-expanded", "false"));
        syncPanels();
    }
    function syncPanels() {
        const compact = window.matchMedia("(max-width: 1100px)").matches;
        root.querySelectorAll("[data-panel-content]").forEach((panel) => {
            panel.inert = compact && root.dataset.openPanel !== panel.dataset.panelContent;
        });
    }
    function updatePanelTop() {
        root.style.setProperty("--studio-panel-top", root.querySelector(".studio-shell").getBoundingClientRect().top + "px");
    }
    function button(label, title, action) {
        const item = document.createElement("button");
        item.type = "button";
        item.className = "studio-icon-button";
        item.textContent = label;
        item.title = title;
        item.setAttribute("aria-label", title);
        item.addEventListener("click", action);
        return item;
    }
    function sectionOrder() { return ["cover", ...design.section_order, "closing"]; }
    function renderSectionList() {
        list.replaceChildren();
        sectionOrder().forEach((key, index) => {
            const row = document.createElement("div");
            row.className = "studio-section-row" + (section === key ? " active" : "") + (design.sections[key] === false ? " muted" : "");
            const select = document.createElement("button");
            select.type = "button";
            select.className = "studio-section-select";
            const number = document.createElement("span");
            number.className = "studio-section-index";
            number.textContent = String(index + 1).padStart(2, "0");
            const label = document.createElement("span");
            label.className = "studio-section-label";
            label.textContent = names[key];
            const note = document.createElement("small");
            note.textContent = key === "cover" || key === "closing" ? "Tetap tampil" : design.sections[key] === false ? "Disembunyikan" : "Tampil";
            label.append(note);
            select.append(number, label);
            select.addEventListener("click", () => { section = key; selectedId = null; rememberSection(); closePanel(); render(); });
            row.append(select);
            if (key !== "cover" && key !== "closing") {
                if (key !== "couple") row.append(button(design.sections[key] === false ? "⊘" : "◉", "Tampilkan atau sembunyikan " + names[key], () => { design.sections[key] = !design.sections[key]; markDirty(); renderSectionList(); }));
                if (index > 1) row.append(button("↑", "Naikkan " + names[key], () => moveSection(key, -1)));
                if (index < design.section_order.length) row.append(button("↓", "Turunkan " + names[key], () => moveSection(key, 1)));
            }
            list.append(row);
        });
    }
    function moveSection(key, direction) {
        const index = design.section_order.indexOf(key);
        const target = index + direction;
        if (target < 0 || target >= design.section_order.length) return;
        [design.section_order[index], design.section_order[target]] = [design.section_order[target], design.section_order[index]];
        markDirty();
        renderSectionList();
    }
    function renderCanvasContent() {
        content.replaceChildren();
        const layout = design.canvas[section];
        content.style.width = layout.content_width + "%";
        content.style.marginLeft = layout.content_x + "%";
        content.style.marginTop = layout.content_top + "px";
        content.style.textAlign = layout.content_align;
        const groom = demo.groom?.nickname || "Mempelai Pria";
        const bride = demo.bride?.nickname || "Mempelai Wanita";
        const eyebrow = document.createElement("small");
        eyebrow.textContent = section === "cover" ? "The Wedding Of" : names[section];
        const title = document.createElement("h2");
        title.textContent = {
            cover: groom + " & " + bride, couple: groom + " & " + bride, countdown: "Hari Bahagia Kami",
            events: "Acara Pernikahan", story: "Cerita Cinta", gallery: "Galeri Kami",
            rsvp: "RSVP", gift: "Wedding Gift", wishes: "Ucapan", quote: "Merayakan Cinta", closing: "Terima Kasih",
        }[section] || names[section];
        const detail = document.createElement("p");
        detail.textContent = {
            cover: "Kepada Yth. Tamu Undangan",
            quote: demo.quote || "Kutipan undangan",
            couple: (demo.groom?.full_name || groom) + "  ·  " + (demo.bride?.full_name || bride),
            countdown: demo.wedding_date || "Tanggal pernikahan",
            events: (demo.events || []).map((event) => event.name + " · " + event.location).join("   |   ") || "Rangkaian acara",
            story: (demo.stories || []).map((story) => story.title).join("   |   ") || "Cerita perjalanan pasangan",
            gallery: "Foto galeri dari undangan pelanggan",
            rsvp: "Formulir konfirmasi kehadiran",
            gift: (demo.banks || []).map((bank) => bank.bank_name + " · " + bank.account_number).join("   |   ") || "Informasi hadiah",
            wishes: "Ucapan dan doa dari tamu",
            closing: groom + " & " + bride,
        }[section] || "Konten undangan akan terisi otomatis dari data pelanggan.";
        content.append(eyebrow, title, detail);
        content.style.color = design.text_color;
        content.style.fontFamily = design.body_font === "serif" ? "Georgia, serif" : "Arial, sans-serif";
        title.style.fontFamily = design.heading_font === "sans" ? "Arial, sans-serif" : design.heading_font === "monospace" ? "monospace" : "Georgia, serif";
        eyebrow.style.color = design.primary_color;
    }
    function renderLayers() {
        layers.replaceChildren();
        currentElements().forEach((element) => {
            const node = document.createElement("div");
            node.className = "studio-layer " + element.kind + " " + (element.shape || "") + (selectedId === element.id ? " selected" : "");
            Object.assign(node.style, {
                left: element.x + "%", top: element.y + "%", width: element.width + "%", height: element.height + "%",
                color: element.color, opacity: element.opacity, transform: "rotate(" + element.rotation + "deg)",
                fontSize: (canvas.classList.contains("mobile") ? Math.round(element.font_size * .65) : element.font_size) + "px",
            });
            if (element.kind === "text") node.textContent = element.content;
            if (element.kind === "image" && element.image) {
                const image = document.createElement("img");
                image.src = new URL(element.image, root.dataset.assetBase).href;
                image.alt = "";
                node.append(image);
            }
            node.addEventListener("pointerdown", (event) => startDrag(event, element.id, node));
            layers.append(node);
        });
    }
    function renderProperties() {
        const element = selectedElement();
        properties.hidden = !element;
        root.querySelector("[data-empty-selection]").hidden = Boolean(element);
        if (!element) return;
        root.querySelector("[data-text-control]").hidden = element.kind !== "text";
        root.querySelector("[data-shape-control]").hidden = element.kind !== "shape";
        root.querySelector("[data-font-control]").hidden = element.kind !== "text";
        properties.querySelectorAll("[data-property]").forEach((input) => { input.value = element[input.dataset.property] ?? ""; });
    }
    function render() {
        renderSectionList();
        root.querySelector("[data-current-section]").textContent = names[section];
        const current = design.canvas[section];
        root.querySelector("[data-current-meta]").textContent = current.elements.length + " elemen tambahan · seret untuk mengatur posisi";
        canvas.style.background = current.background;
        canvas.style.minHeight = current.height + "px";
        root.querySelector("[data-section-background]").value = current.background;
        root.querySelector("[data-section-height]").value = current.height;
        root.querySelector("[data-height-output]").textContent = current.height + "px";
        root.querySelectorAll("[data-content-property]").forEach((input) => { input.value = current[input.dataset.contentProperty]; });
        root.querySelectorAll("[data-design-color]").forEach((input) => { input.value = design[input.dataset.designColor]; });
        root.querySelectorAll("[data-design-font]").forEach((input) => { input.value = design[input.dataset.designFont]; });
        renderCanvasContent();
        renderLayers();
        renderProperties();
    }
    function createElement(kind, image = "") {
        if (currentElements().length >= 30) { alert("Maksimal 30 elemen per section."); return; }
        const element = {
            id: window.crypto?.randomUUID?.() || "element-" + Date.now() + "-" + Math.random().toString(36).slice(2), kind, x: 32, y: 30, width: kind === "text" ? 36 : 25,
            height: kind === "text" ? 12 : 22, font_size: 32, rotation: 0, opacity: 1,
            color: design.primary_color, content: kind === "text" ? "Teks baru" : "", image,
            shape: kind === "shape" ? "circle" : "rectangle",
        };
        currentElements().push(element);
        selectedId = element.id;
        markDirty();
        selectInspector("element");
        render();
    }
    function startDrag(event, id, node) {
        if (event.button !== 0) return;
        event.preventDefault();
        selectedId = id;
        selectInspector("element");
        const element = selectedElement();
        const startX = event.clientX;
        const startY = event.clientY;
        const originalX = Number(element.x);
        const originalY = Number(element.y);
        node.setPointerCapture(event.pointerId);
        renderProperties();
        layers.querySelectorAll(".studio-layer").forEach((item) => item.classList.toggle("selected", item === node));
        const move = (pointer) => {
            const rect = canvas.getBoundingClientRect();
            element.x = Math.round(Math.max(0, Math.min(100 - Number(element.width), originalX + (pointer.clientX - startX) / rect.width * 100)) * 10) / 10;
            element.y = Math.round(Math.max(0, Math.min(100 - Number(element.height), originalY + (pointer.clientY - startY) / rect.height * 100)) * 10) / 10;
            node.style.left = element.x + "%";
            node.style.top = element.y + "%";
            renderProperties();
            markDirty();
        };
        node.addEventListener("pointermove", move);
        node.addEventListener("pointerup", () => node.removeEventListener("pointermove", move), { once: true });
        node.addEventListener("pointercancel", () => node.removeEventListener("pointermove", move), { once: true });
    }
    function startContentDrag(event) {
        if (event.button !== 0 || event.target.closest("input,button,select,textarea")) return;
        event.preventDefault();
        const layout = design.canvas[section];
        const startX = event.clientX;
        const startY = event.clientY;
        const originalX = Number(layout.content_x);
        const originalTop = Number(layout.content_top);
        content.setPointerCapture(event.pointerId);
        const move = (pointer) => {
            const rect = canvas.getBoundingClientRect();
            layout.content_x = Math.round(Math.max(0, Math.min(100 - layout.content_width, originalX + (pointer.clientX - startX) / rect.width * 100)) * 10) / 10;
            layout.content_top = Math.round(Math.max(0, Math.min(300, originalTop + pointer.clientY - startY)));
            content.style.marginLeft = layout.content_x + "%";
            content.style.marginTop = layout.content_top + "px";
            root.querySelector('[data-content-property="content_x"]').value = layout.content_x;
            root.querySelector('[data-content-property="content_top"]').value = layout.content_top;
            markDirty();
        };
        content.addEventListener("pointermove", move);
        content.addEventListener("pointerup", () => content.removeEventListener("pointermove", move), { once: true });
        content.addEventListener("pointercancel", () => content.removeEventListener("pointermove", move), { once: true });
    }

    root.querySelectorAll("[data-add]").forEach((button) => button.addEventListener("click", () => {
        if (button.dataset.add === "image") imageInput.click();
        else createElement(button.dataset.add);
    }));
    imageInput.addEventListener("change", async () => {
        const file = imageInput.files[0];
        if (!file) return;
        const uploadSection = section;
        if (file.size > 5 * 1024 * 1024) { alert("Gambar maksimal 5 MB."); imageInput.value = ""; return; }
        const data = new FormData();
        data.append("image", file);
        try {
            const response = await fetch(root.dataset.uploadUrl, {
                method: "POST", headers: { "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content, "Accept": "application/json" },
                body: data,
            });
            const result = await response.json();
            if (!response.ok) throw new Error(result.message || "Gambar gagal diunggah.");
            section = uploadSection;
            createElement("image", result.path);
        } catch (error) { alert(error.message); }
        imageInput.value = "";
    });
    root.querySelector("[data-section-background]").addEventListener("input", (event) => { design.canvas[section].background = event.target.value; canvas.style.background = event.target.value; markDirty(); });
    root.querySelector("[data-section-height]").addEventListener("input", (event) => { design.canvas[section].height = Number(event.target.value); canvas.style.minHeight = event.target.value + "px"; root.querySelector("[data-height-output]").textContent = event.target.value + "px"; markDirty(); });
    root.querySelectorAll("[data-content-property]").forEach((input) => input.addEventListener("input", () => {
        const layout = design.canvas[section];
        const field = input.dataset.contentProperty;
        layout[field] = input.type === "number" ? Math.max(Number(input.min), Math.min(Number(input.max), Number(input.value))) : input.value;
        if (field === "content_x" || field === "content_width") layout.content_x = Math.min(layout.content_x, 100 - layout.content_width);
        root.querySelector('[data-content-property="content_x"]').value = layout.content_x;
        renderCanvasContent();
        markDirty();
    }));
    content.addEventListener("pointerdown", startContentDrag);
    root.querySelectorAll("[data-design-color]").forEach((input) => input.addEventListener("input", () => { design[input.dataset.designColor] = input.value; renderCanvasContent(); markDirty(); }));
    root.querySelectorAll("[data-design-font]").forEach((input) => input.addEventListener("change", () => { design[input.dataset.designFont] = input.value; renderCanvasContent(); markDirty(); }));
    properties.querySelectorAll("[data-property]").forEach((input) => input.addEventListener("input", () => {
        const element = selectedElement();
        if (!element) return;
        const field = input.dataset.property;
        if (input.type === "number") {
            const minimum = input.min === "" ? -Infinity : Number(input.min);
            const maximum = input.max === "" ? Infinity : Number(input.max);
            element[field] = Math.max(minimum, Math.min(maximum, Number(input.value)));
            if (field === "x" || field === "width") element.x = Math.min(element.x, 100 - element.width);
            if (field === "y" || field === "height") element.y = Math.min(element.y, 100 - element.height);
            input.value = element[field];
        } else {
            element[field] = input.value;
        }
        renderLayers();
        markDirty();
    }));
    root.querySelector("[data-delete-element]").addEventListener("click", () => {
        design.canvas[section].elements = currentElements().filter((element) => element.id !== selectedId);
        selectedId = null;
        markDirty();
        render();
    });
    root.querySelectorAll("[data-device]").forEach((button) => button.addEventListener("click", () => {
        canvas.classList.toggle("mobile", button.dataset.device === "mobile");
        root.querySelectorAll("[data-device]").forEach((item) => {
            item.classList.toggle("active", item === button);
            item.setAttribute("aria-pressed", String(item === button));
        });
        renderLayers();
    }));
    const fullscreenButton = root.querySelector("[data-toggle-fullscreen]");
    if (!document.documentElement.requestFullscreen) fullscreenButton.hidden = true;
    fullscreenButton.addEventListener("click", async () => {
        try {
            if (document.fullscreenElement) await document.exitFullscreen();
            else await document.documentElement.requestFullscreen();
        } catch (error) {
            fullscreenButton.hidden = true;
        }
    });
    document.addEventListener("fullscreenchange", () => {
        fullscreenButton.setAttribute("aria-label", document.fullscreenElement ? "Keluar layar penuh" : "Masuk layar penuh");
        fullscreenButton.setAttribute("aria-pressed", String(Boolean(document.fullscreenElement)));
    });
    root.querySelectorAll("[data-inspector-tab]").forEach((tab) => tab.addEventListener("click", () => selectInspector(tab.dataset.inspectorTab)));
    root.querySelectorAll("[data-panel-toggle]").forEach((button) => button.addEventListener("click", () => {
        const next = root.dataset.openPanel === button.dataset.panelToggle ? "" : button.dataset.panelToggle;
        closePanel();
        if (next) {
            root.dataset.openPanel = next;
            root.querySelector("[data-panel-close]").hidden = false;
            button.setAttribute("aria-expanded", "true");
            syncPanels();
        }
    }));
    root.querySelector("[data-panel-close]").addEventListener("click", closePanel);
    window.addEventListener("resize", () => { updatePanelTop(); syncPanels(); });
    root.querySelector("[data-publish-form]").addEventListener("submit", (event) => {
        if (dirty) {
            event.preventDefault();
            alert("Simpan perubahan desain terlebih dahulu sebelum menerbitkan.");
            return;
        }
        if (!confirm("Terbitkan draft ini? Semua undangan yang memakai templat ini akan berubah.")) event.preventDefault();
    });
    root.querySelector("[data-preview-link]").addEventListener("click", (event) => {
        if (dirty && !confirm("Perubahan belum disimpan. Pratinjau akan menampilkan draft tersimpan. Tetap buka?")) event.preventDefault();
    });
    form.addEventListener("submit", () => {
        clearTimeout(persistTimer);
        persistDraft();
        root.querySelector("[data-studio-payload]").value = JSON.stringify({
            primary_color: design.primary_color, text_color: design.text_color,
            background_color: design.background_color, heading_font: design.heading_font,
            body_font: design.body_font, section_order: design.section_order,
            sections: design.sections, canvas: design.canvas,
        });
        dirty = false;
    });
    window.addEventListener("beforeunload", (event) => { if (dirty) { persistDraft(); event.preventDefault(); event.returnValue = ""; } });
    document.addEventListener("keydown", (event) => {
        if ((event.metaKey || event.ctrlKey) && event.key.toLowerCase() === "s") { event.preventDefault(); form.requestSubmit(); }
        if (event.key === "Escape") { closePanel(); selectedId = null; renderLayers(); renderProperties(); }
        if ((event.key === "Delete" || event.key === "Backspace") && selectedId && !event.target.closest("input,textarea,select")) {
            design.canvas[section].elements = currentElements().filter((element) => element.id !== selectedId);
            selectedId = null;
            markDirty();
            render();
        }
    });
    render();
    updatePanelTop();
    syncPanels();
    if (recoveredDraft) {
        markDirty();
        root.querySelector("[data-save-state]").textContent = "Perubahan dipulihkan, belum disimpan";
        root.querySelector("[data-save-state-mobile]").textContent = "Perubahan dipulihkan, belum disimpan";
    }
})();
