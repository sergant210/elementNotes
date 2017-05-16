var elementNotes = function(config) {
	config = config || {};
	elementNotes.superclass.constructor.call(this,config);
};

Ext.extend(elementNotes,Ext.Component,{
	page:{},window:{},grid:{},tree:{},panel:{},combo:{},config:{},view:{},keymap:{}, plugin:{}
});

Ext.reg('elementnotes', elementNotes);
elementNotes = new elementNotes();

elementNotes.panel.notesIP = function(config) {
	config = config || {};
	Ext.applyIf(config,{
		layout: 'form'
		,cls: 'form-with-labels'
		,autoHeight: true
		,border: false
		,labelAlign: 'top'
		,labelSeparator: ''
		,items:this.getItems(config)
	});
	elementNotes.panel.notesIP.superclass.constructor.call(this,config);
};
Ext.extend(elementNotes.panel.notesIP,MODx.Panel,{
	
	getItems:function(config)
	{
		var items = [];
		items.push({
			xtype: 'combo-boolean'
			,fieldLabel: _('required')
			,description: MODx.expandHelp ? '' : _('required_desc')
			,name: 'inopt_allowBlank'
			,hiddenName: 'inopt_allowBlank'
			,id: 'inopt_allowBlank'+config.tv
			,value: config.properties['allowBlank'] == 0 || config.properties['allowBlank'] == 'false' ? false : true
			,width: 200
			,listeners: config.oc
		},{
			xtype: MODx.expandHelp ? 'label' : 'hidden'
			,forId: 'inopt_allowBlank'+config.tv
			,html: _('required_desc')
			,cls: 'desc-under'
		});
		return items;
	}
	
});
Ext.reg('notes-ip-panel',elementNotes.panel.notesIP);
