<?php
/**
 * @defgroup controllers_grid_vgwort PixelTagsGrid
 * The PixelTagGrid implements the management interface allowing editors to
 * manage pixel tags.
 */

/**
 * @file plugins/generic/vgWort/controllers/grid/PixelTagGridHandler.inc.php
 *
 * Author: Božana Bokan, Center for Digital Systems (CeDiS), Freie Universität Berlin
 * Last update: May 30, 2015
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class PixelTagGridHandler
 * @ingroup plugins_generic_vgWort
 *
 * @brief Handle pixel tag grid requests.
 */

import('lib.pkp.classes.controllers.grid.GridHandler');
import('plugins.generic.vgWort.controllers.grid.PixelTagGridRow');

// Link action & modal classes
import('lib.pkp.classes.linkAction.request.AjaxModal');

class PixelTagGridHandler extends GridHandler {

	/** @var int Context id. */
	private $_context;

	/** @var int Pixel tags status */
	var $pixelTagStatus;

	/**
	 * Constructor
	 */
	function PixelTagGridHandler() {
		parent::GridHandler();
		$this->addRoleAssignment(
			array(ROLE_ID_SERIES_EDITOR, ROLE_ID_MANAGER),
			array(
				'fetchGrid',
				'deletePixelTag', 'registerPixelTag', 'writeInHtaccess',
				'orderPixelTags', 'editOrderPixelTags', 'updateOrderPixelTags',
				'insertPixelTag'
			)
		);
	}


	//
	// Overridden methods
	//
	/**
	 * @copydoc PKPHandler::initialize()
	 */
	function initialize($request, $args) {
		parent::initialize($request, $args);

		$router = $request->getRouter();
		$this->_context = $router->getContext($request);

		if ($request->getUserVar('pixelTagStatus')) {
			$this->pixelTagStatus = $request->getUserVar('pixelTagStatus');
		}

		//AppLocale::requireComponents(LOCALE_COMPONENT_APP_EDITOR);
		AppLocale::requireComponents(LOCALE_COMPONENT_APP_SUBMISSION, LOCALE_COMPONENT_PKP_SUBMISSION);

		// Grid columns.
		import('plugins.generic.vgWort.controllers.grid.PixelTagGridCellProvider');
		$pixelTagGridCellProvider = new PixelTagGridCellProvider();

		$this->setTitle('plugins.generic.vgWort.editor.pixelTags');
		$this->addColumn(
			new GridColumn(
				'privateCode',
				'plugins.generic.vgWort.pixelTag.privateCode',
				null,
				'controllers/grid/gridCell.tpl',
				$pixelTagGridCellProvider
			)
		);
		if ($this->pixelTagStatus == '' || $this->pixelTagStatus == PT_STATUS_AVAILABLE) {
			$this->addColumn(
				new GridColumn(
					'publicCode',
					'plugins.generic.vgWort.pixelTag.publicCode',
					null,
					'controllers/grid/gridCell.tpl',
					$pixelTagGridCellProvider
				)
			);
			$this->addColumn(
				new GridColumn(
					'ordered',
					'plugins.generic.vgWort.pixelTag.dateOrdered',
					null,
					'controllers/grid/gridCell.tpl',
					$pixelTagGridCellProvider
				)
			);
		}
		if ($this->pixelTagStatus == '') {
			$this->addColumn(
				new GridColumn(
					'status',
					'common.status',
					null,
					'controllers/grid/gridCell.tpl',
					$pixelTagGridCellProvider
				)
			);
		}
		if ($this->pixelTagStatus == PT_STATUS_AVAILABLE) {
			$this->addColumn(
				new GridColumn(
					'domain',
					'plugins.generic.vgWort.pixelTag.domain',
					null,
					'controllers/grid/gridCell.tpl',
					$pixelTagGridCellProvider
				)
			);
		}
		if ($this->pixelTagStatus == PT_STATUS_UNREGISTERED || $this->pixelTagStatus == PT_STATUS_REGISTERED) {
			$this->addColumn(
				new GridColumn(
					'authors',
					'submission.authors',
					null,
					'controllers/grid/gridCell.tpl',
					$pixelTagGridCellProvider
				)
			);
			$this->addColumn(
				new GridColumn(
					'title',
					'submission.title',
					null,
					'controllers/grid/gridCell.tpl',
					$pixelTagGridCellProvider
				)
			);
		}
		if ($this->pixelTagStatus == PT_STATUS_UNREGISTERED) {
			$this->addColumn(
				new GridColumn(
					'assigned',
					'plugins.generic.vgWort.pixelTag.dateAssigned',
					null,
					'controllers/grid/gridCell.tpl',
					$pixelTagGridCellProvider
				)
			);
		}
		if ($this->pixelTagStatus == PT_STATUS_REGISTERED) {
			$this->addColumn(
				new GridColumn(
					'registered',
					'plugins.generic.vgWort.pixelTag.dateRegistered',
					null,
					'controllers/grid/gridCell.tpl',
					$pixelTagGridCellProvider
				)
			);
		}
		if ($this->pixelTagStatus == PT_STATUS_AVAILABLE) {
			$this->addAction(
				new LinkAction(
					'orderPixelTags',
					new AjaxModal(
						$router->url($request, null, null, 'orderPixelTags'),
						__('plugins.generic.vgWort.editor.order')
					),
					__('plugins.generic.vgWort.editor.order')
				)
			);
		}
		if ($this->pixelTagStatus == PT_STATUS_UNREGISTERED || $this->pixelTagStatus == PT_STATUS_REGISTERED) {
			$this->addAction(
				new LinkAction(
					'insertPixelTag',
					new AjaxModal(
						$router->url($request, null, null, 'insertPixelTag', null, array('pixelTagStatus' => $this->pixelTagStatus)),
						__('plugins.generic.vgWort.editor.insertPixelTag')
					),
					__('plugins.generic.vgWort.editor.insertPixelTag')
				)
			);
		}

	}

