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
		
		// privateCode, publicCode, domain and submissionId are required
		$this->addCheck(new FormValidator($this, 'privateCode', FORM_VALIDATOR_REQUIRED_VALUE, 'plugins.generic.vgWort.create.privateCodeRequired'));
		$this->addCheck(new FormValidator($this, 'publicCode', FORM_VALIDATOR_REQUIRED_VALUE, 'plugins.generic.vgWort.create.publicCodeRequired'));
		$this->addCheck(new FormValidator($this, 'domain', FORM_VALIDATOR_REQUIRED_VALUE, 'plugins.generic.vgWort.create.domainRequired'));
		$this->addCheck(new FormValidator($this, 'submissionId', FORM_VALIDATOR_REQUIRED_VALUE, 'plugins.generic.vgWort.create.submissionIDRequired'));
		
		// check if privateCode and publicCode contain only alphaNumeric content
		$this->addCheck(new FormValidatorAlphaNum($this, 'privateCode', FORM_VALIDATOR_REQUIRED_VALUE, 'plugins.generic.vgWort.create.privateCodeAlphaNum'));
		$this->addCheck(new FormValidatorAlphaNum($this, 'publicCode', FORM_VALIDATOR_REQUIRED_VALUE, 'plugins.generic.vgWort.create.publicCodeAlphaNum'));
		
		// check the length of privateCode and publicCode (max 32)
		$this->addCheck(new FormValidatorLength($this, 'privateCode', FORM_VALIDATOR_REQUIRED_VALUE, 'plugins.generic.vgWort.create.privateCodeLength', '==', 32));
		$this->addCheck(new FormValidatorLength($this, 'publicCode', FORM_VALIDATOR_REQUIRED_VALUE, 'plugins.generic.vgWort.create.publicCodeLength', '==', 32));
		
		// TODO: check the syntax of the domain 
	//	$this->addCheck(new FormValidatorRegExp($this, 'domain', FORM_VALIDATOR_REQUIRED_VALUE, 'plugins.generic.vgWort.create.domainPattern', '/^vg[0-9][0-9]\.met\.vgwort\.de$/'));
		
		// check if the submission id does exist
		$this->addCheck(new FormValidatorCustom($this, 'submissionId', FORM_VALIDATOR_REQUIRED_VALUE, 'plugins.generic.vgWort.create.submissionIDDoesNotExist', create_function('$submissionId,$contextId,$submissionDao', '$submission = $submissionDao->getById($submissionId, $contextId); return isset($submission);'), array($this->_contextId, DAORegistry::getDAO('PublishedMonographDAO'))));
		
		// check if book has a pixel
		// TODO: allow user to update the pixel tag of a book
		$this->addCheck(new FormValidatorCustom($this, 'submissionId', FORM_VALIDATOR_REQUIRED_VALUE, 'plugins.generic.vgWort.create.submissionIDPixelTagExists', create_function('$submissionId,$contextId,$pixelTagDao', '$pixelTag = $pixelTagDao->getPixelTagBySubmissionId($contextId, $submissionId); return !isset($pixelTag);'), array($this->_contextId, DAORegistry::getDAO('PixelTagDAO'))));
		

		// TODO: check if pixel tag already exists
		// getPixelTagsByContextId
		// getPrivateCode
		
	/*	
		$this->addCheck(new FormValidatorCustom($this, 'privateCode', FORM_VALIDATOR_REQUIRED_VALUE, 'plugins.generic.vgWort.create.PixelTagExists',create_function('$privateCode,$contextId,$pixelTagDao', '$pixelTags = $pixelTagDao->getPixelTagsByContextId($contextId); 
	
		// TODO: 
		$array = [];
		foreach($pixelTags as $key=>$pixelTag){	
			$array[] = PixelTag::getPrivateCode(); 
		}
		return in_array($privateCode, $array);'), 
		array($this->_contextId, DAORegistry::getDAO('PixelTagDAO'))));
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
	//	$this->writeInHtaccess($request, $this->getData('submissionId'), $this->getData('publicCode'));
		
	}
	
	
	/*
	* Update the urls of a book in .htaccess. 
	* @param $pixelTagId int id of the pixel tag 
	*/
	function updateBookInHtaccess($pixelTagId){
		
		$pixelTag = getPixelTag($pixelTagId);
		$sumissionId = $pixelTag->getSubmissionId();
		$vgWortPublicCode = $pixelTag->getPublicCode();
		
		
		$this->writeInHtaccess($request, $sumissionId, $vgWortPublicCode);
		
		
	}
	
	/*
	* Write redirects for all files of a book in .htaccess. 
	* TODO: get names of publicationFormat from settings
	* @param $request object
	* @param $submissionId int id of the submission
	* @param $vgWortPublicCode int public code of the vg wort pixel
	* @return string
	*/
	function writeInHtaccess($request, $submissionId, $vgWortPublicCode){
		// write data in htaccess file 
		
		// open htaccess file or create one if not existent
		import('lib.pkp.classes.file.FileManager');
		$fileMgr = new FileManager();
				
		// find section for pixels or create one if not existent
		// TODO
		
		// create rewrite rule with given variables and urls of this book 
		
		// load helpers and define variables
		$submissionFileDao = DAORegistry::getDAO('SubmissionFileDAO');
		$submissionDAO = DAORegistry::getDAO('PublishedMonographDAO');
		$dispatcher = PKPApplication::getDispatcher();
		
		$submission = $submissionDAO->getById($submissionId);
		$submissionId = $submission->getId();
	
		$publicationFormats = $submission->getPublicationFormats();
		
		$rewriteRules = "RewriteEngine On \n";
		
		// write title of submission for overview
		$rewriteRules .= "# ".$submissionId." ".$submission->getLocalizedTitle()."\n";
		
		// go through all publicationFormats - handle only publicationFormats that are named "Complete book" and are available
		// TODO: get names of publicationFormats from settings
		foreach ($publicationFormats as $publicationFormat){
			// 
			if ($publicationFormat->getIsAvailable() && $publicationFormat->getLocalizedName()!=="Bibliography"){
				
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
		
		// write rewriteRules to file .htaccess
		$fileMgr->writeFile(".htaccess", $rewriteRules);
	
		// test output
		//	$fileMgr->writeFile("debug_.txt", $rewriteRule);
		
		
	}
	

}

?>
