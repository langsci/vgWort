<?php

/**
 * @file plugins/generic/vgWort/controllers/grid/form/InsertPixelTagForm.inc.php
 *
 * Author: Božana Bokan, Center for Digital Systems (CeDiS), Freie Universität Berlin
 * Last update: June 01, 2015
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class InsertPixelTagForm
 * @ingroup plugins_generic_vgWort
 *
 * @brief Form to insert already assigned (uregistered or registere) pixel tag.
 */

import('lib.pkp.classes.form.Form');

class InsertPixelTagForm extends Form {

	/** @var int Context id */
	var $_contextId;

	/** @var Plugin (VG Wort) */
	var $_plugin;

	/** @var int Pixel tag status */
	var $_pixelTagStatus;

	/**
	 * Constructor.
	 * @param $plugin Plugin (VG Wort).
	 * @param $contextId int Context id.
	 * @param $pixelTagStatus int Pixel tag status.
	 */
	function InsertPixelTagForm($plugin, $contextId, $pixelTagStatus) {
		parent::Form($plugin->getTemplatePath() . 'controllers/grid/form/insertPixelTagForm.tpl');
		$this->_contextId = $contextId;
		$this->_plugin = $plugin;
		$this->_pixelTagStatus = $pixelTagStatus;

		// Validation checks for this form
		
	//	$this->addCheck(new FormValidator($this, 'privateCode', FORM_VALIDATOR_REQUIRED_VALUE, 'plugins.generic.vgWort.create.privateCodeRequired'));
		$this->addCheck(new FormValidatorCustom($this, 'submissionId', FORM_VALIDATOR_REQUIRED_VALUE, 'plugins.generic.vgWort.create.submissionIDDoesNotExist', create_function('$submissionId,$contextId,$submissionDao', '$submission = $submissionDao->getById($submissionId, $contextId); return isset($submission);'), array($this->_contextId, DAORegistry::getDAO('PublishedMonographDAO'))));
		
		//TODO: check if book has a pixel getPixelTagBySubmissionId($contextId, $submissionId)
		
		/*
		$this->addCheck(new FormValidator($this, 'privateCode', FORM_VALIDATOR_REQUIRED_VALUE, 'plugins.generic.vgWort.create.privateCodeRequired'));
		$this->addCheck(new FormValidator($this, 'publicCode', FORM_VALIDATOR_REQUIRED_VALUE, 'plugins.generic.vgWort.create.publicCodeRequired'));
		$this->addCheck(new FormValidatorAlphaNum($this, 'privateCode', FORM_VALIDATOR_REQUIRED_VALUE, 'plugins.generic.vgWort.create.privateCodeAlphaNum'));
		$this->addCheck(new FormValidatorAlphaNum($this, 'publicCode', FORM_VALIDATOR_REQUIRED_VALUE, 'plugins.generic.vgWort.create.publicCodeAlphaNum'));
		$this->addCheck(new FormValidatorLength($this, 'privateCode', FORM_VALIDATOR_REQUIRED_VALUE, 'plugins.generic.vgWort.create.privateCodeLength', '==', 32));
		$this->addCheck(new FormValidatorLength($this, 'publicCode', FORM_VALIDATOR_REQUIRED_VALUE, 'plugins.generic.vgWort.create.publicCodeLength', '==', 32));
		$this->addCheck(new FormValidator($this, 'domain', FORM_VALIDATOR_REQUIRED_VALUE, 'plugins.generic.vgWort.create.domainRequired'));
		$this->addCheck(new FormValidatorRegExp($this, 'domain', FORM_VALIDATOR_REQUIRED_VALUE, 'plugins.generic.vgWort.create.domainPattern', '/^vg[0-9][0-9]\.met\.vgwort\.de$/'));
		$this->addCheck(new FormValidator($this, 'submissionId', FORM_VALIDATOR_REQUIRED_VALUE, 'plugins.generic.vgWort.create.submissionIDRequired'));
		$this->addCheck(new FormValidatorCustom($this, 'submissionId', FORM_VALIDATOR_REQUIRED_VALUE, 'plugins.generic.vgWort.create.submissionIDDoesNotExist', create_function('$submissionId,$journalId,$articleDao', '$submission = $articleDao->getArticle($submissionId, $journalId); return isset($submission);'), array($this->journalId, DAORegistry::getDAO('ArticleDAO'))));
		$this->addCheck(new FormValidatorCustom($this, 'submissionId', FORM_VALIDATOR_REQUIRED_VALUE, 'plugins.generic.vgWort.create.submissionIDPixelTagExists', create_function('$submissionId,$journalId,$pixelTagDao', '$pixelTag = $pixelTagDao->getPixelTagByArticleId($journalId, $submissionId); return !isset($pixelTag);'), array($this->journalId, DAORegistry::getDAO('PixelTagDAO'))));
		*/
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

	/**
	 * Get the pixel tag status.
	 * @return int
	 */
	function getPixelTagStatus() {
		return $this->_pixelTagStatus;
	}

	//
	// Implement template methods from Form.
	//
	/**
	 * @copydoc Form::readInputData()
	 */
	function fetch($request) {
		$templateMgr = TemplateManager::getManager($request);
		$statusOptions = array(
			PT_STATUS_UNREGISTERED => 'plugins.generic.vgWort.pixelTag.unregistered',
			PT_STATUS_REGISTERED => 'plugins.generic.vgWort.pixelTag.registered'
		);
		$templateMgr->assign('typeOptions', PixelTag::getTextTypeOptions());
		$templateMgr->assign('pixelTagStatus', $this->getPixelTagStatus());
		return parent::fetch($request);
	}

	/**
	 * @copydoc Form::readInputData()
	 */
	function readInputData() {
		$this->readUserVars(
			array(
				'privateCode',
				'publicCode',
				'domain',
				'dateOrderedYear',
				'dateOrderedMonth',
				'dateOrderedDay',
				'dateOrdered',
				'submissionId',
				'vgWortTextType',
				'dateAssignedYear',
				'dateAssignedMonth',
				'dateAssignedDay',
				'dateAssigned',
				'dateRegisteredYear',
				'dateRegisteredMonth',
				'dateRegisteredDay',
				'dateRegistered',
				'pixelTagStatus'
			)
		);
		// Format the dates
		/*
		$this->_data['dateOrdered'] = $this->_data['dateOrderedYear'] . '-' . $this->_data['dateOrderedMonth'] . '-' . $this->_data['dateOrderedDay'] . ' 00:00:00';
		$this->_data['dateAssigned'] = $this->_data['dateAssignedYear'] . '-' . $this->_data['dateAssignedMonth'] . '-' . $this->_data['dateAssignedDay'] . ' 00:00:00';
		$this->_data['dateRegistered'] = $this->_data['dateRegisteredYear'] . '-' . $this->_data['dateRegisteredMonth'] . '-' . $this->_data['dateRegisteredDay'] . ' 00:00:00';
		*/
	}

	/**
	 * @copydoc Form::execute()
	 */
	function execute($request) {
		$contextId = $this->getContextId();
		$pixelTagDao = DAORegistry::getDAO('PixelTagDAO');
		$pixelTag = new PixelTag();
		$pixelTag->setContextId($contextId);
		$pixelTag->setPrivateCode($this->getData('privateCode'));
		$pixelTag->setPublicCode($this->getData('publicCode'));
		$pixelTag->setDomain($this->getData('domain'));
		$pixelTag->setDateOrdered(DAO::formatDateToDB($this->getData('dateOrdered')));
		$pixelTag->setStatus($this->getData('pixelTagStatus'));
		$pixelTag->setSubmissionId((int)$this->getData('submissionId'));
		$pixelTag->setTextType((int)$this->getData('vgWortTextType'));
		$pixelTag->setDateAssigned(DAO::formatDateToDB($this->getData('dateAssigned')));
		$dateRegistered = $this->getData('dateRegistered');
		if ($dateRegistered) {
			$pixelTag->setDateRegistered(DAO::formatDateToDB($dateRegistered));
		}
		$pixelTagId = $pixelTagDao->insertObject($pixelTag);
		
		// write redirect to vg wort with book in .htaccess 
		$this->writeInHtaccess($request, $contextId, $this->getData('submissionId'), $this->getData('publicCode'));
		
	}
	
	
	/* TODO
	* Update the urls of a book in .htaccess. 
	* @param $pixelTagId int id of the pixel tag 
	*/
	function updateBookInHtaccess($request, $contextId, $pixelTagId){
		
		// get pixelTag object by Id
		$pixelTag = getPixelTag($pixelTagId);
		// get the id of the associated submission
		$sumissionId = $pixelTag->getSubmissionId();
		// get the public code of the pixel tag
		$vgWortPublicCode = $pixelTag->getPublicCode();
		
		// write new line in htaccess
		// TODO: delete old line
		$this->writeInHtaccess($request, $contextId, $sumissionId, $vgWortPublicCode);
		
	}
	
	/*
	* Write redirects for all files of a book in .htaccess. 
	* TODO: get names of publicationFormat from settings
	* @param $request object
	* @param $submissionId int id of the submission
	* @param $vgWortPublicCode int public code of the vg wort pixel
	* @return string
	*/
	function writeInHtaccess($request, $contextId, $submissionId, $vgWortPublicCode){

		// load helpers
		import('lib.pkp.classes.file.FileManager'); // file manager
		$fileMgr = new FileManager(); // file manager
		$submissionFileDao = DAORegistry::getDAO('SubmissionFileDAO');
		$submissionDAO = DAORegistry::getDAO('PublishedMonographDAO');
		$dispatcher = PKPApplication::getDispatcher();
		$vgWortPlugin = PluginRegistry::getPlugin('generic', VGWORT_PLUGIN_NAME); // settings
		
		// define variables
		$submission = $submissionDAO->getById($submissionId);
		$publicationFormats = $submission->getPublicationFormats();
		$excludedPubFormat = $vgWortPlugin->getSetting($contextId, 'vgWortPubFormat'); // get names of excluded publicationFormats from settings
		
		// write title of submission for overview
		$rewriteRules = "# ".$submissionId." ".$submission->getLocalizedTitle()."\n";
		
		// go through all publicationFormats 
		foreach ($publicationFormats as $publicationFormat){
			
			// handle all publicationFormats but $excludedPubFormat that and are available
			if ($publicationFormat->getIsAvailable() && $publicationFormat->getLocalizedName()!==$excludedPubFormat){
				
				// get the id of the publicationFormat
				$publicationFormatId = $publicationFormat->getId();
				
				// filter files in this publicationFormat and show only files that are available in the public catalog
				$availableFiles = array_filter(
					$submissionFileDao->getLatestRevisionsByAssocId(ASSOC_TYPE_PUBLICATION_FORMAT,$publicationFormatId,	$submissionId),
					create_function('$a', 'return $a->getViewable() && $a->getDirectSalesPrice() !== null;')
				);
				
				// add name of publicationFormat for overview
				$rewriteRules .= "#".$publicationFormat->getLocalizedName()."\n";
				
				// go through all file in this publicationFormat
				foreach($availableFiles as $file){
				
					// generate download url of the file
					$url = $dispatcher->url($request, ROUTE_PAGE, null, 'catalog', 'view', array($submissionId, $publicationFormatId, $file->getFileIdAndRevision()));
					
					// compose RewriteRule and append it to variable $rewriteRules
					$vgWortUrl = "http://vg05.met.vgwort.de/na/".$vgWortPublicCode."?l=".$url."?rewrite=no [R,L]";
					list($pre, $shortUrl) = split($request->getBaseUrl()."/", $url);
					$rewriteRules .= "RewriteRule ^".$shortUrl." ".$vgWortUrl."\n";
					
				}
			}
		} 
		
		/**
		* write to file
		*/
		
		// open .htaccess file or create one if not existent
		if(!$fileMgr->fileExists(".htaccess", 'file')){
			
			// create .htaccess and write first line and rewriteRules to file 
			$fileMgr->writeFile(".htaccess", "RewriteEngine On \n" . $rewriteRules);
			
		}else{
			
			// write rewriteRules to file .htaccess
			$file = fopen(".htaccess", "a");
			fwrite($file, $rewriteRules);
			
		}
		
		// test output
		//	$fileMgr->writeFile("debug_.txt", $rewriteRule);
		
	}
	
}

?>
