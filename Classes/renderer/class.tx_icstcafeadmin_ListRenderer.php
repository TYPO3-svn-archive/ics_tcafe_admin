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
 *   53: class tx_icstcafeadmin_ListRenderer extends tx_icstcafeadmin_CommonRenderer
 *   72:     function __construct($pi, tslib_cObj $cObj, $table, array $fields, array $fieldLabels, array $lConf)
 *   86:     public function render(array $rows)
 *   97:     private function renderListEmpty()
 *  111:     private function renderList(array $rows)
 *  154:     private function renderHeaderTitles(&$markers)
 *  174:     private function renderRowFields(array $row, &$markers)
 *  195:     private function renderRowActions(array $row, &$markers)
 *  209:     private function getListGetPageBrowser()
 *
 * TOTAL FUNCTIONS: 8
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */


/**
 * Class 'tx_icstcafeadmin_ListRenderer' for the 'ics_tcafe_admin' extension.
 * Render the list view
 *
 * @author	Tsi YANG <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icstcafeadmin
 */
class tx_icstcafeadmin_ListRenderer extends tx_icstcafeadmin_CommonRenderer {
	private $headersId = array();

	private $fields;
	private $labelFields;

	private static $view = 'viewList';

	/**
	 * Constructor
	 *
	 * @param	tx_icstcafeadmin_pi1		$pi: Instance of tx_icstcafeadmin_pi1
	 * @param	tslib_cObj		$cObj: tx_icstcafeadmin_pi1 cObj
	 * @param	string		$table: The tablename
	 * @param	array		$fields: Array of fields
	 * @param	array		$fieldLabels: Associative array of fields labels like field=>labelfield
	 * @param	array		$lConf: Typoscript configuration
	 * @return	void
	 */
	function __construct($pi, tslib_cObj $cObj, $table, array $fields, array $fieldLabels, array $lConf) {
		$this->table = $table;
		$this->fields = $fields;
		$this->fieldLabels = $fieldLabels;

		parent::__construct($pi, $cObj, $table, $lConf);
	}

	/**
	 * Render the view
	 *
	 * @param	array		$rows: Table rows
	 * @return	string		HTML list content
	 */
	public function render(array $rows) {
		if (!empty($rows))
			return $this->renderList($rows);
		return $this->renderListEmpty();
	}

	/**
	 * Render empty list content
	 *
	 * @return	string		HTML empty list content
	 */
	private function renderListEmpty() {
		$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_RESULTS_LIST_EMPTY###');
		$markers = array(
			'TEXT_LISTEMPTY' => $this->getLL('empty_list', 'Empty list', true),
		);
		return $this->cObj->substituteMarkerArray($template, $markers, '###|###', false, true);
	}

	/**
	 * Render list content
	 *
	 * @param	array		$rows : Table rows
	 * @return	string		HTML list content
	 */
	private function renderList(array $rows) {
		foreach ($this->fields as $field) {
			$this->headersId[$field] = uniqid($this->prefixId . '_headers');
		}

		$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_RESULTS_LIST###');
		$subparts = array();
		$markers = array();

		$locMarkers = array(
			'HEADERTITLES' => $this->renderHeaderTitles($markers),
			'HEADERACTIONS' => $this->getLl('actions', 'Actions', true),
		);
		$headerTemplate = $this->cObj->getSubpart($template, '###HEADER###');
		$headerContent = $this->cObj->substituteMarkerArray($headerTemplate, $locMarkers, '###|###');
		$subparts['###HEADER###'] = $this->cObj->substituteMarkerArray($headerContent, $markers, '###|###');

		$itemTemplate = $this->cObj->getSubpart($template, '###ROW###');
		foreach ($rows as $row) {
			$markers = array();
			$locMarkers = array();
			$locMarkers['FIELDS'] = $this->renderRowFields($row, $markers);
			$locMarkers['ACTIONS'] = $this->renderRowActions($row, $markers);
			$itemContent = $this->cObj->substituteMarkerArray($itemTemplate, $locMarkers, '###|###');
			$subparts['###ROW###'] .= $this->cObj->substituteMarkerArray($itemContent, $markers, '###|###');
		}

		$data = array(
			'newId' => 'New'.uniqid(),
			'table' => $this->table,
			'fields' => implode(',', $this->fields),
			'hidden' => $row['hidden'],
		);
		$cObj = t3lib_div::makeInstance('tslib_cObj');
		$cObj->start($data, 'TCAFE_Admin_actions');
		$cObj->setParent($this->cObj->data, $this->cObj->currentRecord);
		$markers = array(
			'PREFIXID' => $this->prefixId,
			'TABLENAME' => $this->table,
			'PAGEBROWSE' => $this->getListGetPageBrowser(),
			'CAPTION' => $this->getLL('caption_list', 'List rows', true),
			'NEW' => $cObj->stdWrap('', $this->conf['listActions.']['new.']),
		);

		$template = $this->cObj->substituteSubpartArray($template, $subparts);
		return $this->cObj->substituteMarkerArray($template, $markers, '###|###');
	}

