{**
 * plugins/generic/vgWort/templates/pixelTagsTabs.tpl
 *
 * Author: Božana Bokan, Center for Digital Systems (CeDiS), Freie Universität Berlin
 * Last update: May 28, 2015
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * VG Wort pixel tags grid and related actions.
 *}
{url|assign:pixelTagsGridUrl router=$smarty.const.ROUTE_COMPONENT component="plugins.generic.vgWort.controllers.grid.PixelTagGridHandler" op="fetchGrid" pixelTagStatus=$pixelTagStatus escape=false}
{load_url_in_div id="pixelTagsGridContainer" url=$pixelTagsGridUrl}
