<div id="tv-input-properties-form{$tv}"></div>

{literal}
<script type="text/javascript">
// <![CDATA[
MODx.load({
	xtype: 'notes-ip-panel'
	,properties: {{/literal}{foreach from=$params key=k item=v name='p'}'{$k}': '{$v|escape:"javascript"}'{if NOT $smarty.foreach.p.last},{/if}{/foreach}{literal}}
	,renderTo: 'tv-input-properties-form{/literal}{$tv}{literal}'
	,tv:'{/literal}{$tv|default}{literal}'
	,oc: {'change':{fn:function(){Ext.getCmp('modx-panel-tv').markDirty();},scope:this}}
});
// ]]>
</script>
{/literal}
