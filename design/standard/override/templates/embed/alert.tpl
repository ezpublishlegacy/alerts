{* Alert - Embed View *}

{let myurl=''}

{if is_set($node.data_map.subitem_limit)}
   {def $alert_kids_limit = $node.data_map.subitem_limit.data_text}
{/if}

<div class="content-view-embed class-alert document" {if $myurl|count_chars} style='cursor: pointer' onmousedown="javascript: document.location.href='{$myurl}'"{/if}>
	<div class="attribute-image column">{attribute_view_gui attribute=$node.data_map.image image_class=small}</div>
	<div class="attribute-long column">{attribute_view_gui attribute=$node.data_map.description}</div>
</div>

{/let}

