{*?template charset=utf-8?*}
{def $last_user = false}
{if is_set( $can_admin )|not}
	{def $can_admin = false()}
{/if}

{if $addresses|count}

<table class="list" cellspacing="0">
<tr>
    {if $can_admin}
    <th class="tight"><img src={'toggle-button-16x16.gif'|ezimage} alt="{'Invert selection.'|i18n( 'extension/cmsxauthip' )}" title="{'Invert selection.'|i18n( 'extension/cmsxauthip' )}" onclick="ezjs_toggleCheckboxes( document.ipslist, 'IpsIDArray[]' ); return false;" /></th>
	{/if}
	<th class="tight">{'ID'|i18n( 'extension/cmsxauthip' )}</th>
	<th class="wide">{'IP Address/Range'|i18n( 'extension/cmsxauthip' )}</th>
	<th class="wide">{'Type'|i18n( 'extension/cmsxauthip' )}</th>
	<th class="wide">{'User'|i18n( 'extension/cmsxauthip' )}</th>
	<th class="wide">{'Range start'|i18n( 'extension/cmsxauthip' )}</th>
	<th class="wide">{'Range end'|i18n( 'extension/cmsxauthip' )}</th>	
	<th class="tight">{'Status'|i18n( 'extension/cmsxauthip' )}</th>
	{if $can_admin}<th class="tight"></th>{/if}
</tr>

{foreach $addresses as $item sequence array( 'bgdark', 'bglight' ) as $style}

<tr class="{$style}">
    {if $can_admin}
    <td><input type="checkbox" name="IPsIDArray[]" value="{$item.id}" title="{'Select address.'|i18n( 'extension/cmsxauthip' )}" /></td>
	{/if}
	<td>{$item.id}</td>
	<td>{$item.address|wash}</td>
	<td align="right">
	{if eq( $item.type, 1 )}
	    {'IP Address'|i18n( 'extension/cmsxauthip' )}
	{else}
	    {'Range Address'|i18n( 'extension/cmsxauthip' )}
	{/if}
	</td>
    <td>
	{if ne( $item.user_id, $last_user.id )}
	    {set $last_user = fetch( 'content', 'object', hash( 'object_id', $item.user_id ) )}
	{/if}
	{$last_user.name|wash}
	</td>
    <td>
	{if ne( $item.type, 1 )}
	    {$item.range_start|long2ip}
	{/if}
	</td>
    <td>
	{if ne( $item.type, 1 )}
	    {$item.range_end|long2ip}
	{/if}
	</td>		
    <td>
	{if eq( $item.status, 1 )}
	    {'Enabled'|i18n( 'extension/cmsxauthip' )}
	{else}
	    {'Disabled'|i18n( 'extension/cmsxauthip' )}
	{/if}
	</td>
	{if $can_admin}
    <td>
	<a href={concat( 'authip/edit/(id)/', $item.id )|ezurl}><img src={'edit.gif'|ezimage} alt="" title="{'Edit address'|i18n( 'extension/cmsxauthip' )}" /></a>
	</td>
	{/if}	
</tr>
{/foreach}
</table>

{else}

<div class="block">
<p>{'No addresses found.'|i18n( 'extension/cmsxauthip' )}</p>
</div>

{/if}
