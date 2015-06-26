<?php

/**
 * @file plugins/generic/vgWort/controllers/grid/form/OrderPixelTagsForm.inc.php
 *
 * Author: Božana Bokan, Center for Digital Systems (CeDiS), Freie Universität Berlin
 * Last update: June 01, 2015
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class OrderPixelTagsForm
 * @ingroup plugins_generic_vgWort
 *
 * @brief Form to order new pixel tags.
 */

import('lib.pkp.classes.form.Form');

class OrderPixelTagsForm extends Form {

	/** @var int Context id */
	var $_contextId;

	/** @var Plugin (VG Wort) */
	var $_plugin;

	/**
	 * Constructor.
	 * @param $plugin Plugin (VG Wort).
	 * @param $contextId int Context id.
	 */
	function OrderPixelTagsForm($plugin, $contextId) {
		parent::Form($plugin->getTemplatePath() . 'controllers/grid/form/orderPixelTagsForm.tpl');
		$this->_contextId = $contextId;
		$this->_plugin = $plugin;

		// Validation checks for this form
		$this->addCheck(new FormValidatorRegExp($this, 'count', FORM_VALIDATOR_REQUIRED_VALUE, 'plugins.generic.vgWort.order.count', '/^([1-9][0-9]?|100)$/'));
		$this->addCheck(new FormValidatorPost($this));
	}

	//
	// Getters and Setters
	//
	/**
	 * Get the context id.
	 * @return int
	 */
	function getContextId() {
		return $this->_contextId;
	}

	/**
	 * Get the plugin.
	 * @return Plugin
	 */
	function getPlugin() {
		return $this->_plugin;
	}

	//
	// Implement template methods from Form.
	//
	/**
	 * @copydoc Form::readInputData()
	 */
	function readInputData() {
		$this->readUserVars(array('count'));
	}

	/**
	 * @copydoc Form::execute()
	 */
	function execute($request) {
		$count = (int) $this->getData('count');
		import('plugins.generic.vgWort.classes.VGWortEditorAction');
		$vgWortEditorAction = new VGWortEditorAction();
		// order
		$orderResult = $vgWortEditorAction->orderPixel($this->getContextId(), $count);
		$isError = !$orderResult[0];
		if ($isError) {
$file = 'debug.txt';
$current = file_get_contents($file);
$current .= print_r("--- ERROR ---", true);
file_put_contents($file, $current);
			//fatalError($orderResult[1]);
		} else {
			// insert ordered pixel tags in the db
			$vgWortEditorAction->insertOrderedPixel($this->getContextId(), $orderResult[1]);
		}
	}

}

?>
