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
 *   52: class tx_icstcafeadmin_ListRenderer extends tx_icstcafeadmin_CommonRenderer
 *   62:     public function render(array $rows)
 *   73:     private function renderListEmpty()
 *   91:     private function renderList(array $rows)
 *  139:     private function renderHeaderTitles(&$markers)
 *  159:     private function renderRowFields(array $row, &$markers)
 *  200:     private function renderRowActions(array $row, &$markers)
 *  223:     private function getListGetPageBrowser()
 *
 * TOTAL FUNCTIONS: 7
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
	private static $view = 'viewList';

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
		$cObj = t3lib_div::makeInstance('tslib_cObj');
		$cObj->start($this->cObjDataActions($row), 'TCAFE_Admin_actions');
		$cObj->setParent($this->cObj->data, $this->cObj->currentRecord);
		$markers = array(
			'TEXT_LISTEMPTY' => $this->getLL('empty_list', 'Empty list', true),
			'NEW' => $cObj->stdWrap('', $this->conf['renderOptions.']['new.']),
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

		$cObj = t3lib_div::makeInstance('tslib_cObj');
		$cObj->start($this->cObjDataActions($row), 'TCAFE_Admin_actions');
		$cObj->setParent($this->cObj->data, $this->cObj->currentRecord);
		$markers = array(
			'PREFIXID' => $this->prefixId,
			'TABLENAME' => $this->table,
			'PAGEBROWSE' => $this->getListGetPageBrowser(),
			'CAPTION' => $this->getLL('caption_list', 'List rows', true),
			'NEW' => $cObj->stdWrap('', $this->conf['renderOptions.']['new.']),
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
				'FIELDLABEL' => $this->cObj->stdWrap($this->fieldLabels[$field], $this->conf['renderConf.'][$this->table.'.'][$field.'.'][self::$view.'.']['label.']),
				'FIELD' => $field,
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
		// Hook for render row fields
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['renderListRowFields'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['renderListRowFields'] as $class) {
				$procObj = & t3lib_div::getUserObj($class);
				$content = $procObj->renderListRowFields($this->pi_base, $this->table, $this->fields, $this->fieldLabels, $row, $markers, $this->conf, $this);
				if (is_string($content))
					break;
			}
		}
		if (!isset($content) || is_bool($content)) {
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
					$locMarkers['FIELDLABEL'] = $this->cObj->stdWrap($this->fieldLabels[$field], $this->conf['renderConf.'][$this->table.'.'][$field.'.'][self::$view.'.']['label.']);
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

		$cObj = t3lib_div::makeInstance('tslib_cObj');
		$cObj->start($this->cObjDataActions($row), 'TCAFE_Admin_actions');
		$cObj->setParent($this->cObj->data, $this->cObj->currentRecord);

		$locMarkers = array(
			'EDIT' => $cObj->stdWrap('', $this->conf['renderOptions.']['optionList.']['edit.']),
			'SINGLE' => $cObj->stdWrap('', $this->conf['renderOptions.']['optionList.']['single.']),
			'NEW' => $cObj->stdWrap('', $this->conf['renderOptions.']['optionList.']['new.']),
			'DELETE' => $cObj->stdWrap('', $this->conf['renderOptions.']['optionList.']['delete.']),
			'HIDE' => $cObj->stdWrap('', $this->conf['renderOptions.']['optionList.']['hide.']),
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