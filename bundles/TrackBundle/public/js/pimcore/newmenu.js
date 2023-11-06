pimcore.registerNS("pimcore.plugin.newbutton");
pimcore.plugin.newbutton = Class.create({
    systemPanel: null,

    initialize: function () {
        document.addEventListener(pimcore.events.pimcoreReady, this.pimcoreReady.bind(this));
    },

    pimcoreReady: function (e) {
        const user = pimcore.globalmanager.get("user");
        const permissions = user.permissions;

        if (permissions.indexOf("objects")) {
            const navigationUl = Ext.get(Ext.query("#pimcore_navigation UL"));
            const customMenuItem = Ext.DomHelper.createDom('<li id="pimcore_menu_custom-item" data-menu-tooltip="Custom Settings" class="pimcore_menu_item icon-system"></li>');
            navigationUl.appendChild(customMenuItem);
            pimcore.helpers.initMenuTooltips();
            const iconImage = document.createElement("img");
            iconImage.src = "/bundles/pimcoreadmin/img/icon/book.png";
            customMenuItem.appendChild(iconImage);


            const submenu = new Ext.menu.Menu({
                items: [
                    {
                        text: "System Settings",
                        iconCls: "pimcore_icon_settings",
                        handler: this.toggleSystem.bind(this)
                    }
                ]
            });

            customMenuItem.onclick = function () {
                var customMenuItemPosition = Ext.get(customMenuItem).getXY();
                submenu.showAt([customMenuItemPosition[0] + customMenuItem.offsetWidth, customMenuItemPosition[1]]);
            };
        }
    },
    toggleSystem: function () {
        if (this.systemPanel) {
            const mainPanel = Ext.getCmp("pimcore_panel_tabs");
            mainPanel.setActiveTab(this.systemPanel);
        } else {
            this.systemPanel = new Ext.Panel({
                title: "System Settings",
                iconCls: "pimcore_icon_settings",
                closable: true,
                layout: 'form',
                items: [
                    {
                        xtype: 'fieldset',
                        title: 'Product Basic',
                        collapsible: true,
                        collapsed: true,
                        items: [
                            {
                                xtype: 'textfield',
                                fieldLabel: 'Product Name',
                                id: 'productName',
                            },
                            {
                                xtype: 'textfield',
                                fieldLabel: 'SKU',
                                id: 'sku'
                            },
                        ],
                    },
                    {
                        xtype: 'fieldset',
                        title: 'Details',
                        collapsible: true,
                        collapsed: true,
                        items: [
                            {
                                xtype: 'numberfield',
                                fieldLabel: 'Quantity',
                                id: 'quantity',
                            },

                            {
                                xtype: 'combo',
                                fieldLabel: 'Product Color',
                                id: 'productColor',
                                multiSelect: true,
                                store: ['Black', 'White', 'Grey'],
                            },
                            {
                                xtype: 'combo',
                                fieldLabel: 'Product Type',
                                id: 'productType',
                                store: ['Automatic', 'Gear'],
                            },
                            // {
                            //     xtype: 'datefield',
                            //     fieldLabel: 'Launch Date',
                            //     id: 'selectedDate',
                            //     format: 'Y-m-d',
                            // },
                            {
                                xtype: 'checkboxfield',
                                boxLabel: 'Check me',
                                id: 'checked',
                                inputValue: true,
                                uncheckedValue: false,
                            }


                        ],
                    },
                ],
                bbar: [
                    '->',
                    {
                        xtype: 'button',
                        text: 'Save',
                        handler: this.saveData.bind(this),
                    },
                ],
                listeners: {
                    afterrender: function () {
                        // Load data when the panel is rendered
                        this.loadData();
                    }.bind(this)
                }
            });

            const mainPanel = Ext.getCmp("pimcore_panel_tabs");
            mainPanel.add(this.systemPanel);
            mainPanel.setActiveTab(this.systemPanel);
        }

        this.systemPanel.on('beforeclose', function (tab) {
            this.systemPanel = null;
        }.bind(this));
    },

    validateForm: function () {
        const productName = Ext.getCmp('productName').getValue();
        const quantity = Ext.getCmp('quantity').getValue();
        const sku = Ext.getCmp('sku').getValue();
        // const checked = Ext.getCmp('checked').getValue();

        // Validate ProductName
        if (!productName) {
            Ext.Msg.alert('Error', 'ProductName is a mandatory field.');
            return false;
        }

        // Validate Quantity
        if (!quantity || quantity <= 0) {
            Ext.Msg.alert('Error', 'Quantity must be a positive number.');
            return false;
        }

        // Validate SKU
        if (!sku) {
            Ext.Msg.alert('Error', 'SKU is a mandatory field.');
            return false;
        }

        // Validate Checkbox
        // if (!checked) {
        //     Ext.Msg.alert('Error', 'Checkbox is a mandatory field.');
        //     return false;
        // }

        return true;
    },

    saveData: function () {
        if (!this.validateForm()) {
            return;
        }
        const productName = Ext.getCmp('productName').getValue();
        const sku = Ext.getCmp('sku').getValue();
        const quantity = Ext.getCmp('quantity').getValue();
        const productColor = Ext.getCmp('productColor').getValue();
        const productType = Ext.getCmp('productType').getValue();
        // const selectedDate = Ext.getCmp('selectedDate').getValue();
        const checked = Ext.getCmp('checked').getValue();


        const data = {
            productName: productName,
            sku: sku,
            quantity: quantity,
            productColor: productColor,
            productType: productType,
            // selectedDate: selectedDate,
            checked: checked,

        };
        Ext.Ajax.request({
            url: '/save',
            method: 'POST',
            params: {
                data: Ext.encode(data)
            },
            success: function (response) {
                const result = Ext.decode(response.responseText);
                if (result.success) {
                    Ext.Msg.alert('Success', 'Product Data Saved Successfully.');
                } else {
                    console.error("Failed to save data");
                }
            },
            failure: function (response) {
                console.error("Failed to save data");
            }
        });
    },
    loadData: function () {
        // Make an Ajax request to fetch data from the server
        Ext.Ajax.request({
            url: '/load',
            method: 'GET',
            success: function (response) {
                const result = Ext.decode(response.responseText);
                if (result.success) {
                    // Populate the fields with the retrieved data
                    Ext.getCmp('productName').setValue(result.data.productName);
                    Ext.getCmp('sku').setValue(result.data.sku);
                    Ext.getCmp('quantity').setValue(result.data.quantity);
                    Ext.getCmp('productColor').setValue(result.data.productColor);
                    Ext.getCmp('productType').setValue(result.data.productType);
                    // Ext.getCmp('selectedDate').setValue(result.data.selectedDate);
                    Ext.getCmp('checked').setValue(result.data.checked);

                } else {
                    console.error("Failed to load data");
                }
            },
            failure: function (response) {
                console.error("Failed to load data");
            }
        });
    },

});

const newmenuPlugin = new pimcore.plugin.newbutton();