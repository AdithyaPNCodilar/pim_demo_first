pimcore.registerNS("pimcore.plugin.MyBundle");

pimcore.plugin.MyBundle = Class.create({

    initialize: function () {
        document.addEventListener(pimcore.events.pimcoreReady, this.pimcoreReady.bind(this));
    },

    pimcoreReady: function (e) {
        // alert("MyBundle ready!");
    }
});

var MyBundlePlugin = new pimcore.plugin.MyBundle();
