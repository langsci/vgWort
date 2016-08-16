{**
 * plugins/generic/vgWort/templates/pixelTagsTabs.tpl
 *
 * Author: Božana Bokan, Center for Digital Systems (CeDiS), Freie Universität Berlin
 * Last update: May 28, 2015
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * VG Wort PixelTags Tabs
 *
 *}
{strip}
{assign var="pageTitle" value="plugins.generic.vgWort.editor.pixelTags"}
{include file="common/header.tpl"}
{/strip}
<script src="{$baseUrl}/{$pluginJavaScriptPath}/PixelTagsTabHandler.js"></script>
<script type="text/javascript">
// Attach the JS file tab handler.
$(function() {ldelim}
	$('#pixelTagsTabs').pkpHandler(
		'$.pkp.plugins.generic.vgwort.PixelTagsTabHandler',
		{ldelim}
			notScrollable: true,
			tabsUrl:'{url|escape:javascript router=$smarty.const.ROUTE_PAGE
				op='vgWort' escape=false}',
			emptyLastTab: true,
		{rdelim}
	);
{rdelim});
</script>
<div id="pixelTagsTabs">
	<ul>
		<li>
			<a id="pixelTagsAll"
				href="{url router=$smarty.const.ROUTE_PAGE op="pixelTags"}">{translate key="plugins.generic.vgWort.all"}</a>
		</li>
		<li>
			<a id="pixelTagsAvailable"
				href="{url router=$smarty.const.ROUTE_PAGE op="pixelTags"
				pixelTagStatus=$smarty.const.PT_STATUS_AVAILABLE}">{translate key="plugins.generic.vgWort.available"}</a>
		</li>
		<li>
			<a id="pixelTagsUnregistered"
				href="{url router=$smarty.const.ROUTE_PAGE op="pixelTags"
				pixelTagStatus=$smarty.const.PT_STATUS_UNREGISTERED}">{translate key="plugins.generic.vgWort.unregistered"}</a>
		</li>
		<li>
			<a id="pixelTagsRegistered"
				href="{url router=$smarty.const.ROUTE_PAGE op="pixelTags"
				pixelTagStatus=$smarty.const.PT_STATUS_REGISTERED}">{translate key="plugins.generic.vgWort.registered"}</a>
		</li>
		<li>
			<a id="pixelTagsStatistics"
				href="{url router=$smarty.const.ROUTE_PAGE op="pixelStatistics"}">{translate key="plugins.generic.vgWort.editor.statistics"}</a>
		</li>
	</ul>
</div>


{include file="common/footer.tpl"}

