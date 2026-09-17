/**
 * Very simple example Plugin utilising URL Dialog
 *
 * @author Marty Friedel
 */
(function () {
    var iframe = (function () {
        'use strict';

        tinymce.PluginManager.add("mediatheque", function (editor, url) {

            /*
            Add a custom icon to TinyMCE
            Icon Backup:<svg version="1.0" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" preserveAspectRatio="xMidYMid meet"><g transform="translate(0.000000,24) scale(0.100000,-0.100000)" fill="#000000" stroke="none"><path d="M68 214 c-31 -16 -58 -61 -58 -96 0 -62 69 -116 133 -104 94 18 116 146 35 196 -39 23 -73 25 -110 4z m107 -39 c33 -32 33 -78 0 -110 -49 -50 -135 -15 -135 55 0 41 39 80 80 80 19 0 40 -9 55 -25z"/><path d="M96 135 c-8 -22 4 -45 24 -45 10 0 20 7 24 15 8 22 -4 45 -24 45 -10 0 -20 -7 -24 -15z"/></g></svg>
             */
            editor.ui.registry.addIcon('media', `<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="16pt" height="16pt" viewBox="0 0 16 16" version="1.1">
                <g id="surface1">
                <path style=" stroke:none;fill-rule:nonzero;fill:rgb(100%,64.313725%,21.176471%);fill-opacity:1;" d="M 14.828125 2.8125 L 13.890625 2.8125 L 13.890625 5.625 L 15.765625 5.625 L 15.765625 3.75 C 15.765625 3.230469 15.347656 2.8125 14.828125 2.8125 Z M 14.828125 2.8125 "/>
                <path style=" stroke:none;fill-rule:nonzero;fill:rgb(100%,73.72549%,16.862745%);fill-opacity:1;" d="M 14.828125 3.75 L 12.015625 7.5 L 0.234375 7.5 L 0.234375 1.875 C 0.234375 1.355469 0.652344 0.9375 1.171875 0.9375 L 5.738281 0.9375 C 5.984375 0.9375 6.222656 1.035156 6.398438 1.210938 L 8 2.8125 L 13.890625 2.8125 C 14.410156 2.8125 14.828125 3.230469 14.828125 3.75 Z M 14.828125 3.75 "/>
                <path style=" stroke:none;fill-rule:nonzero;fill:rgb(54.117647%,90.588235%,100%);fill-opacity:1;" d="M 13.890625 3.75 L 12.015625 6.09375 L 14.828125 6.09375 L 14.828125 3.75 Z M 13.890625 3.75 "/>
                <path style=" stroke:none;fill-rule:nonzero;fill:rgb(74.117647%,94.901961%,100%);fill-opacity:1;" d="M 13.890625 3.75 L 13.890625 8.4375 L 1.171875 4.6875 L 1.171875 3.75 Z M 13.890625 3.75 "/>
                <path style=" stroke:none;fill-rule:nonzero;fill:rgb(100%,100%,100%);fill-opacity:1;" d="M 1.171875 4.6875 L 10.261719 4.6875 L 10.261719 7.5 L 1.171875 7.5 Z M 1.171875 4.6875 "/>
                <path style=" stroke:none;fill-rule:nonzero;fill:rgb(100%,73.72549%,16.862745%);fill-opacity:1;" d="M 14.828125 4.6875 L 13.890625 5.625 L 13.890625 15.0625 L 14.828125 15.0625 C 15.347656 15.0625 15.765625 14.644531 15.765625 14.125 L 15.765625 5.625 C 15.765625 5.105469 15.347656 4.6875 14.828125 4.6875 Z M 14.828125 4.6875 "/>
                <path style=" stroke:none;fill-rule:nonzero;fill:rgb(100%,81.176471%,40%);fill-opacity:1;" d="M 14.828125 4.6875 L 14.828125 14.125 C 14.828125 14.644531 14.410156 15.0625 13.890625 15.0625 L 1.171875 15.0625 C 0.652344 15.0625 0.234375 14.644531 0.234375 14.125 L 0.234375 7.5 C 0.234375 6.980469 0.652344 6.5625 1.171875 6.5625 L 8 6.5625 L 9.601562 4.960938 C 9.777344 4.785156 10.015625 4.6875 10.261719 4.6875 Z M 14.828125 4.6875 "/>
                <path style=" stroke:none;fill-rule:nonzero;fill:rgb(0%,0%,0%);fill-opacity:1;" d="M 14.828125 2.578125 L 8.097656 2.578125 L 6.566406 1.046875 C 6.34375 0.824219 6.050781 0.703125 5.738281 0.703125 L 1.171875 0.703125 C 0.527344 0.703125 0 1.230469 0 1.875 L 0 14.125 C 0 14.769531 0.527344 15.296875 1.171875 15.296875 L 14.828125 15.296875 C 15.472656 15.296875 16 14.769531 16 14.125 L 16 3.75 C 16 3.105469 15.472656 2.578125 14.828125 2.578125 Z M 1.171875 1.171875 L 5.738281 1.171875 C 5.925781 1.171875 6.101562 1.246094 6.234375 1.378906 L 7.835938 2.976562 C 7.878906 3.023438 7.9375 3.046875 8 3.046875 L 14.828125 3.046875 C 15.214844 3.046875 15.53125 3.363281 15.53125 3.75 L 15.53125 4.6875 C 15.394531 4.585938 15.234375 4.511719 15.0625 4.476562 L 15.0625 3.75 C 15.0625 3.621094 14.957031 3.515625 14.828125 3.515625 L 1.171875 3.515625 C 1.042969 3.515625 0.9375 3.621094 0.9375 3.75 L 0.9375 6.351562 C 0.765625 6.386719 0.605469 6.460938 0.46875 6.5625 L 0.46875 1.875 C 0.46875 1.488281 0.785156 1.171875 1.171875 1.171875 Z M 3.046875 4.453125 C 2.917969 4.453125 2.8125 4.558594 2.8125 4.6875 C 2.8125 4.816406 2.917969 4.921875 3.046875 4.921875 L 9.308594 4.921875 L 7.902344 6.328125 L 1.40625 6.328125 L 1.40625 4.921875 L 2.109375 4.921875 C 2.238281 4.921875 2.34375 4.816406 2.34375 4.6875 C 2.34375 4.558594 2.238281 4.453125 2.109375 4.453125 L 1.40625 4.453125 L 1.40625 3.984375 L 14.59375 3.984375 L 14.59375 4.453125 Z M 15.53125 14.125 C 15.53125 14.511719 15.214844 14.828125 14.828125 14.828125 L 1.171875 14.828125 C 0.785156 14.828125 0.46875 14.511719 0.46875 14.125 L 0.46875 7.5 C 0.46875 7.113281 0.785156 6.796875 1.171875 6.796875 L 8 6.796875 C 8.0625 6.796875 8.121094 6.773438 8.164062 6.726562 L 9.765625 5.128906 C 9.898438 4.996094 10.074219 4.921875 10.261719 4.921875 L 14.828125 4.921875 C 15.214844 4.921875 15.53125 5.238281 15.53125 5.625 Z M 15.53125 14.125 "/>
                </g>
                </svg>`
            );

            /*
            Used to store a reference to the dialog when we have opened it
             */
            var _api = false;

            /*
            * Les variables
            */
            var fileUrl = '';
            var _fileURL = '';
            var _fileLink = '';
            var _fileAlt = '';
            var _fileTitle = '';
            var _icon = '';
            var _iconize = false;
            var _isImage = false;

            /*
            Define configuration for the iframe
             */
            var _urlDialogConfig = {
                title: 'Médiathèque',
                url: '/admin/media/iframe?type=0',
                buttons: [
                    {
                        type: 'custom',
                        name: 'custom',
                        primary: true,
                        text: 'Insérer'
                    },
                    {
                        type: 'cancel',
                        name: 'cancel',
                        text: 'Fermer'
                    }
                ],
                onAction: function (instance, trigger) {
                    if (_fileURL.length > 0) {
                        if (_isImage) {
                            fileUrl = `<img src="${_fileURL}" alt="${_fileAlt}" title="${_fileTitle}">`
                        } else {
                            let icon = '';
                            if (_iconize) {
                                icon = `<img src="/assets/images/icon-${_icon}">`
                            }
                            fileUrl = `<a href="${_fileURL}" title="${_fileTitle}">${icon}&nbsp;${_fileTitle}</a>`
                        }
                        editor.insertContent(fileUrl)
                    }
                    instance.close();
                },
                width: 800,
                height: 500,
                onMessage: (instance, data) => {
                    switch(data.mceAction)
                    {
                        case 'setOne':
                            _fileURL = data.data.url
                            _fileLink = data.data.link
                            _fileAlt = data.data.alt
                            _fileTitle = data.data.title
                            _isImage = data.data.isImage
                            _icon = data.data.icon
                            _iconize = data.data.iconize
                            break;
                        case 'replaceContent':
                            // run code for replacing the content
                            break;
                    }
                }
            };

            // Define the Toolbar button
            editor.ui.registry.addButton('mediatheque', {
                text: "Médiathèque",
                icon: 'media',
                onAction: () => {
                    _api = editor.windowManager.openUrl(_urlDialogConfig)
                }
            });

        });
    }());
})();