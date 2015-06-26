<?php

/**
 * @file plugins/generic/vgWort/classes/PixelTagDAO.inc.php
 *
 * Author: Božana Bokan, Center for Digital Systems (CeDiS), Freie Universität Berlin
 * Last update: May 30, 2015
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class PixelTagDAO
 * @ingroup plugins_generic_vgWort
 * @see PixelTag
 *
 * @brief Operations for retrieving and modifying PixelTag objects.
 */

import('lib.pkp.classes.db.DAO');

/* These constants are used for user-selectable search fields. */
define('PT_FIELD_PRIVCODE', 	'private_code');
define('PT_FIELD_PUBCODE', 		'public_code');
define('PT_FIELD_NONE', 		null);

class PixelTagDAO extends DAO {
	/** @var string Name of parent plugin */
	var $parentPluginName;

	/**
	 * Constructor.
	 */
	function PixelTagDAO($parentPluginName) {
		parent::DAO();
		$this->parentPluginName = $parentPluginName;
	}

	/**
	 * Retrieve a pixel tag by pixel tag ID.
	 * @param $pixelTagId int
	 * @param $contextId int optional
	 * @return PixelTag
	 */
	function getPixelTag($pixelTagId, $contextId = null) {
		$params = array($pixelTagId);
		if ($contextId) $params[] = $contextId;

		$result = $this->retrieve(
			'SELECT * FROM pixel_tags WHERE pixel_tag_id = ?' . ($contextId ? ' AND context_id = ?' : ''),
			$params
		);

		$returner = null;
		if ($result->RecordCount() != 0) {
			$returner = $this->_fromRow($result->GetRowAssoc(false));
		}
		$result->Close();
		return $returner;
	}

	/**
	 * Construct a new data object corresponding to this DAO.
	 * @return PixelTag
	 */
	function newDataObject() {
		$vgWortPlugin = PluginRegistry::getPlugin('generic', $this->parentPluginName);
		$vgWortPlugin->import('classes.PixelTag');
		return new PixelTag();
	}

	/**
	 * Internal function to return a PixelTag object from a row.
	 * @param $row array
	 * @return PixelTag
	 */
	function _fromRow($row) {
		$pixelTag = $this->newDataObject();
		$pixelTag->setId($row['pixel_tag_id']);
		$pixelTag->setContextId($row['context_id']);
		$pixelTag->setSubmissionId($row['submission_id']);
		$pixelTag->setPrivateCode($row['private_code']);
		$pixelTag->setPublicCode($row['public_code']);
		$pixelTag->setDomain($row['domain']);
		$pixelTag->setDateOrdered($this->datetimeFromDB($row['date_ordered']));
		$pixelTag->setDateAssigned($this->datetimeFromDB($row['date_assigned']));
		$pixelTag->setDateRegistered($this->datetimeFromDB($row['date_registered']));
		$pixelTag->setDateRemoved($this->datetimeFromDB($row['date_removed']));
		$pixelTag->setStatus($row['status']);
		$pixelTag->setTextType($row['text_type']);

		HookRegistry::call('PixelTagDAO::_fromRow', array(&$pixelTag, &$row));

		return $pixelTag;
	}

	/**
	 * Insert a new PixelTag.
	 * @param $pixelTag PixelTag
	 * @return int
	 */
	function insertObject($pixelTag) {
		
		$ret = $this->update(
			sprintf('
				INSERT INTO pixel_tags
					(context_id,
					submission_id,
					private_code,
					public_code,
					domain,
					date_ordered,
					date_assigned,
					date_registered,
					date_removed,
					status,
					text_type)
				VALUES
					(?, ?, ?, ?, ?, %s, %s, %s, %s, ?, ?)',
				$this->datetimeToDB($pixelTag->getDateOrdered()),
				$this->datetimeToDB($pixelTag->getDateAssigned()),
				$this->datetimeToDB($pixelTag->getDateRegistered()),
				$this->datetimeToDB($pixelTag->getDateRemoved())
				),
			array(
				$pixelTag->getContextId(),
				$pixelTag->getSubmissionId(),
				$pixelTag->getPrivateCode(),
				$pixelTag->getPublicCode(),
				$pixelTag->getDomain(),
				$pixelTag->getStatus(),
				$pixelTag->getTextType()
			)
		);
		$pixelTag->setId($this->getInsertId());
		return $pixelTag->getId();
	}

	
	
	
	/**
	 * Update an existing pixel tag.
	 * @param $pixelTag PixelTag
	 */
	function updateObject($pixelTag) {
		$this->update(
			sprintf('UPDATE pixel_tags
				SET
					context_id = ?,
					submission_id = ?,
					private_code = ?,
					public_code = ?,
					domain = ?,
					date_ordered = %s,
					date_assigned = %s,
					date_registered = %s,
					date_removed = %s,
					status = ?,
					text_type = ?
					WHERE pixel_tag_id = ?',
				$this->datetimeToDB($pixelTag->getDateOrdered()),
				$this->datetimeToDB($pixelTag->getDateAssigned()),
				$this->datetimeToDB($pixelTag->getDateRegistered()),
				$this->datetimeToDB($pixelTag->getDateRemoved())
				),
			array(
				$pixelTag->getContextId(),
				$pixelTag->getSubmissionid(),
				$pixelTag->getPrivateCode(),
				$pixelTag->getPublicCode(),
				$pixelTag->getDomain(),
				$pixelTag->getStatus(),
				$pixelTag->getTextType(),
				$pixelTag->getId()
			)
		);
	}

	/**
	 * Delete a pixel tag.
	 * @param $pixelTag PixelTag
	 */
	function deleteObject($pixelTag) {
		$this->deletePixelTagById($pixelTag->getId());
	}

