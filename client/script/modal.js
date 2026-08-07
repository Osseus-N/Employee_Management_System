const Modal = {

    open(id) {

        Utils.byId(id).classList.remove("hidden");

    },

    close(id) {

        Utils.byId(id).classList.add("hidden");

    }

};