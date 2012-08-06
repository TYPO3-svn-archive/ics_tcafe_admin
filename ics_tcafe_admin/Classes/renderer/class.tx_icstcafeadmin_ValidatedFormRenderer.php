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
 *   47: class tx_icstcafeadmin_ValidatedFormRenderer extends tx_icstcafeadmin_CommonRenderer
 *   58:     function __construct($pi_base, $table, array $fields, array $fieldLabels, array $conf)
 *   67:     public function render()
 *
 * TOTAL FUNCTIONS: 2
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */


/**
 * Class 'tx_icstcafeadmin_ValidatedFormRenderer' for the 'ics_tcafe_admin' extension.
 * Render the validated form view
 *
 * @author	Tsi YANG <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icstcafeadmin
 */
class tx_icstcafeadmin_ValidatedFormRenderer extends tx_icstcafeadmin_CommonRenderer {
	/**
 * Constructor
 *
 * @param	tx_icstcafeadmin_pi1		$pi_base: Instance of tx_icstcafeadmin_pi1
 * @param	string		$table: The tablename
 * @param	array		$fields: Array of fields
 * @param	array		$fieldLabels: Associative array of fields labels like field=>labelfield
 * @param	array		$conf: Typoscript configuration
 * @return	void
 */
	function __construct($pi_base, $table, array $fields, array $fieldLabels, array $conf) {
		parent::__construct($pi_base, $table, $fields, $fieldLabels, $conf);
	}

	/**
	 * Render the view
	 *
	 * @return	string		HTML list content
	 */
	public function render() {
		$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_VALIDATED_FORM###');
		if ($this->pi_base->newUid)
			$text = $this->getLL('record_added', 'New record is added.', true);
		else
			$text = $this->getLL('record_updated', 'New record is updated.', true);
		$markers = array(
			'VALIDATED_FORM_TEXT' => $text,
		);
		$criteria = $this->pi_base->piVars['criteria'];
		$markers['BACKLINK'] =  $this->renderBackLink();

		$cObj = t3lib_div::makeInstance('tslib_cObj');
		$cObj->start($this->cObjDataActions(), 'TCAFE_Admin_actions');
		$cObj->setParent($this->cObj->data, $this->cObj->currentRecord);
		$markers['SINGLE'] =  $cObj->stdWrap('', $this->conf['renderOptions.']['single.']);

		return $this->cObj->substituteMarkerArray($template, $markers, '###|###');
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_tcafe_admin/pi1/class.tx_icstcafeadmin_ValidatedFormRenderer.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_tcafe_admin/pi1/class.tx_icstcafeadmin_ValidatedFormRenderer.php']);
}

?>