{* Main Alerts Include *}
{def $alerts = $current_node_id|alert_list()}
<div id="alerts">
	<span class="close"></span>
	{foreach $alerts as $alert}
		{node_view_gui view='embed' content_node=$alert}
	{/foreach}
	<input type="hidden" name="AlertID" value="node-id-{$alerts[0].node_id}" />
</div>
