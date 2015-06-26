{**
 * plugins/generic/vgWort/templates/controllers/grid/form/insertPixelTagForm.tpl
 *
 * Author: Božana Bokan, Center for Digital Systems (CeDiS), Freie Universität Berlin
 * Last update: June 01, 2015
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Insert pixel tag form
 *
 *}

<script>
	$(function() {ldelim}
		// Attach the form handler.
		$('#vgWortInsertPixelTagForm').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
	{rdelim});
</script>
--{$pixelTagStatus}--
<form class="pkp_form" id="vgWortInsertPixelTagForm" method="post" action="{url router=$smarty.const.ROUTE_COMPONENT component="plugins.generic.vgWort.controllers.grid.PixelTagGridHandler" op="updateInsertPixelTag" pixelTagStatus=$pixelTagStatus}">
	{include file="common/formErrors.tpl"}
	<input type="hidden" name="pixelTagStatus" id="pixelTagStatus" value="{$pixelTagStatus|escape}" />
	{fbvFormArea id="vgWortPixelTag" title="plugins.generic.vgWort.editor.insertPixelTag" class="border"}
		{fbvFormSection title="plugins.generic.vgWort.pixelTag.privateCode" for="privateCode" inline=true size=$fbvStyles.size.MEDIUM required="true"}
			{fbvElement type="text" name="privateCode" id="privateCode" value=""}
		{/fbvFormSection}
		{fbvFormSection title="plugins.generic.vgWort.pixelTag.publicCode" for="publicCode" inline=true size=$fbvStyles.size.MEDIUM required="true"}
			{fbvElement type="text" name="publicCode" id="publicCode" value=""}
		{/fbvFormSection}
		{fbvFormSection title="plugins.generic.vgWort.pixelTag.domain" for="domain" inline=true size=$fbvStyles.size.MEDIUM required="true"}
			{fbvElement type="text" name="domain" id="domain" value=""}
		{/fbvFormSection}
		<script>
			$('input[id^="dateOrdered"]').datepicker({ldelim} dateFormat: 'yy-mm-dd' {rdelim});
		</script>
		{fbvFormSection title="plugins.generic.vgWort.pixelTag.dateOrdered" for="dateOrdered" inline=true size=$fbvStyles.size.MEDIUM required="true"}
			{fbvElement type="text" id="dateOrdered" value=$dateOrdered|date_format:"%y-%m-%d"}
		{/fbvFormSection}
		{fbvFormSection title="plugins.generic.vgWort.pixelTag.submissionId" for="submissionId" inline=true size=$fbvStyles.size.MEDIUM required="true"}
			{fbvElement type="text" name="submissionId" id="submissionId" value=""}
		{/fbvFormSection}
		{fbvFormSection title="plugins.generic.vgWort.textType" for="vgWortTextType" inline=true size=$fbvStyles.size.MEDIUM required="true"}
			{fbvElement type="select" id="vgWortTextType" from=$typeOptions selected=$vgWortTextType translate=true size=$fbvStyles.size.MEDIUM}
		{/fbvFormSection}
		{fbvFormSection description="plugins.generic.vgWort.textType.description"}
		{/fbvFormSection}
		<script>
			$('input[id^="dateAssigned"]').datepicker({ldelim} dateFormat: 'yy-mm-dd' {rdelim});
		</script>
		{fbvFormSection title="plugins.generic.vgWort.pixelTag.dateAssigned" for="dateAssigned" inline=true size=$fbvStyles.size.MEDIUM required="true"}
			{fbvElement type="text" id="dateAssigned" value=$dateAssigned|date_format:"%y-%m-%d"}
		{/fbvFormSection}
		{if $pixelTagStatus==$smarty.const.PT_STATUS_REGISTERED}
			<script>
				$('input[id^="dateRegistered"]').datepicker({ldelim} dateFormat: 'yy-mm-dd' {rdelim});
			</script>
			{fbvFormSection title="plugins.generic.vgWort.pixelTag.dateRegistered" for="dateRegistered" inline=true size=$fbvStyles.size.MEDIUM required="true"}
				{fbvElement type="text" id="dateRegistered" value=$dateRegistered|date_format:"%y-%m-%d"}
			{/fbvFormSection}
		{/if}
	{/fbvFormArea}
	{fbvFormButtons submitText="common.save"}
</form>
<p><span class="formRequired">{translate key="common.requiredField"}</span></p>
