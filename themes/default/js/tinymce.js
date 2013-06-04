(function() {
	tinymce.create('tinymce.plugins.Heading', {
		init : function(ed, url) {
                   
			ed.addButton('heading', {
				title : 'heading.button',
				image : url+'/../img/heading.png',
				onclick : function() {
                                    if (ed.selection.getContent() != ''){
                                        ed.execCommand('mceReplaceContent', false, '<h2>'+ed.selection.getContent()+'</h2>');
                                        ed.execCommand('mceCleanup', false);
                                    }
				}
			});
		},
		createControl : function(n, cm) {
			return null;
		}
	});
	tinymce.PluginManager.add('heading', tinymce.plugins.Heading);
})();