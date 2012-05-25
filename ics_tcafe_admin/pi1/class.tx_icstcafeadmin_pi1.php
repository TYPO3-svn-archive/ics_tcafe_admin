<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2012 In Cite Solution <techbnique@in-cite.net>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   62: class tx_icstcafeadmin_pi1 extends tslib_pibase
 *   88:     function main($content, $conf)
 *  195:     protected function init()
 *  246:     private function setTable()
 *  259:     private function setFields()
 *  301:     public function displayList()
 *  340:     public function displaySingle()
 *  360:     public function displayEdit()
 *  380:     public function displayNew()
 *  400:     public function displayValidatedForm()
 *  417:     public function displayErrorValidatedForm()
 *  431:     public function displayDelete()
 *  444:     public function displayHide()
 *  463:     private function getRecords()
 *  495:     private function getSingleRecord()
 *  515:     function saveDB()
 *  595:     public function deleteRecord($table, $rowUid)
 *  612:     public function hideRecord($table, $rowUid)
 *
 * TOTAL FUNCTIONS: 17
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

require_once(PATH_tslib.'class.tslib_pibase.php');

/**
 * Plugin 'Generate FE forms' for the 'ics_tcafe_admin' extension.
 *
 * @author	Tsi YANG <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icstcafeadmin
 */
class tx_icstcafeadmin_pi1 extends tslib_pibase {
	var $prefixId      = 'tx_icstcafeadmin_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_icstcafeadmin_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'ics_tcafe_admin';	// The extension key.

	var $templateFile = 'typo3conf/ext/ics_tcafe_admin/res/template.html';
	var $codes = array('LIST');

	var $defaultPage = 1;
	var $defaultSize = 20;

	private $fieldLabels = array();
	private $fields = array();
	private $table;

	private $groupBy = '';
	private $limit = '';
	private $orderBy = '';

	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The		content that is displayed on the website
	 */
	function main($content, $conf) {
		$this->conf = $conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->pi_USER_INT_obj = 1;	// Configuring so caching is not expected. This value means that no cHash params are ever set. We do this, because it's a USER_INT object!

		$this->pi_initPIflexForm();

		$content = '';
		
		// Hook plugin before init
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['process_beforeInit'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['process_beforeInit'] as $class) {
				$procObj = & t3lib_div::getUserObj($class);
				if (!$process = $procObj->process_beforeInit($content, $this->conf, $this)) {
					return $this->pi_wrapInBaseClass($content);
				}
			}
		}
		
		$this->init();

		$this->setTable();
		if (!$this->table) {
			tx_icstcafeadmin_debug::error('Table must not be empty.');
			return $this->pi_wrapInBaseClass($this->pi_getLL('data_not_available', 'Invalid table.', true));
		}
		$GLOBALS['TSFE']->includeTCA();
		t3lib_div::loadTCA($this->table);
		if (!$GLOBALS['TCA'][$this->table]) {
			tx_icstcafeadmin_debug::error('Table can not be loaded from TCA.');
			return $this->pi_wrapInBaseClass($this->pi_getLL('data_not_available', 'Invalid table ' . $this->table, true));
		}

		$this->setFields();

