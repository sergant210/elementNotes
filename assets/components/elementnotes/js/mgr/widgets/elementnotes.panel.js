elementNotes.panel.Notes = function(config) {
	config = config || {};

	Ext.apply(config,{
		listeners:{render: {fn:function(a){this.getElementNote();},scope:this}},
		id:'elementnotes-panel',
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
				xtype: Ext.ComponentMgr.types['modx-texteditor'] ? 'modx-texteditor' : 'textarea',
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
				listeners: {
					click: {fn:function(btn) {
						if(btn.disabled)return false;
						btn.setText(_('saving'));
						this.saveElementNote();
					},scope:this}
				}
			}]
		}]
	});
	elementNotes.panel.Notes.superclass.constructor.call(this,config);
	this.on('afterSave',function(){
		btn = Ext.getCmp('elementnotes-save-btn');
		btn.setDisabled(true);
		btn.setText(_('save'));
	},this);
};
Ext.extend(elementNotes.panel.Notes,MODx.Panel, {
	getElementNote: function() {
		MODx.Ajax.request({
			url: elementNotes.config.connectorUrl,
			params: {
				action: 'mgr/note/get',
				id: this.note.id,
				type: this.note.type
			},
			listeners: {
				'success': {fn:function(r) {
					if (r.success) {
						Ext.getCmp('elementnotes-text').setValue(r.object.text);
					}
				},scope:this}
			}
		});
	}
	,saveElementNote:function() {
		MODx.Ajax.request({
			url: elementNotes.config.connectorUrl,
			params: {
				action: 'mgr/note/save',
				id: this.note.id,
				type: this.note.type,
				text: Ext.getCmp('elementnotes-text').getValue()
			},
			listeners: {
				'success': {fn:function(r) {
					if(r.success){this.fireEvent('afterSave',{response:r});}
				},scope:this}
			}
		});
	}
});
Ext.reg('elementnotes-panel',elementNotes.panel.Notes);
