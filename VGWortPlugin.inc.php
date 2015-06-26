<?php

/**
 * @file plugins/generic/vgWort/VGWortPlugin.inc.php
 *
 * Author: Božana Bokan, Center for Digital Systems (CeDiS), Freie Universität Berlin
 * Last update: May 29, 2015
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class VGWortPlugin
 * @ingroup plugins_generic_vgWort
 *
 * @brief VG Wort plugin class
 *
 *
 * TO-DOs:
 * -- pixel tags in the publications public pages
 * -- authorization
 * -- display errors
 * -- enter assigned pixel tags
 * -- test order, register, statistics, notify
 * -- ? consider author pixel tags
 *
 */

import('lib.pkp.classes.plugins.GenericPlugin');


class VGWortPlugin extends GenericPlugin {

	/**
	 * Called as a plugin is registered to the registry
	 * @param $category String Name of category plugin was registered to
	 * @return boolean True iff plugin initialized successfully; if false,
	 * 	the plugin will not be registered.
	 */
	function register($category, $path) {
		$success = parent::register($category, $path);
		if ($success && $this->getEnabled()) {
			$this->addLocaleData();
			//$this->addHelpData();
			$this->import('classes.PixelTag');
			$this->import('classes.PixelTagDAO');
			$pixelTagDao = new PixelTagDAO($this->getName());
			$returner =& DAORegistry::registerDAO('PixelTagDAO', $pixelTagDao);
		

			// pixel tags operations can be done just by editors specified in the plug-in settings
			if ($this->authorize()) {
				// Management navigation main menu link to VG Wort pages
				HookRegistry::register('Templates::Header::Localnav::AdditionalManagementItems', array($this, 'displayLocalNavLink'));
			}

			// Handler for VG Wort pages
			HookRegistry::register('LoadHandler', array($this, 'setupHandler'));
			HookRegistry::register('LoadComponentHandler', array($this, 'setupGridHandler'));

			// VG Wort tab on the submission catalog page
			HookRegistry::register('Templates::Controllers::Modals::SubmissionMetadata::CatalogEntryTabs::Tabs', array($this, 'vgWortEntryTab'));

			// Insert and consider a new field into the author metadata form
			HookRegistry::register('Common::UserDetails::AdditionalItems', array($this, 'metadataFieldEdit'));
			HookRegistry::register('authorform::initdata', array($this, 'metadataInitData'));
			HookRegistry::register('authorform::readuservars', array($this, 'metadataReadUserVars'));
			HookRegistry::register('authorform::execute', array($this, 'metadataExecute'));
			HookRegistry::register('authorform::Constructor', array($this, 'addCheck'));
			HookRegistry::register('authordao::getAdditionalFieldNames', array($this, 'addFieldName'));
			


/*
 			// TO-DO (for OMP)
			// Hook for article galley view -- add the pixel tag
			HookRegistry::register ('TemplateManager::display', array(&$this, 'handleTemplateDisplay'));
*/

		}
		return $success;
	}

	function getDisplayName() {
		return __('plugins.generic.vgWort.displayName');
	}

	function getDescription() {
		if ($this->requirementsFulfilled()) return __('plugins.generic.vgWort.description');
		return __('plugins.generic.vgWort.descriptionDisabled');
	}

	/**
	 * Check whether or not the requirements for this plug-in are fullfilled
	 * @return boolean
	 */
	function requirementsFulfilled() {
		$isPHPVersion = checkPhpVersion('5.0.1');
		$isSoapExtension = in_array('soap', get_loaded_extensions());
		$isOpenSSL = in_array('openssl', get_loaded_extensions());

		return $isPHPVersion && $isSoapExtension && $isOpenSSL;
	}

	/**
	 * Get the path and filename of the ADODB schema for this plugin.
	 */
	function getInstallSchemaFile() {
		return $this->getPluginPath() . '/xml/schema.xml';
	}

	/**
	 * Get the path and filename of the email keys for this plugin.
	 */
	function getInstallEmailTemplatesFile() {
		return $this->getPluginPath() . '/xml/emailTemplates.xml';
	}

	/**
	 * Get the path and filename of the email locale data for this plugin.
	 */
	function getInstallEmailTemplateDataFile() {
		return $this->getPluginPath() . '/locale/{$installedLocale}/emailTemplates.xml';
	}

	/**
	 * Get the template path for this plugin.
	 */
	function getTemplatePath() {
		return parent::getTemplatePath() . 'templates/';
	}

	/**
	 * Get the handler path for this plugin.
	 */
	function getHandlerPath() {
		return $this->getPluginPath() . '/pages/';
	}

	/**
	 * Display VG Wort management link (in the main navigation).
	 */
	function displayLocalNavLink($hookName, $params) {
		$smarty =& $params[1];
		$output =& $params[2];
		$output .= $smarty->fetch($this->getTemplatePath() . 'localnav.tpl');
		return false;
	}

