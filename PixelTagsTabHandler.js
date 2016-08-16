/**
 * @defgroup plugins_generic_vgwort_js
 */
/**
 * @file plugins/generic/vgWort/PixelTagsTabHandler.js
 *
 * Author: Božana Bokan, Center for Digital Systems (CeDiS), Freie Universität Berlin
 * Last update: May 27, 2015
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class PixelTagsTabHandler
 * @ingroup plugins_generic_vgwort
 *
 * @brief A subclass of TabHandler for handling the pixel tags tabs.
 * It adds a listener for grid refreshes, so the tab interface can be reloaded.
 */
(function($) {

	/** @type {Object} */
	$.pkp.plugins.generic.vgwort =
		$.pkp.plugins.generic.vgwort || { };



	/**
	 * @constructor
	 *
	 * @extends $.pkp.controllers.TabHandler
	 *
	 * @param {jQueryObject} $tabs A wrapped HTML element that
	 *  represents the tabbed interface.
	 * @param {Object} options Handler options.
	 */
	$.pkp.plugins.generic.vgwort.PixelTagsTabHandler =
			function($tabs, options) {
	
		this.parent($tabs, options);

		this.tabsUrl_ = options.tabsUrl;
		this.bind('refreshTabs', this.refreshTabsHandler_);
	};
	$.pkp.classes.Helper.inherits(
			$.pkp.plugins.generic.vgwort.PixelTagsTabHandler,
			$.pkp.controllers.TabHandler);


	//
	// Private properties
	//
	/**
	 * The URL for retrieving tabs.
	 * @private
	 * @type {string?}
	 */
	$.pkp.plugins.generic.vgwort.PixelTagsTabHandler.prototype.
			tabsUrl_ = null;


	//
	// Private methods
	//
	/**
	 * Tab refresh handler.
	 *
	 * @private
	 *
	 * @param {HTMLElement} sourceElement The parent DIV element
	 *  which contains the tabs.
	 * @param {Event} event The triggered event (refreshTabs).
	 */
	$.pkp.plugins.generic.vgwort.PixelTagsTabHandler.prototype.
			refreshTabsHandler_ = function(sourceElement, event) {

		if (this.tabsUrl_) {
			$.get(this.tabsUrl_, 
					this.callbackWrapper(this.updateTabsHandler_), 'json');
		}
	};


	/**
	 * A callback to update the tabs on the interface.
	 *
	 * @private
	 *
	 * @param {Object} ajaxContext The AJAX request context.
	 * @param {Object} data A parsed JSON response object.
	 */
	$.pkp.plugins.generic.vgwort.PixelTagsTabHandler.prototype.
			updateTabsHandler_ = function(ajaxContext, data) {

		this.trigger('gridRefreshRequested');

		var jsonData = this.handleJson(data),
				$tabs = this.getHtmlElement();

		if (jsonData !== false) {
			// Replace the grid content
			$tabs.replaceWith(jsonData.content);
		}
	};


/** @param {jQuery} $ jQuery closure. */
}(jQuery));
