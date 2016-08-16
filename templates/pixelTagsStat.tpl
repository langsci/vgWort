{**
 * @file plugins/generic/vgWort/templates/pixelTagsStat.tpl
 *
 * Author: Božana Bokan, Center for Digital Systems (CeDiS), Freie Universität Berlin
 * Last update: May 29, 2015
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Display VG Wort pixel tags statistics.
 *
 *}

<br />
{include file="../plugins/generic/vgWort/templates/formErrors.tpl"}
<br />

<p>Ordered pixel tags till now: {$orderedPixelTillToday}</p>

<p>Tracked pixel tags till now: {$startedPixelTillToday}</p>

<br />

{assign var=colspan value="5"}
{assign var=colspanPage value="3"}

<table id="pixelTagsStatistics" width="100%" class="pixelTagsStatistics">
	<thead>
		<tr>
			<th width="15%"> {translate key="plugins.generic.vgWort.month"} </th>
			<th width="20%"> {translate key="plugins.generic.vgWort.orderedPixel"} </th>
			<th width="20%"> {translate key="plugins.generic.vgWort.startedPixel"} </th>
			<th width="20%"> {translate key="plugins.generic.vgWort.minAccess"} </th>
			<th width="25%"> {translate key="plugins.generic.vgWort.minAccessNoMessage"} </th>
		</tr>
	</thead>
	<tbody>
	{foreach from=$qualityControlValues item=qualityControlValue}
		<tr>
			<td>{$qualityControlValue->year|escape}-{$qualityControlValue->month|escape}</td>
			<td>{$qualityControlValue->orderedPixel|escape}</td>
			<td>{$qualityControlValue->startedPixel|escape}</td>
			<td>{$qualityControlValue->minAccess|escape}</td>
			<td>{$qualityControlValue->minAccessNoMessage|escape}</td>
		</tr>
	{/foreach}
	</tbody>
</table>