	/**
	 * Enable editor pixel tags management.
	 */
	function setupHandler($hookName, $params) {
		$page =& $params[0];

		if ($page == 'editor' || $page == 'management') {
			$op =& $params[1];

			if ($op) {
				$editorPages = array(
					'vgWort',
					'pixelTags',
					'pixelStatistics'
				);

				if (in_array($op, $editorPages)) {
					define('HANDLER_CLASS', 'VGWortEditorHandler');
					define('VGWORT_PLUGIN_NAME', $this->getName());
					AppLocale::requireComponents(array(LOCALE_COMPONENT_APP_COMMON, LOCALE_COMPONENT_APP_EDITOR));
					$handlerFile =& $params[2];
					$handlerFile = $this->getHandlerPath() . 'VGWortEditorHandler.inc.php';
				}
			}
		}
	}

	/**
	 * Set up the pixel tags grid handler.
	 */
	function setupGridHandler($hookName, $params) {
		$component =& $params[0];
		if ($component == 'plugins.generic.vgWort.controllers.grid.PixelTagGridHandler') {
			define('VGWORT_PLUGIN_NAME', $this->getName());
			return true;
		}
		if ($component == 'plugins.generic.vgWort.controllers.tab.vgWortEntry.VGWortEntryTabHandler') {
			define('VGWORT_PLUGIN_NAME', $this->getName());
			return true;
		}
		return false;
	}

	/**
	 * VG Wort tab on the submission catalog page.
	 */
	function vgWortEntryTab($hookName, $params) {
		$smarty =& $params[1];
		$output =& $params[2];
		$output .= $smarty->fetch($this->getTemplatePath() . 'vgWortEntryTab.tpl');
		return false;
	}


	/*
	 * VG Wort Card No
	 */

	/**
	 * Insert VG Wort field vgWortCardNo into author edit form
	 */
	function metadataFieldEdit($hookName, $params) {
		$smarty =& $params[1];
		$output =& $params[2];
		$output .= $smarty->fetch($this->getTemplatePath() . 'vgWortCardNoEdit.tpl');
		return false;
	}

	/**
	 * Init VG Wort field vgWorgCardNo in the author form
	 */
	function metadataInitData($hookName, $params) {
		$form = $params[0];
		$author = $form->getAuthor();
		if ($author) {
			$form->setData('vgWortCardNo', $author->getData('vgWortCardNo'));
		}
		return false;
	}

	/**
	 * Read the value of the VG Wort field vgWortCardNo in the author form
	 */
	function metadataReadUserVars($hookName, $params) {
		$form = $params[0];
		$vars =& $params[1];
		$vars[] = 'vgWortCardNo';
		return false;
	}

	/**
	 * Add the validation check for the vgWortCardNo field (2-7 numbers)
	 */
	function addCheck($hookName, $params) {
		$form =& $params[0];
		$form->addCheck(new FormValidatorRegExp($form, 'vgWortCardNo', 'optional', 'plugins.generic.vgWort.cardNoValid', '/^\d{2,7}$/'));
		return false;
	}

	/**
	 * Set author VG Wort card number
	 */
	function metadataExecute($hookName, $params) {
		$form = $params[0];
		$author = $form->getAuthor();
		if ($form->getData('vgWortCardNo')) {
			$author->setData('vgWortCardNo', $form->getData('vgWortCardNo'));
		}
		return false;
	}

	/**
	 * Consider vgWortCardNo filed in the AuthorDAO
	 */
	function addFieldName($hookName, $params) {
		$fields =& $params[1];
		$fields[] = 'vgWortCardNo';
		return false;
	}


	/*
	 * VG Wort pixel tag integration into the public publication pages
	 */

	/**
	 * Handle article and submission summary view template display.
	 */
	// TO-DO (for OMP)
	function handleTemplateDisplay($hookName, $params) {
		$smarty =& $params[0];
		$template =& $params[1];

		switch ($template) {
			case 'article/article.tpl':
			case 'rt/printerFriendly.tpl':
				$smarty->register_outputfilter(array(&$this, 'insertPixelTag'));
				break;
			case 'sectionEditor/submission.tpl':
				if ($this->authorize()) {
					HookRegistry::register ('TemplateManager::include', array(&$this, 'assignPixelTag'));
				}
				break;
		}
		return false;
	}


