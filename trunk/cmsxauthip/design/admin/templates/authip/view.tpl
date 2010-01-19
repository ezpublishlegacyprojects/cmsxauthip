{*?template charset=utf-8?*}
{def $can_admin = true()
	 $item_type = ezpreference( 'authip_limit' )
	 $limit = min( $item_type, 3 )|choose( 10, 10, 25, 50 )}
<form name="ipslist" method="post" action={concat( '/authip/view' )|ezurl}>

<div class="context-block">

{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

<h1 class="context-title">{'Addresses from'|i18n( 'extension/cmsxauthip' )}: {$current_user.name}</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div></div></div></div></div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

<div class="context-toolbar">
<div class="block">
<div class="left">
<p>
    {switch match=$limit}
    {case match=25}
        <a href={'/user/preferences/set/authip_limit/1/'|ezurl} title="{'Show 10 items per page.'|i18n( 'design/admin/node/view/full' )}">10</a>
        <span class="current">25</span>
        <a href={'/user/preferences/set/authip_limit/3/'|ezurl} title="{'Show 50 items per page.'|i18n( 'design/admin/node/view/full' )}">50</a>
    {/case}

    {case match=50}
        <a href={'/user/preferences/set/authip_limit/1/'|ezurl} title="{'Show 10 items per page.'|i18n( 'design/admin/node/view/full' )}">10</a>
        <a href={'/user/preferences/set/authip_limit/2/'|ezurl} title="{'Show 25 items per page.'|i18n( 'design/admin/node/view/full' )}">25</a>
        <span class="current">50</span>
    {/case}

    {case}
        <span class="current">10</span>
        <a href={'/user/preferences/set/authip_limit/2/'|ezurl} title="{'Show 25 items per page.'|i18n( 'design/admin/node/view/full' )}">25</a>
        <a href={'/user/preferences/set/authip_limit/3/'|ezurl} title="{'Show 50 items per page.'|i18n( 'design/admin/node/view/full' )}">50</a>
    {/case}

    {/switch}
</p>
</div>
<div class="break"></div>

</div>
</div>

	{include uri="design:authip/addresses.tpl" addresses = $addresses}

<div class="context-toolbar">
{include name=navigator
         uri='design:navigator/google.tpl'
         page_uri='/authip/view'
         item_count=$addresses_count
         view_parameters=$view_parameters
         item_limit=$limit}
</div>

{* DESIGN: Content END *}</div></div></div>
<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">

<div class="block">
<div class="button-left">
    <input class="button" type="submit" name="NewIpButton" value="{'New Address'|i18n( 'extension/cmsxauthip' )}" title="{'New IP Address/Range.'|i18n( 'extension/cmsxauthip' )}" />
</div>
<div class="button-right">
     <input class="button" type="submit" name="ActivateIpButton" value="{'Enable/disable selected'|i18n( 'extension/cmsxauthip' )}" title="{'Enable/disable selected'|i18n( 'extension/cmsxauthip' )}" />
     <input class="button" type="submit" name="DeleteIpButton" value="{'Remove selected'|i18n( 'extension/cmsxauthip' )}" title="{'Remove selected'|i18n( 'extension/cmsxauthip' )}" />
</div>
<div class="break"></div>

</div>

{* DESIGN: Control bar END *}</div></div></div></div></div></div>
</div>

</form>
