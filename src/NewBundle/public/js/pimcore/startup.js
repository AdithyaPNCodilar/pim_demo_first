pimcore.registerNS("pimcore.plugin.NewBundle");

pimcore.plugin.NewBundle = Class.create({

    initialize: function () {
        document.addEventListener(pimcore.events.pimcoreReady, this.pimcoreReady.bind(this));
    },

    pimcoreReady: function (e) {
        // alert("NewBundle ready!");
    }
});

var NewBundlePlugin = new pimcore.plugin.NewBundle();
