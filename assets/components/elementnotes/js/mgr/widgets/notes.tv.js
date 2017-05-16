elementNotes.grid.notes = function(config) {
	config = config || {};

	this.sm = this.setSelectionModel(this.rememberRow,this.forgotRow);
	var grid = this;
	if(config.resource===0)
	{
		this.localmode=true;
		this.inputEl = Ext.get(config.localID);
	}
	
	Ext.applyIf(config,{
		url: elementNotes.config.connectorUrl
		,baseParams: {
			action: 'mgr/tv/notes/getlist'
			,mode: 'grid'
			,tv : config.tv
			,resource : config.resource
			,'HTTP_MODAUTH': config.auth
		}
		,save_action: 'mgr/tv/notes/save'
		,saveParams:{tv : config.tv,resource : config.resource}
		,autosave: true
		,preventSaveRefresh: false
		,fields: ['id','type','text','createdon']
		,paging: false
		,remoteSort: true
		,autoHeight: true
		,autoWidth: true
		,collapsible: true
		,resizable: true
		,viewConfig: {
			forceFit: true
			,autoFill: true
			,getRowClass: function(record, index){}
		}
		,enableDragDrop: false
		,sm: this.sm
		,columns: [this.sm,{
			header: _('id')
			,dataIndex: 'id'
			,sortable: false
			,hidden: true
		},{
			header: _('notes.grid.columns.createdon.title')
			,dataIndex: 'createdon'
			,sortable: false
			,resizable: false
			,width: 150
			,fixed:true
			,renderer: {fn:function(value, metaData, record, rowIndex, colIndex, store)
			{
				if(!value||value==='')return '';
				if(!isNaN(parseInt(value)))value = new Date(value*1000);
				else{value = Date.parseDate(value, 'Y-m-d H:i:s');}
				if(!value)return '';
				return String.format('{0}', value.format(MODx.config.manager_date_format+' '+MODx.config.manager_time_format));
			},scope:this}
		},{
			header: _('notes.grid.columns.text.title')
			,dataIndex: 'text'
			,sortable: false
			,resizable: true
			,minWidth: 200
			,autoSizeColumn: true
			,renderer: {fn:function(value, metaData, record, rowIndex, colIndex, store)
			{
				return value.replace(/\n/g,'<br>');
			},scope:this}
			,editor: new Ext.grid.GridEditor(new Ext.form.TextArea({}),{grid:grid
				,listeners: {
					complete: {fn:function (editor, value, startValue){grid.local__addSaveParams();}}
				}
			})
		}]
		,tbar: [{
			xtype: 'textarea'
			,width: 400
			,name: 'note_text'
			,key: 'note_text'
		},' ',{
			text: _('notes.grid.add.text')
			,height: 70
			,style:{lineHeight:'45px'}
			,handler: this.addRecord
			,scope: this
		}]
		,listeners:{'render':{fn:this.registerGridDropTarget,scope:this}}
	});
	config.baseParams.localData = this.local__getValue();
	elementNotes.grid.notes.superclass.constructor.call(this,config);
	
	this.getView().on('refresh', this.refreshSelection, this);
	if(this.topToolbar){this.topToolbar.container.setSize("auto");this.topToolbar.setSize("auto");}
	if(this.bottomToolbar){this.bottomToolbar.container.setSize("auto");this.bottomToolbar.setSize("auto");}
	if(this.footerToolbar){this.footerToolbar.container.setSize("auto");this.footerToolbar.setSize("auto");}
	if(this.localmode)
	{
		this.getStore().on('beforeload', this.local__beforeLoad, this);
		this.config.save_callback=this.local__saveFromResponse;
	}
};

