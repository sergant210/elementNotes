elementNotes.panel.Notes = function(config) {
	config = config || {};

	Ext.apply(config,{
		//url: elementNotes.config.connector_url,
		listeners: {
			render: {fn: function(a) {
				this.getElementNote();
			}, scope: this}
		},
		id: 'elementnotes-page',
		items: [{
			border: false,
			baseCls: 'panel-desc',
			style: {padding: '5px', margin: '10px 5px 0'},
			html: '<p>' + _('elementNote_panelDesc') + '</p>'
		},{
			border: false,
			style: {padding: '5px', overflow: 'hidden'},
			layout: 'anchor',
			items: [{
				xtype: 'modx-texteditor',
				cls: 'elementnotes-note',
				id: 'elementnotes-text',
				grow: true,
				growMin: '400',
				growMax: '500',
				width: '100%',
				enableKeyEvents: true,
				listeners: {
					keyup: {
						fn: function () {
							var button = Ext.getCmp('elementnotes-save-btn');
							if (this.value !== this.getValue()) {
								if (button.disabled) {
									button.setDisabled(false);
								}
							} else {
								if (!button.disabled) {
									button.setDisabled(true);
								}
							}
						}
					}
				}
			},{
				xtype: 'button',
				id: 'elementnotes-save-btn',
				text: _('save'),
				cls: 'primary-button',
				style: {marginTop: '10px'},
				disabled: true,
				/*keys: [{
				 key: MODx.config.keymap_save || 's'
				 ,ctrl: true
				 }],*/
				listeners: {
					click: {fn:function() {
						if (this.disabled) return false;
						var type = '';
						if (MODx.request.a.indexOf('snippet') > 0)
							type = 'snippet';
						else if (MODx.request.a.indexOf('chunk') > 0)
							type = 'chunk';
						else if (MODx.request.a.indexOf('plugin') > 0)
							type = 'plugin';
						else if (MODx.request.a.indexOf('template') > 0)
							type = 'template';
						var text = Ext.getCmp('elementnotes-text').getValue();

						this.setText(_('saving'));
						MODx.Ajax.request({
							url: elemNotes.config.connector_url,
							params: {
								action: 'mgr/note/save',
								id: MODx.request.id,
								type: type,
								text: text
							},
							listeners: {
								'success': {fn:function(r) {
									if (r.success) {
										this.setDisabled(true);
										this.setText(_('save'));
									}
								},scope:this}
							}
						});
					}}
				}
			}]
		}]
	});
	elementNotes.panel.Notes.superclass.constructor.call(this,config);
};
Ext.extend(elementNotes.panel.Notes,MODx.Panel, {

	getElementNote: function() {
		var type = '';
		if (MODx.request.a.indexOf('snippet') > 0)
			type = 'snippet';
		else if (MODx.request.a.indexOf('chunk') > 0)
			type = 'chunk';
		else if (MODx.request.a.indexOf('plugin') > 0)
			type = 'plugin';
		else if (MODx.request.a.indexOf('template') > 0)
			type = 'template';
		MODx.Ajax.request({
			url: elemNotes.config.connector_url,
			params: {
				action: 'mgr/note/get',
				id: MODx.request.id,
				type: type
			},
			listeners: {
				'success': {fn:function(r) {
					if (r.success) {
						Ext.getCmp('elementnotes-text').setValue(r.object.text);
					}
				},scope:this}
			}
		});
		//document.getElementById('minishop2-product-header-image').src = thumb;
	}

});
Ext.reg('elementnotes-page',elementNotes.panel.Notes);