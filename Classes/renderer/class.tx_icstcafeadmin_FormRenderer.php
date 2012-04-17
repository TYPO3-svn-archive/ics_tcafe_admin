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
 *   63: class tx_icstcafeadmin_FormRenderer extends tx_icstcafeadmin_CommonRenderer
 *   90:     function __construct($pi, tslib_cObj $cObj, $table, $row, array $fields, array $fieldLabels, array $lConf)
 *  104:     public function render()
 *  127:     private function renderPIDStorage()
 *  142:     private function renderFormFields()
 *  173:     private function renderEntries()
 *  192:     public function handleFormField($field)
 *  222:     private function handleFormField_typeInput($field, array $config)
 *  260:     private function handleFormField_typeText($field, array $config)
 *  293:     private function handleFormField_typeCheck($field, array $config)
 *  319:     private function handleFormField_typeCheck_item($field, array $config, $col=null)
 *  352:     private function handleFormField_typeSelect($field, array $config)
 *  383:     private function handleFormField_typeSelect_single(array $items, $field, array $config)
 *  419:     private function handleFormField_typeSelect_multiple(array $items, $field, array $config)
 *  458:     private function getEntryValue($field, array $config)
 *  474:     public  function getDefaultEntryValue($field)
 *  521:     private function getSelectItemArray($field, array $config)
 *  556:     protected function initItemArray(array $config)
 *  575:     private static function includeLibDatepicker()
 *
 * TOTAL FUNCTIONS: 18
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
	 * Render the form view
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
	 * Render pid storage select
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
	 * Render form entries
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
	private function renderEntries() {
		$content = '';
		foreach ($this->fields as $field) {
			// TODO : hook
			// if hook
			//		Insert code here
			// else {
			$content .= $this->handleFormField($field);
			// }
		}
		return $content;
	}

	/**
	 * Generates form field
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
			case 'select':
				$content = $this->handleFormField_typeSelect($field, $config);
				break;
			// case 'group':
				// break;
			default:
				$content = '';
		}
		return $content;
	}

	/**
	 * Generates form element of the TCA type "input". This will render an input form field of type text.
	 *
	 * @param	string		$field: The field name
	 * @param	array		$config: TCA field conf
	 * @return	string		HTML form field content
	 */
	private function handleFormField_typeInput($field, array $config) {
		$size = t3lib_div::intInRange($config['size'], 5, $this->maxInputWidth, 30);
		$size = $size? 'size="' . $size . '"': '';

		$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_FORM_TEXT###');
		$markers = array(
			'PREFIXID' => $this->prefixId,
			'ITEM_ID' => $field,
			'FIELDLABEL' => $this->fieldLabels[$field],
			'FIELDNAME' => $field,
			'ITEM_NAME' => $this->prefixId . '[' . $field . ']',
			'ITEM_VALUE' => $this->getEntryValue($field, $config),
			'SIZE' => $size,
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
	 * Generates form element of the TCA type "text". This will render an textarea form field.
	 *
	 * @param	string		$field: The field name
	 * @param	array		$config: TCA field conf
	 * @return	string		HTML form field content
	 */
	private function handleFormField_typeText($field, array $config) {
		$cols = t3lib_div::intInRange($config['cols'], 5, $this->maxTextareaCols, 30);
		$cols =  $cols? 'cols="' . $cols . '"': '';
		$rows = t3lib_div::intInRange($config['rows'], 1, $this->maxTextareaRows, 5);
		$rows =  $rows? 'rows="' . $rows . '"': '';

		$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_FORM_TEXTAREA###');

		$markers = array(
			'PREFIXID' => $this->prefixId,
			'ITEM_ID' => $field,
			'FIELDLABEL' => $this->fieldLabels[$field],
			'FIELDNAME' => $field,
			'ITEM_NAME' => $this->prefixId . '[' . $field . ']',
			'ITEM_VALUE' => $this->getEntryValue($field, $config),
			'COLS' => $cols,
			'ROWS' =>  $rows,
			'WRAP' => '',
			'ONCHANGE' => '',
			'CTRL_MESSAGE' => '',
		);

		return $this->cObj->substituteMarkerArray($template, $markers, '###|###');
	}

	/**
	 * Generates form element of the TCA type "check".
	 * This will render an input form field or a group of input form field of type checkbox.
	 *
	 * @param	string		$field: The field name
	 * @param	array		$config: TCA field conf
	 * @return	string		HTML form field content
	 */
	private function handleFormField_typeCheck($field, array $config) {
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
	 * Generates form element of the TCA type "check".
	 * This will render an input form field of type checkbox.
	 *
	 * @param	string		$field: The field name
	 * @param	array		$config: TCA field conf
	 * @param	int		$col: The col number
	 * @return	string		HTML form field content
	 */
	private function handleFormField_typeCheck_item($field, array $config, $col=null) {
		$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_FORM_CHECK_ITEM###');
		$value = $this->getEntryValue($field, $config);
		if (is_null($col) && $value) {
			$checked = 'checked="checked"';
		}
		elseif ($value & pow(2, $col)) {
			$checked = ' checked="checked"';
		}
		else {
			$checked = '';
		}
		$markers = array(
			'PREFIXID' => $this->prefixId,
			'ITEM_ID' => $field,
			'FIELDLABEL' => $this->fieldLabels[$field],
			'FIELDNAME' => $field,
			'ITEM_NAME' => $this->prefixId . '[' . $field . ']',
			'CHECKED' => $checked,
			'ONCHANGE' => '',
			'DISABLED' => '',
		);

		return $this->cObj->substituteMarkerArray($template, $markers, '###|###');
	}

	/**
	 * Generates form element of the TCA type "select".
	 *
	 * @param	string		$field: The field name
	 * @param	array		$config: TCA field conf
	 * @return	string		HTML form field content
	 */
	private function handleFormField_typeSelect($field, array $config) {
		$items = $this->getSelectItemArray($field, $config);
		// TODO : hook
		// if hook
		// else {
			if ($config['maxitems'] <= 1 && $config['renderMode'] !== 'tree') {	// Single selector box
				$content = $this->handleFormField_typeSelect_single($items, $field, $config);
			// } elseif (!strcmp($config['renderMode'], 'checkbox')) {
				// TODO : Implements Checkbox renderMode
			// } elseif (!strcmp($config['renderMode'], 'singlebox')) {
				// TODO : Implements Single selector box renderMode
			// } elseif (!strcmp($config['renderMode'], 'tree')) { //
				// TODO : Implements Tree renderMode
			}
			else { // Traditional multiple selector box:
				$content = $this->handleFormField_typeSelect_multiple($items, $field, $config);
			}
		// }

		return $content;
	}

	/**
	 * Generates form element of the TCA type "select".
	 * This will render a selector box form field.
	 *
	 * @param	array		$items: The items array
	 * @param	string		$field: The field name
	 * @param	array		$config: TCA field conf
	 * @return	string		HTML form field content
	 */
	private function handleFormField_typeSelect_single(array $items, $field, array $config) {
		$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_FORM_SELECT_SINGLE###');
		$subparts = array();

		$itemTemplate =  $this->cObj->getSubpart($template, '###GROUP_OPTIONS###');
		$subparts['###GROUP_OPTIONS###']  = '';
		foreach ($items as $item) {
			$locMarkers = array(
				'OPTION_ITEM_VALUE' => $item['value'],
				'OPTION_SELECTED' => ($item['value'] == $this->getEntryValue($field, $config))? ' selected="selected"': '',
				'OPTION_ITEM_LABEL' => $item['label'],
			);
			$subparts['###GROUP_OPTIONS###'] .= $this->cObj->substituteMarkerArray($itemTemplate, $locMarkers, '###|###');
		}

		$markers = array(
			'PREFIXID' => $this->prefixId,
			'ITEM_ID' => $field,
			'FIELDLABEL' => $this->fieldLabels[$field],
			'FIELDNAME' => $field,
			'ITEM_NAME' => $this->prefixId . '[' . $field . ']',
		);

		$template = $this->cObj->substituteSubpartArray($template, $subparts);
		return $this->cObj->substituteMarkerArray($template, $markers, '###|###');
	}

	/**
	 * Generates form element of the TCA type "select".
	 * This will render a group of input form field of type checkbox.
	 *
	 * @param	array		$items: The items array
	 * @param	string		$field: The field name
	 * @param	array		$config: TCA field conf
	 * @return	string		HTML form field content
	 */
	private function handleFormField_typeSelect_multiple(array $items, $field, array $config) {
		$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_FORM_SELECT_MULTIPLE###');
		$subparts = array();

		$itemTemplate =  $this->cObj->getSubpart($template, '###GROUP_OPTIONS###');
		$subparts['###GROUP_OPTIONS###']  = '';
		foreach ($items as $item) {
			if (($item['value']==0 && $item['label']!=='') || $item['value']>0) {
				$locMarkers = array(
					'OPTION_ITEM_NAME' => $this->prefixId . '[' . $field . '][' . $item['value'] . ']',
					'OPTION_ITEM_ID' => $field . '_' . $item['value'],
					'OPTION_CHECKED' => in_array($item['value'], $this->getEntryValue($field, $config))? ' checked="checked"': '',
					'OPTION_ITEM_LABEL' => $item['label'],
				);
				$subparts['###GROUP_OPTIONS###'] .= $this->cObj->substituteMarkerArray($itemTemplate, $locMarkers, '###|###');
			}
		}

		$markers = array(
			'PREFIXID' => $this->prefixId,
			'FIELDLABEL' => $this->fieldLabels[$field],
			'FIELDNAME' => $field,
		);

		$template = $this->cObj->substituteSubpartArray($template, $subparts);
		return $this->cObj->substituteMarkerArray($template, $markers, '###|###');
	}


	// private function FormField_typeGroup();


	/**
	 * Retrieves entry value
	 *
	 * @param	string		$field: The field name
	 * @param	array		$config: TCA field conf
	 * @return	mixed		The entry value
	 */
	private function getEntryValue($field, array $config) {
		if ($config['type']=='select') {
			$value = $this->getEntryValue_select($field, $config);
		}
		else {
			$value = $this->getDefaultEntryValue($field, $config);
		}
		return $value;
	}

	/**
	 * Retrieves entry value
	 *
	 * @param	string		$field: The field name
	 * @return	mixed		The entry value
	 */
	public  function getDefaultEntryValue($field) {
		if ($this->pi->piVars['valid']) {
			return $this->pi->piVars[$field];
		}
		// $this->pi->piVars['cancel'] or any submit
		return $this->renderValue($field, $this->row['uid'], $this->row[$field], self::$view);
	}

	/**
	 * Retrieves TCA type "select" entry value
	 *
	 * @param	string		$field: The field name
	 * @param	array		$config: TCA field conf
	 * @return	mixed		The entry value
	 */
	private getEntryValue_select($field, $config=null) {
		if ($this->pi->piVars['valid']) {
			if ($config['maxitems'] >1)
				$value = array_keys($this->pi->piVars[$field]);
			else
				$value = $this->pi->piVars[$field];
		}
		else {	// $this->pi->piVars['cancel'] or any submit
			$value = $this->row[$field];
			if ($config['maxitems'] >1) {
				if ($config['MM']) {
					$value = array();
					$result = $this->getMMRecords($this->row['uid'], $config);
					while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result)) {
						$value[] = $row['ft_uid'];
					}
				}
				else {
					$value = t3lib_div::trimExplode(',', $value);
				}
			}
		}
		return $value;
	}

	/**
	 * Retrieves selector box items (pair of key/label)
	 *
	 * @param	string		$field: The field name
	 * @param	array		$config: TCA field conf
	 * @return	mixed		Array of items
	 */
	private function getSelectItemArray($field, array $config) {
		$items = array();
		if ($config['foreign_table']) {
			t3lib_div::loadTCA($config['foreign_table']);
			if ($label = $GLOBALS['TCA'][$config['foreign_table']]['ctrl']['label']) {
				$fields = array('`uid` AS value', '`'.$label.'` AS label');
				// Get records
				$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
					implode(',', $fields),
					$config['foreign_table'],
					'',
					$label
				);
				// Init items
				$items[] = array('value'=>0, 'label'=>'');
				if (is_array($rows) && !empty($rows)) {
					$items = array_merge($items, $rows);
				}
			}
		}
		// TODO : Implements itemsProcFunc case;
		// elseif ($config['itemsProcFunc']) {
		// }
		else {
			$items = $this->initItemArray($config);
		}
		return $items;
	}

	/**
	 * Initialize item array (for checkbox, selectorbox, radio buttons) Will resolve the label value
	 *
	 * @param	array		$config: TCA field conf
	 * @return	mixed		$items
	 */
	protected function initItemArray(array $config)     {
		$items = array();
		if (is_array($config['items']))   {
			foreach ($config['items'] as $key=>$item) {
				$items[] = array(
					'value' => $key,
					'label' => $GLOBALS['TSFE']->sL($item[0])
				);
			}
		}
		return $items;
	}


	/**
	 * Includes datepicker js lib
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