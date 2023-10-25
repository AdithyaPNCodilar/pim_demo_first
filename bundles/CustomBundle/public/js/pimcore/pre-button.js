
document.addEventListener(pimcore.events.postOpenObject, (e) => {
    if (e.detail.object.data.general.className === 'Product') {
        e.detail.object.toolbar.add({
            text: t('Preview'),
            iconCls: 'pimcore_icon_preview',
            scale: 'small',

            handler: function (obj) {
                var id = obj.data.general.id;
                // Make an AJAX request to your Controller
                const xhr = new XMLHttpRequest();
                xhr.open('GET', `/product/${id}`, true);

                xhr.onload = function() {
                    if (xhr.status === 200) {
                        // Create a new window or tab and write the response content to it
                        const newTab = window.open('', '_blank');
                        newTab.document.open();
                        newTab.document.write(xhr.responseText);
                        newTab.document.close();
                        newTab.location.href = '/product/'+id;
                    }
                };

                xhr.send();
            }.bind(this, e.detail.object)
        });
        pimcore.layout.refresh();
    }
});
