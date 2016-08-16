{**
 * plugins/generic/vgWort/templates/assignPixelTag.tpl
 *
 * Author: Božana Bokan, Center for Digital Systems (CeDiS), Freie Universität Berlin
 * Last update: May 29, 2015
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Assign VG Wort pixel tag to the article
 *
 *}
<script type="text/javascript">
	$(function() {ldelim}
		// Attach the form handler.
		$('#assignPixelTagForm').pkpHandler(
			'$.pkp.controllers.form.AjaxFormHandler',
			{ldelim}
				trackFormChanges: true,
			{rdelim}
		);
	{rdelim});
</script>
<form class="pkp_form" id="assignPixelTagForm" action="{url router=$smarty.const.ROUTE_COMPONENT component="plugins.generic.vgWort.controllers.tab.vgWortEntry.VGWortEntryTabHandler" op="assignPixelTag"}" method="post">
	{include file="common/formErrors.tpl"}
	<input type="hidden" name="submissionId" id="submissionId" value="{$submissionId|escape}" />

{if !isset($pixelTag)}
	<input type="hidden" name="function" id="function" value="assign" />

	{if $errorCode}
		<span class="pkp_form_error">{translate key="form.errorsOccurred"}:</span>
		<ul class="pkp_form_error_list"><li>{translate key="plugins.generic.vgWort.assign.errorCode$errorCode"}</li></ul>
	{/if}

	{fbvFormArea id="vgWortAssignPixelTag" class="border"}
		{fbvFormSection description="plugins.generic.vgWort.assignDescription"}
		{/fbvFormSection}
		{fbvFormSection title="plugins.generic.vgWort.textType" for="vgWortTextType"}
			{fbvElement type="select" id="vgWortTextType" from=$typeOptions selected=$vgWortTextType translate=true size=$fbvStyles.size.SMALL}
		{/fbvFormSection}
		{fbvFormSection description="plugins.generic.vgWort.textType.description"}
		{/fbvFormSection}
	{/fbvFormArea}
	{fbvFormButtons submitText="plugins.generic.vgWort.assign"}
{else}
	<input type="hidden" name="function" id="function" value="update" />

	{fbvFormArea id="pixelTagDataArea" class="border"}
	<table id="assignedPixelTagDetails" width="100%" class="assignedPixelTagDetails">
		<tr>
			<td>{translate key="plugins.generic.vgWort.pixelTag.status"}</td>
			<td>{$pixelTag->getStatusString()}</td>
		</tr>
		<tr>
			<td>{translate key="plugins.generic.vgWort.textType"}</td>
			<td>{$pixelTag->getTextTypeString()}</td>
		</tr>
		<tr>
			<td>{translate key="plugins.generic.vgWort.pixelTag.privateCode"}</td>
			<td>{$pixelTag->getPrivateCode()}</td>
		</tr>
	</table>
	{/fbvFormArea}
	{fbvFormArea id="changePixelTagAssignmentFormArea" class="border"}
		{fbvFormSection list="true"}
			{if $pixelTag->getDateRemoved()}
				{assign var="description" value="plugins.generic.vgWort.reinsertDescription"}
				{fbvElement type="checkbox" label="plugins.generic.vgWort.reinsert" id="reinsertPixelTag"}
			{else}
				{assign var="description" value="plugins.generic.vgWort.removeDescription"}
				{fbvElement type="checkbox" label="plugins.generic.vgWort.remove" id="removePixelTag"}
			{/if}
		{/fbvFormSection}
		{fbvFormSection description=$description}{/fbvFormSection}

		{if $pixelTag->getStatus() != PT_STATUS_REGISTERED}
			{fbvFormSection label="plugins.generic.vgWort.changeTextType"}
				{fbvElement type="select" id="vgWortTextType" from=$typeOptions selected=$vgWortTextType translate=true disabled=$readOnly size=$fbvStyles.size.MEDIUM}
			{/fbvFormSection}
			{fbvFormSection description="plugins.generic.vgWort.textType.description"}{/fbvFormSection}
		{/if}
	{/fbvFormArea}
	{fbvFormButtons name="changePixelTagAssignment" id="changePixelTagAssignment" submitText="common.save"}
{/if}
</form>


