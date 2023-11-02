pimcore.registerNS("pimcore.plugin.TrackingBundle");

pimcore.plugin.TrackingBundle = Class.create({

    initialize: function () {
        document.addEventListener(pimcore.events.pimcoreReady, this.pimcoreReady.bind(this));
    },

    pimcoreReady: function (e) {
        // alert("TrackingBundle ready!");
    }
});

var TrackingBundlePlugin = new pimcore.plugin.TrackingBundle();
