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
 *   61: class tx_icstcafeadmin_FormRenderer extends tx_icstcafeadmin_CommonRenderer
 *   88:     function __construct($pi, tslib_cObj $cObj, $table, $row, array $fields, array $fieldLabels, array $lConf)
 *  102:     public function render()
 *  125:     private function renderPIDStorage()
 *  140:     private function renderFormFields()
 *  171:     function renderEntries()
 *  185:     public function handleFormField($field)
 *  213:     private function handleFormField_typeInput($field, $config)
 *  248:     private function handleFormField_typeText($field, $config)
 *  274:     private function handleFormField_typeCheck($field, $config)
 *  293:     private function handleFormField_typeCheck_item($field, $config, $col=null)
 *  321:     function getEntryValue($field)
 *  337:     private function getInputTagSize($size)
 *  350:     private function getTextareaTagCols($size)
 *  362:     private function getTextareaTagRows($size)
 *  376:     private function getInputTagChecked($value, $col=null)
 *  392:     private static function includeLibDatepicker()
 *
 * TOTAL FUNCTIONS: 16
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */


/**
 * Class 'tx_icstcafeadmin_FormRenderer' for the 'ics_tcafe_admin' extension.
 * Render the form view
 *
 * @author	Tsi YANG <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icstcafeadmin
 */
class tx_icstcafeadmin_FormRenderer extends tx_icstcafeadmin_CommonRenderer {
	private $headersId = array();

	private $fields;
	private $labelFields;
	private $row = null;

	private static $view = 'viewForm';

	private static $datepickerIncluded = false;

	var $maxInputWidth = 48; // The maximum abstract value for input fields
	var $maxTextareaCols	= 48;			// The maximum abstract value for textareas
	var $maxTextareaRows	= 20;			// The maximum abstract value for textareas

	/**
	 * Constructor
	 *
	 * @param	tx_icstcafeadmin_pi1		$pi: Instance of tx_icstcafeadmin_pi1
	 * @param	tslib_cObj		$cObj: tx_icstcafeadmin_pi1 cObj
	 * @param	string		$table: The tablename
	 * @param	array		$row: The row
	 * @param	array		$fields: Array of fields
	 * @param	array		$fieldLabels: Associative array of fields labels like field=>labelfield
	 * @param	array		$lConf: Typoscript configuration
	 * @return	void
	 */
	function __construct($pi, tslib_cObj $cObj, $table, $row, array $fields, array $fieldLabels, array $lConf) {
		$this->table = $table;
		$this->row = $row;
		$this->fields = $fields;
		$this->fieldLabels = $fieldLabels;

		parent::__construct($pi, $cObj, $table, $lConf);
	}

	/**
	 * Render the view
	 *
	 * @return	string		HTML list content
	 */
	public function render() {
		$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_FORM###');
		$markers = array(
			'URL' => t3lib_div::getIndpEnv('TYPO3_REQUEST_URL'),
			'PREFIXID' => $this->prefixId,
			'PIDSTORAGE' => $this->renderPIDStorage(),
			'FIELDS' => $this->renderFormFields(),
			'BTNVALID_NAME' => $this->prefixId . '[valid]',
			'BTNVALID_VALUE' => $this->getLL('valid', 'Valid', true),
			'ONCLICK_BTNVALID' => '',
			'BTNCANCEL_NAME' => $this->prefixId . '[cancel]',
			'BTNCANCEL_VALUE' => $this->getLL('cancel', 'Cancel', true),
			'ONCLICK_BTNCANCEL' => '',
			'BACKLINK' => '',
		);
		return $this->cObj->substituteMarkerArray($template, $markers, '###|###');
	}

	/**
	 * Render pid storage
	 *
	 * @return	int		The pid storage
	 */
	private function renderPIDStorage() {
		// TODO :
		//	si $this->row
		//		Alors on renvoit rien
		//	sinon
		//		On propose la sélection du pid storage pour le nouvel enregistrement

		return '';
	}

