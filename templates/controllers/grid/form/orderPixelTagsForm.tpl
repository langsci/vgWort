{**
 * plugins/generic/vgWort/templates/controllers/grid/form/orderPixelTagsForm.tpl
 *
 * Author: Božana Bokan, Center for Digital Systems (CeDiS), Freie Universität Berlin
 * Last update: February 01, 2015
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Order pixel tags form
 *
 *}

<script>
	$(function() {ldelim}
		// Attach the form handler.
		$('#vgWortOrderPixelTagsForm').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
	{rdelim});
</script>
<form class="pkp_form" id="vgWortOrderPixelTagsForm" method="post" action="{url router=$smarty.const.ROUTE_COMPONENT component="plugins.generic.vgWort.controllers.grid.PixelTagGridHandler" op="updateOrderPixelTags"}">
	{include file="common/formErrors.tpl"}
	{fbvFormArea id="vgWortNotification" title="plugins.generic.vgWort.editor.order" class="border"}
		{fbvFormSection description="plugins.generic.vgWort.editor.pixelTagCount"}
		{/fbvFormSection}
		{fbvFormSection for="count" size=$fbvStyles.size.SMALL required="true"}
			{fbvElement type="text" name="count" id="count" value=""}
		{/fbvFormSection}
	{/fbvFormArea}
	{fbvFormButtons submitText="plugins.generic.vgWort.editor.order"}
</form>
<p><span class="formRequired">{translate key="common.requiredField"}</span></p>