	/**
	 * Delete a pixel tag by pixel tag ID.
	 * @param $pixelTag int
	 */
	function deletePixelTagById($pixelTagId) {
		$this->update('DELETE FROM pixel_tags WHERE pixel_tag_id = ?', $pixelTagId);
	}

	/**
	 * Delete pixel tags by context ID.
	 * @param $contextId int
	 */
	function deletePixelTagsByContext($contextId) {
		$pixelTags = $this->getPixelTagsByContextId($contextId);

		while (!$pixelTags->eof()) {
			$pixelTag = $pixelTags->next();
			$this->deletePixelTagById($pixelTag->getId());
		}
	}

	/**
	 * Retrieve all pixel tags matching a particular context ID.
	 * @param $contextId int
	 * @param $searchType int optional, which field to search
	 * @param $search string optional, string to match
	 * @param $status int optional, status to match
	 * @param $rangeInfo object optional, DBRangeInfo object describing range of results to return
	 * @param $sortBy string optional, column name the results should be ordered by
	 * @param $sortDirection int optional, ascending (SORT_DIRECTION_ASC) or descending (SORT_DIRECTION_DESC)
	 * @return DAOResultFactory containing matching PixelTag
	 */
	function getPixelTagsByContextId($contextId, $searchType = null, $search = null, $status = null, $rangeInfo = null, $sortBy = 'pixel_tag_id', $sortDirection = SORT_DIRECTION_ASC) {
		$sql = 'SELECT DISTINCT * FROM pixel_tags ';
		$paramArray = array();

		switch ($searchType) {
			case PT_FIELD_PRIVCODE:
				$sql .= ' WHERE LOWER(private_code) LIKE LOWER(?)';
				$paramArray[] = "%$search%";
				break;
			case PT_FIELD_PUBCODE:
				$sql .= ' WHERE LOWER(public_code) LIKE LOWER(?)';
				$paramArray[] = "%$search%";
				break;
			default:
				$searchType = null;
		}

		if (empty($searchType)) {
			$sql .= ' WHERE';
		} else {
			$sql .= ' AND';
		}

		if ($status != null) {
			$sql .= ' status = ? AND';
			$paramArray[] = (int) $status;
		}

		$sql .= ' context_id = ? ORDER BY ' . $sortBy . ' ' . $this->getDirectionMapping($sortDirection);
		$paramArray[] = (int) $contextId;

		$result = $this->retrieveRange($sql, $paramArray, $rangeInfo);

		return new DAOResultFactory($result, $this, '_fromRow');
	}

	/**
	 * Retrieve a pixel tag for a context by submission ID.
	 * @param $contextId int
	 * @param $submissionId int
	 * @return PixelTag
	 */
	function getPixelTagBySubmissionId($contextId, $submissionId) {
		$result = $this->retrieve(
			'SELECT *
			FROM pixel_tags
			WHERE submission_id = ?
			AND context_id = ?',
			array(
				$submissionId,
				$contextId
			)
		);

		$returner = null;
		if ($result->RecordCount() != 0) {
			$returner = $this->_fromRow($result->GetRowAssoc(false));
		}
		$result->Close();
		return $returner;
	}

	/**
	 * Retrieve the next available pixel tag for a context.
	 * @param $contextId int
	 * @return PixelTag
	 */
	function getAvailablePixelTag($contextId) {
		$result = $this->retrieveLimit(
			'SELECT *
			FROM pixel_tags
			WHERE context_id = ? AND submission_id IS NULL AND date_assigned IS NULL AND status = ?
			ORDER BY date_ordered',
			array(
				(int)$contextId,
				PT_STATUS_AVAILABLE
			),
			1
		);
		$returner = null;
		if (isset($result) && $result->RecordCount() != 0) {
			$returner = $this->_fromRow($result->GetRowAssoc(false));
		}
		$result->Close();
		return $returner;
	}

	/**
	 * Retrieve pixel tags status counts for a context.
	 * @param $contextId int
	 * @param $status int optional, pixel tag status to match
	 * @return int
	 */
	function getPixelTagsStatusCount($contextId, $status = null) {
		$vgWortPlugin = PluginRegistry::getPlugin('generic', $this->parentPluginName);
		$vgWortPlugin->import('classes.PixelTag');

		$sql = 'SELECT COUNT(*)
				FROM pixel_tags
				WHERE context_id = ?';
		$paramArray = array((int)$contextId);

		if ($status) {
			$sql .= ' AND status = ?';
			$paramArray[] = (int) $status;
		}

		$result = $this->retrieve($sql, $paramArray);
		return isset($result->fields[0]) ? $result->fields[0] : 0;
	}

	/**
	 * Retrieve all pixel tags status counts for a context.
	 * @param $contextId int
	 * @return array, status as index
	 */
	function getStatusCounts($contextId) {
		$vgWortPlugin = PluginRegistry::getPlugin('generic', $this->parentPluginName);
		$vgWortPlugin->import('classes.PixelTag');
		$counts = array();

		$counts[PT_STATUS_AVAILABLE] = $this->getPixelTagsStatusCount($contextId, PT_STATUS_AVAILABLE);
		$counts[PT_STATUS_UNREGISTERED] = $this->getPixelTagsStatusCount($contextId, PT_STATUS_UNREGISTERED);
		$counts[PT_STATUS_REGISTERED] = $this->getPixelTagsStatusCount($contextId, PT_STATUS_REGISTERED);

		return $counts;
	}

	/**
	 * Get the ID of the last inserted pixel tag.
	 * @return int
	 */
	function getInsertId() {
		return $this->_getInsertId('pixel_tags', 'pixel_tag_id');
	}
}

?>
