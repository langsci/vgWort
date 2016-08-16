<?php

/**
 * @file plugins/generic/vgWort/controllers/grid/PixelTagGridCellProvider.inc.php
 *
 * Author: Božana Bokan, Center for Digital Systems (CeDiS), Freie Universität Berlin
 * Last update: May 28, 2015
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class PixelTagGridCellProvider
 * @ingroup controllers_grid_vgwort
 *
 * @brief Grid cell provider for the pixel tag management grid
 */

import('lib.pkp.classes.controllers.grid.GridCellProvider');

class PixelTagGridCellProvider extends GridCellProvider {
	/** @var string */
	var $dateFormatShort;

	/**
	 * Constructor
	 */
	function PixelTagGridCellProvider() {
		parent::GridCellProvider();
		$this->dateFormatShort = Config::getVar('general', 'date_format_short');
	}

	//
	// Overridden methods
	//
	/**
	 * @copydoc GridCellProvider::getTemplateVarsFromRowColumn
	 */
	function getTemplateVarsFromRowColumn($row, $column) {
		$pixelTag = $row->getData();
		$columnId = $column->getId();
		assert (is_a($pixelTag, 'PixelTag'));
		assert(!empty($columnId));
		switch ($columnId) {
			case 'privateCode':
				return array('label' => $pixelTag->getPrivateCode());
			case 'publicCode':
				return array('label' => $pixelTag->getPublicCode());
			case 'ordered':
				$dateOrdered = $pixelTag->getDateOrdered();
				if ($dateOrdered) $dateOrdered = strtotime($dateOrdered);
				return array('label' => $dateOrdered?strftime($this->dateFormatShort, $dateOrdered):'');
			case 'status':
				return array('label' => $pixelTag->getStatusString());
			case 'domain':
				return array('label' => $pixelTag->getDomain());
			case 'authors':
				$submission = $pixelTag->getSubmission();
				return array('label' => ($submission)?$submission->getAuthorString(true):'');
			case 'title':
				$submission = $pixelTag->getSubmission();
				return array('label' => ($submission)?$submission->getLocalizedTitle():'');
			case 'assigned':
				$dateAssigned = $pixelTag->getDateAssigned();
				if ($dateAssigned) $dateAssigned = strtotime($dateAssigned);
				return array('label' => $dateAssigned?strftime($this->dateFormatShort, $dateAssigned):'');
			case 'registered':
				$dateRegistered = $pixelTag->getDateRegistered();
				if ($dateRegistered) $dateRegistered = strtotime($dateRegistered);
				return array('label' => $dateRegistered?strftime($this->dateFormatShort, $dateRegistered):'');
			default: assert(false); break;
		}
	}
}

?>
