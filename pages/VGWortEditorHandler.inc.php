 <?php

/**
 * @file plugins/generic/vgWort/pages/VGWortEditorHandler.inc.php
 *
 * Author: Božana Bokan, Center for Digital Systems (CeDiS), Freie Universität Berlin
 * Last update: May 27, 2015
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class VGWortEditorHandler
 * @ingroup plugins_generic_vgWort
 *
 * @brief Handle requests for editor VG Wort functions.
 */

import('classes.handler.Handler');

class VGWortEditorHandler extends Handler {

	/**
	 * Constructor
	 */
	function VGWortEditorHandler() {
		$this->addRoleAssignment(
			array(ROLE_ID_SUB_EDITOR, ROLE_ID_MANAGER),
			array('vgWort', 'pixelTags', 'pixelStatistics')
		);
	}


	/**
	 * Display pixel tags page.
	 * @param $args array
	 * @param $request PKPRequest
	 */
	function vgWort($args, $request) {
		$vgWortPlugin = PluginRegistry::getPlugin('generic', VGWORT_PLUGIN_NAME);
		$templateMgr = TemplateManager::getManager($request);
		// TO-DO (for OMP): pixel counts by status?
		$templateMgr->assign('currentFormatTabId', (int) $request->getUserVar('currentFormatTabId'));
		$templateMgr->assign('pluginJavaScriptPath', $vgWortPlugin->getPluginPath());
		$templateMgr->display($vgWortPlugin->getTemplatePath() . 'pixelTagsTabs.tpl');
	}

	/**
	 * Display the publication format template (grid + actions).
	 * @param $args array
	 * @param $request PKPRequest
	 */
	function pixelTags($args, $request) {
		$vgWortPlugin = PluginRegistry::getPlugin('generic', VGWORT_PLUGIN_NAME);
		$templateMgr = TemplateManager::getManager($request);
		$templateMgr->assign('pixelTagStatus', $request->getUserVar('pixelTagStatus'));
		return $templateMgr->fetchJson($vgWortPlugin->getTemplatePath() . 'pixelTagsTab.tpl');
	}

	/**
	 * Display VG Wort statistics.
	 * @param $args array
	 * @param $request PKPRequest
	 */
	// TEST (for OMP)
	function pixelStatistics($args, $request) {
		$router = $request->getRouter();
		$context = $router->getContext($request);
		$contextId = $context->getId();

		$vgWortPlugin = PluginRegistry::getPlugin('generic', VGWORT_PLUGIN_NAME);
		$vgWortPlugin->import('classes.VGWortEditorAction');
		$vgWortEditorAction = new VGWortEditorAction();
		// get VG Wort statistics
		$qualityControlResult = $vgWortEditorAction->qualityControl($contextId);
		$isError = !$qualityControlResult[0];
		$qualityControlResultObject = null;
		$errors = '';
		if ($isError) {
			$errors = array($qualityControlResult[1]);
		} else {
			$qualityControlResultObject = $qualityControlResult[1];
		}

		// get other information to display
		$templateMgr = TemplateManager::getManager($request);
		if ($qualityControlResultObject) {
			$templateMgr->assign('qualityControlValues', $qualityControlResultObject->qualityControlValues);
			$templateMgr->assign('orderedPixelTillToday', $qualityControlResultObject->orderedPixelTillToday);
			$templateMgr->assign('startedPixelTillToday', $qualityControlResultObject->startedPixelTillToday);
		}
		$templateMgr->assign('errors', $errors);
		$templateMgr->assign('isError', $isError);
		return $templateMgr->fetchJson($vgWortPlugin->getTemplatePath() . 'pixelTagsStat.tpl');
	}

	/**
	 * Ensure that we have a context, the plugin is enabled, and the user is editor selected in the plugin settings.
	 */
	// TO-DO (for OMP)
	function authorize($request, &$args, $roleAssignments) {
		/*
		import('lib.pkp.classes.security.authorization.ContextRequiredPolicy');
		$this->addPolicy(new ContextRequiredPolicy($request));

		import('lib.pkp.classes.security.authorization.PKPSiteAccessPolicy');
		$this->addPolicy(new PKPSiteAccessPolicy($request, null, $roleAssignments));
		*/
		/*
		$router = $request->getRouter();
		$context = $router->getContext($request);
		if (!isset($context)) return false;

		$vgWortPlugin = PluginRegistry::getPlugin('generic', VGWORT_PLUGIN_NAME);

		if (!isset($vgWortPlugin)) return false;

		if (!$vgWortPlugin->getEnabled()) return false;

		if (!Validation::isEditor($context->getId())) Validation::redirectLogin();
		// consider editors from the plugin settings
		$editors = $vgWortPlugin->getSetting($context->getId(), 'vgWortEditors');
		$sessionManager = SessionManager::getManager($request);
		$session = $sessionManager->getUserSession();
		if (!in_array($session->getUserId(), $editors)) Validation::redirectLogin();
		*/
		return parent::authorize($request, $args, $roleAssignments);
	}

}

?>
