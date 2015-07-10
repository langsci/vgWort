<?php

/**
 * @file plugins/generic/vgWort/VGWortSettingsForm.inc.php
 *
 * Author: Božana Bokan, Center for Digital Systems (CeDiS), Freie Universität Berlin
 * Last update: February 01, 2015
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class VGWortSettingsForm
 * @ingroup plugins_generic_vgwort
 *
 * @brief Form for journal managers to setup VG Wort plugin
 */


import('lib.pkp.classes.form.Form');

class VGWortSettingsForm extends Form {

	/** @var $contextId int */
	var $contextId;

	/** @var $plugin object */
	var $plugin;

	/**
	 * Constructor
	 * @param $plugin object
	 * @param $contextId int
	 */
	function VGWortSettingsForm(&$plugin, $contextId) {
		$this->contextId = $contextId;
		$this->plugin =& $plugin;

		parent::Form($plugin->getTemplatePath() . 'settingsForm.tpl');

		$this->addCheck(new FormValidator($this, 'vgWortUserId', FORM_VALIDATOR_REQUIRED_VALUE, 'plugins.generic.vgWort.manager.settings.vgWortUserIdRequired'));
		$this->addCheck(new FormValidator($this, 'vgWortUserPassword', FORM_VALIDATOR_REQUIRED_VALUE, 'plugins.generic.vgWort.manager.settings.vgWortUserPasswordRequired'));
		$this->addCheck(new FormValidator($this, 'vgWortEditors', FORM_VALIDATOR_REQUIRED_VALUE, 'plugins.generic.vgWort.manager.settings.vgWortEditorsRequired'));
		$this->addCheck(new FormValidatorRegExp($this, 'vgWortPixelTagMin', FORM_VALIDATOR_REQUIRED_VALUE, 'plugins.generic.vgWort.manager.settings.vgWortPixelTagMinRequired', '/^([1-9]|10)$/'));
		$this->addCheck(new FormValidatorPost($this));

		$this->setData('pluginName', $plugin->getName());
	}

	/**
	 * Display the form.
	 */
	function fetch($request) {
		$contextId = $this->contextId;
		$plugin =& $this->plugin;

		$roleDao =& DAORegistry::getDAO('RoleDAO');
		$editors = array();
		$users =& $roleDao->getUsersByRoleId(ROLE_ID_MANAGER, $contextId);
		foreach ($users->toArray() as $user) {
			$editors[$user->getId()] = $user->getFullName();
		}

		$templateMgr = TemplateManager::getManager($request);
		$templateMgr->assign('editors', $editors);
		return parent::fetch($request);
	}

	/**
	 * Initialize form data.
	 */
	function initData() {
		$contextId = $this->contextId;
		$plugin =& $this->plugin;

		foreach($this->_getFormFields() as $fieldName => $fieldType) {
			$this->setData($fieldName, $plugin->getSetting($contextId, $fieldName));
		}
	}

	/**
	 * Assign form data to user-submitted data.
	 */
	function readInputData() {
		$this->readUserVars(array_keys($this->_getFormFields()));
	}

	/**
	 * Save settings.
	 */
	function execute() {
		$plugin =& $this->plugin;
		$contextId = $this->contextId;

		foreach($this->_getFormFields() as $fieldName => $fieldType) {
			$plugin->updateSetting($contextId, $fieldName, $this->getData($fieldName), $fieldType);
		}
	}

	//
	// Private helper methods
	//
	function _getFormFields() {
		return array(
			'vgWortEditors' => 'object',
			'vgWortUserId' => 'string',
			'vgWortUserPassword' => 'string',
			'vgWortPubFormat' => 'string',
			'vgWortPixelTagMin' => 'int'
		);
	}
}

?>