	/**
	 * Render header titles
	 *
	 * @param	array		&$markers: The marker array
	 * @return	string		HTML header titles
	 */
	private function renderHeaderTitles(&$markers) {
		$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_RESULTS_HEADERTITLES###');
		$headerTitlesContent = '';
		foreach ($this->fields as $field) {
			$locMarkers = array(
				'HEADERID' => $this->headersId[$field],
				'FIELDLABEL' => $this->fieldLabels[$field],
			);
			$headerTitlesContent .= $this->cObj->substituteMarkerArray($template, $locMarkers, '###|###');
		}
		return $headerTitlesContent;
	}

	/**
	 * Render row fields
	 *
	 * @param	array		$row: The row
	 * @param	array		&$markers: The marker array
	 * @return	string		HTML row fields content
	 */
	private function renderRowFields(array $row, &$markers) {
		$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_RESULTS_ROW_FIELDS###');
		$content = '';
		// Hook for render row fields
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['renderListRowFields'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['renderListRowFields'] as $class) {
				$procObj = & t3lib_div::getUserObj($class);
				$content = $procObj->renderListRowFields($this->table, $this->fields, $template, $markers, $this->conf, $this);
			}
		}
		else {
			$genericFieldTemplate = $this->cObj->getSubpart($template, '###SUBPART_GENERIC###');
			foreach ($this->fields as $field) {
				$value = $this->renderValue($field, $row['uid'], $row[$field], self::$view);
				$locMarkers = array(
					'HEADERID' => $this->headersId[$field],
					'FIELDNAME' => $field,
				);
				if ($specificFieldTemplate = $this->cObj->getSubpart($template, '###ALT_SUBPART_' . strtoupper($field) . '###')) {
					$locMarkers[strtoupper($field) . '_LABEL'] = $this->fieldLabels[$field];
					$locMarkers[strtoupper($field) . '_VALUE'] = $value;
					$content .= $this->cObj->substituteMarkerArray($specificFieldTemplate, $locMarkers, '###|###');
				} 
				else {
					$locMarkers['FIELDLABEL'] = $this->fieldLabels[$field];
					$locMarkers['FIELDVALUE'] = $value;
					$content .= $this->cObj->substituteMarkerArray($genericFieldTemplate, $locMarkers, '###|###');
				}
			}
		}
		return $content;
	}

	/**
	 * Render row actions
	 *
	 * @param	array		$row: The row
	 * @param	array		&$markers: The marker array
	 * @return	string		HTML row fields content
	 */
	private function renderRowActions(array $row, &$markers) {
		$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_RESULTS_ROW_ACTIONS###');
		
		$data = array(
			'id' => $row['uid'],
			'newId' => 'New'.uniqid(),
			'table' => $this->table,
			'fields' => implode(',', $this->fields),
			'hidden' => $row['hidden'],
		);
		$cObj = t3lib_div::makeInstance('tslib_cObj');
		$cObj->start($data, 'TCAFE_Admin_actions');
		$cObj->setParent($this->cObj->data, $this->cObj->currentRecord);

		$locMarkers = array(
			'EDIT' => $cObj->stdWrap('', $this->conf['listActions.']['edit.']),
			'SINGLE' => $cObj->stdWrap('', $this->conf['listActions.']['single.']),
			'NEW' => $cObj->stdWrap('', $this->conf['listActions.']['new.']),
			'DELETE' => $cObj->stdWrap('', $this->conf['listActions.']['delete.']),
			'HIDE' => $cObj->stdWrap('', $this->conf['listActions.']['hide.']),
		);
		
		return $this->cObj->substituteMarkerArray($template, $locMarkers, '###|###');
	}

	/**
	 * Page browser
	 *
	 * @return	page		browser content
	 */
	private function getListGetPageBrowser() {
		// TODO: implements this
		return '';
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_tcafe_admin/pi1/class.tx_icstcafeadmin_ListRenderer.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_tcafe_admin/pi1/class.tx_icstcafeadmin_ListRenderer.php']);
}

?>