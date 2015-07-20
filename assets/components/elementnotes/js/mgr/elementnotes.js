var elementNotes = function(config) {
	config = config || {};
	elementNotes.superclass.constructor.call(this,config);
};

Ext.extend(elementNotes,Ext.Component,{
	page:{},window:{},grid:{},tree:{},panel:{},combo:{},config:{},view:{},keymap:{}, plugin:{}
});

Ext.reg('elementnotes', elementNotes);
elementNotes = new elementNotes();