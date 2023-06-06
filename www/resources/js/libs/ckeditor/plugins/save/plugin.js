CKEDITOR.plugins.add('save', {
	init: function(editor) {
		var pluginName = 'save';
		editor.ui.addButton('Save',
				{
					label: editor.lang.save.save,
					command: 'saveText',
					icon: CKEDITOR.plugins.getPath('save') + 'icons/save.png'
				});
		editor.addCommand('saveText', {
			exec: function(editor) {
				var $el = $(editor.element.$);
				$el.trigger('blur');
			}
		});
	}
});