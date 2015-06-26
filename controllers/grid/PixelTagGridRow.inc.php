<?php

/**
 * @file plugins/generic/vgWort/controllers/grid/PixelTagGridRow.inc.php
 *
 * Author: Božana Bokan, Center for Digital Systems (CeDiS), Freie Universität Berlin
 * Last update: May 30, 2015
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class PixelTagGridRow
 * @ingroup controllers_grid_vgwort
 *
 * @brief Handle pixel tag grid row requests.
 */

import('lib.pkp.classes.controllers.grid.GridRow');
import('lib.pkp.classes.linkAction.request.RemoteActionConfirmationModal');
import('lib.pkp.classes.linkAction.request.RedirectConfirmationModal');

class PixelTagGridRow extends GridRow {
	/** @var int pixel tags status */
	var $pixelTagStatus;

	/**
	 * Constructor
	 */
	function PixelTagGridRow($pixelTagStatus) {
		parent::GridRow();
		$this->pixelTagStatus = $pixelTagStatus;
	}

	//
	// Overridden methods
	//
	/*
	 * @copydoc GridRow::initialize
	 */
	function initialize($request, $template = null) {
		parent::initialize($request);

		$router = $request->getRouter();
		$pixelTagId = $this->getId();
		if (!empty($pixelTagId) && is_numeric($pixelTagId)) {
			switch ($this->pixelTagStatus) {
				case PT_STATUS_AVAILABLE:
					$this->addAction(
							new LinkAction(
								'delete',
								new RemoteActionConfirmationModal(
									__('common.confirmDelete'),
									__('grid.action.delete'),
									$router->url($request, null, null, 'deletePixelTag', null, array('pixelTagId' => $pixelTagId)),
									'modal_delete'
								),
								__('grid.action.delete'),
								'delete'
							)
					);
					break;
				case PT_STATUS_UNREGISTERED:
					$this->addAction(
						new LinkAction(
							'register',
							new RemoteActionConfirmationModal(
								__('editor.issues.confirmPublish'), //TO-DO (for OMP)
								__('plugins.generic.vgWort.editor.register'),
								$router->url($request, null, null, 'registerPixelTag', null, array('pixelTagId' => $pixelTagId)),
								'modal_confirm',
								__('plugins.generic.vgWort.editor.update'),
								$router->url($request, null, null, 'updateBookInHtaccess', null, array('pixelTagId' => $pixelTagId)),
								'modal_confirm'
							),
							__('plugins.generic.vgWort.editor.register'),
							'advance' //TO-DO (for OMP)
						)
					);
					break;
				case PT_STATUS_REGISTERED:
					break;
				default:
					break;
			}
		}
	}
}

?>
