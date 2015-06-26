{**
 * plugins/generic/vgWort/templates/vgWortCardNoEdit.tpl
 *
 * Author: Božana Bokan, Center for Digital Systems (CeDiS), Freie Universität Berlin
 * Last update: May 29, 2015
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Edit VG Wort card number for an author (in the author form)
 *
 *}
<!-- VG Wort -->
{fbvFormSection title="plugins.generic.vgWort.cardNo"}
	{fbvElement type="text" label="plugins.generic.vgWort.cardNo.description" id="vgWortCardNo" value=$vgWortCardNo maxlength="40" inline=true size=$fbvStyles.size.SMALL}
{/fbvFormSection}
<!-- /VG Wort -->