	/**
	 * @copydoc GridHandler::getRowInstance()
	 */
	function getRowInstance() {
		return new PixelTagGridRow($this->pixelTagStatus);
	}

	/**
	 * @copydoc GridHandler::renderFilter()
	 */
	function renderFilter($request) {
		$pixelTagDao = DAORegistry::getDAO('PixelTagDAO');

		$fieldOptions = Array(
			PT_FIELD_PRIVCODE => __('plugins.generic.vgWort.pixelTag.privateCode'),
			PT_FIELD_PUBCODE => __('plugins.generic.vgWort.pixelTag.publicCode')
		);

		$filterData = array(
			'fieldOptions' => $fieldOptions,
			'pixelTagStatus' => $this->pixelTagStatus
		);

		return parent::renderFilter($request, $filterData);
	}

	/**
	 * @copydoc GridHandler::getFilterSelectionData()
	 */
	function getFilterSelectionData($request) {
		$searchField = null;
		$search = $request->getUserVar('search');
		if (!empty($search)) {
			$searchField = $request->getUserVar('searchField');
		}

		return $filterSelectionData = array(
			'searchField' => $searchField,
			'search' => $search ? $search : ''
		);
	}

	/**
	 * @copydoc GridHandler::getFilterForm()
	 */
	function getFilterForm() {
		$vgWortPlugin = PluginRegistry::getPlugin('generic', 'vgwortplugin');
		$template = $vgWortPlugin->getTemplatePath() . 'controllers/grid/pixelTagGridFilter.tpl';
		return $template;
	}

	/**
	 * @copydoc GridHandler::loadData()
	 */
	function loadData($request, $filter) {
/*
$file = 'debug.txt';
$current = file_get_contents($file);
$current .= print_r("--- load data ---", true);
file_put_contents($file, $current);
*/
		$sortBy = 'pixel_tag_id';
		$sortDirection = SORT_DIRECTION_DESC;
		$pixelTagDao = DAORegistry::getDAO('PixelTagDAO');
		$rangeInfo = $this->getGridRangeInfo($request, $this->getId());
		$pixelTags = $pixelTagDao->getPixelTagsByContextId(
			$this->_getContextId(),
			$filter['searchField']?$filter['searchField']:null,
			$filter['search']?$filter['search']:null,
			$this->pixelTagStatus,
			$rangeInfo,
			$sortBy,
			$sortDirection
		);
/*
$file = 'debug.txt';
$current = file_get_contents($file);
$current .= print_r($pixelTags, true);
file_put_contents($file, $current);
*/
		return $pixelTags;
	}

	//
	// Public operations
	//
	/**
	 * Order new pixel tags.
	 * @param $args array
	 * @param $request PKPRequest
	 * @return string JSON message
	 */
	function orderPixelTags($args, $request) {
		return $this->editOrderPixelTags($args, $request);
	}

	/**
	 * Edit the form to order new pixel tags.
	 * @param $args array
	 * @param $request PKPRequest
	 * @return string JSON message
	 */
	function editOrderPixelTags($args, $request) {
		// Instantiate the form.
		import('plugins.generic.vgWort.controllers.grid.form.OrderPixelTagsForm');
		$contextId = $this->_getContextId();
		$vgWortPlugin = PluginRegistry::getPlugin('generic', VGWORT_PLUGIN_NAME);
		$orderPixelTagsForm = new OrderPixelTagsForm($vgWortPlugin, $contextId);
		$orderPixelTagsForm->initData();
		$json = new JSONMessage(true, $orderPixelTagsForm->fetch($request));
		return $json->getString();
	}

	/**
	 * Update pixel tags data on database and grid.
	 * @param $args array
	 * @param $request PKPRequest
	 * @return string JSON message
	 */
	// TEST (for OMP)
	function updateOrderPixelTags($args, $request) {
		// Instantiate the form.
		import('plugins.generic.vgWort.controllers.grid.form.OrderPixelTagsForm');
		$contextId = $this->_getContextId();
		$vgWortPlugin = PluginRegistry::getPlugin('generic', VGWORT_PLUGIN_NAME);
		$orderPixelTagsForm = new OrderPixelTagsForm($vgWortPlugin, $contextId);
		$orderPixelTagsForm->readInputData();
		if($orderPixelTagsForm->validate()) {
			$orderPixelTagsForm->execute($request);
			return DAO::getDataChangedEvent();
		} else {
			$json = new JSONMessage(true, $orderPixelTagsForm->fetch($request));
			return $json->getString();
		}
	}

