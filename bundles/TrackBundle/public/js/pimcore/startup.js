pimcore.registerNS("pimcore.plugin.TrackBundle");

pimcore.plugin.TrackBundle = Class.create({

    initialize: function () {
        document.addEventListener(pimcore.events.pimcoreReady, this.pimcoreReady.bind(this));
    },

    pimcoreReady: function () {
        // Check user permissions
        const user = pimcore.globalmanager.get("user");
        const permissions = user.permissions;

        if (permissions.includes("objects")) {
            const navigationUl = Ext.get(Ext.query("#pimcore_navigation UL"));
            const newMenuItem = Ext.DomHelper.createDom('<li id="pimcore_menu_new-item" data-menu-tooltip="Admin Action" class="pimcore_menu_item pimcore_menu_needs_children icon-book_open"></li>');
            navigationUl.appendChild(newMenuItem);
            pimcore.helpers.initMenuTooltips();

            const iconImage = document.createElement("img");
            iconImage.src = "/bundles/pimcoreadmin/img/icon/marker.png";
            newMenuItem.appendChild(iconImage);

            newMenuItem.onclick = function () {
                // alert("Custom menu item clicked");
                this.openTabPanel();
            }.bind(this);
        }
        // alert("TrackBundle ready!");
    },

    openTabPanel: function () {

        const store = Ext.create('Ext.data.Store', {
            fields: ['id','user_id', 'action', 'timestamp'],
            pageSize: 100,
            proxy: {
                type: 'ajax',
                url: '/tracked',
                reader: {
                    type: 'json',
                    rootProperty: 'data',
                },
            },
            autoLoad: true,
        });

        const tabPanel = new Ext.grid.Panel({
            title: "Admin",
            closable: true,
            store: store,
            columns: [
                {text: "ID", dataIndex: "id"},
                {text: "User ID", dataIndex: "user_id"},
                {text: "Action", dataIndex: "action"},
                {text: "Timestamp", dataIndex: "timestamp"},
            ],
            bbar: Ext.create('Ext.PagingToolbar', {
                store: store,
                displayInfo: true,
                displayMsg: 'Displaying {0} - {1} of {2}',
                emptyMsg: "No data to display",
            }),
        });

        const mainPanel = Ext.getCmp("pimcore_panel_tabs");
        mainPanel.add(tabPanel);
        mainPanel.setActiveTab(tabPanel);
    },
});

var TrackBundlePlugin = new pimcore.plugin.TrackBundle();
