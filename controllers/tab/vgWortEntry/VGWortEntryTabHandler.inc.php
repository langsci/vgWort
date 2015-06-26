<?php

/**
 * @file plugins/generic/vgWort/controllers/tab/vgWortEntry/VGWortEntryTabHandler.inc.php
 *
 * Author: Božana Bokan, Center for Digital Systems (CeDiS), Freie Universität Berlin
 * Last update: May 30, 2015
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class VGWortEntryTabHandler
 * @ingroup plugins_generic_vgwort
 *
 * @brief Handle AJAX operations for the VG Wort tabs on the submission catalog page.
 */

// Import the base Handler.
import('classes.handler.Handler');

import('plugins.generic.vgWort.classes.PixelTag');

class VGWortEntryTabHandler extends Handler {

	/**
	 * Constructor
	 */
	function VGWortEntryTabHandler() {
		parent::Handler();
		$this->addRoleAssignment(
			array(ROLE_ID_SUB_EDITOR, ROLE_ID_MANAGER),
			array(
				'assignPixelTag'
			)
		);
	}


	//
	// Public handler methods
	//

	/**
	 * Assign a pixel tag to an article or
	 * update the pixel tag assignment.
	 * @param $args array
	 * @param $request PKPRequest
	 */
	function assignPixelTag($args, $request) {
		$submissionId = $request->getUserVar('submissionId');
		$submissionDao = Application::getSubmissionDAO();
		$submission = $submissionDao->getById($submissionId);

		$router = $request->getRouter();
		$context = $router->getContext($request);

		$vgWortPlugin = PluginRegistry::getPlugin('generic', VGWORT_PLUGIN_NAME);
		$templateMgr = TemplateManager::getManager($request);

		$pixelTagDao = DAORegistry::getDAO('PixelTagDAO');
		$pixelTag = $pixelTagDao->getPixelTagBySubmissionId($context->getId(), $submissionId);

		$function = $request->getUserVar('function');
		if ($function == 'assign' && isset($submission)) {
			// check if there is a VG Wort card number
			$vgWortCardNoExists = false;
			foreach ($submission->getAuthors() as $author) {
				$vgWortCardNo = $author->getData('vgWortCardNo');
				if (!empty($vgWortCardNo)) {
					$vgWortCardNoExists = true;
					break;
				}
			}
			if (!$vgWortCardNoExists) {
				$templateMgr->assign('errorCode', 1);
			} else {
				// assign
				$vgWortPlugin->import('classes.VGWortEditorAction');
				$vgWortEditorAction = new VGWortEditorAction();
				$vgWortTextType = (int) $request->getUserVar('vgWortTextType');
				$assigned = $vgWortEditorAction->assignPixelTag($context, $submissionId, $vgWortTextType);
				if (!$assigned) { // no available pixel tags
					$templateMgr->assign('errorCode', 2);
				}
				$pixelTag = $pixelTagDao->getPixelTagBySubmissionId($context->getId(), $submissionId);
			}
		}
		if ($function == 'update' && isset($submission)) {
			$updatePixelTag = false;
			$removePixelTag = $request->getUserVar('removePixelTag');
			if ($removePixelTag) {
				if ($pixelTag && $pixelTag->getStatus() != PT_STATUS_AVAILABLE && !$pixelTag->getDateRemoved()) {
					$pixelTag->setDateRemoved(Core::getCurrentDate());
					$updatePixelTag = true;
				}
			} else {
				if ($pixelTag && $pixelTag->getDateRemoved()) {
					$pixelTag->setDateRemoved(NULL);
					$updatePixelTag = true;
				}
			}
			if($pixelTag && $pixelTag->getStatus() != PT_STATUS_REGISTERED) {
				$vgWortTextTypeNew = $request->getUserVar('vgWortTextType') ? (int) $request->getUserVar('vgWortTextType') : null;
				if (isset($vgWortTextTypeNew) && $vgWortTextTypeNew != $pixelTag->getTextType()) {
						$pixelTag->setTextType($vgWortTextTypeNew);
						$updatePixelTag = true;
				}
			}
			if ($updatePixelTag) $pixelTagDao->updateObject($pixelTag);
		}

		$vgWortTextType = !isset($pixelTag) ? 0 : $pixelTag->getTextType();

		$templateMgr->assign('submissionId', $submissionId);
		$templateMgr->assign('pixelTag', $pixelTag);
		$templateMgr->assign('vgWortTextType', $vgWortTextType);
		$templateMgr->assign('typeOptions', PixelTag::getTextTypeOptions());
		$returner = $templateMgr->display($vgWortPlugin->getTemplatePath() . 'assignPixelTag.tpl', null, null, false);
		$json = new JSONMessage(true, $returner);
		return $json->getString();

	}

}

?>