	/* Insert pixel tag.
	 * @param $args array
	 * @param $request PKPRequest
	 * @return string JSON message
	 */
	function insertPixelTag($args, $request) {
		$pixelTagStatus = $request->getUserVar('pixelTagStatus');
		import('plugins.generic.vgWort.controllers.grid.form.InsertPixelTagForm');
		$contextId = $this->_getContextId();
		$vgWortPlugin = PluginRegistry::getPlugin('generic', VGWORT_PLUGIN_NAME);
		$insertPixelTagForm = new InsertPixelTagForm($vgWortPlugin, $contextId, $pixelTagStatus);
		$insertPixelTagForm->initData();
		$json = new JSONMessage(true, $insertPixelTagForm->fetch($request));
		return $json->getString();
	}

	/**
	 * Update inserted pixel tags data on database and grid.
	 * @param $args array
	 * @param $request PKPRequest
	 * @return string JSON message
	 */
	function updateInsertPixelTag($args, $request) {
		$pixelTagStatus = $request->getUserVar('pixelTagStatus');
		import('plugins.generic.vgWort.controllers.grid.form.InsertPixelTagForm');
		$contextId = $this->_getContextId();
		$vgWortPlugin = PluginRegistry::getPlugin('generic', VGWORT_PLUGIN_NAME, $pixelTagStatus);
		$insertPixelTagForm = new InsertPixelTagForm($vgWortPlugin, $contextId);
		$insertPixelTagForm->readInputData();
		if($insertPixelTagForm->validate()) {
			$insertPixelTagForm->execute($request);
			return DAO::getDataChangedEvent();
		} else {
			$json = new JSONMessage(true, $insertPixelTagForm->fetch($request));
			return $json->getString();
		}
	}


	/**
	 * Delte pixel tag.
	 * @param $args array
	 * @param $request PKPRequest
	 * @return string JSON message.
	 */
	function deletePixelTag($args = array(), $request) {
		$pixelTagId = $request->getUserVar('rowId');
		if (!$pixelTagId) $pixelTagId = $request->getUserVar('pixelTagId');

		$contextId = $this->_getContextId();

		$pixelTagDao = DAORegistry::getDAO('PixelTagDAO');
		$pixelTag = $pixelTagDao->getPixelTag($pixelTagId, $contextId);
		if (isset($pixelTag) && $pixelTag->getStatus() == PT_STATUS_AVAILABLE) {
			$pixelTagDao->deleteObject($pixelTag);
		}
		return DAO::getDataChangedEvent($pixelTagId);
	}

	/**
	 * Register pixel tag.
	 * @param $args array
	 * @param $request PKPRequest
	 * @return string JSON message.
	 */
	// TEST (for OMP)
	function registerPixelTag($args = array(), $request) {
		$pixelTagId = $request->getUserVar('rowId');
		if (!$pixelTagId) $pixelTagId = $request->getUserVar('pixelTagId');

		$context = $this->_getContextId();
		$contextId = $context->getId();

		$pixelTagDao = DAORegistry::getDAO('PixelTagDAO');
		$pixelTag = $pixelTagDao->getPixelTag($pixelTagId, $contextId);
		// the pixel tag exists, it is unregistered and not removed
		if (isset($pixelTag) && $pixelTag->getStatus() == PT_STATUS_UNREGISTERED && !$pixelTag->getDateRemoved()) {
			// check if the requirements for the registration are fulfilled
			import('plugins.generic.vgWort.classes.VGWortEditorAction');
			$vgWortEditorAction = new VGWortEditorAction();
			$checkResult = $vgWortEditorAction->check($pixelTag);
			$isError = !$checkResult[0];
			if ($isError) {
				$errors[] = $checkResult[1];
			} else {
				// register
				$registerResult = $vgWortEditorAction->newMessage($pixelTagId, $request);
				$isError = !$registerResult[0];
				$errors[] = $registerResult[1];
				if (!$isError) {
					// update the registered pixel tag
					$pixelTag->setDateRegistered(Core::getCurrentDate());
					$pixelTag->setStatus(PT_STATUS_REGISTERED);
					$pixelTagDao->updateObject($pixelTag);
					// send a notification email to the authors
					$vgWortEditorAction->notifyAuthors($context, $pixelTag);
				}
			}
		}
		/*
		$dispatcher = $request->getDispatcher();
		// FIXME: Find a better way to reload the containing tabs.
		// Without this, issues don't move between tabs properly.
		return $request->redirectUrlJson($dispatcher->url($request, ROUTE_PAGE, null, 'manageIssues'));
		*/
		return DAO::getDataChangedEvent($pixelTagId);
	}


	//
	// Private operations
	//
	/**
	 * Get context.
	 * @return Context
	 */
	private function _getContext() {
		return $this->_context;
	}

	/**
	 * Get context id.
	 * @return int
	 */
	private function _getContextId() {
		$context = $this->_getContext();
		return $context->getId();
	}


}

?>
