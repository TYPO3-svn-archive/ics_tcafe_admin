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
 *   48: class tx_icstcafeadmin_SingleRenderer extends tx_icstcafeadmin_CommonRenderer
 *   63:     function __construct($pi_base, $table, array $fields, array $fieldLabels, array $row, array $conf)
 *   73:     public function render()
 *   88:     private function renderFields()
 *
 * TOTAL FUNCTIONS: 3
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */


/**
 * Class 'tx_icstcafeadmin_SingleRenderer' for the 'ics_tcafe_admin' extension.
 * Render the single view
 *
 * @author	Tsi YANG <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icstcafeadmin
 */
class tx_icstcafeadmin_SingleRenderer extends tx_icstcafeadmin_CommonRenderer {
	private $row;
	private static $view = 'viewSingle';

	/**
	 * Constructor
	 *
	 * @param	tx_icstcafeadmin_pi1		$pi_base: Instance of tx_icstcafeadmin_pi1
	 * @param	string		$table: The tablename
	 * @param	array		$fields: Array of fields
	 * @param	array		$fieldLabels: Associative array of fields labels like field=>labelfield
	 * @param	array		$row: The row
	 * @param	array		$conf: Typoscript configuration
	 * @return	void
	 */
	function __construct($pi_base, $table, array $fields, array $fieldLabels, array $row, array $conf) {
		$this->row = $row;
		parent::__construct($pi_base, $table, $fields, $fieldLabels, $conf);
	}

	/**
	 * Render the view
	 *
	 * @return	string		HTML list content
	 */
	public function render() {
		$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_SINGLE###');
		$markers = array(
			'PREFIXID' => $this->prefixId,
			'FIELDS' => $this->renderFields(),
			'BACKLINK' => '',
		);
		return $this->cObj->substituteMarkerArray($template, $markers, '###|###');
	}

	/**
	 * Render fields
	 *
	 * @return	string		HTML fields content
	 */
	private function renderFields() {
		$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_SINGLE_FIELDS###');
		$content = '';
		// Hook for render row fields
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['renderSingleFields'])) {
			$markers = array();
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['renderSingleFields'] as $class) {
				$procObj = & t3lib_div::getUserObj($class);
				$content = $procObj->renderSingleFields($this->pi_base, $this->table, $this->fields, $this->fieldLabels, $this->row, $markers, $this->conf, $this);
			}
		}
		else {
			$genericFieldTemplate = $this->cObj->getSubpart($template, '###SUBPART_GENERIC###');
			foreach ($this->fields as $field) {
				$value = $this->renderValue($field, $this->row['uid'], $this->row[$field], self::$view);
				$locMarkers = array(
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

}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_tcafe_admin/pi1/class.tx_icstcafeadmin_SingleRenderer.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_tcafe_admin/pi1/class.tx_icstcafeadmin_SingleRenderer.php']);
}

?>