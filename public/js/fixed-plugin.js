// Ambil semua elemen yang dibutuhkan di awal
var fixedPlugin = document.querySelector("[fixed-plugin]");
var fixedPluginButton = document.querySelector("[fixed-plugin-button]");
var fixedPluginButtonNav = document.querySelector("[fixed-plugin-button-nav]");
var fixedPluginCard = document.querySelector("[fixed-plugin-card]");
var fixedPluginCloseButton = document.querySelector("[fixed-plugin-close-button]");
var navbar = document.querySelector("[navbar-main]");
var buttonNavbarFixed = document.querySelector("[navbarFixed]");
var sidenav = document.querySelector("aside");
var whiteBtn = document.querySelector("[transparent-style-btn]");
var darkBtn = document.querySelector("[white-style-btn]");
var dark_mode_toggle = document.querySelector("[dark-toggle]");
var root_html = document.querySelector("html");

// --- Fixed plugin toggle ---
// Hanya jalankan jika elemen utama plugin ada
if (fixedPlugin && fixedPluginButton && fixedPluginButtonNav && fixedPluginCloseButton && fixedPluginCard) {
    var pageName = window.location.pathname.split("/").pop().split(".")[0];

    if (pageName != "rtl") {
        fixedPluginButton.addEventListener("click", function () {
            fixedPluginCard.classList.toggle("-right-90");
            fixedPluginCard.classList.toggle("right-0");
        });

        fixedPluginButtonNav.addEventListener("click", function () {
            fixedPluginCard.classList.toggle("-right-90");
            fixedPluginCard.classList.toggle("right-0");
        });

        fixedPluginCloseButton.addEventListener("click", function () {
            fixedPluginCard.classList.toggle("-right-90");
            fixedPluginCard.classList.toggle("right-0");
        });

        window.addEventListener("click", function (e) {
            if (!fixedPlugin.contains(e.target) && !fixedPluginButton.contains(e.target) && !fixedPluginButtonNav.contains(e.target)) {
                if (fixedPluginCard.classList.contains("right-0")) {
                    fixedPluginCloseButton.click();
                }
            }
        });
    } else {
        // ... (logika untuk RTL bisa ditambahkan di sini jika perlu)
    }
}

// --- Sidenav style ---
// Hanya jalankan jika kedua tombol style ada
if (whiteBtn && darkBtn && sidenav) {
    var non_active_style = ["bg-none", "bg-transparent", "text-blue-500", "border-blue-500"];
    var active_style = ["bg-gradient-to-tl", "from-blue-500", "to-violet-500", "bg-blue-500", "text-white", "border-transparent"];
    var white_sidenav_classes = ["bg-white", "shadow-xl"];
    var black_sidenav_classes = ["bg-slate-850", "shadow-none"];

    whiteBtn.addEventListener("click", function () {
        // ... (sisanya biarkan sama)
        const active_style_attr = document.createAttribute("active-style");
        if (!this.hasAttribute(active_style_attr)) {
            this.setAttributeNode(active_style_attr);
            non_active_style.forEach((style_class) => this.classList.remove(style_class));
            active_style.forEach((style_class) => this.classList.add(style_class));
            darkBtn.removeAttribute(active_style_attr);
            active_style.forEach((style_class) => darkBtn.classList.remove(style_class));
            non_active_style.forEach((style_class) => darkBtn.classList.add(style_class));
            black_sidenav_classes.forEach((style_class) => sidenav.classList.remove(style_class));
            white_sidenav_classes.forEach((style_class) => sidenav.classList.add(style_class));
            sidenav.classList.remove("dark");
        }
    });

    darkBtn.addEventListener("click", function () {
        // ... (sisanya biarkan sama)
        const active_style_attr = document.createAttribute("active-style");
        if (!this.hasAttribute(active_style_attr)) {
            this.setAttributeNode(active_style_attr);
            non_active_style.forEach((style_class) => this.classList.remove(style_class));
            active_style.forEach((style_class) => this.classList.add(style_class));
            whiteBtn.removeAttribute(active_style_attr);
            active_style.forEach((style_class) => whiteBtn.classList.remove(style_class));
            non_active_style.forEach((style_class) => whiteBtn.classList.add(style_class));
            white_sidenav_classes.forEach((style_class) => sidenav.classList.remove(style_class));
            black_sidenav_classes.forEach((style_class) => sidenav.classList.add(style_class));
            sidenav.classList.add("dark");
        }
    });
}

// --- Navbar fixed plugin ---
// Hanya jalankan jika navbar dan tombolnya ada
if (navbar && buttonNavbarFixed) {
    if (navbar.getAttribute("navbar-scroll") == "true") {
        buttonNavbarFixed.setAttribute("checked", "true");
    }
    const white_elements = navbar.querySelectorAll(".text-white");
    const white_bg_elements = navbar.querySelectorAll("[sidenav-trigger] i.bg-white");
    const white_before_elements = navbar.querySelectorAll(".before\\:text-white");

    buttonNavbarFixed.addEventListener("change", function () {
        // ... (sisanya biarkan sama)
        if (this.checked) {
            white_elements.forEach(element => { element.classList.remove("text-white"); element.classList.add("dark:text-white") });
            white_bg_elements.forEach(element => { element.classList.remove("bg-white"); element.classList.add("dark:bg-white"); element.classList.add("bg-slate-500") });
            white_before_elements.forEach(element => { element.classList.add("dark:before:text-white"); element.classList.remove("before:text-white") });
            navbar.setAttribute("navbar-scroll", "true"); navbar.classList.add("sticky", "top-[1%]", "backdrop-saturate-200", "backdrop-blur-2xl", "dark:bg-slate-850/80", "dark:shadow-dark-blur", "bg-[hsla(0,0%,100%,0.8)]", "shadow-blur", "z-110");
        } else {
            navbar.setAttribute("navbar-scroll", "false"); navbar.classList.remove("sticky", "top-[1%]", "backdrop-saturate-200", "backdrop-blur-2xl", "dark:bg-slate-850/80", "dark:shadow-dark-blur", "bg-[hsla(0,0%,100%,0.8)]", "shadow-blur", "z-110");
            white_elements.forEach(element => { element.classList.add("text-white"); element.classList.remove("dark:text-white") });
            white_bg_elements.forEach(element => { element.classList.add("bg-white"); element.classList.remove("dark:bg-white"); element.classList.remove("bg-slate-500") });
            white_before_elements.forEach(element => { element.classList.remove("dark:before:text-white"); element.classList.add("before:text-white") });
        }
    });
}

// --- Dark mode toggle ---
// Hanya jalankan jika tombol dark mode ada
if (dark_mode_toggle && root_html) {
    dark_mode_toggle.addEventListener("change", function () {
        dark_mode_toggle.setAttribute("manual", "true");
        if (this.checked) {
            root_html.classList.add("dark");
        } else {
            root_html.classList.remove("dark");
        }
    });
}