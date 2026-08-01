const Utils = {

    qs(selector) {
        return document.querySelector(selector);
    },

    qsa(selector) {
        return document.querySelectorAll(selector);
    },

    byId(id) {
        return document.getElementById(id);
    },

    show(element) {
        element.classList.remove("hidden");
    },

    hide(element) {
        element.classList.add("hidden");
    }

};