		// Hook plugin before init
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['process_afterInit'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['process_afterInit'] as $class) {
				$procObj = & t3lib_div::getUserObj($class);
				if (!$process = $procObj->process_afterInit($this->table, $this->fields, $this->fieldLabels, $content, $this->conf, $this)) {
					return $this->pi_wrapInBaseClass($content);
				}
			}
		}

		if ($this->showUid && in_array('SINGLE', $this->codes)) {
			try {
				$content = $this->displaySingle();
			} catch (Exception $e) {
				tx_icstcafeadmin_debug::error('Retrieves data failed: ' . $e);
			}
		}
		elseif ($this->showUid && in_array('EDIT', $this->codes)) {
			try {
				if ($this->piVars['valid']) {
					if ($this->saveDB()) {
						$content = $this->displayValidatedForm();
						if ($this->conf['displayFormAfterSaveDB'])
							$content .= $this->displayEdit();
					}
					else {
						$content = $this->displayErrorValidatedForm();
						$content .= $this->displayEdit();
					}
				}
				else {
					$content = $this->displayEdit();
				}
			} catch (Exception $e) {
				tx_icstcafeadmin_debug::error('Edit data failed: ' . $e);
			}
		}
		elseif ($this->newUid && in_array('NEW', $this->codes)) {
			try {
				if ($this->piVars['valid']) {
					if ($this->saveDB()) {
						$content = $this->displayValidatedForm();
						if ($this->conf['displayFormAfterSaveDB'])
							$content .= $this->displayNew();
					}
					else {
						$content = $this->displayErrorValidatedForm();
						$content .= $this->displayNew();
					}
				}
				else {
					$content = $this->displayNew();
				}
			} catch (Exception $e) {
				tx_icstcafeadmin_debug::error('New data failed: ' . $e);
			}
		}
		elseif ($this->showUid && in_array('DELETE', $this->codes)) {
			try {
				$row = $this->getSingleRecord();
				$this->deleteRecord($this->table, $this->showUid);
				$content = $this->displayDelete($row);
			} catch (Exception $e) {
				tx_icstcafeadmin_debug::error('Delete data failed: ' . $e);
			}
		}
		elseif ($this->showUid && in_array('HIDE', $this->codes)) {
			try {
				$this->hideRecord($this->table, $this->showUid);
				$content = $this->displayHide();
			} catch (Exception $e) {
				tx_icstcafeadmin_debug::error('Hide data failed: ' . $e);
			}
		}
		elseif (count(array_intersect(array('SEARCH', 'LIST'), $this->codes)) > 0) {
			try {
				$content = $this->displayList();
			} catch (Exception $e) {
				tx_icstcafeadmin_debug::error('Retrieves data list failed: ' . $e);
			}
		}
		else {
			tx_icstcafeadmin_debug::warning('Any mode is set.');
			return '';
		}

		return $this->pi_wrapInBaseClass($content);
	}

	/**
	 * Initialize the plugin
	 *
	 * @return	boolean
	 */
	protected function init() {
		// Get template code
		$templateFile = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'templateFile', 'general');
		$templateFile = $templateFile ? $templateFile : $this->conf['template'];
		$templateFile = $templateFile ? $templateFile : $this->templateFile;
		$this->templateCode = $this->cObj->fileResource($templateFile);

		// Get display mode
		$codes = array();
		if (isset($this->piVars['showUid'])) {
			$this->showUid = $this->piVars['showUid'];
		}
		if ($this->piVars['mode'])
			$codes = array($this->piVars['mode']);
		$codes = $codes? $codes: t3lib_div::trimExplode(',', $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'what_to_display', 'general'), true);
		if (empty($codes))
			$codes = t3lib_div::trimExplode(',', $this->conf['view.']['modes'], true);
		$this->codes = array_unique($codes);

		$PIDitemDisplay = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'PIDitemDisplay', 'general');
		if ($PIDitemDisplay)
			$this->conf['view.']['PIDitemDisplay'] = $PIDitemDisplay;
		if (!$this->conf['view.']['PIDitemDisplay'])
			$this->conf['view.']['PIDitemDisplay'] = $GLOBALS['TSFE']->id;

		// Get pid storage
		if ($this->cObj->data['pages'])
			$pids = t3lib_div::trimExplode(',', $this->cObj->data['pages'], true);
		if ($this->piVars['pidStorage'])
			$pids = t3lib_div::trimExplode(',', $this->piVars['pidStorage'], true);
		$this->storage = $pids? $pids: array(0);

		$this->newUid = $this->piVars['newUid'];

			// Get page size
		// $size = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'size', 'general');
		// $this->conf['view.']['size'] = $size ? $size : $this->conf['view.']['size'];
		// $this->conf['view.']['size'] = $this->conf['view.']['size'] ? $this->conf['view.']['size'] : $this->defaultSize;
		// $this->limit = $this->conf['view.']['size']? $this->conf['view.']['size']: '';

		if (isset($this->piVars['page']))
			$this->conf['view.']['page'] = $this->piVars['page'] + 1;
		if (!$this->conf['view.']['page'])
			$this->conf['view.']['page'] = 1;
	}

	/**
	 * Sets the table
	 *
	 * @return	void
	 */
	private function setTable() {
		$table = $this->piVars['table'];
		$table = $table? $table: $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'tablename', 'table');
		$table = $table? $table: $this->conf['table.']['tablename'];
		$this->table = $table;
	}

	/**
	 * Sets fields
	 * Sets fields labels
	 *
	 * @return	void
	 */
	private function setFields() {
		$fields = t3lib_div::trimExplode(',', $this->piVars['fields'], true);
		$fields = !empty($fields)? $fields: t3lib_div::trimExplode(',', $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'fields', 'table'), true);

		$fields = !empty($fields)? $fields: t3lib_div::trimExplode(',', $this->conf['table.']['fields'], true);

		$fields_confTS = t3lib_div::trimExplode(',', $this->conf['table.']['fields'], true);
		$labels_confTS = t3lib_div::trimExplode(',', $this->conf['table.']['fieldLabels'], true);
		$fieldLabels = array();
		foreach ($labels_confTS as $key=>$label) {
			if ($fields_confTS[$key])
				$fieldLabels[$fields_confTS[$key]] = $label;
		}

		$columns = $GLOBALS['TCA'][$this->table]['columns'];
		if (empty($fields)) {
			$fields = array_keys($columns);
		}
		if ($this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'withUid', 'table') && !in_array('uid', $fields))
			$fields = array_merge(array('uid'), $fields);

		$this->fields = array();
		$this->fieldLabels = array();
		foreach ($fields as $field) {
			if ($columns[$field] || $field=='uid') {
				$this->fields[] = $field;

				$label = $this->piVars[$fieldname . 'Label'];
				$label = $label? $label: $this->pi_getFFvalue($this->cObj->data['pi_flexform'], $fieldname . 'Label', 'table');
				$label = $label? $label: $fieldLabels[$field];
				$label = $label? $label: $GLOBALS['TSFE']->sL($GLOBALS['TCA'][$this->table]['columns'][$field]['label']);
				$label = $label? $label: $field;
				$this->fieldLabels[$field] = $label;
			}
		}
	}

	/**
	 * Display the list view of table rows
	 *
	 * @return	string		HTML content for list view
	 */
	public function displayList() {
		if (in_array('SEARCH', $this->codes)) {
			$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_SEARCH###');
			// TODO : Implements search code
			// Insert hook?
		}
		else {
			$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_RESULTS_NOSEARCH###');
		}
		if (in_array('LIST', $this->codes)) {
			$renderList = t3lib_div::makeInstance(
				'tx_icstcafeadmin_ListRenderer',
				$this,
				$this->table,
				$this->fields,
				$this->fieldLabels,
				$this->conf
			);
			$renderList->init();
			$locMarkers['RESULT_LIST'] = $renderList->render($this->getRecords());
		}
		else {
			$locMarkers['RESULT_LIST'] = '';
		}
		$template = $this->cObj->substituteMarkerArray($template, $locMarkers, '###|###');

		$markers = array(
			'PREFIXID' => $this->prefixId,
		);
		$template = $this->cObj->substituteMarkerArray($template, $markers, '###|###');
		return $template;
	}

	/**
	 * Display the single view of table row
	 *
	 * @return	string		HTML content for single view
	 */
	public function displaySingle() {
		$renderSingle = t3lib_div::makeInstance(
			'tx_icstcafeadmin_SingleRenderer',
			$this,
			$this->table,
			$this->fields,
			$this->fieldLabels,
			$this->getSingleRecord(),
			$this->conf
		);
		$renderSingle->init();
		return $renderSingle->render();
	}

	/**
	 * Display the edit form of the row
	 *
	 * @return	string		HTML content for edit form
	 */
	public function displayEdit() {
		$renderEdit = t3lib_div::makeInstance(
			'tx_icstcafeadmin_FormRenderer',
			$this,
			$this->table,
			$this->fields,
			$this->fieldLabels,
			$this->showUid,
			$this->conf
		);
		$renderEdit->init();
		return $renderEdit->render();
	}

	/**
	 * Display the new form of new row
	 *
	 * @return	string		HTML content for new form
	 */
	public function displayNew() {
		$renderEdit = t3lib_div::makeInstance(
			'tx_icstcafeadmin_FormRenderer',
			$this,
			$this->table,
			$this->fields,
			$this->fieldLabels,
			null,
			$this->conf
		);
		$renderEdit->init();
		return $renderEdit->render();
	}

	/**
	 * Display submitted form
	 *
	 * @return	string		HTML content for new form
	 */
	public function displayValidatedForm() {
		$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_VALIDATED_FORM###');
		if ($this->newUid)
			$text = $this->pi_getLL('record_added', 'New record is added.', true);
		else
			$text = $this->pi_getLL('record_updated', 'New record is updated.', true);
		$markers = array(
			'VALIDATED_FORM_TEXT' => $text,
		);
		return $this->cObj->substituteMarkerArray($template, $markers, '###|###');
	}

	/**
	 * Display submitted form
	 *
	 * @return	string		HTML content for new form
	 */
	public function displayErrorValidatedForm() {
		$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_ERROR_VALIDATED_FORM###');
		$markers = array(
			'ERROR_VVALIDATED_FORM_TEXT' => $this->pi_getLL('error_validated_form', 'Form is properly filled.', true),
		);
		return $this->cObj->substituteMarkerArray($template, $markers, '###|###');
	}


	/**
	 * Display delete record
	 *
	 * @return	string		HTML content
	 */
	public function displayDelete($previousRow) {
		$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_DELETED_RECORD###');
		$markers = array(
			'DELETED_RECORD_TEXT' => sprintf($this->pi_getLL('deleted_record', 'Record\'s %2$s is deleted.', true), $this->showUid, $previousRow[$GLOBALS['TCA'][$this->table]['ctrl']['label']]),
		);
		return $this->cObj->substituteMarkerArray($template, $markers, '###|###');
	}

	/**
	 * Display hide record
	 *
	 * @return	string		HTML content
	 */
	public function displayHide() {
		$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_HIDDEN_SHOWN_RECORD###');
		$row = $this->getSingleRecord();
		if ($row['hidden'])
			$text = sprintf($this->pi_getLL('hidden_record', 'Record\'s %2$s is hidden.', true), $this->showUid, $row[$GLOBALS['TCA'][$this->table]['ctrl']['label']]) ;
		else
			$text = sprintf($this->pi_getLL('shown_record', 'Record\'s %2$s is visible.', true), $this->showUid, $row[$GLOBALS['TCA'][$this->table]['ctrl']['label']]);

		$markers = array(
			'HIDDEN_SHOWN_RECORD_TEXT' => $text,
		);
		return $this->cObj->substituteMarkerArray($template, $markers, '###|###');
	}

	/**
	 * Retrieves rows
	 *
	 * @return	mixed		The table rows
	 */
	private function getRecords() {
		$requestFields = $this->fields;
		if (!in_array('uid', $requestFields))
			$requestFields = array_merge(array('uid'), $requestFields);
		if (!in_array('hidden', $requestFields))
			$requestFields = array_merge(array('hidden'), $requestFields);

			// TODO: implements select on pid storage on displayNew and decomments this
		// $addWhere_storage = '';
		// if (!empty($this->storage) && ((count($this->storage)>1) || count($this->storage)==1 && $this->storage[0]>0))
			$addWhere_storage = ' AND pid IN(' . implode(',', $this->storage) . ')';

		$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			implode(',', $requestFields),
			$this->table,
			'deleted = 0' . $addWhere_storage,
			$this->groupBy,
			$this->orderBy,
			$this->limit,
			'uid'
		);

		// TODO : insert here the hook to get rows with complex resquest

		return $rows;
	}

	/**
	 * Retrieves single row
	 *
	 * @return	mixed		The record
	 */
	private function getSingleRecord() {
		$requestFields = $this->fields;
		if (!in_array('uid', $requestFields))
			$requestFields = array_merge(array('uid'), $requestFields);
		$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			implode(',', $requestFields),
			$this->table,
			'deleted = 0 AND uid=' . $this->showUid,
			'',
			'',
			'1'
		);
		return $rows[0];
	}

	/**
	 * Save in DB
	 *
	 * @return	boolean		The result process
	 */
	function saveDB() {
		$this->ctrlEntries = t3lib_div::makeInstance(
			'tx_icstcafeadmin_controlForm',
			$this,
			$this->table,
			($this->showUid? $this->showUid: 0),
			$this->fields,
			$this->conf
		);


		if ($this->ctrlEntries->controlEntries()) {
			$dbTools = t3lib_div::makeInstance('tx_icstcafeadmin_DBTools', $this, $this->conf);
			$fields = array_diff($this->fields, array('uid'));
			$dataArray = $dbTools->process_valuesToDB($this->table, ($this->showUid? $this->showUid: 0), $fields, $this->piVars);

			// var_dump($dataArray);

			if ($this->newUid) { // Insert new record
				$result = $this->cObj->DBgetInsert(
					$this->table,
					$this->storage[0],
					$dataArray,
					implode(',', $fields),
					true
				);

			} else {	// Update record
				$result = $this->cObj->DBgetUpdate(
					$this->table,
					$this->showUid,
					$dataArray,
					implode(',', $fields),
					true
				);
			}

			if ($select_MM = $dbTools->getSelect_MM()) {
				foreach ($select_MM as $field=>$foreign_uids) {
					$config = $GLOBALS['TCA'][$this->table]['columns'][$field]['config'];
					$GLOBALS['TYPO3_DB']->exec_DELETEquery(
						$config['MM'],
						'`uid_local` =' . $this->piVars['showUid']
					);

					foreach ($foreign_uids as $foreign) {
						$dataArray = array(
							'uid_local' => $this->piVars['showUid'],
							'uid_foreign' => $foreign,
						);
						if ($config['MM_opposite_field']) {
							$dataArray = array(
								'uid_foreign' => $this->piVars['showUid'],
								'uid_local' => $foreign,
							);
						}
						$GLOBALS['TYPO3_DB']->exec_INSERTquery(
							$config['MM'],
							$dataArray
						);
					}

				}
			}

			return true;

		}

		return false;
	}

	/**
	 * Delete record
	 *
	 * @param	string		$table	The tablename
	 * @param	int		$rowUid	The record's uid
	 * @return	mixed		Result from handler
	 */
	public function deleteRecord($table, $rowUid) {
		$delete = false;
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['deleteRecord'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['deleteRecord'] as $class) {
				$procObj = & t3lib_div::getUserObj($class);
				if ($process = $procObj->deleteRecord($table, $rowUid, $this->conf, $this, $delete))
					break;
			}
		}
		if ($process)
			return $delete;
			
		return $this->cObj->DBgetUpdate(
			$table,
			$rowUid,
			array('deleted' => '1'),
			'deleted',
			true
		);
	}

	/**
	 * Hide record
	 *
	 * @param	string		$table	The tablename
	 * @param	int		$rowUid	The record's uid
	 * @return	mixed		Result from handler
	 */
	public function hideRecord($table, $rowUid) {
		$row = $this->getSingleRecord();
		if ($row['hidden']) {
			return $this->cObj->DBgetUpdate(
				$table,
				$rowUid,
				array('hidden' => '0'),
				'hidden',
				true
			);
		}
		return $this->cObj->DBgetUpdate(
			$table,
			$rowUid,
			array('hidden' => '1'),
			'hidden',
			true
		);
	}


}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_tcafe_admin/pi1/class.tx_icstcafeadmin_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_tcafe_admin/pi1/class.tx_icstcafeadmin_pi1.php']);
}

?>