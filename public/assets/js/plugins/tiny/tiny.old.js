(function () {
    var mediatheque = (function () {
        'use strict';

        tinymce.PluginManager.add("mediatheque", function (editor, url) {

            function _onAction()
            {
                // Open a Dialog
                editor.windowManager.open({
                    title: 'Hello World Example Plugin',
                    body: {
                        type: 'panel',
                        items: [{
                            type: 'selectbox',
                            name: 'type',
                            label: 'Dropdown',
                            items: [
                                {text: 'Option 1', value: '1'},
                                {text: 'Option 2', value: '2'}
                            ],
                            flex: true
                        }]
                    },
                    onSubmit: function (api) {
                        // insert markup
                        editor.insertContent('<p>You selected Option ' + api.getData().type + '.</p>');

                        // close the dialog
                        api.close();
                    },
                    buttons: [
                        {
                            text: 'Close',
                            type: 'cancel',
                            onclick: 'close'
                        },
                        {
                            text: 'Insert',
                            type: 'submit',
                            primary: true,
                            enabled: false
                        }
                    ]
                });
            }

            // Define the Toolbar button
            editor.ui.registry.addButton('mediatheque', {
                text: "Médiathèque",
                onAction: _onAction
            });

            // Return details to be displayed in TinyMCE's "Help" plugin, if you use it
            // This is optional.
            return {
                getMetadata: function () {
                    return {
                        name: "Hello World example",
                        url: "https://www.martyfriedel.com/blog/tinymce-5-creating-a-plugin-with-a-dialog-and-custom-icons"
                    };
                }
            };
        });
    }());
})();