Ext.extend(elementNotes.grid.notes,MODx.grid.Grid,{
	local__getValue:function(decode)
	{
		if(!this.localmode)return '';
		return decode?Ext.util.JSON.decode(this.inputEl.dom.value||'[]'):this.inputEl.dom.value||'[]';
	}
	,local__setValue:function(v)
	{
		if(!this.localmode)return;
		if(typeof(v)==='object')v=Ext.util.JSON.encode(v);
		this.inputEl.dom.value = v;
	}
	,local__addSaveParams:function()
	{
		if(!this.localmode)return;
		this.config.saveParams.localData = this.local__getValue();
	}
	,local__beforeLoad:function(that,options)
	{
		if(this.localmode)options.params.localData = this.local__getValue();
		return true;
	}
	,local__saveFromResponse:function(response)
	{
		if(this.localmode&&response.object)this.local__setValue(response.object);
	}
	/////////////////////////////////////////////////////////////////
	////////////////////		SELCTION
	,selectedRecords: []
	,setSelectionModel: function(rowselect,rowdeselect)
	{
		return new Ext.grid.CheckboxSelectionModel({
			listeners: {
				rowselect:{fn:function(sm,rowIndex,record){rowselect(record,this);},scope:this},
				rowdeselect:{fn:function(sm,rowIndex,record){rowdeselect(record,this);},scope: this}
			}
		});
	}
	,rememberRow: function(record,that){if(that.selectedRecords.indexOf(record.id)==-1){that.selectedRecords.push(record.id);}}
	,forgotRow: function(record,that){that.selectedRecords.remove(record.id);}
	,refreshSelection: function()
	{
		var rowsToSelect = [];
		var rowsToDeselect = [];
		Ext.each(this.selectedRecords, function(item,index){
			idx = this.store.indexOfId(item);
			(idx===-1)?rowsToDeselect.push(item):rowsToSelect.push(idx);
		},this);
		this.selectedRecords = this.selectedRecords.filter(function(item){return rowsToDeselect.indexOf(item) === -1;});
		this.getSelectionModel().selectRows(rowsToSelect);
	}
	,getSelectedAsList: function(){return this.selectedRecords.join();}
	
	////////////////////////////////////////////////////////////////
	////////////////////		DRAG
	,registerGridDropTarget: function() {
		
	}
	
	////////////////////////////////////////////////////////////////
	////////////////////		MENU
	,getMenu: function() {
		var m = [];
		
		m.push({
			text: _('notes.grid.menu.remove.title')
			,handler: this.removeRecord
		});
		
		if(this.selectedRecords.length > 1)
		{
			m.push('-',{
				text: _('notes.grid.menu.removeselected.title')
				,handler: this.removeSelectedRecords
			});
		}

		return m;
	}
	////////////////////////////////////////////////////////////////
	////////////////////		ACTIONS
	,addRecord:function()
	{
		field = this.getTopToolbar().items.find(function(el){if(el.name=='note_text');return true;});

		MODx.Ajax.request({
			url: this.config.url
			,params:
			{
				action: this.config.save_action
				,resource: this.config.resource
				,tv: this.config.tv
				,text: field.getValue()
				,localData: this.local__getValue()
			} 
			,listeners:{
				success:{fn: function(response){this.local__saveFromResponse(response);this.refresh();}, scope: this}
				,failure: {fn: function (r){MODx.msg.alert(_('error'), r.message);}, scope: this}
			}
		});
	}
	,removeRecord:function(btn,e)
	{
		if (!this.menu.record) return false;
		this.removeRecordById([this.menu.record.id]);
	}
	,removeSelectedRecords:function(btn,e)
	{
		this.removeRecordById(this.selectedRecords);
	}
	,removeRecordById:function(ids)
	{
		MODx.msg.confirm({
			title: _('notes.grid.msg.remove.title')
			,text: _('notes.grid.msg.remove.text')
			,url: this.config.url
			,params: {
				action: 'mgr/tv/notes/remove'
				,resource: this.config.resource
				,tv: this.config.tv
				,'ids[]': ids
				,localData:this.local__getValue()
			}
			,listeners: {
				'success': {fn:function(r) {this.local__saveFromResponse(r);this.refresh();},scope:this}
			}
		});
	}
});
Ext.reg('notes-tv-grid',elementNotes.grid.notes);