	/**
	 * Render form fields
	 *
	 * @return	string		HTML fields content
	 */
	private function renderFormFields() {
		$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_FORM_FIELDS###');
		$content = '';
		// Hook for render row fields
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['renderFormFields'])) {
			$markers = array(
				'PREFIXID' => $this->prefixId,
			);
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['renderFormFields'] as $class) {
				$procObj = & t3lib_div::getUserObj($class);
				$content = $procObj->renderFormFields($template, $markers, $this->conf, $this);
			}
		}
		else {
			$markers = array(
				'PREFIXID' => $this->prefixId,
				'FIELDSET_FIELDS' => $this->getLL('formFields_fieldset', 'Fields entries', true),
				'FIELDS_TITLE' => $this->getLL('formFields_title', 'Fields entries', true),
				'ENTRIES' => $this->renderEntries(),
			);
			$content = $this->cObj->substituteMarkerArray($template, $markers, '###|###');
		}
		return $content;
	}


	/**
	 * Render entries
	 *
	 * @return	string		HTML fields content
	 */
	function renderEntries() {
		$content = '';
		foreach ($this->fields as $field) {
			$content .= $this->handleFormField($field);
		}
		return $content;
	}

	/**
	 * Handles form field
	 *
	 * @param	string		$field: The field name
	 * @return	string		HTML form field content
	 */
	public function handleFormField($field) {
		$config = $GLOBALS['TCA'][$this->table]['columns'][$field]['config'];
		switch ($config['type']) {
			case 'input':
				$content =  $this->handleFormField_typeInput($field, $config);
				break;
			case 'text':
				$content =  $this->handleFormField_typeText($field, $config);
				break;
			case 'check':
				$content = $this->handleFormField_typeCheck($field, $config);
				break;
			// case 'selct':
				// break;
			// case 'group':
				// break;
			default:
				$content = '';
		}
		return $content;
	}

	/**
	 * Handles form field type input
	 *
	 * @param	string		$field: The field name
	 * @param	array		$config: the field conf
	 * @return	string		HTML form field content
	 */
	private function handleFormField_typeInput($field, $config) {
		$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_FORM_TEXT###');
		$markers = array(
			'PREFIXID' => $this->prefixId,
			'ITEM_ID' => $field,
			'FIELDLABEL' => $this->fieldLabels[$field],
			'FIELDNAME' => $field,
			'ITEM_NAME' => $this->prefixId . '[' . $field . ']',
			'ITEM_VALUE' => $this->getEntryValue($field),
			'SIZE' => $this->getInputTagSize($config['size']),
			'ONCHANGE' => '',
			'CTRL_MESSAGE' => '',
		);

		// $format = $this->fetchInputFieldFormat($config);

		// if ($format == 'date' || $format == 'datetime') {
			// self::includeLibDatepicker();
			// $template .= '<script>
				// $(function() {
				// $( "#' . $field . '" ).datepicker();
				// });
				// </script>';
		// }

		return $this->cObj->substituteMarkerArray($template, $markers, '###|###');
	}

	/**
	 * Handles form field type text
	 *
	 * @param	string		$field: The field name
	 * @param	array		$config: The field conf
	 * @return	string		HTML form field content
	 */
	private function handleFormField_typeText($field, $config) {
		$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_FORM_TEXTAREA###');
		$markers = array(
			'PREFIXID' => $this->prefixId,
			'ITEM_ID' => $field,
			'FIELDLABEL' => $this->fieldLabels[$field],
			'FIELDNAME' => $field,
			'ITEM_NAME' => $this->prefixId . '[' . $field . ']',
			'ITEM_VALUE' => $this->getEntryValue($field),
			'COLS' => $this->getTextareaTagCols($config['cols']),
			'ROWS' =>  $this->getTextareaTagCols($config['rows']),
			'WRAP' => '',
			'ONCHANGE' => '',
			'CTRL_MESSAGE' => '',
		);

		return $this->cObj->substituteMarkerArray($template, $markers, '###|###');
	}

	/**
	 * Handles form field type check
	 *
	 * @param	string		$field: The field name
	 * @param	array		$config: The field conf
	 * @return	string		HTML form field content
	 */
	private function handleFormField_typeCheck($field, $config) {
		if ($config['cols'] && $config['cols']>1) {
			$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_FORM_CHECK###');
			/* TODO : implements form field with tca field on type check and sevrals cols
				Makes loop to fill ###CHECK_ITEMS### markers like
					for ($col=0; $cols<$config['cols']; $cols++) {
						$locMarkers['CHECK_ITEMS'] .= handleFormField_typeCheck_item($field, $config, $cols);
						.....
					}
			*/
		}
		else {
			$content = $this->handleFormField_typeCheck_item($field, $config);
		}
		return $content;
	}

	/**
	 * [Describe function...]
	 *
	 * @param	[type]		$field: ...
	 * @param	[type]		$config: ...
	 * @param	[type]		$col: ...
	 * @return	[type]		...
	 */
	private function handleFormField_typeCheck_item($field, $config, $col=null) {
		$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_FORM_CHECK_ITEM###');
		$markers = array(
			'PREFIXID' => $this->prefixId,
			'ITEM_ID' => $field,
			'FIELDLABEL' => $this->fieldLabels[$field],
			'FIELDNAME' => $field,
			'ITEM_NAME' => $this->prefixId . '[' . $field . ']',
			'CHECKED' => $this->getInputTagChecked($this->getEntryValue($field)),
			'ONCHANGE' => '',
			'DISABLED' => '',
		);

		return $this->cObj->substituteMarkerArray($template, $markers, '###|###');
	}

	// private function FormField_typeRadio();
	// private function FormField_typeSelect();
	// private function FormField_typeGroup();


	/**
	 * Retrieves entry value
	 *
	 * @param	string		$field: The field name
	 * @return	string		The entry value
	 */
	function getEntryValue($field) {
		if ($this->pi->piVars['valid']) {
			$value = $this->pi->piVars[$field];
		}
		else {	// $this->pi->piVars['cancel'] or any submit
			$value = $this->renderValue($field, $this->row['uid'], $this->row[$field], self::$view);
		}
		return $value;
	}

	/**
	 * Retrieves input tag size
	 *
	 * @param	int		$size: The size
	 * @return	string		The input tag size
	 */
	private function getInputTagSize($size) {
		if ($size)
			$size = t3lib_div::intInRange($size, 5, $this->maxInputWidth, 30);

		return ($size? 'size="' . $size . '"': '');
	}

	/**
	 * Retrieves text tag cols
	 *
	 * @param	int		$size: The size
	 * @return	string		The text tag cols
	 */
	private function getTextareaTagCols($size) {
		if ($size)
			$size = t3lib_div::intInRange($size, 5, $this->maxTextareaCols, 30);

		return ($size? 'size="' . $size . '"': '');
	}
	/**
	 * Retrieves text tag rows
	 *
	 * @param	int		$size: The size
	 * @return	string		The text tag rows
	 */
	private function getTextareaTagRows($size) {
		if ($size)
			$size = t3lib_div::intInRange($size, 1, $this->maxTextareaRows, 5);

		return ($size? 'size="' . $size . '"': '');
	}

	/**
	 * Retrieves imput tag checked
	 *
	 * @param	int		$value: Checkboxes value
	 * @param	int		$cols: The col number
	 * @return	string		The input tag checked
	 */
	private function getInputTagChecked($value, $col=null) {
		$checked = '';
		if (is_null($col)) {
			$checked = $value? 'checked="checked"' : '';
		}
		else {
			$checked = ($value & pow(2, $col)) ? ' checked="checked"' : '';
		}
		return $checked;
	}

	/**
	 * Includes datepicker
	 *
	 * @return	void
	 */
	private static function includeLibDatepicker() {
		if (self::$datepickerIncluded)
			return;

		$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['ics_tcafe_admin']);
		$jquery_ui_css = $extConf['datepicker.']['jquery_ui_css'];
		$jquery_ui = $extConf['datepicker.']['jquery_ui'];
		$jquery = $extConf['datepicker.']['jquery'];

		if (!$jquery_ui_css || !$jquery_ui || !$jquery)
			return;

		$tags = array(
			'	<link rel="stylesheet" type="text/css" href="' . htmlspecialchars($jquery_ui_css) . '" media="all" />' . PHP_EOL,
			'	<script src="' . htmlspecialchars($jquery_ui) . '" type="text/javascript"></script>' . PHP_EOL,
			'	<script src="' . htmlspecialchars($jquery) . '" type="text/javascript"></script>' . PHP_EOL,
		);

		$GLOBALS['TSFE']->additionalHeaderData['datepicker'] = implode('', $tags);

		self::$datepickerIncluded = true;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_tcafe_admin/pi1/class.tx_icstcafeadmin_FormRenderer.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_tcafe_admin/pi1/class.tx_icstcafeadmin_FormRenderer.php']);
}

?>