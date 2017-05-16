<div id='tvcontainer{$tv->id}'>
	<input type="hidden" name="tv{$tv->id}_local" id="tv{$tv->id}_local">
</div>

{literal}
<script type="text/javascript">
Ext.onReady(function(){
	MODx.load({
		xtype: 'notes-tv-grid'
		,renderTo: 'tvcontainer{/literal}{$tv->id}{literal}'
		,tv:{/literal}{$tv->id}{literal}
		,resource: {/literal}{$modx->resource->id}{literal}||0
		,auth:"{/literal}{$auth}{literal}"
		,id:"notes-tv-grid__{/literal}{$tv->id}{literal}"
		,localID:"tv{/literal}{$tv->id}{literal}_local"
	});
	
	panel = Ext.getCmp("modx-content");
	tabs = Ext.getCmp("modx-resource-tabs");
	grid = Ext.getCmp('notes-tv-grid__{/literal}{$tv->id}{literal}');
	autoresize = function(that,tab){this.getView().refresh();}
	tabs.on('tabchange',autoresize,grid);
	panel.on('bodyresize',autoresize,grid);
});
</script>
{/literal}
