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
 *   54: class tx_icstcafeadmin_pi1 extends tslib_pibase
 *   76:     function main($content, $conf)
 *  147:     protected function init()
 *  193:     private function setTable()
 *  206:     private function setFields()
 *  243:     public function displayList()
 *  282:     public function displaySingle()
 *  290:     public function displayEdit()
 *  298:     public function displayNew()
 *  306:     private function getRows()
 *
 * TOTAL FUNCTIONS: 9
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

		$this->init();

		$this->setTable();
		if (!$this->table) {
			tx_icstcafeadmin_debug::error('Table must not be empty.');
			return $this->pi_wrapInBaseClass($this->pi_getLL('data_not_available', 'Invalid table.', true));
		}
		t3lib_div::loadTCA($this->table);
		if (!$GLOBALS['TCA'][$this->table]) {
			tx_icstcafeadmin_debug::error('Table can not be loaded from TCA.');
			return $this->pi_wrapInBaseClass($this->pi_getLL('data_not_available', 'Invalid table ' . $this->table, true));
		}

		$this->setFields();

		if ($this->showUid && in_array('SINGLE', $this->codes)) {
			try {
				$content .= $this->displaySingle();
			} catch (Exception $e) {
				tx_icstcafeadmin_debug::error('Retrieves data set failed: ' . $e);
			}
		}
		elseif ($this->showUid && in_array('EDIT', $this->codes)) {
			try {
				$content .= $this->displayEdit();
			} catch (Exception $e) {
				tx_icstcafeadmin_debug::error('Edit data set failed: ' . $e);
			}
		}
		elseif ($this->newUid && in_array('NEW', $this->codes)) {
			try {
				$content .= $this->displayNew();
			} catch (Exception $e) {
				tx_icstcafeadmin_debug::error('New data set failed: ' . $e);
			}
		}
		elseif (count(array_intersect(array('SEARCH', 'LIST'), $this->codes)) > 0) {
			try {
				$content .= $this->displayList();
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
		$templateFile = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'template', 'main');
		$templateFile = $templateFile ? $templateFile : $this->conf['template'];
		$templateFile = $templateFile ? $templateFile : $this->templateFile;
		$this->templateCode = $this->cObj->fileResource($templateFile);

		// Get display mode
		$codes = array();
		if (isset($this->piVars['showUid'])) {
			$this->showUid = $this->piVars['showUid'];
		}
		$codes = t3lib_div::trimExplode(',', $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'what_to_display', 'main'), true);
		if (empty($codes))
			$codes = t3lib_div::trimExplode(',', $this->conf['view.']['modes'], true);
		$this->codes = array_unique($codes);

		$PIDitemDisplay = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'PIDitemDisplay', 'main');
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

			// Get page size
		// $size = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'size', 'main');
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
		$this->fields = array();
		$this->fieldLabels = array();
		foreach ($fields as $field) {
			if ($columns[$field]) {
				$this->fields[] = $field;

				$label = $this->piVars[$fieldname . 'Label'];
				$label = $label? $label: $this->pi_getFFvalue($this->cObj->data['pi_flexform'], $fieldname . 'Label', 'table');
				$label = $label? $label: $fieldLabels[$field];
				$label = $label? $label: $GLOBALS['TSFE']->sL($GLOBALS['TCA'][$this->table]['columns'][$field]['label']);
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
				$this->cObj,
				$this->table,
				$this->fields,
				$this->fieldLabels,
				$this->conf
			);
			$renderList->init();
			$locMarkers['RESULT_LIST'] = $renderList->render($this->getRows());
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
	}

	/**
	 * Display the edit form of the row
	 *
	 * @return	string		HTML content for edit form
	 */
	public function displayEdit() {
	}

	/**
	 * Display the new form of new row
	 *
	 * @return	string		HTML content for new form
	 */
	public function displayNew() {
	}

	/**
	 * Retrieves rows
	 *
	 * @return	array	The table rows
	 */
	private function getRows() {
		$requestFields = array_merge(array('uid'), $this->fields);
		$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			implode(',', $requestFields),
			$this->table,
			'deleted = 0 AND pid IN(' . implode(',', $this->storage) . ')',
			$this->groupBy,
			$this->orderBy,
			$this->limit,
			'uid'
		);
		
		// TODO : insert here the hook to get rows with complex resquest
		
		return $rows;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_tcafe_admin/pi1/class.tx_icstcafeadmin_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_tcafe_admin/pi1/class.tx_icstcafeadmin_pi1.php']);
}

?>