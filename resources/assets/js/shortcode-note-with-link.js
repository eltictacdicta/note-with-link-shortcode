(function() {
    tinymce.PluginManager.add('button-shortcode', function( editor, url ) {
        editor.addButton( 'button-shortcode', {
            text: ' Note with link',
            icon: 'mce-ico mce-i-pluscircle',
            onclick: function() {
                editor.windowManager.open( {
                    title: 'Add Note',
                    body: [
                        {
                            type: 'listbox',
                            label: 'Button Type',
                            name: 'type',
                            values: [
                                { text: 'Note', value: 'note' },
                                { text: 'Download Button', value: 'download' },
                                { text: 'Home Button', value: 'home' }
                            ],
                            value: 'note'
                        },
                        
                        {
                            type: 'textbox',
                            label: 'Text from before',
                            name: 'texto',
                            // tooltip: 'Some nice tooltip to use',
                            value: ' También te puede interesar:'
                        },
                        {
                            type: 'textbox',
                            label: 'Link (URL)',
                            name: 'url',
                            //tooltip: 'If linking to another page, please use a relative URL - eg. /about not http://google.com/about',
                            value: 'https://domain.com/about-us'
                        },
                        {
                            type: 'textbox',
                            label: 'Anchor',
                            name: 'anchor',
                            // tooltip: 'Some nice tooltip to use',
                            value: 'Learn More'
                        }
                    ],
                    onsubmit: function( e ) {
                        editor.insertContent( '[nota_personalizada url="' + e.data.url + '" texto="' + e.data.texto + '" anchor="' + e.data.anchor + '" type="' + e.data.type + '"]');
                    }
                });
            },
        });
        
    });
    

})();
