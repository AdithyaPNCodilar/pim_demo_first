pimcore.registerNS("pimcore.plugin.MessengerBundle");

pimcore.plugin.MessengerBundle = Class.create({

    initialize: function () {
        document.addEventListener(pimcore.events.pimcoreReady, this.pimcoreReady.bind(this));
    },

    pimcoreReady: function (e) {
        // alert("MessengerBundle ready!");
    }
});

var MessengerBundlePlugin = new pimcore.plugin.MessengerBundle();
