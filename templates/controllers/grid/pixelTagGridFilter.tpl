{**
 * plugins/generic/vgWort/templates/controllers/grid/pixelTagGridFilter.tpl
 *
 * Author: Božana Bokan, Center for Digital Systems (CeDiS), Freie Universität Berlin
 * Last update: May 28, 2015
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Filter template for pixel tag grid search filter.
 *}
<script type="text/javascript">
	// Attach the form handler to the form.
	$('#pixelTagSearchForm').pkpHandler('$.pkp.controllers.form.ClientFormHandler',
		{ldelim}
			trackFormChanges: false
		{rdelim}
	);
</script>
<form class="pkp_form" id="pixelTagSearchForm" action="{url router=$smarty.const.ROUTE_COMPONENT component="plugins.generic.vgWort.controllers.grid.PixelTagGridHandler" op="fetchGrid"}" method="post">
	--{$filterData.pixelTagStatus}--
	<input type="hidden" name="pixelTagStatus" value="{$filterData.pixelTagStatus}" />
	{fbvFormArea id="pixelTagSearchFormArea"}
		{fbvFormSection title="common.search" required="false" for="search"}
			{fbvElement type="text" name="search" id="search" value=$filterSelectionData.search size=$fbvStyles.size.LARGE inline="true"}
			{fbvElement type="select" name="searchField" id="searchField" from=$filterData.fieldOptions selected=$filterSelectionData.searchField size=$fbvStyles.size.SMALL translate=false inline="true"}
		{/fbvFormSection}
		{fbvFormButtons hideCancel=true submitText="common.search"}
	{/fbvFormArea}
</form>
<div class="pkp_helpers_clear">&nbsp;</div>

