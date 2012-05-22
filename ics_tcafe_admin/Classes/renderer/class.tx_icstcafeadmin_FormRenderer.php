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
 *   66: class tx_icstcafeadmin_FormRenderer extends tx_icstcafeadmin_CommonRenderer
 *   93:     function __construct($pi_base, tslib_cObj $cObj, $table, $row, array $fields, array $fieldLabels, array $lConf)
 *  107:     public function render()
 *  130:     private function renderPIDStorage()
 *  145:     private function renderFormFields()
 *  176:     private function renderEntries()
 *  199:     public function handleFormField($field)
 *  230:     public function handleFormField_typeInput($field, array $config)
 *  268:     public function handleFormField_typeText($field, array $config)
 *  301:     public function handleFormField_typeCheck($field, array $config)
 *  336:     public function handleFormField_typeCheck_item($field, array $config, $col=null)
 *  369:     public function handleFormField_typeSelect($field, array $config)
 *  404:     public function handleFormField_typeSelect_single(array $items, $field, array $config)
 *  440:     public function handleFormField_typeSelect_multiple(array $items, $field, array $config)
 *  476:     public function handleFormField_typeGroup($field, array $config)
 *  500:     public function handleFormField_typeGroup_file($field, array $config)
 *  570:     public function getEntryValue($field, array $config)
 *  586:     public  function getDefaultEntryValue($field)
 *  601:     private function getEntryValue_select($field, $config=null)
 *  633:     public function getSelectItemArray($field, array $config)
 *  668:     private function initItemArray(array $config)
 *  687:     private static function includeLibDatepicker()
 *
 * TOTAL FUNCTIONS: 21
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
	private $row = null;
	private static $view = 'viewForm';

	private static $datepickerIncluded = false;
	private static $conformInputIncluded = false;

	var $maxInputWidth = 48; 		// The maximum abstract value for input fields
	var $maxTextareaCols	= 48;	// The maximum abstract value for textareas
	var $maxTextareaRows	= 20;	// The maximum abstract value for textareas

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
	function __construct($pi_base, $table, array $fields, array $fieldLabels, $row=null, array $conf) {
		$this->row = $row;
		parent::__construct($pi_base, $table, $fields, $fieldLabels, $conf);
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
				if ($content = $procObj->renderFormFields($this->pi_base, $this->table, $this->fields, $this->fieldLabels, $this->row, $markers, $this->conf, $this))
					break;
			}
		}
		if (!$content) {
			$markers = array(
				'PREFIXID' => $this->prefixId,
				'FIELDSET_FIELDS' => $this->getLL('formFields_fieldset', 'Fields entries', true),
				'FIELDS_TITLE' => $this->getLL('formFields_title', 'Fields entries', true),
				'ENTRIES' => $this->renderEntries(),
			);
		}
		$content = $this->cObj->substituteMarkerArray($template, $markers, '###|###');
		return $content;
	}


	/**
	 * Render entries
	 *
	 * @return	string		HTML fields content
	 */
	private function renderEntries() {
		$content = '';
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['renderEntries'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['renderEntries'] as $class) {
				$procObj = & t3lib_div::getUserObj($class);
				if ($content = $procObj->renderEntries($this->pi_base, $this->table, $this->fields, $this->fieldLabels, $this->row, $this->conf, $this))
					break;
			}
		}
		if (!$content)
			foreach ($this->fields as $field) {
				$content .= $this->handleFormField($field);
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
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['handleFormField'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['handleFormField'] as $class) {
				$procObj = & t3lib_div::getUserObj($class);
				if ($content = $procObj->handleFormField($this->pi_base, $this->table, $field, $this->fieldLabels, $this->row, $this->conf, $this))
					break;
			}
		}
		if (!$content) {
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
				case 'group':
					$content = $this->handleFormField_typeGroup($field, $config);
					break;
				default:
					$content = '';
			}
		}
		return $content;
	}

	/**
	 * Generates form element of the TCA type "input". This will render an input form field of type text.
	 *
	 * @param	string		$field: The field name
	 * @param	array		$config: TCA field conf
	 * @param	string		$template: The template code
	 *
	 * @return	string		HTML form field content
	 */
	public function handleFormField_typeInput($field, array $config, $template) {
		$size = t3lib_div::intInRange($config['size'], 5, $this->maxInputWidth, 30);
		$size = $size? 'size="' . $size . '"': '';

		$label = $this->fieldLabels[$field];
		$evalList = t3lib_div::trimExplode(',', $config['eval'], true);
		if (in_array('required', $evalList))
			$label = $this->cObj->stdWrap($label, $this->conf['defaultConf.']['require.']);
		
		if (!$template)
			$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_FORM_TEXT###');
			
		$markers = array(
			'PREFIXID' => $this->prefixId,
			'ITEM_ID' => $field,
			'FIELDLABEL' => $label,
			'FIELDNAME' => $field,
			'ITEM_NAME' => $this->prefixId . '[' . $field . ']',
			'ITEM_VALUE' => $this->getEntryValue($field, $config),
			'SIZE' => $size,
			'CONFORM' => $this->getConformInput($field, $config),
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
	 * Conform input entry
	 *
	 * @param	array	$config: TCA field conf
	 *
	 * @return	string	The	suitability control on entry
	 */
	private function getConformInput($field, array $config) {
		self::includeJSConformInput(t3lib_div::trimExplode(',', $this->conf['conformInput.']['files']));
		
		if ($this->conf['conformInput.']['field.'][$field . '.'])
			return $this->conf['conformInput.']['field.'][$field . '.'];
		
		$evalList = t3lib_div::trimExplode(',', $config['eval']);
		if (in_array('date', $evalList))
			return $this->cObj->stdWrap($field, $this->conf['conformInput.']['date.']);
		if (in_array('datetime', $evalList))
			return $this->cObj->stdWrap($field, $this->conf['conformInput.']['datetime.']);
		if (in_array('time', $evalList))
			return $this->cObj->stdWrap($field, $this->conf['conformInput.']['time.']);
		if (in_array('timesec', $evalList))
			return $this->cObj->stdWrap($field, $this->conf['conformInput.']['timesec.']);
		if (in_array('year', $evalList))
			return $this->cObj->stdWrap($field, $this->conf['conformInput.']['year.']);
		if (in_array('int', $evalList))
			return $this->cObj->stdWrap($field, $this->conf['conformInput.']['int.']);
		if (in_array('double2', $evalList))
			return $this->cObj->stdWrap($field, $this->conf['conformInput.']['float.']);
		if (in_array('alphanum', $evalList))
			return $this->cObj->stdWrap($field, $this->conf['conformInput.']['alphanum.']);
		if (in_array('upper', $evalList))
			return $this->cObj->stdWrap($field, $this->conf['conformInput.']['upper.']);
		if (in_array('lower', $evalList))
			return $this->cObj->stdWrap($field, $this->conf['conformInput.']['lower.']);
		if (in_array('nospace', $evalList))
			return $this->cObj->stdWrap($field, $this->conf['conformInput.']['nospace.']);
		
		return '';
	}
	
	/**
	 * Generates form element of the TCA type "text". This will render an textarea form field.
	 *
	 * @param	string		$field: The field name
	 * @param	array		$config: TCA field conf
	 * @param	string		$template: The template code
	 *
	 * @return	string		HTML form field content
	 */
	public function handleFormField_typeText($field, array $config, $template) {
		$cols = t3lib_div::intInRange($config['cols'], 5, $this->maxTextareaCols, 30);
		$cols =  $cols? 'cols="' . $cols . '"': '';
		$rows = t3lib_div::intInRange($config['rows'], 1, $this->maxTextareaRows, 5);
		$rows =  $rows? 'rows="' . $rows . '"': '';

		$label = $this->fieldLabels[$field];
		$evalList = t3lib_div::trimExplode(',', $config['eval'], true);
		if (in_array('required', $evalList))
			$label = $this->cObj->stdWrap($label, $this->conf['defaultConf.']['require.']);

		if (!$template)
			$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_FORM_TEXTAREA###');
			
		$markers = array(
			'PREFIXID' => $this->prefixId,
			'ITEM_ID' => $field,
			'FIELDLABEL' => $label,
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
	public function handleFormField_typeCheck($field, array $config) {
		// Hook to handle form field
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['handleFormField_typeCheck'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['handleFormField_typeCheck'] as $class) {
				$procObj = & t3lib_div::getUserObj($class);
				if ($content = $procObj->handleFormField_typeCheck($this->pi_base, $this->table, $field, $this->fieldLabels, $this->row, $this->conf, $this))
					break;
			}
		}
		if (!$content) {
			if ($config['cols'] && $config['cols']>1) {
				tx_icstcafeadmin_debug::notice('handleFormField_typeCheck with cols  is not implemented.');
				$content = '<div>
					<p>' . $this->fieldLabels[$field]  . ': handle associates with this fiels is not implemented</p>
					<input type="hidden" name="' . $this->prefixId . '[' . $field . ']" value="' . $this->row[$field] . '"/>
					</div>';
				/* TODO : implements form field with tca field on type check and sevrals cols
				$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_FORM_CHECK###');
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
		}
		return $content;
	}

	/**
	 * Generates form element of the TCA type "check".
	 * This will render an input form field of type checkbox.
	 *
	 * @param	string		$field: The field name
	 * @param	array		$config: TCA field conf
	 * @param	int			$col: The col number
	 * @param	string		$template: The template code
	 *
	 * @return	string		HTML form field content
	 */
	public function handleFormField_typeCheck_item($field, array $config, $col=null, $template) {
		if (!$template)
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
	public function handleFormField_typeSelect($field, array $config) {
		$items = $this->getSelectItemArray($field, $config);
		// Hook to handle form field
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['handleFormField_typeSelect'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['handleFormField_typeSelect'] as $class) {
				$procObj = & t3lib_div::getUserObj($class);
				if ($content = $procObj->handleFormField_typeSelect($this->pi_base, $this->table, $field, $this->fieldLabels, $this->row, $this->conf, $this))
					break;
			}
		}
		if (!$content) {
			if ($config['maxitems'] <= 1 && $config['renderMode'] !== 'tree') {	// Single selector box
				$content = $this->handleFormField_typeSelect_single($items, $field, $config);
			} elseif (!strcmp($config['renderMode'], 'checkbox')) {
				// TODO : Implements Checkbox renderMode
				tx_icstcafeadmin_debug::notice('handleFormField_typeSelect with renderMode  is not implemented.');
				$content = '<div>
					<p>' . $this->fieldLabels[$field]  . ': handle associates with this fiels is not implemented</p>
					<input type="hidden" name="' . $this->prefixId . '[' . $field . ']" value="' . $this->row[$field] . '"/>
					</div>';
			} elseif (!strcmp($config['renderMode'], 'singlebox')) {
				// TODO : Implements Single selector box renderMode
				tx_icstcafeadmin_debug::notice('handleFormField_typeSelect with renderMode  is not implemented.');
				$content = '<div>
					<p>' . $this->fieldLabels[$field]  . ': handle associates with this fiels is not implemented</p>
					<input type="hidden" name="' . $this->prefixId . '[' . $field . ']" value="' . $this->row[$field] . '"/>
					</div>';
			} elseif (!strcmp($config['renderMode'], 'tree')) { //
				// TODO : Implements Tree renderMode
				tx_icstcafeadmin_debug::notice('handleFormField_typeSelect with renderMode  is not implemented.');
				$content = '<div>
					<p>' . $this->fieldLabels[$field]  . ': handle associates with this fiels is not implemented</p>
					<input type="hidden" name="' . $this->prefixId . '[' . $field . ']" value="' . $this->row[$field] . '"/>
					</div>';
			}
			else { // Multiple checkbox
				$content = $this->handleFormField_typeSelect_multiple($items, $field, $config);
			}
		}
		return $content;
	}

	/**
	 * Generates form element of the TCA type "select".
	 * This will render a selector box form field.
	 *
	 * @param	array		$items: The items array
	 * @param	string		$field: The field name
	 * @param	array		$config: TCA field conf
	 * @param	string		$template: The template code
	 *
	 * @return	string		HTML form field content
	 */
	public function handleFormField_typeSelect_single(array $items, $field, array $config, $template) {
		if (!$template)
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

		$label = $this->fieldLabels[$field];
		if ($config['minitems']>0)
			$label = $this->cObj->stdWrap($label, $this->conf['defaultConf.']['require.']);
		$markers = array(
			'PREFIXID' => $this->prefixId,
			'ITEM_ID' => $field,
			'FIELDLABEL' => $label,
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
	 * @param	string		$template: The template code
	 *
	 * @return	string		HTML form field content
	 */
	public function handleFormField_typeSelect_multiple(array $items, $field, array $config, $template) {
		if (!$template)
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

		$label = $this->fieldLabels[$field];
		if ($config['minitems']>0)
			$label = $this->cObj->stdWrap($label, $this->conf['defaultConf.']['require.']);
		$markers = array(
			'PREFIXID' => $this->prefixId,
			'FIELDLABEL' => $label,
			'FIELDNAME' => $field,
		);

		$template = $this->cObj->substituteSubpartArray($template, $subparts);
		return $this->cObj->substituteMarkerArray($template, $markers, '###|###');
	}


	/**
	 * Generates form element of the TCA type "group".
	 *
	 * @param	string		$field: The field name
	 * @param	array		$config: TCA field conf
	 * @return	string		HTML form field content
	 */
	public function handleFormField_typeGroup($field, array $config) {
		// Hook to handle form field
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['handleFormField_typeGroup'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['handleFormField_typeGroup'] as $class) {
				$procObj = & t3lib_div::getUserObj($class);
				if ($content = $procObj->handleFormField_typeGroup($this->pi_base, $this->table, $field, $this->labelfield, $this->row, $this->conf, $this))
					break;
			}
		}
		if (!$content) {
			if ($config['internal_type']=='file') {
				$content = $this->handleFormField_typeGroup_file($field, $config);
			}
			else {
				tx_icstcafeadmin_debug::notice('handleFormField_typeGroup of internal_type "' . $config['internal_type'] . '" is not implemented.');
				$content = '<div>
					<p>' . $this->fieldLabels[$field]  . ': handle associates with this fiels is not implemented</p>
					<input type="hidden" name="' . $this->prefixId . '[' . $field . ']" value="' . $this->row[$field] . '"/>
					</div>';
			}
		}
		return $content;
	}

	/**
	 * Generates form element of the TCA type "group".
	 * This will render an input file form field.
	 *
	 * @param	string		$field: The field name
	 * @param	array		$config: TCA field conf
	 * @param	string		$template: The template code
	 *
	 * @return	string		HTML form field content
	 */
	public function handleFormField_typeGroup_file($field, array $config, $template) {
		if (!$template)
			$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_FORM_FILE###');
			
		$subparts = array();

		$isIllustration = false;
		if (array_intersect(t3lib_div::trimExplode(',', $config['allowed'], true), tx_icstcafeadmin_CommonRenderer::$allowedImgFileExtArray))
			$isIllustration = true;
		
		$cObj = t3lib_div::makeInstance('tslib_cObj');
		$cObj->setParent($this->cObj->data, $this->cObj->currentRecord);
		$lConf = $this->conf['defaultConf.']['file.']['viewForm.']['delete.'];

		$files = t3lib_div::trimExplode(',', $this->row[$field], true);

		$itemTemplate = $this->cObj->getSubpart($template, '###SUBPART_FILE_DELETE###');
		$subparts['###SUBPART_FILE_DELETE###'] = '';
		foreach ($files as $file) {
			$uniqid = uniqid();
			if ($isIllustration) {
				$illustration = $file;
				if ($config['uploadfolder'])
					$illustration = $config['uploadfolder'].'/'.$file;
			}
			$data = array(
				'illustration' => $illustration,
				'filename' => $file,
			);

			$cObj->start($data, 'File_delete');
			$locMarkers = array(
				'FILE_DEL_ILLUSTRATION' => $cObj->stdWrap('', $lConf['illustration.']),
				'FILE_DEL_ID' => $field . '_' . $uniqid,
				'FILE_DEL_NAME' => $this->prefixId . '[' . $field . '][' . $uniqid . ']',
				'FILE_DEL_VALUE' => htmlspecialchars($file),
				'FILE_DEL_LABEL' => $cObj->stdWrap('', $lConf['label.']) ,
			);
			$subparts['###SUBPART_FILE_DELETE###'] .= $this->cObj->substituteMarkerArray($itemTemplate, $locMarkers, '###|###');
		}

		$lConf = $this->conf['defaultConf.']['file.']['viewForm.'];
		$data = array(
			'maxsize' => $config['max_size'],
			'allowed' => $config['allowed'],
			'disallowed' => $config['disallowed'],
		);
		$cObj->start($data, 'Files');

		$itemTemplate = $this->cObj->getSubpart($template, '###SUPBART_ADDFILE###');
		$subparts['###SUPBART_ADDFILE###'] = '';
		if (count($files)< $config['maxitems']) {
			$locMarkers = array(
				'ADDFILE_NAME' => $this->prefixId.'['.$field.'][file]',
				'GROUPFILE_INFORMATIONS' => $cObj->stdWrap('', $lConf['informations.']),
			);
			$subparts['###SUPBART_ADDFILE###'] = $this->cObj->substituteMarkerArray($itemTemplate, $locMarkers, '###|###');
		}

		$markers = array(
			'PREFIXID' => $this->prefixId,
			'ITEM_ID' => $field,
			'FIELDLABEL' => $this->fieldLabels[$field],
			'FIELDNAME' => $field,
			'ITEM_NAME' => $this->prefixId . '[' . $field . '][files]',
			'ITEM_VALUE' => htmlspecialchars($this->row[$field]),
		);

		$template = $this->cObj->substituteSubpartArray($template, $subparts);
		return $this->cObj->substituteMarkerArray($template, $markers, '###|###');
	}

	/**
	 * Retrieves entry value
	 *
	 * @param	string		$field: The field name
	 * @param	array		$config: TCA field conf
	 * @return	mixed		The entry value
	 */
	public function getEntryValue($field, array $config) {
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
	public function getDefaultEntryValue($field) {
		if ($this->pi_base->piVars['valid']) {
			return $this->pi_base->piVars[$field];
		}
		// $this->pi_base->piVars['cancel'] or any submit
		return $this->renderValue($field, $this->row['uid'], $this->row[$field], self::$view);
	}

	/**
	 * Retrieves TCA type "select" entry value
	 *
	 * @param	string		$field: The field name
	 * @param	array		$config: TCA field conf
	 * @return	mixed		The entry value
	 */
	private function getEntryValue_select($field, $config=null) {
		if ($this->pi_base->piVars['valid']) {
			if ($config['maxitems'] >1)
				$value = array_keys($this->pi_base->piVars[$field]);
			else
				$value = $this->pi_base->piVars[$field];
		}
		else {	// $this->pi_base->piVars['cancel'] or any submit
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
	public function getSelectItemArray($field, array $config) {
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
	private function initItemArray(array $config)     {
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
	 * Includes JS conform input
	 *
	 * @return	void
	 */
	private static function includeJSConformInput(array $files) {
		if (empty($files) || self::$conformInputIncluded)
			return;
		
		$tags = array();
		foreach ($files as $filename) {
			$file = t3lib_div::resolveBackPath($GLOBALS['TSFE']->tmpl->getFileName($filename));
			$tags[] = '<script src="' . htmlspecialchars($file) . '" type="text/javascript"></script>' . PHP_EOL;
		}
		$GLOBALS['TSFE']->additionalHeaderData['conformInput'] = implode('', $tags);
		
		self::$conformInputIncluded = true;
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