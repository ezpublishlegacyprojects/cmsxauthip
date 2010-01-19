{* Warnings *}
{def $can_admin = fetch( 'user', 'has_access_to', hash( 'module', 'authip', 
                                          'function', 'administrate' ) )}
{if $errorMessages}
    <div class="message-error">
        <h2>
            <span class="time">[{currentdate()|l10n( shortdatetime )}]</span>
        {'The address could not be stored.'|i18n( 'extension/cmsxauthip/edit' )}
        </h2>
        <p>{'The following information is either missing or invalid'|i18n( 'design/admin/class/edit' )}:</p>

        <ul>
            {foreach $errorMessages as $errorMessage}
                <li>{$errorMessage}</li>
            {/foreach}
        </ul>
		{if $address.used}
			{include uri="design:authip/addresses.tpl" addresses = $address.used}
		{/if}
    </div>
{elseif $address.is_saved}
	    <div class="message-feedback">
	        <h2>
	            <span class="time">[{currentdate()|l10n( shortdatetime )}]</span>
	        {'The address was successfully stored.'|i18n( 'extension/cmsxauthip/edit' )}
	        </h2>
		</div>
{/if}

<div class="context-block">

{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

<h1 class="context-title">{'Edit Address'|i18n( 'extension/cmsxauthip' )}</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div></div></div></div></div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">


<div class="context-attributes">
    <form id="AddressEdit" action="" method="post" enctype="multipart/form-data">
        <input type="hidden" name="AddressID" value="{$address.id}" />
        {* Address *}
        <div class="block">
{* $address|attribute(show, 3) *}
            <label>{'IP Address/Range'|i18n( 'extension/cmsxauthip' )}:</label> <input type="text" class="halfbox" name="Address" value="{$address.content}"/><br/>
        </div>
        {* Show if range *}
		{if and( $address.range_start, $address.range_end )}
		    <div class="block">
			<label>{'Range Address'|i18n( 'extension/cmsxauthip' )}:</label>
			{'Range start'|i18n( 'extension/cmsxauthip' )}: {$address.range_start} <br />
			{'Range end'|i18n( 'extension/cmsxauthip' )}: {$address.range_end}
			</div>
		{/if}
        {* Status of Address (active|inactive) *}
        <div class="block">
	    <label>{'Address enabled'|i18n( 'extension/cmsxauthip/edit' )}:</label>
	    <input type="checkbox" name="AddressStatus" {if $$address.status}checked="checked"{/if} title="{'Use this checkbox to set this address enabled.'|i18n( 'extension/cmsxauthip/edit' )|wash}" value="1" />
	    </div>
	
	    {* User info *}
        <div class="block">
	    <label>{'User'|i18n( 'extension/cmsxauthip' )}:</label>
	    {$user.name|wash}
		{if $can_admin}
	         <input type="hidden" name="AddressUserID" value="{$address.user_id}" />
	         <input type="submit" name="BrowseForUserButton" value="{'Choose a user'|i18n('extension/cmsxauthip/edit')}" class="button"/>
	    {/if}
	    </div>

</div>

{* DESIGN: Content END *}</div></div></div>

<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">
<div class="block">
<div class="button-left">
<input type="hidden" name="LastView" value="{$last_view}" />
<input type="submit" name="SaveAddressButton" value="{'Save address'|i18n('extension/cmsxauthip/edit')}" class="button"/>
<input type="button" name="CancelAddressrButton" value="{'Cancel'|i18n('design/admin/class/edit')}" class="button" onclick="javascript:window.location = {$last_view|ezurl( 'single', 'full' )}"/>
</div>
<div class="break"></div>
</div>
    </form>
{* DESIGN: Control bar END *}</div></div></div></div></div></div>
</div>

</div>