	/**
	 * Insert the VG Wort pixel tag in the publication page.
	 */
	// TO-DO (for OMP)
	function insertPixelTag($output, &$smarty) {
		$smarty->unregister_outputfilter('insertPixelTag');

		$journal =& $smarty->get_template_vars('currentJournal');
		$articleId =& $smarty->get_template_vars('articleId');
		$galley =& $smarty->get_template_vars('galley');

		if (isset($galley)) {
			if($galley->isHtmlGalley() || $galley->isPdfGalley()) {
				$publishedArticleDao =& DAORegistry::getDAO('PublishedArticleDAO');
				$publishedArticle = & $publishedArticleDao->getPublishedArticleByBestArticleId($journal->getId(), $articleId);
				// the article and the issue have to be published
				if (isset($publishedArticle)) {
					$issueDao =& DAORegistry::getDAO('IssueDAO');
					$issue = & $issueDao->getIssueById($publishedArticle->getIssueId(), $journal->getId());
					if ($issue->getPublished()) {
						// get the assigned pixel tag
						$pixelTagDao =& DAORegistry::getDAO('PixelTagDAO');
						$pixelTag =& $pixelTagDao->getPixelTagByArticleId($journal->getId(), $publishedArticle->getId());
						if (isset($pixelTag) && !$pixelTag->getDateRemoved()) {
							// insert the pixel tag in the HTML version, just after the element <div id="content">
							$src = 'http://' . $pixelTag->getDomain() . '/na/' . $pixelTag->getPublicCode();
							$pixelTagImg = '<img src="' .$src .'" width="1" height="1" alt="" />';
							$output = str_replace ('<div id="content">', '<div id="content">'.$pixelTagImg, $output);
							// consider the pixel tag in the PDF download links, i.e. change the PDF download links
							if ($galley->isPdfGalley()) {
								$pdfUrl = Request::url(null, 'article', 'download', array($publishedArticle->getBestArticleId($journal), $galley->getBestGalleyId($journal)));
								$newPdfLink = 'http://' . $pixelTag->getDomain() . '/na/' . $pixelTag->getPublicCode() . '?l=' . $pdfUrl;
								$output = str_replace ('href="'.$pdfUrl, 'href="'.$newPdfLink, $output);
							}
						}
					}
				}
			}
		}
		return $output;
	}

	/**
	 * Display verbs for the management interface.
	 */
	function getManagementVerbs() {
		$verbs = parent::getManagementVerbs();
		if ($this->requirementsFulfilled()) {
			if ($this->getEnabled()) {
				$verbs[] = array('settings', __('plugins.generic.vgWort.manager.settings'));
			}
		}
		return $verbs;
	}

 	/*
 	 * Execute a management verb on this plugin
 	 * @param $verb string
 	 * @param $args array
	 * @param $message string Location for the plugin to put a result msg
 	 * @return boolean
 	 */
	function manage($verb, $args, &$message, &$messageParams, &$pluginModalContent = null) {
		if (!parent::manage($verb, $args, $message, $messageParams, $pluginModalContent)) return false;

		switch ($verb) {
			case 'settings':
				$request = $this->getRequest();
				$router = $request->getRouter();
				$context = $router->getContext($request);
				$templateMgr = TemplateManager::getManager($request);
				$templateMgr->register_function('plugin_url', array($this, 'smartyPluginUrl'));

				$this->import('classes.form.VGWortSettingsForm');
				$form = new VGWortSettingsForm($this, $context->getId());
				if (Request::getUserVar('save')) {
					$form->readInputData();
					if ($form->validate()) {
						$form->execute();
						$message = NOTIFICATION_TYPE_SUCCESS;
						return false;
					} else {
						$pluginModalContent = $form->fetch($request);
					}
					return $json->getString();
				} else {
					$form->initData();
					$pluginModalContent = $form->fetch($request);
				}
				return true;
			default:
				// Unknown management verb
				assert(false);
				return false;
		}
	}

	/**
	 * Define management link actions for the settings verb.
	 * @return LinkAction
	 */
	function getManagementVerbLinkAction($request, $verb) {
		$router = $request->getRouter();

		list($verbName, $verbLocalized) = $verb;

		if ($verbName === 'settings') {
			import('lib.pkp.classes.linkAction.request.AjaxLegacyPluginModal');
			$actionRequest = new AjaxLegacyPluginModal(
					$router->url($request, null, null, 'plugin', null, array('verb' => 'settings', 'plugin' => $this->getName(), 'category' => 'generic')),
					$this->getDisplayName()
			);
			return new LinkAction($verbName, $actionRequest, $verbLocalized, null);
		}

		return null;
	}

	/**
	 * Ensure that the user is an editor specified in the plugin settings.
 	 * @return boolean
	 */
	private function authorize() {
		$request = $this->getRequest();
		$router = $request->getRouter();
		$context = $router->getContext($request);

		$editors = $this->getSetting($context->getId(), 'vgWortEditors');
		if (empty($editors)) return false;
		$sessionManager =& SessionManager::getManager();
		$session =& $sessionManager->getUserSession();
		if (!in_array($session->getUserId(), $editors)) return false;
		return true;
	}

}
?>
