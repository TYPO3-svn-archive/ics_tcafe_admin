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
 *   68: class tx_icstcafeadmin_FormRenderer extends tx_icstcafeadmin_CommonRenderer
 *   89:     function __construct($pi_base, $table, array $fields, array $fieldLabels, $recordId=0, array $conf)
 *  112:     public function render()
 *  135:     private function renderPIDStorage()
 *  150:     private function renderFormFields()
 *  182:     private function renderEntries()
 *  206:     public function handleFormField($field)
 *  254:     public function handleFormField_typeInput($field, array $config, $template='')
 *  289:     public function handleFormField_typeInput_date($field, array $config, $template='')
 *  332:     public function getConformInput($field, array $config)
 *  373:     public function handleFormField_typeText($field, array $config, $template='')
 *  412:     public function handleFormField_typeCheck($field, array $config)
 *  454:     public function handleFormField_typeCheck_item($field, array $config, $col=null, $template='')
 *  489:     public function handleFormField_typeSelect($field, array $config)
 *  541:     public function handleFormField_typeSelect_single(array $items, $field, array $config, $template='')
 *  583:     public function handleFormField_typeSelect_multiple(array $items, $field, array $config, $template='')
 *  627:     public function handleFormField_typeGroup($field, array $config)
 *  660:     public function handleFormField_typeGroup_file($field, array $config, $template='')
 *  738:     public function getEntryValue($field)
 *  754:     public function getEntryValue_selectedOption($field, $item, array $config)
 *  789:     public function getEntryValue_selectedArray($field)
 *  826:     public function getSelectItemArray($field, array $config)
 *  873:     public function initItemArray(array $config)
 *  893:     private static function includeJSConformInput(array $files)
 *
 * TOTAL FUNCTIONS: 23
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
	 * @param	int		$recordId: The record id
	 * @param	array		$conf: Typoscript configuration
	 * @return	void
	 */
	function __construct($pi_base, $table, array $fields, array $fieldLabels, $recordId=0, array $conf) {
		$this->recordId = $recordId;
		if ($this->recordId) {
			$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
				'*',
				$table,
				'deleted = 0 AND uid=' . $recordId,
				'',
				'',
				'1'
			);
			if (is_array($rows) && !empty($rows))
				$this->row = $rows[0];
		}

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
			'BACKLINK' => $this->renderBackLink($this->row),
		);
		// Hook on additionnal markers
		$subpartArray = array();
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['formRenderer_additionnalMarkers'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['formRenderer_additionnalMarkers'] as $class) {
				$procObj = & t3lib_div::getUserObj($class);
				$process = $procObj->formRenderer_additionnalMarkers($template, $markers, $subpartArray, $this->table, $field, $this->row, $this->conf, $this->pi_base, $this);
			}
		}		
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
				if ($content = $procObj->renderFormFields($this->pi_base, $this->table, $this->fields, $this->fieldLabels, $this->recordId, $markers, $this->conf, $this))
					break;
			}
		}
		if (!$content) {
			$markers = array(
				'PREFIXID' => $this->prefixId,
				'FIELDSET_FIELDS' => $this->getLL('formFields_fieldset', 'Fields entries', true),
				'FIELDS_TITLE' => $this->getLL('formFields_title', 'Fields entries', true),
				'ENTRIES' => '',
			);
			$entries = t3lib_div::trimExplode(',', $this->conf['renderForm.']['entries_group'], true);
			if (is_array($entries) && !empty($entries)) {	// Process entries by group
				foreach ($entries as $entry) {
					$fields = t3lib_div::trimExplode(',', $this->conf['renderForm.']['entries_group.'][$entry.'.']['fields'], true);
					// t3lib_div::debug(array($entry, $fields),'entry: fields');
					$subTemplate = $this->cObj->getSubpart($template, '###SUBPART_ENTRIES_'.strtoupper($entry).'###');
					$lMarkers['ENTRIES_'.strtoupper($entry)] = $this->renderEntries($subTemplate, $fields);
					$lContent = $this->cObj->substituteMarkerArray($subTemplate, $lMarkers, '###|###');
					$subparts['###SUBPART_ENTRIES_'.strtoupper($entry).'###'] = $this->cObj->substituteMarkerArray($lContent, $lMarkers, '###|###');
					// Cleans fields subparts
					foreach ($fields as $field) {
						$subparts['###ALT_SUBPART_FORM_'.strtoupper($field).'###'] = '';
					}
				}
				$template = $this->cObj->substituteSubpartArray($template, $subparts);
			}
			else {	// Process entries (basic method)
				$markers['ENTRIES'] = $this->renderEntries($template);
			}
		}
		$content = $this->cObj->substituteMarkerArray($template, $markers, '###|###');
		return $content;
	}


	/**
	 * Render entries
	 *
	 * @param	string		$template
	 * @param	array		$fields: Array of fields to render
	 * @return	string		HTML fields content
	 */
	private function renderEntries($template, $fields) {
		$content = '';
		// Hook on renderEntries
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['renderEntries'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['renderEntries'] as $class) {
				$procObj = & t3lib_div::getUserObj($class);
				if ($content = $procObj->renderEntries($this->pi_base, $this->table, $this->fields, $this->fieldLabels, $this->recordId, $this->conf, $this))
					break;
			}
		}
		if (!$content) {
			if (!is_array($fields) || empty($fields)) {
				$fields = $this->fields;
			}
			$template = $template? $template: $this->templateCode;
			foreach ($fields as $field) {
				// The specific template field
				$subTemplate = $this->cObj->getSubpart($template, '###ALT_SUBPART_FORM_'.strtoupper($field).'###');
				$content .= $this->handleFormField($field, $subTemplate);
			}
		}

		return $content;
	}

	/**
	 * Generates form field
	 *
	 * @param	string		$field: The field name
	 * @param	string		$template
	 * @return	string		HTML form field content
	 */
	public function handleFormField($field, $template) {
		// Hook on handleFormField
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['handleFormField'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['handleFormField'] as $class) {
				$procObj = & t3lib_div::getUserObj($class);
				if ($content = $procObj->handleFormField($this->pi_base, $this->table, $field, $this->fieldLabels, $this->recordId, $this->conf, $this))
					break;
			}
		}
		if (!$content) {
			// The field config
			$config = $GLOBALS['TCA'][$this->table]['columns'][$field]['config'];
			switch ($config['type']) {
				case 'input':
					$format = $this->fetchInputFieldFormat($config);
					if ($format == 'date' || $format == 'datetime') {
						$content =  $this->handleFormField_typeInput_date($field, $config, $template);
					}
					else {
						$content =  $this->handleFormField_typeInput($field, $config, $template);
					}
					break;
				case 'text':
					$content =  $this->handleFormField_typeText($field, $config, $template);
					break;
				case 'check':
					$content = $this->handleFormField_typeCheck($field, $config, $template);
					break;
				case 'select':
					$content = $this->handleFormField_typeSelect($field, $config, $template);
					break;
				case 'group':
					$content = $this->handleFormField_typeGroup($field, $config, $template);
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
	 * @return	string		HTML form field content
	 */
	public function handleFormField_typeInput($field, array $config, $template='') {
		$size = t3lib_div::intInRange($config['size'], 5, $this->maxInputWidth, 30);
		$size = $size? 'size="' . $size . '"': '';

		$label = $this->fieldLabels[$field];
		$evalList = t3lib_div::trimExplode(',', $config['eval'], true);
		if (in_array('required', $evalList))
			$label = $this->cObj->stdWrap($label, $this->conf['defaultConf.']['requireEntryLabel.']);

		if (!$template)
			$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_FORM_TEXT###');

		$markers = array(
			'PREFIXID' => $this->prefixId,
			'ITEM_ID' => $field,
			'FIELDLABEL' => $this->cObj->stdWrap($label, $this->conf['renderConf.'][$this->table.'.'][$field.'.'][self::$view.'.']['label.']),
			'FIELDNAME' => $field,
			'ITEM_NAME' => $this->prefixId . '[' . $field . ']',
			'ITEM_VALUE' => $this->getEntryValue($field),
			'SIZE' => $size,
			'CONFORM' => $this->getConformInput($field, $config),
			'CTRL_MESSAGE' => '',
		);
		return $this->cObj->substituteMarkerArray($template, $markers, '###|###');
	}


	/**
	 * Generates form element of the TCA type "input", eval "date" or "datetime". This will render an input form field of type text.
	 *
	 * @param	string		$field: The field name
	 * @param	array		$config: TCA field conf
	 * @param	string		$template: The template code
	 * @return	string		HTML form field content
	 */
	public function handleFormField_typeInput_date($field, array $config, $template='') {
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['handleFormField_typeInput_date'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['handleFormField_typeInput_date'] as $class) {
				$procObj = & t3lib_div::getUserObj($class);
				if ($content = $procObj->handleFormField_typeInput_date($this->pi_base, $this->table, $field, $this->fieldLabels, $this->recordId, $this->conf, $this))
					break;
			}
		}
		if ($content)
			return $content;

		$size = t3lib_div::intInRange($config['size'], 5, $this->maxInputWidth, 30);
		$size = $size? 'size="' . $size . '"': '';

		$label = $this->fieldLabels[$field];
		$evalList = t3lib_div::trimExplode(',', $config['eval'], true);
		if (in_array('required', $evalList))
			$label = $this->cObj->stdWrap($label, $this->conf['defaultConf.']['requireEntryLabel.']);

		if (!$template)
			$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_FORM_TEXT_DATE###');

		$markers = array(
			'PREFIXID' => $this->prefixId,
			'ITEM_ID' => $field,
			'FIELDLABEL' => $this->cObj->stdWrap($label, $this->conf['renderConf.'][$this->table.'.'][$field.'.'][self::$view.'.']['label.']),
			'FIELDNAME' => $field,
			'ITEM_NAME' => $this->prefixId . '[' . $field . ']',
			'ITEM_VALUE' => $this->getEntryValue($field),
			'SIZE' => $size,
			'CONFORM' => $this->getConformInput($field, $config),
			'CTRL_MESSAGE' => '',
		);
		return $this->cObj->substituteMarkerArray($template, $markers, '###|###');
	}


	/**
	 * Conform input entry
	 *
	 * @param	array		$config: TCA field conf
	 * @return	string		The	suitability control on entry
	 */
	public function getConformInput($field, array $config) {
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
	 * @return	string		HTML form field content
	 */
	public function handleFormField_typeText($field, array $config, $template='') {
		$cols = t3lib_div::intInRange($config['cols'], 5, $this->maxTextareaCols, 30);
		$cols =  $cols? 'cols="' . $cols . '"': '';
		$rows = t3lib_div::intInRange($config['rows'], 1, $this->maxTextareaRows, 5);
		$rows =  $rows? 'rows="' . $rows . '"': '';

		$label = $this->fieldLabels[$field];
		$evalList = t3lib_div::trimExplode(',', $config['eval'], true);
		if (in_array('required', $evalList))
			$label = $this->cObj->stdWrap($label, $this->conf['defaultConf.']['requireEntryLabel.']);

		if (!$template)
			$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_FORM_TEXTAREA###');

		$markers = array(
			'PREFIXID' => $this->prefixId,
			'ITEM_ID' => $field,
			'FIELDLABEL' => $this->cObj->stdWrap($label, $this->conf['renderConf.'][$this->table.'.'][$field.'.'][self::$view.'.']['label.']),
			'FIELDNAME' => $field,
			'ITEM_NAME' => $this->prefixId . '[' . $field . ']',
			'ITEM_VALUE' => $this->getEntryValue($field),
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
	public function handleFormField_typeCheck($field, array $config, $template='') {
		// Hook to handle form field
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['handleFormField_typeCheck'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['handleFormField_typeCheck'] as $class) {
				$procObj = & t3lib_div::getUserObj($class);
				if ($content = $procObj->handleFormField_typeCheck($this->pi_base, $this->table, $field, $this->fieldLabels, $this->recordId, $this->conf, $this))
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
				if ($config['items']) {
					$template = $template? $template: $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_FORM_CHECKBOXES###');
					$subTemplate = $this->cObj->getSubpart($template, '###ALT_SUBPART_FORM_'.strtoupper($field).'_CHECK_ITEM###');
					$selItems = $this->initItemArray($config);
					for ($c = 0; $c < count($selItems); $c++) {
						$p = $selItems[$c];
						$itemContent .= $this->handleFormField_typeCheck_item($field, $config, $c, $subTemplate, $GLOBALS['TSFE']->sL($p[label]));
					}
					$locMarkers = array(
						'PREFIXID' => $this->prefixId,
						'ITEMFORMEL_CHECK_LABEL' => $this->fieldLabels[$field],
						'CHECK_ITEMS' => $itemContent,
					);
					$template = $this->cObj->substituteSubpart($template, '###ALT_SUBPART_FORM_'.strtoupper($field).'_CHECK_ITEM###', $itemContent);
					$content = $this->cObj->substituteMarkerArray($template, $locMarkers, '###|###');
				}
				else {
					$template = $template? $template: $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_FORM_CHECK###');
					$subTemplate = $this->cObj->getSubpart($template, '###ALT_SUBPART_FORM'.strtoupper($field).'_CHECK_ITEM###');
					$itemContent = $this->handleFormField_typeCheck_item($field, $config, null, $subTemplate);
					$locMarkers = array(
						'PREFIXID' => $this->prefixId,
						'CHECK_ITEMS' => $itemContent,
					);
					$template = $this->cObj->substituteSubpart($template, '###ALT_SUBPART_FORM'.strtoupper($field).'_CHECK_ITEM###', $itemContent);
					$content = $this->cObj->substituteMarkerArray($template, $locMarkers, '###|###');
				}
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
	 * @param	int		$col: The col number
	 * @param	string		$template: The template code
	 * @return	string		HTML form field content
	 */
	public function handleFormField_typeCheck_item($field, array $config, $col=null, $template='', $label='') {
		if (!$template)
			$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_FORM_CHECK_ITEM###');

		
		$value = $this->getEntryValue($field);
		if (is_null($col) && $value) {
			$checked = 'checked="checked"';
		}
		elseif ($value & pow(2, $col)) {
			$checked = ' checked="checked"';
		}
		else {
			$checked = '';
		}
		$label = $label? $label: $this->fieldLabels[$field];
		$markers = array(
			'PREFIXID' => $this->prefixId,
			'ITEM_ID' => $field,
			'FIELDLABEL' => $this->cObj->stdWrap($label, $this->conf['renderConf.'][$this->table.'.'][$field.'.'][self::$view.'.']['label.']),
			'FIELDNAME' => $field,
			'ITEM_NAME' => $this->prefixId . '[' . $field . '][]',
			'ITEM_VALUE' => ($col? pow(2,$col): 1),
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
	public function handleFormField_typeSelect($field, array $config, $template='') {
		$items = $this->getSelectItemArray($field, $config);
		// Hook to handle form field
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['handleFormField_typeSelect'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['handleFormField_typeSelect'] as $class) {
				$procObj = & t3lib_div::getUserObj($class);
				if ($content = $procObj->handleFormField_typeSelect($this->pi_base, $this->table, $field, $this->fieldLabels, $this->recordId, $this->conf, $this))
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
	 * @return	string		HTML form field content
	 */
	public function handleFormField_typeSelect_single(array $items, $field, array $config, $template='') {
		if (!$template)
			$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_FORM_SELECT_SINGLE###');

		$subparts = array();

		$itemTemplate =  $this->cObj->getSubpart($template, '###GROUP_OPTIONS###');
		$subparts['###GROUP_OPTIONS###']  = '';
		foreach ($items as $item) {
			$locMarkers = array(
				'OPTION_ITEM_VALUE' => $item['value'],
				'OPTION_SELECTED' => $this->getEntryValue_selectedOption($field, $item['value'], $config),
				'OPTION_ITEM_LABEL' => $item['label'],
			);
			$subparts['###GROUP_OPTIONS###'] .= $this->cObj->substituteMarkerArray($itemTemplate, $locMarkers, '###|###');
		}

		$label = $this->fieldLabels[$field];
		if ($config['minitems']>0)
			$label = $this->cObj->stdWrap($label, $this->conf['defaultConf.']['requireEntryLabel.']);
		$markers = array(
			'PREFIXID' => $this->prefixId,
			'ITEM_ID' => $field,
			'FIELDLABEL' => $this->cObj->stdWrap($label, $this->conf['renderConf.'][$this->table.'.'][$field.'.'][self::$view.'.']['label.']),
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
	 * @return	string		HTML form field content
	 */
	public function handleFormField_typeSelect_multiple(array $items, $field, array $config, $template='') {
		if (!$template) {
			$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_FORM_SELECT_MULTIPLE###');
		}
		$subparts = array();

		$options = $this->getEntryValue_selectedArray($field);

		$itemTemplate =  $this->cObj->getSubpart($template, '###GROUP_OPTIONS###');
		$subparts['###GROUP_OPTIONS###']  = '';
		foreach ($items as $item) {
			// if (($item['value']==0 && $item['label']!=='') || $item['value']>0) {
			if ($item['value']>0) {
				$locMarkers = array(
					'OPTION_ITEM_VALUE' => $item['value'],
					'OPTION_SELECTED' => in_array($item['value'], $options)? ' selected="selected"': '',
					'OPTION_ITEM_LABEL' => $item['label'],
					// Use for render checkboxes
					'OPTION_ITEM_ID' => $field . '_' . $item['value'],
					'OPTION_ITEM_NAME' => $this->prefixId . '[' . $field . '][' . $item['value'] . ']',
					'OPTION_CHECKED' => in_array($item['value'], $options)? ' checked="checked"': '',
				);
				$subparts['###GROUP_OPTIONS###'] .= $this->cObj->substituteMarkerArray($itemTemplate, $locMarkers, '###|###');
			}
		}

		$label = $this->fieldLabels[$field];
		if ($config['minitems']>0)
			$label = $this->cObj->stdWrap($label, $this->conf['defaultConf.']['requireEntryLabel.']);
		$markers = array(
			'PREFIXID' => $this->prefixId,
			'ITEM_ID' => $field,
			'FIELDLABEL' => $this->cObj->stdWrap($label, $this->conf['renderConf.'][$this->table.'.'][$field.'.'][self::$view.'.']['label.']),
			'FIELDNAME' => $field,
			'ITEM_NAME' => $this->prefixId . '[' . $field . '][]',
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
	public function handleFormField_typeGroup($field, array $config, $template='') {
		// Hook to handle form field
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['handleFormField_typeGroup'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['handleFormField_typeGroup'] as $class) {
				$procObj = & t3lib_div::getUserObj($class);
				if ($content = $procObj->handleFormField_typeGroup($this->pi_base, $this->table, $field, $this->labelfield, $this->recordId, $this->conf, $this))
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
	 * @return	string		HTML form field content
	 */
	public function handleFormField_typeGroup_file($field, array $config, $template='') {
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
				'FILE_DEL_VALUE' => $cObj->stdWrap(htmlspecialchars($file), $this->conf['renderConf.'][$this->table.'.'][$field.'_del.'][self::$view.'.']),
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

		$label = $this->fieldLabels[$field];
		$markers = array(
			'PREFIXID' => $this->prefixId,
			'ITEM_ID' => $field,
			'FIELDLABEL' => $this->cObj->stdWrap($label, $this->conf['renderConf.'][$this->table.'.'][$field.'.'][self::$view.'.']['label.']),
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
	 * @return	mixed		The entry value
	 */
	public function getEntryValue($field) {
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
	 * @param	int		$item: The item value
	 * @param	array		$config: TCA field conf
	 * @return	mixed		The entry value
	 */
	public function getEntryValue_selectedOption($field, $item, array $config) {
		$selectedOption = '';
		$options = array();
		if ($this->pi_base->piVars['valid']) {
			if ($config['maxitems'] >1)
				$options = array_keys($this->pi_base->piVars[$field]);
			else
				$options = array($this->pi_base->piVars[$field]);
		}
		else {	// $this->pi_base->piVars['cancel'] or any submit
			if ($config['MM']) {
				$loadDBGroup = t3lib_div::makeInstance('FE_loadDBGroup');
				$loadDBGroup->start('', $config['foreign_table'], $config['MM'], $this->row['uid'], $this->table, $config);
				foreach($loadDBGroup->itemArray as $item) {
					$options[] = $item['id'];
				}
			}
			elseif ($config['maxitems'] >1) {
				$options = t3lib_div::trimExplode(',', $this->row[$field]);
			}
			else {
				$options = array($this->row[$field]);
			}
		}
		if (in_array($item, $options))
			$selectedOption = 'selected="selected"';
		return $selectedOption;
	}

	/**
	 * Retrieves TCA type "select multiple" entry value
	 *
	 * @param	string		$field: The field name
	 * @return	mixed		The array of selected value
	 */
	public function getEntryValue_selectedArray($field) {
		if (!$field)
			throw new Exception('Field is not set on getEntryValue_selectedArray.');

		$config = $GLOBALS['TCA'][$this->table]['columns'][$field]['config'];
		$options = array();
		if ($this->pi_base->piVars['valid']) {
			if ($config['maxitems'] >1)
				$options = array_keys($this->pi_base->piVars[$field]);
			else
				$options = array($this->pi_base->piVars[$field]);
		}
		else {	// $this->pi_base->piVars['cancel'] or any submit
			if ($config['MM']) {
				$loadDBGroup = t3lib_div::makeInstance('FE_loadDBGroup');
				$loadDBGroup->start('', $config['foreign_table'], $config['MM'], $this->row['uid'], $this->table, $config);
				foreach($loadDBGroup->itemArray as $item) {
					$options[] = $item['id'];
				}
			}
			elseif ($config['maxitems'] >1) {
				$options = t3lib_div::trimExplode(',', $this->row[$field]);
			}
			else {
				$options = array($this->row[$field]);
			}
		}
		return $options;
	}

	/**
	 * Retrieves selector box items (pair of key/label)
	 *
	 * @param	string		$field: The field name
	 * @param	array		$config: TCA field conf
	 * @return	mixed		Array of items
	 */
	public function getSelectItemArray($field, array $config) {
		// Hook on retrieves select items
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['getSelectItemArray'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['getSelectItemArray'] as $class) {
				$procObj = & t3lib_div::getUserObj($class);
				if ($items = $procObj->getSelectItemArray($this->pi_base, $this->table, $field, $this->fieldLabels, $this->recordId, $this->conf, $this))
					break;
			}
		}
		if (!empty($items))
			return $items;

		$items = array();
		if ($config['foreign_table']) {
			t3lib_div::loadTCA($config['foreign_table']);
			if ($label = $GLOBALS['TCA'][$config['foreign_table']]['ctrl']['label']) {
				$fields = array('`uid` AS value', '`'.$label.'` AS label');
				// Get records
				$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
					implode(',', $fields),
					$config['foreign_table'],
					'1' . $this->cObj->enableFields($config['foreign_table']),
					$label
				);
				// Init items
				$items[] = array('value'=>0);
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
	public function initItemArray(array $config)     {
		$items = array();
		if (is_array($config['items']))   {
			foreach ($config['items'] as $key=>$item) {
				$items[] = array(
					'value' => ($item[1]? $item[1]: $key),
					'label' => $GLOBALS['TSFE']->sL($item[0])
				);
			}
		}
		return $items;
	}


	/**
	 * Includes JS conform input
	 *
	 * @param	[type]		$array $files: ...
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

}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_tcafe_admin/pi1/class.tx_icstcafeadmin_FormRenderer.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_tcafe_admin/pi1/class.tx_icstcafeadmin_FormRenderer.php']);
}

?>