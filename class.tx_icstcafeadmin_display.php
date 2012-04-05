<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 In Cite Solution <techbnique@in-cite.net>
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
 *   86: class tx_icstcafeadmin_display
 *  139:     function __construct(&$pibase, $lConf, $prefixId=null, $cObj=null, $storage=null, $templateFile=null)
 *
 *              SECTION: Functions to render view list and single
 *  166:     public function displayList($table=null, $fields=null, $labelFields=null, $lConf=null)
 *  236:     public function renderListHeaders($template, $table=null, $fields=null, $labelFields=null, $lConf=null)
 *  293:     public function renderListRows($template, $table=null, $fields=null, $labelFields=null, $lConf=null)
 *  334:     public function renderListRow($template, $table=null, $fields=null, $row=null, $labelFields=null, $lConf=null)
 *  555:     public function displaySingle($table=null, $fields=null, $row=null, $labelFields=null, $lConf=null)
 *  632:     public function handleFieldValue($value, $conf)
 *  662:     private function handleFieldValue_typeInput($value, $conf)
 *  697:     private function  handleFieldValue_typeCheck($value, $conf)
 *  719:     private function  handleFieldValue_typeRadio($value, $conf)
 *
 *              SECTION: Functions to render form
 *  742:     public function displayEdit($table=null, $fields=null, $row=null, $labelFields=null, $lConf=null)
 *  767:     public function displayNew($table=null, $fields=null, $labelFields=null, $lConf=null)
 *  793:     public function renderCapture($table=null, $fields=null, $row=null, $labelFields=null, $lConf=null)
 *  935:     public function renderForm($table=null, $fields=null, $row=null, $labelFields=null, $lConf=null)
 * 1050:     public function getListedFields	($table=null, $row=null, $fields=null, $labelFields=null, $lConf)
 * 1135:     public function getSingleFormField($field=null, $row=null, &$PA)
 * 1191:     public function getSingleFormField_typeInput($field, $row, &$PA)
 * 1313:     public function getSingleFormField_typeText($field, $row, &$PA)
 * 1368:     public function getSingleFormField_typeCheck($field, $row, &$PA)
 * 1437:     public function getSingleFormField_typeRadio( $field, $row, &$PA)
 * 1487:     public function getSingleFormField_typeSelect($field, $row, &$PA)
 * 1502:     public function getSingleFormField_typeGroup($field, $row, &$PA)
 * 1625:     public function getSingleFormField_typeUnknown($field, $row, &$PA)
 *
 *              SECTION: Item-array manipulation functions (check/select/radio)
 * 1652:     function procItems($items, $iArray, $config, $table, $row, $field)
 * 1671:     protected function initItemArray($fieldValue)
 *
 *              SECTION: Control and process form	 - Process on DB
 * 1695:     protected function controlField($field=null, $evals=null, $value, $lConf)
 * 1828:     protected function process_fieldValue($field, $type, array $evals, $value, $lastValue)
 * 1970:     protected function makeJSControlField($field, $evalList, $inputId, $message)
 * 2047:     protected function saveDB($table=null, $fields=null, $dataArray=null,$id=null, $new=false)
 * 2080:     public function deleteRecord($table, $rowUid)
 * 2097:     public function hideRecord($table, $rowUid)
 *
 *              SECTION: JS functions
 * 2117:     private static function includeLibTableFilter()
 * 2132:     private static function includeLibTableFilterCss()
 * 2146:     private function includeTableFilterJS($jsId, $PA=null, $fields=null)
 * 2201:     private static function includeFormJS($controlForm, $fields, $jsControl)
 *
 *              SECTION: Other functions
 * 2224:     private function getTableFields($table=null)
 * 2240:     private function mergeLabelFields($table=null, $fields=null, $labelFields=null)
 * 2267:     protected function switchDisplay($table=null, $fields=null, $labelFields=null, $lConf=null)
 * 2343:     public function getErrors()
 *
 * TOTAL FUNCTIONS: 39
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */
class tx_icstcafeadmin_display {
	var $templateFile = 'typo3conf/ext/ics_tcafe_admin/res/template.html';	// The template file
	var $cObj;
	var $extKey = 'ics_tcafe_admin';
	var $prefixId = 'tx_icstcafeadmin_display';		// Should be same as classname of the calling plugin, used for variables
	var $icstcafeadminLL = 'LLL:EXT:ics_tcafe_admin/lib/locallang.xml:';

	var $errors = array();

	var $table = '';						// The table name to show
	var $fields = array();					// The field names to show
	var $labelFields = array();				// The label fields for fields

	var $linkTargets = array(				// The target type for link
		'_blank',
		'_self',
		'_parent',
		'_top',
	);

	var $maxInputWidth		= 48;			// The maximum abstract value for input fields
	var $maxTextareaCols	= 48;			// The maximum abstract value for textareas
	var $maxTextareaRows	= 20;			// The maximum abstract value for textareas
	var $charsPerRow		= 40;			// The number of chars expected per row when the height of a text area field is automatically calculated based on the number of characters found in the field content.
	private $dateFormat	= array(			// The format of date (See "Document library/ Core documentation")
								'date' => 'd-m-Y',
								'datetime' => 'H:i d-m-Y',
								'time' => 'H:i',
								'timesec' => 'H:i:s',
								'year' => 'Y',
							);
	var $maxGroupFileWidth 	= '150px';			// The max width for group file thumbs
	var $maxGroupFileHeight = '150px';			// The max height for group file thumbs
	var $maxGroupFileSizeWrap = array('wrap' => 'Max size&nbsp;|&nbsp;ko');	// Wrap the max file size of group file
	var $groupFileAllowedWrap = array('wrap' => '*|');				// Wrap the allowed file of group file
	var $groupFileDisallowedWrap = array('wrap' => '-|');			// Wrap the disallowed file of group file

	var $jsControl = array();

	private static $tableFilterIncluded = false;
	private static $formJSIncluded = false;

	/**
	 * Constructor
	 *
	 * @param	tslib_pibase		$pibase			Object pibase
	 * @param	array		$lConf			Local conf
	 * @param	string		$prefixId		Classname of the calling plugin
	 * @param	tslib_cObj		$cObj			Object cObj
	 * @param	string		$storage		List of pid storage
	 * @param	string		$templateFile	The template file
	 * @return	void
	 */
	function __construct(&$pibase, $lConf, $prefixId=null, $cObj=null, $storage=null, $templateFile=null) {
		$this->pibase =& $pibase;
		$this->conf = $lConf;
		$this->prefixId = ($prefixId)? $prefixId : $this->prefixId;
		$this->cObj = $cObj;
		if ($storage)
			$this->pidStorage = t3lib_div::trimExplode(',', $storage, true);
		if (empty($this->pidStorage))
			$this->errors[] =  $this->pibase->pi_getLL('error_noPidStorage', $GLOBALS['TSFE']->sL($this->icstcafeadminLL . 'error_noPidStorage'), true);
		$this->templateCode = ($templateFile)? $this->cObj->fileResource($templateFile) : $this->cObj->fileResource($this->templateFile);
	}

	/*******************************************************
	 *
	 * Functions to render view list and single
	 *
	 *******************************************************/

	/**
	 * Display list view
	 *
	 * @param	string		$table			The table name
	 * @param	mixed		$fields			List of fields
	 * @param	array		$labelFields	An associative array with key=>value where key is the field name and value the label
	 * @param	array		$lConf			The plugin's configuration
	 * @return	string		The content of list view
	 */
	public function displayList($table=null, $fields=null, $labelFields=null, $lConf=null) {
		if (!$table)
			return '';

		$content = $this->switchDisplay($table, $fields, $labelFields, $lConf);
		if ($content !== false)
			return $content;

		$lConf = $lConf? array_merge($this->conf, $lConf) : $this->conf;
		$fields = $fields? $fields: $this->getTableFields($table);
		$fields = !is_array($fields)? t3lib_div::trimExplode(',', $fields): $fields;
		$labelFields = $this->mergeLabelFields($table, implode(',', $fields), $labelFields);

		$viewId = uniqid($this->prefixId . '_list');
		self::includeLibTableFilter();
		self::includeLibTableFilterCss();
		if ($lConf['displayList.']['tableFilter'])
			$this->includeTableFilterJS($viewId, array('props.' => $lConf['displayList.']['tableFilterProps.'], 'fields.' => $lConf['displayList.']['fields.']), $fields);

		$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_LIST###');
		$subpartArray = array();
		$markers = array(
			'###PREFIXID###' => $this->prefixId,
			'###UNIQID###' => $viewId,
			'###CAPTION###' => htmlspecialchars($GLOBALS['TSFE']->sL($GLOBALS['TCA'][$table]['ctrl']['title'])),
		);

		$subpartArray['###SUBPART_HEADER###'] = $this->renderListHeaders($this->cObj->getSubpart($template, '###SUBPART_HEADER###'), $table, $fields, $labelFields, $lConf);
		$subpartArray['###SUBPART_ROWS###'] = $this->renderListRows($this->cObj->getSubpart($template, '###SUBPART_ROWS###'), $table, $fields, $labelFields, $lConf);

		$link = $this->pibase->pi_linkTP_keepPIvars_url(array('codes' => 'NEW'), 0, 1 );
		if ($lConf['target_actions.']['new.']['link']) {
			if (is_numeric($lConf['target_actions.']['new.']['link']))
				$link = $this->pibase->pi_linkTP_keepPIvars_url(
					array(
						'codes' => 'NEW',
						'backPid' => $GLOBALS['TSFE']->id,
					),
					0,
					1,
					$lConf['target_actions.']['new.']['link']
				);
			else
				$link = $lConf['target_actions.']['new.']['link'];
		}
		$markers['###LINK_NEW###'] = $link;
		$markers['###TARGET_NEW###'] = (in_array($lConf['target_actions.']['new.']['target'], $this->linkTargets))? 'target="' . $lConf['target_actions.']['new.']['target'] . '"': '';
		$markers['###LABEL_NEW###'] = $this->pibase->pi_getLL('page_new', $GLOBALS['TSFE']->sL($this->icstcafeadminLL . 'page_new'), true);

		// Hook for additionnal markers
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['additionalListMarkers'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['additionalListMarkers'] as $_classRef) {
				$_procObj = & t3lib_div::getUserObj($_classRef);
				$_procObj->additionalListMarkers($markers, $subpartArray, $template, $lConf, $this);
			}
		}

		return $this->cObj->substituteMarkerArrayCached($template, $markers, $subpartArray);
	}

	/**
	 * Render header's content
	 *
	 * @param	string		$template		The template to substitute
	 * @param	string		$table			The table name
	 * @param	mixed		$fields			List of fields
	 * @param	array		$labelFields	An associative array with key=>value where key is the field name and value the label
	 * @param	array		$lConf			The plugin's configuration
	 * @return	string		Headers content
	 */
	public function renderListHeaders($template, $table=null, $fields=null, $labelFields=null, $lConf=null) {
		if (!$table)
			return '';

		$lConf = $lConf? array_merge($this->conf, $lConf) : $this->conf;
		$fields = $fields? $fields: $this->getTableFields($table);
		$fields = !is_array($fields)? t3lib_div::trimExplode(',', $fields): $fields;
		$labelFields = $this->mergeLabelFields($table, implode(',', $fields), $labelFields);

		$markers = array();

		foreach ($fields as $field) {
			$this->headersId[$field] = uniqid($this->prefixId . '_headers');
		}

		t3lib_div::loadTCA($table);
		$sub_tmpl = $this->cObj->getSubpart($template, '###HEADER_ITEM###');
		foreach ($fields as $field) {
			$markerArray = array(
				'###HEADERID###' => $this->headersId[$field],
				'###HEADERFIELD###' => htmlspecialchars($labelFields[$field]),
			);
			$subpartArray['###HEADER_ITEM###'] .= $this->cObj->substituteMarkerArray($sub_tmpl, $markerArray);
		}

		$actions = t3lib_div::trimExplode(',', $lConf['actions'], true);
		if (!empty($actions)) {
			$this->headersId['tcafeadmin_actions'] = uniqid($this->prefixId . '_headers');
			$markerArray = array(
				'###HEADERID###' => $this->headersId['tcafeadmin_actions'],
				'###HEADERFIELD###' => $this->pibase->pi_getLL('lbl_actions', $GLOBALS['TSFE']->sL($this->icstcafeadminLL . 'lbl_actions'), true),
			);
			$subpartArray['###HEADER_ITEM###'] .= $this->cObj->substituteMarkerArray($sub_tmpl, $markerArray);
		}

		// Hook for additionnal header markers
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['additionalListHeaderMarkers'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['additionalListHeaderMarkers'] as $_classRef) {
				$_procObj = & t3lib_div::getUserObj($_classRef);
				$_procObj->additionalListHeaderMarkers($markers, $subpartArray, $template, $lConf, $this);
			}
		}

		$template = $this->cObj->substituteSubpartArray($template, $subpartArray);
		return $this->cObj->substituteMarkerArray($template, $markers);
	}

	/**
	 * Render rows
	 *
	 * @param	string		$template		The template to substitute
	 * @param	string		$table			The table name
	 * @param	mixed		$fields			List of fields
	 * @param	array		$labelFields	An associative array with key=>value where key is the field name and value the label
	 * @param	array		$lConf			The plugin's configuration
	 * @return	string		Rows content
	 */
	public function renderListRows($template, $table=null, $fields=null, $labelFields=null, $lConf=null) {
		if (!$table)
			return '';

		$lConf = $lConf? array_merge($this->conf, $lConf) : $this->conf;
		$fields = $fields? $fields: $this->getTableFields($table);
		$fields = !is_array($fields)? t3lib_div::trimExplode(',', $fields): $fields;
		$labelFields = $this->mergeLabelFields($table, implode(',', $fields), $labelFields);

		$requestFields = array_merge(array('uid'), $fields);
		$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			implode(',', $requestFields),
			$table,
			'deleted=0 AND pid IN(' . implode(',', $this->pidStorage) . ')',
			'',
			'',
			''
		);
		if (is_array($rows)) {
			foreach ($rows as $row) {
				$content .= $this->cObj->substituteSubpart(
					$template,
					'###SUBPART_ROW###',
					$this->renderListRow($this->cObj->getSubpart($template, '###SUBPART_ROW###'), $table, $fields, $row, $labelFields, $lConf)
				);
			}
		}
		return $content;
	}

	/**
	 * Render row
	 *
	 * @param	string		$template		The template to substitute
	 * @param	string		$table			The table name
	 * @param	mixed		$fields			List of fields
	 * @param	array		$row			The record to render from the database table
	 * @param	array		$labelFields	An associative array with key=>value where key is the field name and value the label
	 * @param	array		$lConf			The plugin's configuration
	 * @return	string		Row's content
	 */
	public function renderListRow($template, $table=null, $fields=null, $row=null, $labelFields=null, $lConf=null) {
		if (!$table)
			return '';

		$lConf = $lConf? array_merge($this->conf, $lConf) : $this->conf;
		$fields = $fields? $fields: $this->getTableFields($table);
		$fields = !is_array($fields)? t3lib_div::trimExplode(',', $fields): $fields;
		$labelFields = $this->mergeLabelFields($table, implode(',', $fields), $labelFields);

		t3lib_div::loadTCA($table);

		$markers = array();

		// Render fields
		$sub_tmpl['###FIELD_ITEM###'] = $this->cObj->getSubpart($template, '###FIELD_ITEM###');
		foreach ($fields as $field) {
			$value = $this->handleFieldValue($row[$field], array_merge($GLOBALS['TCA'][$table]['columns'][$field], $lConf));
			$stdWrap = $lConf['displayList.']['fields.'][$field . '.']['fieldWrap.'];
			$stdWrap = (!$stdWrap && $GLOBALS['TCA'][$table]['columns'][$field]['config']['type'] == 'text')? $lConf['displayList.']['text.']: '';
			$sub_markers = array(
				'###HEADERID###' => $this->headersId[$field],
				'###HEADERFIELD###' =>  htmlspecialchars($labelFields[$field]),
				'###FIELD###' => htmlspecialchars($this->cObj->stdWrap($value, $stdWrap)),
			);
			$subpartArray['###FIELD_ITEM###'] .= $this->cObj->substituteMarkerArray($sub_tmpl['###FIELD_ITEM###'], $sub_markers);
		}

		// Render action
		$subpartArray['###SUBPART_ACTIONS##'] = '';
		$actions = t3lib_div::trimExplode(',', $lConf['actions'], true);
		if (!empty($actions)) {
			$sub_tmpl['###SUBPART_ACTIONS###'] = $this->cObj->getSubpart($template, '###SUBPART_ACTIONS###');
				// Action single
			$subpartAction['###ACTION_SINGLE##'] = '';
			if (in_array('single', $actions)) {
				$link = $this->pibase->pi_linkTP_keepPIvars_url(
					array(
						'showUid' => $row['uid'],
						'codes' => 'SINGLE'
					),
					0,
					1
				);
				if ($lConf['target_actions.']['single.']['link']) {
					if (is_numeric($lConf['target_actions.']['single.']['link']))
						$link = $this->pibase->pi_linkTP_keepPIvars_url(
							array(
								'showUid' => $row['uid'],
								'codes' => 'SINGLE',
								'backPid' => $GLOBALS['TSFE']->id,
							),
							0,
							1,
							$lConf['target_actions.']['single.']['link']
						);
					else
						$link = $lConf['target_actions.']['single.']['link'];
				}
				$sub_markers = array(
					'###LINK_SINGLE###' => $link,
					'###TARGET_SINGLE###'=> (in_array($lConf['target_actions.']['single.']['target'], $this->linkTargets))? 'target="' . $lConf['target_actions.']['single.']['target'] . '"': '',
					'###LABEL_SINGLE###' => $this->pibase->pi_getLL('page_single', $GLOBALS['TSFE']->sL($this->icstcafeadminLL . 'page_single'), true),
				);
				$subpartAction['###ACTION_SINGLE##'] = $this->cObj->substituteMarkerArray(
					$this->cObj->getSubpart($sub_tmpl['###SUBPART_ACTIONS###'], '###ACTION_SINGLE###'),
					$sub_markers
				);
			}
				// Action edit
			$subpartAction['###ACTION_EDIT##'] = '';
			if (in_array('edit', $actions)) {
				$link = $this->pibase->pi_linkTP_keepPIvars_url(
					array(
						'showUid' => $row['uid'],
						'codes' => 'EDIT'
					),
					0,
					1
				);
				if ($lConf['target_actions.']['edit.']['link']) {
					if (is_numeric($lConf['target_actions.']['edit.']['link']))
						$link = $this->pibase->pi_linkTP_keepPIvars_url(
							array(
								'showUid' => $row['uid'],
								'codes' => 'EDIT',
								'backPid' => $GLOBALS['TSFE']->id,
							),
							0,
							1,
							$lConf['target_actions.']['edit.']['link']
						);
					else
						$link = $lConf['target_actions.']['edit.']['link'];
				}
				$sub_markers = array(
					'###LINK_EDIT###' => $link,
					'###TARGET_EDIT###'=> (in_array($lConf['target_actions.']['edit.']['target'], $this->linkTargets))? 'target="' . $lConf['target_actions.']['edit.']['target'] . '"': '',
					'###LABEL_EDIT###' => $this->pibase->pi_getLL('page_edit', $GLOBALS['TSFE']->sL($this->icstcafeadminLL . 'page_edit'), true),
				);
				$subpartAction['###ACTION_EDIT##'] = $this->cObj->substituteMarkerArray(
					$this->cObj->getSubpart($sub_tmpl['###SUBPART_ACTIONS###'], '###ACTION_EDIT###'),
					$sub_markers
				);
			}
				// Action new
			$subpartAction['###ACTION_NEW##'] = '';
			if (in_array('new', $actions)) {
				$link = $this->pibase->pi_linkTP_keepPIvars_url(array('codes' => 'NEW'), 0, 1 );
				if ($lConf['target_actions.']['new.']['link']) {
					if (is_numeric($lConf['target_actions.']['new.']['link']))
						$link = $this->pibase->pi_linkTP_keepPIvars_url(
							array(
								'codes' => 'NEW',
								'backPid' => $GLOBALS['TSFE']->id,
							),
							0,
							1,
							$lConf['target_actions.']['new.']['link']
						);
					else
						$link = $lConf['target_actions.']['new.']['link'];
				}
				$sub_markers = array(
					'###LINK_NEW###' => $link,
					'###TARGET_NEW###'=> (in_array($lConf['target_actions.']['new.']['target'], $this->linkTargets))? 'target="' . $lConf['target_actions.']['new.']['target'] . '"': '',
					'###LABEL_NEW###' => $this->pibase->pi_getLL('page_new', $GLOBALS['TSFE']->sL($this->icstcafeadminLL . 'page_new'), true),
				);
				$subpartAction['###ACTION_NEW##'] = $this->cObj->substituteMarkerArray(
					$this->cObj->getSubpart($sub_tmpl['###SUBPART_ACTIONS###'], '###ACTION_NEW###'),
					$sub_markers
				);
			}
				// Action delete
			$subpartAction['###ACTION_DELETE##'] = '';
			if (in_array('delete', $actions)) {
				$link = $this->pibase->pi_linkTP_keepPIvars_url(array('codes' => 'DELETE,LIST', 'rowUid' => $row['uid']), 0, 1 );
				if ($lConf['target_actions.']['delete.']['link']) {
					if (is_numeric($lConf['target_actions.']['delete.']['link'])) {
						$codes = array('DELETE');
						$codes[] = $lConf['target_actions.']['delete.']['redirectCode'];
						$link = $this->pibase->pi_linkTP_keepPIvars_url(
							array(
								'codes' => implode(',', $codes),
								'rowUid' => $row['uid'],
								'backPid' => $GLOBALS['TSFE']->id,
							),
							0,
							1,
							$lConf['target_actions.']['delete.']['link']
						);
					} else {
						$link = $lConf['target_actions.']['delete.']['link'];
					}
				}
				$sub_markers = array(
					'###LINK_DELETE###' => $link,
					'###TARGET_DELETE###'=> (in_array($lConf['target_actions.']['delete.']['target'], $this->linkTargets))? 'target="' . $lConf['target_actions.']['delete.']['target'] . '"': '',
					'###LABEL_DELETE###' => $this->pibase->pi_getLL('page_delete', $GLOBALS['TSFE']->sL($this->icstcafeadminLL . 'page_delete'), true),
				);
				$subpartAction['###ACTION_DELETE##'] = $this->cObj->substituteMarkerArray(
					$this->cObj->getSubpart($sub_tmpl['###SUBPART_ACTIONS###'], '###ACTION_DELETE###'),
					$sub_markers
				);
			}
				// Action hide
			$subpartAction['###ACTION_HIDE##'] = '';
			if (in_array('hide', $actions)) {
				$link = $this->pibase->pi_linkTP_keepPIvars_url(array('codes' => 'HIDE,LIST', 'rowUid' => $row['uid']), 0, 1 );
				if ($lConf['target_actions.']['hide.']['link']) {
					if (is_numeric($lConf['target_actions.']['hide.']['link'])) {
						$codes = array('HIDE');
						$codes[] = $lConf['target_actions.']['delete.']['redirectCode'];
						$link = $this->pibase->pi_linkTP_keepPIvars_url(
							array(
								'codes' => implode(',', $codes),
								'rowUid' => $row['uid'],
								'backPid' => $GLOBALS['TSFE']->id,
							),
							0,
							1,
							$lConf['target_actions.']['hide.']['link']
						);
					} else {
						$link = $lConf['target_actions.']['hide.']['link'];
					}
				}
				$sub_markers = array(
					'###LINK_HIDE###' => $link,
					'###TARGET_HIDE###'=> (in_array($lConf['target_actions.']['hide.']['target'], $this->linkTargets))? 'target="' . $lConf['target_actions.']['hide.']['target'] . '"': '',
					'###LABEL_HIDE###' => $this->pibase->pi_getLL('page_hide', $GLOBALS['TSFE']->sL($this->icstcafeadminLL . 'page_hide'), true),
				);
				$subpartAction['###ACTION_HIDE##'] = $this->cObj->substituteMarkerArray(
					$this->cObj->getSubpart($sub_tmpl['###SUBPART_ACTIONS###'], '###ACTION_HIDE###'),
					$sub_markers
				);
			}

			$subpartArray['###SUBPART_ACTIONS##'] = $this->cObj->substituteSubpartArray($sub_tmpl['###SUBPART_ACTIONS###'], $subpartAction);
		}

		// Hook for additionnal row markers
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['additionalListRowMarkers'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['additionalListRowMarkers'] as $_classRef) {
				$_procObj = & t3lib_div::getUserObj($_classRef);
				$_procObj->additionalListRowMarkers($markers, $subpartArray, $template, $row, $lConf, $this);
			}
		}

		return $this->cObj->substituteMarkerArrayCached($template, $markers, $subpartArray);
	}

	/**
	 * Display single view
	 *
	 * @param	string		$table			The table name
	 * @param	mixed		$fields			List of fields
	 * @param	array		$row			The record to render from the database table
	 * @param	array		$labelFields	An associative array with key=>value where key is the field name and value the label
	 * @param	array		$lConf			The plugin's configuration
	 * @return	string		The content of single view
	 */
	public function displaySingle($table=null, $fields=null, $row=null, $labelFields=null, $lConf=null) {
		if (!$table || !$row)
			return '';

		$lConf = $lConf? array_merge($this->conf, $lConf) : $this->conf;
		$fields = $fields? $fields: $this->getTableFields($table);
		$fields = !is_array($fields)? t3lib_div::trimExplode(',', $fields): $fields;
		$labelFields = $this->mergeLabelFields($table, implode(',', $fields), $labelFields);

		t3lib_div::loadTCA($table);

		$content = $this->switchDisplay($table, $fields, $labelFields, $lConf);
		if ($content !== false)
			return $content;

		$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_SINGLE###');
		if ($this->pibase->piVars['backPid']) {
			$backlink = $this->pibase->pi_linkToPage(
				$this->pibase->pi_getLL('backlink', $GLOBALS['TSFE']->sL($this->icstcafeadminLL . 'backlink'), true),
				$this->pibase->piVars['backPid']
			);
		} else {
			$backlink = $this->pibase->pi_linkTP(
				$this->pibase->pi_getLL('backlink', $GLOBALS['TSFE']->sL($this->icstcafeadminLL . 'backlink'), true),
				array()
			);
		}

		$markers = array(
			'###PREFIXID###' => $this->prefixId,
			'###BACKLINK###' => $backlink,
		);

		$fields = array_merge(array('uid'), $fields);
		$labelFields['uid'] = $this->pibase->pi_getLL('lbl_uid', $GLOBALS['TSFE']->sL($this->icstcafeadminLL . 'lbl_uid'), true);

		$sub_tmpl = $this->cObj->getSubpart($template, '###SUBPART_FIELD###');
		$subpartArray = array();
		foreach ($fields as $field) {
			if ($row[$field] || $lConf['displaySingle.']['showEmptyFields']) {
				$value = $this->handleFieldValue($row[$field], array_merge($GLOBALS['TCA'][$table]['columns'][$field], $lConf));

				$markerArray = array(
					'###FIELDNAME###' => $field,
					'###FIELD_LABEL###' => htmlspecialchars($this->cObj->stdWrap($labelFields[$field], $lConf['displaySingle.']['fields.'][$field. '.']['labelWrap.'])),
					'###FIELD_VALUE###' => htmlspecialchars($this->cObj->stdWrap($value, $lConf['displaySingle.']['fields.'][$field. '.']['fieldWrap.'])),
				);
				// Hook for additionnal field markers
				if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['additionalSingleFieldMarkers'])) {
					foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['additionalSingleFieldMarkers'] as $_classRef) {
						$_procObj = & t3lib_div::getUserObj($_classRef);
						$_procObj->additionalSingleFieldMarkers($markers, $subpartArray, $template, $row, $lConf, $this);
					}
				}
				$subpartArray['###SUBPART_FIELD###'] .= $this->cObj->substituteMarkerArray($sub_tmpl, $markerArray);
			}
		}

		// Hook for additionnal markers
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['additionalSingleMarkers'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['additionalSingleMarkers'] as $_classRef) {
				$_procObj = & t3lib_div::getUserObj($_classRef);
				$_procObj->additionalSingleMarkers($markers, $subpartArray, $template, $row, $lConf, $this);
			}
		}

		$template = $this->cObj->substituteSubpartArray($template, $subpartArray);
		return $this->cObj->substituteMarkerArray($template, $markers);
	}

	/**
	 * Handles field's value
	 *
	 * @param	array		$conf	The field's configuration
	 * @param	mixed		$value	The field's value
	 * @return	the		value
	 */
	public function handleFieldValue($value, $conf) {
		$type = $conf['config']['type'];
		switch ($type) {
			case 'input' :
				$value = $this->handleFieldValue_typeInput ($value, $conf);
			break;

			case 'text' :
			break;

			case 'check' :
				$value = $this->handleFieldValue_typeCheck ($value, $conf);
			break;

			case 'radio' :
				$value = $this->handleFieldValue_typeRadio ($value, $conf);
			break;
			
			case 'select' :
				$value = $this->handleFieldValue_typeSelect ($value, $conf);
			break;

			default:
		}
		return $value;
	}

	/**
	 * Handles  input field's value
	 *
	 * @param	array		$conf
	 * @param	string		$value
	 * @return	the		processed value
	 */
	private function handleFieldValue_typeInput($value, $conf) {
		$evalList = t3lib_div::trimExplode(',', $conf['config']['eval'], true);
		$dateFormat = '';
		if (in_array('date', $evalList)) {
			$dateFormat = $this->dateFormat['date'];
		}
		if (in_array('datetime', $evalList)) {
			$dateFormat = $this->dateFormat['datetime'];
		}
		if (in_array('time', $evalList)) {
			$dateFormat = $this->dateFormat['time'];
		}
		if (in_array('timesec', $evalList)) {
			$dateFormat = $this->dateFormat['timesec'];
		}
		if (in_array('year', $evalList)) {
			$dateFormat = $this->dateFormat['year'];
		}
		if ($dateFormat) {
			$value = $value? date($dateFormat, $value) : '';
		}
		// Process password
		if (in_array('password', $evalList)) {
			$value = '**********';
		}
		return $this->cObj->stdWrap($value, $stdWrap);
	}

	/**
	 * Handles check field's value
	 *
	 * @param	array		$conf
	 * @param	string		$value
	 * @return	the		processed value
	 */
	private function  handleFieldValue_typeCheck($value, $conf) {
		$cols = $conf['config']['cols'];
		if ($cols) {
			$selItems = $this->initItemArray($conf);
			$valueChecked = array();
			for ($c=0; $c<$cols; $c++) {
				if ($value & pow(2, $c)) {
					$valueChecked[] = $selItems[$c][0];
				}
			}
			$value = implode(', ', $valueChecked);
		}
		return $value;
	}

	/**
	 * Handles radio field's value
	 *
	 * @param	array		$conf
	 * @param	string		$value
	 * @return	the		processed value
	 */
	private function  handleFieldValue_typeRadio($value, $conf) {
		$selItems = $this->initItemArray($conf);
		return $selItems[$value][0];
	}

	/**
	 * Handles select field's value
	 *
	 * @param	array		$conf
	 * @param	string		$value
	 * @return	the		processed value
	 */
	private function  handleFieldValue_typeSelect($value, $conf) {
		$config = $conf['config'];
	
		return $value;
	}


	/*******************************************************
	 *
	 * Functions to render form
	 *
	 *******************************************************/

	/**
	 * Display edit view
	 *
	 * @param	string		$template	The template to substitute
	 * @param	string		$table		The table name
	 * @param	mixed		$fields		List of fields
	 * @param	array		$row		The record to render from the database table
	 * @param	array		$labelFields	An associative array with key=>value where key is the field name and value the label
	 * @param	array		$lConf		The plugin's configuration
	 * @return	string		The content of edit view
	 */
	public function displayEdit($table=null, $fields=null, $row=null, $labelFields=null, $lConf=null) {
		if (!$table || !$row)
			return '';

		$lConf = $lConf? array_merge($this->conf, $lConf) : $this->conf;
		$fields = $fields? $fields: $this->getTableFields($table);
		$fields = !is_array($fields)? t3lib_div::trimExplode(',', $fields): $fields;
		$labelFields = $this->mergeLabelFields($table, implode(',', $fields), $labelFields);

		$content = $this->switchDisplay($table, $fields, $labelFields, $lConf);
		if ($content !== false)
			return $content;

		return $this->renderCapture($table, $fields, $row, $labelFields, $lConf);
	}

	/**
	 * Display new view
	 *
	 * @param	string		$table		The table name
	 * @param	mixed		$fields		List of fields
	 * @param	array		$labelFields	An associative array with key=>value where key is the field name and value the label
	 * @param	array		$lConf		The plugin's configuration
	 * @return	string		The content of new view
	 */
	public function displayNew($table=null, $fields=null, $labelFields=null, $lConf=null) {
		if (!$table)
			return '';

		$lConf = $lConf? array_merge($this->conf, $lConf) : $this->conf;
		$fields = $fields? $fields: $this->getTableFields($table);
		$fields = !is_array($fields)? t3lib_div::trimExplode(',', $fields): $fields;
		$labelFields = $this->mergeLabelFields($table, implode(',', $fields), $labelFields);

		$content = $this->switchDisplay($table, $fields, $labelFields, $lConf);
		if ($content !== false)
			return $content;

		return $this->renderCapture($table, $fields, null, $labelFields, $lConf);
	}

	/**
	 * Render capture
	 *
	 * @param	string		$table		The table name
	 * @param	mixed		$fields		List of fields
	 * @param	array		$row		The record to render from the database table
	 * @param	array		$labelFields	An associative array with key=>value where key is the field name and value the label
	 * @param	array		$lConf		The plugin's configuration
	 * @return	string		The form content
	 */
	public function renderCapture($table=null, $fields=null, $row=null, $labelFields=null, $lConf=null) {
		if (!$table)
			return '';

		t3lib_div::loadTCA($table);

		$lConf = $lConf? array_merge($this->conf, $lConf) : $this->conf;
		$fields = $fields? $fields: $this->getTableFields($table);
		$fields = !is_array($fields)? t3lib_div::trimExplode(',', $fields): $fields;
		$labelFields = $this->mergeLabelFields($table, implode(',', $fields), $labelFields);
		$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_CAPTURE###');

		//	Process submitted form
		$control = true;
		$infotext = '';
		if (isset($this->pibase->piVars['btn_save'])) {
			$dataArray = array();
			// Control and process fields values
			foreach ($fields as $field) {
				$config = $GLOBALS['TCA'][$table]['columns'][$field]['config'];
				$evals = t3lib_div::trimExplode(',', $config['eval'], true);
				if ($lConf['controlFields.']['php_control']) {
					if (!empty($evals)) {
						$control = $this->controlField (
							$field,
							$evals,
							$this->pibase->piVars['data'][$table][$field]['value'],
							$lConf
						);
						if (!$control)
							break;
					}
				}
				$dataArray[$field] = $this->process_fieldValue(
					$field,
					$config['type'],
					$evals,
					$this->pibase->piVars['data'][$table][$field]['value'],
					$this->pibase->piVars['data'][$table][$field]['hiddenvalue']
				);
				if (!empty($this->errors)) {
					$control = false;
					break;
				}
			}

			// Save in DB
			if ($control) {
				$pass_saveDB = true;
				if (is_array($row) && !empty($row)) {	// update
					if (!$this->saveDB($table, $fields, $dataArray, $this->pibase->piVars['rowUid'])) {
						$pass_saveDB = false;
						$this->errors[] = $this->pibase->pi_getLL('error_saveDB', $GLOBALS['TSFE']->sL($this->icstcafeadminLL . 'error_saveDB'), true);
					}
				} else {	// insert
					if (!$this->pibase->piVars['pidStorage']) {
						$pass_saveDB = false;
						$this->errors[] = $this->pibase->pi_getLL('error_record_nopidstorage', $GLOBALS['TSFE']->sL($this->icstcafeadminLL . 'error_record_nopidstorage'), true);
					}
					if ($pass_saveDB && !$this->saveDB($table, $fields, $dataArray, $this->pibase->piVars['pidStorage'], true)) {
						$pass_saveDB = false;
						$this->errors[] = $this->pibase->pi_getLL('error_saveDB', $GLOBALS['TSFE']->sL($this->icstcafeadminLL . 'error_saveDB'), true);
					}
				}
			}

			// Get infotext
			if ($pass_saveDB) {
				if (is_array($row) && !empty($row)) {
					if ($lConf['infoFields_updateDB']) {
						$info_fields = t3lib_div::trimExplode(',', $lConf['infoFields_updateDB']);
						$info_update = array();
						foreach ($info_fields as $field) {
							$info_update[] = $this->pibase->piVars['data'][$table][$field]['value'];
						}
						$info_update = implode(',', $info_update);
					}
					$infotext = '<p>' . sprintf($this->pibase->pi_getLL('record_save', $GLOBALS['TSFE']->sL($this->icstcafeadminLL . 'record_update'), true), $table, $info_update, $row['uid']) . '</p>';
				} else {
					if ($lConf['infoFields_insertDB']) {
						$info_fields = t3lib_div::trimExplode(',', $lConf['infoFields_insertDB']);
						$info_insert = array();
						foreach ($info_fields as $field) {
							$info_insert[] = $this->pibase->piVars['data'][$table][$field];
						}
						$info_insert = implode(',', $info_insert);
					}
					$infotext = '<p>' . sprintf($this->pibase->pi_getLL('record_save', $GLOBALS['TSFE']->sL($this->icstcafeadminLL . 'record_insert'), true), $table, $info_insert, $row['uid']) . '</p>';
				}
			}
		}

		// Render form
		$form = '';
		if (!isset($this->pibase->piVars['btn_save']) || (isset($this->pibase->piVars['btn_save']) && (!$control || ($lConf['renderformaftersave'] == 1)))) {
			if ($this->pibase->piVars['btn_save'])
				$lConf['submitted_form'] = true;
			$form = $this->renderForm($table, $fields, $row, $labelFields, $lConf);
		}

		// Get backlink
		$backlink = '';
		if ($this->pibase->piVars['backPid']) {
			$backlink = $this->pibase->pi_linkToPage(
				$this->pibase->pi_getLL('backlink', $GLOBALS['TSFE']->sL($this->icstcafeadminLL . 'backlink'), true),
				$this->pibase->piVars['backPid']
			);
		} else {
			$backlink = $this->pibase->pi_linkTP(
				$this->pibase->pi_getLL('backlink', $GLOBALS['TSFE']->sL($this->icstcafeadminLL . 'backlink'), true),
				array()
			);
		}

		$markers = array(
			'###INFOTEXT###' => $infotext,
			'###FORM###' => $form,
			'###BACKLINK###' => $backlink,
		);

		// Hook for additionnal markers
		$subpartArray = array();
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['additionalSingleMarkers'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['additionalSingleMarkers'] as $_classRef) {
				$_procObj = & t3lib_div::getUserObj($_classRef);
				$_procObj->additionalCaptureMarkers($markers, $subpartArray, $template, $row, $lConf, $this);
			}
		}
		$template = $this->cObj->substituteSubpartArray($template, $subpartArray);
		return $this->cObj->substituteMarkerArray($template, $markers);
	}

	/**
	 * Render form
	 *
	 * @param	string		$table		The table name
	 * @param	mixed		$fields		List of fields
	 * @param	array		$row		The record to render from the database table
	 * @param	array		$labelFields	An associative array with key=>value where key is the field name and value the label
	 * @param	array		$lConf		The plugin's configuration
	 * @return	string		The form content
	 */
	public function renderForm($table=null, $fields=null, $row=null, $labelFields=null, $lConf=null) {
		if (!$table)
			return '';

		$lConf = $lConf? array_merge($this->conf, $lConf) : $this->conf;
		$fields = $fields? $fields: $this->getTableFields($table);
		$fields = !is_array($fields)? t3lib_div::trimExplode(',', $fields): $fields;
		$labelFields = $this->mergeLabelFields($table, implode(',', $fields), $labelFields);

		$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_FORM###');

		$fieldset_formFields_id = 'tx-icstcafeadmin-formfields';
		$el_pidStorage_id = 'tx-icstcafeadmin-formstorage';

		// Render pidStorage control "SELECT"
		$subpartArray['###SUBPART_PIDSTORAGE###'] = '';
		if (!is_array($row) || empty($row)) {
			if (count($this->pidStorage)>1) {
				$storage = '\'' . implode('\',\'',$this->pidStorage) . '\'';
				$pids = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
					'uid, title',
					'pages',
					'uid IN(' . $storage . ')',
					'',
					'title ASC'
				);
				if (is_array($pids) && !empty($pids)) {
					$subPidstorage_tmpl = $this->cObj->getSubpart($template, '###SUBPART_PIDSTORAGE###');
					$subPidstorage_markers = array(
						'###FIELDSET_PIDSTORAGE###' => $this->pibase->pi_getLL('fieldset_pidstorage', $GLOBALS['TSFE']->sL($this->icstcafeadminLL . 'fieldset_pidstorage'), true),
						'###PIDSTORAGE_LABEL###' =>  $this->pibase->pi_getLL('pidstorage', $GLOBALS['TSFE']->sL($this->icstcafeadminLL . 'pidstorage'), true),
						'###PIDSTORAGE_NAME###' => $this->prefixId . '[pidStorage]',
						'###PIDSTORAGE_ID###' => $el_pidStorage_id,
						'###ONCLICK_PIDSTORAGE###' => 'onclick="show_formFields(this, \'' . $fieldset_formFields_id . '\')"',
					);
					foreach ($pids as $pid) {
						$subPidstorageItems_tmpl = $this->cObj->getSubpart($template, '###PIDSTORAGE_ITEMS###');
						$subPidstorageItems_markers = array(
							'###PIDSTORAGE_ITEM_VALUE###' => $pid['uid'],
							'###PIDSTORAGE_ITEM_TITLE###' => $pid['title'],
							'###PIDSTORAGE_ITEM_UID###' => $pid['uid'],
						);
						$subPidstorageItems .= $this->cObj->substituteMarkerArray($subPidstorageItems_tmpl,$subPidstorageItems_markers);
					}
					$subPidstorage_tmpl = $this->cObj->substituteSubpart($subPidstorage_tmpl, '###PIDSTORAGE_ITEMS###', $subPidstorageItems);
					$subpartArray['###SUBPART_PIDSTORAGE###'] = $this->cObj->substituteMarkerArray($subPidstorage_tmpl, $subPidstorage_markers);

					$styles = '
						#' . $fieldset_formFields_id . ' {
							visibility: hidden;
							display: none;
						}
					';
					$GLOBALS['TSFE']->additionalCSS['tcafeadmin_formFields'] = $styles;
				} else {
					$subpartArray['###SUBPART_PIDSTORAGE###'] = '<p class="error">' . sprintf($this->pibase->pi_getLL('error_noRecordInPidStorage' ,$GLOBALS['TSFE']->sL($this->icstcafeadminLL . 'error_noRecordInPidStorage'), true), implode(',', $this->pidStorage)) . '</p>';
				}
			} else {
				$subpartArray['###SUBPART_PIDSTORAGE###'] = '<input type="hidden" name="' . $this->prefixId . '[pidStorage]" value="' . $this->pidStorage[0] . '"/>';
			}
		}

		// Render form fields
		$this->formName = $lConf['formName']? $lConf['formName']: 'form';

		$contentFields = $this->getListedFields(
			$table,
			$row,
			implode(',', $fields),
			$labelFields,
			$lConf
		);

		if (is_array($row) && !empty($row)) {
			$caption = $this->pibase->pi_getLL('editform', $GLOBALS['TSFE']->sL($this->icstcafeadminLL . 'editform'), true);
		} else {
			$caption = $this->pibase->pi_getLL('newform', $GLOBALS['TSFE']->sL($this->icstcafeadminLL . 'newform'), true);
		}

		// Get control form
		$click_registration = '';
		if ($lConf['controlFields.']['js_control']) {
			$click_registration = 'onclick="return control.submit()"';
		}
		self::includeFormJS($lConf['controlFields.']['js_control'], $fields, $this->jsControl);

		$markers = array(
			'###PREFIXID###' => $this->prefixId,
			'###TITLE_FORM###' => $caption,
			'###FORM_NAME###' => $this->formName,
			'###URL###' => t3lib_div::getIndpEnv('TYPO3_REQUEST_URL'),
			'###FIELDSET_FIELDS###' => $this->pibase->pi_getLL('fieldset_fields', $GLOBALS['TSFE']->sL($this->icstcafeadminLL . 'fieldset_fields'), true),
			'###FIELDS_ID###' => $fieldset_formFields_id,
			'###FIELDS###' => $contentFields,
			'###ROW_NAME###' => $this->prefixId . '[rowUid]',
			'###ROW_VALUE###' => $row['uid'],
			'###ROW_ID###' => uniqid($this->prefixId . '_rowID'),
			'###BTNREGISTRATION_NAME###' => $this->prefixId . '[btn_save]',
			'###BTNREGISTRATION_VALUE###' => $this->pibase->pi_getLL('btn_save', $GLOBALS['TSFE']->sL($this->icstcafeadminLL . 'btn_save'), true),
			'###ONCLICK_BTNREGISTRATION###' => $click_registration,
			'###BACKLINK###' => $backlink,
		);
		return $this->cObj->substituteMarkerArrayCached($template, $markers, $subpartArray);
	}

	/**
	 * Retrieves form for the list of fields
	 *
	 * @param	string		$table			The table name
	 * @param	array		$row			The record to render from the database table
	 * @param	string		$fields			A comma list of fields
	 * @param	array		$labelFields	An associative array with key=>value where key is the field name and value the label
	 * @param	array		$lConf			Plugin's local configuration
	 * @return	mixed		The form or null
	 */
	public function getListedFields	($table=null, $row=null, $fields=null, $labelFields=null, $lConf) {
		if (!$table)
			return '';
		t3lib_div::loadTCA($table);

		$fields = $fields? t3lib_div::trimExplode(',', $fields, true) : $this->getTableFields($table);
		if (!$labelFields)
			$labelFields = $this->mergeLabelFields($table, implode(',', $fields), $labelFields);

		$PA = $lConf;
		foreach ($fields as $field) {
			// Get the TCA configuration for the current field:
			$PA['table'] = $table;
			$PA['fieldConf'] = $GLOBALS['TCA'][$table]['columns'][$field];
			$PA['fieldConf']['config'] = $GLOBALS['TCA'][$table]['columns'][$field]['config'];
			// Get current field configuration onChange
			if ($lConf['renderForm.']['fields.'][$field . '.']['onChange']) {
				$PA['fieldConf']['onChange'] = $lConf['renderForm.']['fields.'][$field . '.']['onChange'];
			}
			// Get current field configuration groupFile
			if ($lConf['renderForm.']['groupFile.']) {
				$PA['fieldConf']['maxGroupFileWidth'] = $lConf['renderForm.']['groupFile.']['maxGroupFileWidth'];
				$PA['fieldConf']['maxGroupFileHeight'] = $lConf['renderForm.']['groupFile.']['maxGroupFileHeight'];
				$PA['fieldConf']['allowed_stdWrap'] = $lConf['renderForm.']['groupFile.']['allowed_stdWrap'];
				$PA['fieldConf']['disallowed_stdWrap'] = $lConf['renderForm.']['groupFile.']['disallowed_stdWrap'];
				$PA['fieldConf']['separatorExtFile'] = $lConf['renderForm.']['groupFile.']['separatorExtFile'];
				$PA['fieldConf']['show_thumbs'] = $lConf['renderForm.']['groupFile.']['show_thumbs'];
			}
			if ($lConf['renderForm.']['fields.'][$field . '.']['maxGroupFileWidth']) {
				$PA['fieldConf']['maxGroupFileWidth'] = $lConf['renderForm.']['fields.'][$field . '.']['maxGroupFileWidth'];
			}
			if ($lConf['renderForm.']['fields.'][$field . '.']['maxGroupFileHeight']) {
				$PA['fieldConf']['maxGroupFileHeight'] = $lConf['renderForm.']['fields.'][$field . '.']['maxGroupFileHeight'];
			}
			if ($lConf['renderForm.']['fields.'][$field . '.']['allowed_stdWrap']) {
				$PA['fieldConf']['allowed_stdWrap'] = $lConf['renderForm.']['fields.'][$field . '.']['allowed_stdWrap'];
			}
			if ($lConf['renderForm.']['fields.'][$field . '.']['disallowed_stdWrap']) {
				$PA['fieldConf']['disallowed_stdWrap'] = $lConf['renderForm.']['fields.'][$field . '.']['disallowed_stdWrap'];
			}
			if ($lConf['renderForm.']['fields.'][$field . '.']['separatorExtFile']) {
				$PA['fieldConf']['separatorExtFile'] = $lConf['renderForm.']['fields.'][$field . '.']['separatorExtFile'];
			}
			if ($lConf['renderForm.']['fields.'][$field . '.']['show_thumbs']) {
				$PA['fieldConf']['show_thumbs'] = $lConf['renderForm.']['fields.'][$field . '.']['show_thumbs'];
			}
			// Get current field configuration text
			$PA['fieldConf']['maxTextareaCols'] = $this->maxTextareaCols;
			$PA['fieldConf']['maxTextareaRows'] = $this->maxTextareaRows;
			$PA['fieldConf']['charsPerRow'] = $this->charsPerRow;
			if ($lConf['renderForm.']['text.']) {
				$PA['fieldConf']['maxTextareaCols'] = $lConf['renderForm.']['text.']['maxTextareaCols']? $lConf['renderForm.']['text.']['maxTextareaCols']: $this->maxTextareaCols;
				$PA['fieldConf']['maxTextareaRows'] = $lConf['renderForm.']['text.']['maxTextareaRows']? $lConf['renderForm.']['text.']['maxTextareaRows']: $this->maxTextareaRows;
				$PA['fieldConf']['charsPerRow'] = $lConf['renderForm.']['text.']['charsPerRow']? $lConf['renderForm.']['text.']['charsPerRow']: $this->charsPerRow;
			}
			if ($lConf['renderForm.']['fields.'][$field . '.']['cols'])
				$PA['fieldConf']['config']['cols'] = $lConf['renderForm.']['fields.'][$field . '.']['cols'];
			if ($lConf['renderForm.']['fields.'][$field . '.']['rows'])
				$PA['fieldConf']['config']['rows'] = $lConf['renderForm.']['fields.'][$field . '.']['rows'];

			// Form field's name
			$PA['itemFormElName'] = $this->prefixId . '[data][' . $table . '][' . $field . '][value]';
			// Form field's label
			$PA['itemFormElLabel'] = $labelFields[$field];
			// The value to display in the form field.
			$PA['itemFormElValue'] = $row[$field];
			if ($PA['submitted_form'])
				$PA['submitted_itemFormElValue'] = $this->pibase->piVars['data'][$table][$field]['value'];
			$PA['itemFormElHiddenName'] = $this->prefixId . '[data][' . $table . '][' . $field . '][hiddenvalue]';
			// Form field's ID
			$PA['itemFormElID'] = uniqid($this->prefixId);

			$content .= $this->getSingleFormField($field, $row, $PA);
		}
		return $content;
	}

	/**
	 * Retrieves form for the field
	 *
	 * @param	string		$field	The field name
	 * @param	array		$row	The record to edit from the database table
	 * @param	array		$PA		Array of parameters
	 * @return	string		The form
	 */
	public function getSingleFormField($field=null, $row=null, &$PA) {
		if (!$field)
			return;

		$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_FIELDS###');
		switch($PA['fieldConf']['config']['type']) {
			case 'input':
				if (in_array('password', t3lib_div::trimExplode(',', $PA['fieldConf']['config']['eval'])))
					$PA['template'] =  $this->cObj->getSubpart($template, '###SUBPART_PASSWORD###');
				else
					$PA['template'] =  $this->cObj->getSubpart($template, '###SUBPART_INPUT###');
				$item = $this->getSingleFormField_typeInput($field, $row, $PA);
			break;

			case 'text':
				$PA['template'] =  $this->cObj->getSubpart($this->templateCode, '###SUBPART_TEXT###');
				$item = $this->getSingleFormField_typeText($field, $row, $PA);
			break;

			case 'check':
				$PA['template'] =  $this->cObj->getSubpart($this->templateCode, '###SUBPART_CHECK###');
				if (!is_array($row))
					$PA['itemFormElValue'] = $PA['fieldConf']['config']['default'];
				$item = $this->getSingleFormField_typeCheck($field, $row, $PA);
			break;

			case 'radio':
				$PA['template'] =  $this->cObj->getSubpart($this->templateCode, '###SUBPART_RADIO###');
				$item = $this->getSingleFormField_typeRadio($field, $row, $PA);
			break;

			case 'select':
				$PA['template'] =  $this->cObj->getSubpart($this->templateCode, '###SUBPART_SELECT###');
				$item = $this->getSingleFormField_typeSelect($field, $row, $PA);
			break;

			case 'group':
				$PA['template'] =  $this->cObj->getSubpart($this->templateCode, '###SUBPART_GROUP###');
				$item = $this->getSingleFormField_typeGroup($field, $row, $PA);
			break;

			default:
				$item = $this->getSingleFormField_typeUnknown($field, $row, $PA);
				break;
			}
		return $item;
	}

	/**
	 * Render field type input
	 *
	 * @param	string		$field	The field name
	 * @param	array		$row	The record to edit from the database table
	 * @param	array		$PA		Array of parameters
	 * @return	string		The HTML code for table field
	 */
	public function getSingleFormField_typeInput($field, $row, &$PA) {
		$config = $PA['fieldConf']['config'];
		$onChange = $PA['fieldConf']['onChange'];
		$template = $PA['template'];
		$size = t3lib_div::intInRange(($config['size']? $config['size']:30), 5, $this->maxInputWidth);
		$evalList = t3lib_div::trimExplode(',', $config['eval'], true);

		$inputId = $PA['itemFormElID'] . '-textfield';
		if (in_array('required', $evalList)) {
			$info_eval .= $this->pibase->pi_getLL('info_evalRequired', $GLOBALS['TSFE']->sL($this->icstcafeadminLL . 'info_evalRequired'), true);
		}
		if (in_array('date', $evalList)) {
			$dateFormat = $this->dateFormat['date'];
			$inputId = $PA['itemFormElID'] . '-datefield';
			$cssClasses = 'tx-icstcafeadmin-datefield';
			$info_eval .= sprintf($this->pibase->pi_getLL('info_evalDate', $GLOBALS['TSFE']->sL($this->icstcafeadminLL . 'info_evalDate'), true), $this->dateFormat['date'], date($this->dateFormat['date']));
			$onChange .= ' onKeyUp="conformDate(this);"';

		} elseif (in_array('datetime', $evalList)) {
			$dateFormat = $this->dateFormat['datetime'];
			$inputId = $PA['itemFormElID'] . '-datetimefield';
			$cssClasses = 'tx-icstcafeadmin-datetimefield';
			$info_eval .= sprintf($this->pibase->pi_getLL('info_evalDatetime', $GLOBALS['TSFE']->sL($this->icstcafeadminLL . 'info_evalDatetime'), true), $this->dateFormat['datetime'], date($this->dateFormat['datetime']));
			$onChange .= ' onKeyUp="conformDatetime(this);"';

		} elseif (in_array('time', $evalList)) {
			$dateFormat = $this->dateFormat['time'];
			$inputId = $PA['itemFormElID'] . '-timefield';
			$cssClasses = 'tx-icstcafeadmin-timefield';
			$info_eval .= sprintf($this->pibase->pi_getLL('info_evalTime', $GLOBALS['TSFE']->sL($this->icstcafeadminLL . 'info_evalTime'), true), $this->dateFormat['time'], date($this->dateFormat['time']));
			$onChange .= ' onKeyUp="conformTime(this);"';

		} elseif (in_array('timesec', $evalList)) {
			$dateFormat = $this->dateFormat['timesec'];
			$inputId = $PA['itemFormElID'] . '-timesecfield';
			$cssClasses = 'tx-icstcafeadmin-timesecfield';
			$info_eval .= sprintf($this->pibase->pi_getLL('info_evalTimesec', $GLOBALS['TSFE']->sL($this->icstcafeadminLL . 'info_evalTimesec'), true), $this->dateFormat['timesec'], date($this->dateFormat['timesec']));
			$onChange .= ' onKeyUp="conformTimesec(this);"';

		} elseif (in_array('year', $evalList)) {
			$dateFormat = $this->dateFormat['year'];
			$inputId = $PA['itemFormElID'] . '-yearfield';
			$cssClasses = 'tx-icstcafeadmin-yearfield';
			$info_eval .= sprintf($this->pibase->pi_getLL('info_evalYear', $GLOBALS['TSFE']->sL($this->icstcafeadminLL . 'info_evalYear'), true), $this->dateFormat['year'], date($this->dateFormat['year']));
			$onChange .= ' onBlur="conformInt(this);"';

		} elseif (in_array('int', $evalList)) {
			$inputId = $PA['itemFormElID'] . '-intfield';
			$cssClasses = 'tx-icstcafeadmin-intfield';
			$info_eval .= sprintf($this->pibase->pi_getLL('info_evalInt', $GLOBALS['TSFE']->sL($this->icstcafeadminLL . 'info_evalInt'), true), $this->dateFormat['int'], date($this->dateFormat['int']));
			$onChange .= ' onBlur="conformInt(this);"';

		} elseif (in_array('double2', $evalList)) {
			$inputId = $PA['itemFormElID'] . '-doublefield';
			$cssClasses = 'tx-icstcafeadmin-doublefield';
			$info_eval .= sprintf($this->pibase->pi_getLL('info_evalDouble', $GLOBALS['TSFE']->sL($this->icstcafeadminLL . 'info_evalDouble'), true), $this->dateFormat['double'], date($this->dateFormat['double']));
			$onChange .= ' onBlur="conformFloat(this);"';

		}
		if (in_array('alphanum', $evalList)) {
			$info_eval .= sprintf($this->pibase->pi_getLL('info_evalAlphanum', $GLOBALS['TSFE']->sL($this->icstcafeadminLL . 'info_evalAlphanum'), true), $this->dateFormat['double'], date($this->dateFormat['double']));
			$onChange .= ' onBlur="conformAlphanum(this);"';
		}
		if (in_array('upper', $evalList)) {
			$info_eval .= sprintf($this->pibase->pi_getLL('info_evalUpper', $GLOBALS['TSFE']->sL($this->icstcafeadminLL . 'info_evalUpper'), true), $this->dateFormat['double'], date($this->dateFormat['double']));
			$onChange .= ' onBlur="conformUpper(this);"';
		}
		if (in_array('lower', $evalList)) {
			$info_eval .= sprintf($this->pibase->pi_getLL('info_evalLower', $GLOBALS['TSFE']->sL($this->icstcafeadminLL . 'info_evalLower'), true), $this->dateFormat['double'], date($this->dateFormat['double']));
			$onChange .= ' onBlur="conformLower(this);"';
		}
		if (in_array('nospace', $evalList)) {
			$info_eval .= sprintf($this->pibase->pi_getLL('info_evalNospace', $GLOBALS['TSFE']->sL($this->icstcafeadminLL . 'info_evalNospace'), true), $this->dateFormat['double'], date($this->dateFormat['double']));
			$onChange .= ' onBlur="conformNospace(this);"';
		}


		// Get code for global js control
		if ($PA['controlFields.']['js_control'] && !empty($evalList)) {
			$this->makeJSControlField($field, $evalList, $inputId, $PA['controlFields.'][$field . '.']['message.']);
		}
		// Get message for php control message
		if ($PA['controlFields.']['php_control']) {
			$ctrl_message = $this->ctrl_messages[$field]? $this->cObj->stdWrap($this->ctrl_messages[$field], $PA['controlFields.']['message.']['input.']['msg_stdWrap.']): '';
		}

		// Get value to display
		if ($PA['submitted_form']) {
			$value = $PA['submitted_itemFormElValue'];
		} else {
			$value = $PA['itemFormElValue'];
			if ($dateFormat) {
				$value = $PA['itemFormElValue']? date($dateFormat, $PA['itemFormElValue']) : '';
			}
			if (in_array('md5', $evalList)) {
				$md5Content = '<input type="hidden" name="' . $PA['itemFormElHiddenName'] . '" id="' . uniqid($this->prefixId) . '-hidden-md5" value="' . $value . '"/>';
			}
		}

		$markers = array(
			'###ITEMFORMEL_LABEL###' => htmlspecialchars($PA['itemFormElLabel']),
			'###ITEMFORMEL_ID###' => $inputId,
			'###ITEMFORMEL_CLASS###' => $cssClasses,
			'###ITEMFORMEL_NAME###' => $PA['itemFormElName'],
			'###ITEMFORMEL_VALUE###' => htmlspecialchars($value),
			'###ITEMFORMEL_ONCHANGE###' => $onChange,
			'###ITEMFORMEL_INFOEVAL###' => $info_eval,
			'###ITEMFORMEL_SIZE###' => ($size? 'size="' . $size . '"' : ''),
			'###CTRL_MESSAGE###' => $ctrl_message ,
		);

		return $this->cObj->substituteMarkerArray($template, $markers) . $md5Content;
	}

	/**
	 * Render field type text
	 *
	 * @param	string		$field	The field name
	 * @param	array		$row	The record to edit from the database table
	 * @param	array		$PA		Array of parameters
	 * @return	string		The HTML code for table field
	 */
	public function getSingleFormField_typeText($field, $row, &$PA) {
		$conf = $PA['fieldConf'];
		$config = $PA['fieldConf']['config'];
		$evalList = t3lib_div::trimExplode(',', $config['eval'], true);
		$template = $PA['template'];

		$cols = t3lib_div::intInRange($config['cols']? $config['cols'] : 30, 5, $conf['maxTextareaCols']);
		$rows = t3lib_div::intInRange($config['rows']? $config['rows'] : 5, 1, $conf['maxTextareaRows']);

		// Setting number of rows:
		if (strlen($PA['itemFormElValue']) > $conf['charsPerRow']*2) {
			$cols = $conf['maxTextareaCols'];
			$calcRows = t3lib_div::intInRange(
				round(strlen($PA['itemFormElValue'])/$conf['charsPerRow']),
				count(explode(chr(10),$PA['itemFormElValue'])),
				$conf['maxTextareaRows']
			);
			$rows = ($calcRows>$rows) ? $calcRows: $rows;
		}

		$inputId = $PA['itemFormElID'] . '-textareafield';
		// Get code for global js control
		if ($PA['controlFields.']['js_control'] && !empty($evalList)) {
			$this->makeJSControlField($field, $evalList, $inputId, $PA['controlFields.'][$field . '.']['message.']);
		}
		// Get message for php control message
		if ($PA['controlFields.']['php_control']) {
			$ctrl_message = $this->ctrl_messages[$field]? $this->cObj->stdWrap($this->ctrl_messages[$field], $PA['controlFields.']['message.']['text.']['msg_stdWrap.']): '';
		}

		$value = $PA['submitted_form']? $PA['submitted_itemFormElValue']: $PA['itemFormElValue'];

		$markers = array(
			'###ITEMFORMEL_LABEL###' => htmlspecialchars($PA['itemFormElLabel']),
			'###ITEMFORMEL_ID###' => $inputId,
			'###ITEMFORMEL_COLS###' => 'cols="' . $cols . '"',
			'###ITEMFORMEL_ROWS###' => 'rows="' . $rows . '"',
			'###ITEMFORMEL_WRAP###' => '',	// wrap="wrap"
			'###ITEMFORMEL_NAME###' => $PA['itemFormElName'],
			'###ITEMFORMEL_VALUE###' => htmlspecialchars($value),
			'###ITEMFORMEL_ONCHANGE###' => '',
			'###CTRL_MESSAGE###' => $ctrl_message,
		);

		return $this->cObj->substituteMarkerArray($template, $markers);
	}

	/**
	 * Render field type check
	 *
	 * @param	string		$field	The field name
	 * @param	array		$row	The record to edit from the database table
	 * @param	array		$PA		Array of parameters
	 * @return	string		The HTML code for table field
	 */
	public function getSingleFormField_typeCheck($field, $row, &$PA) {
		$config = $PA['fieldConf']['config'];
		$template = $PA['template'];

		$value = $PA['submitted_form']? $PA['submitted_itemFormElValue']: $PA['itemFormElValue'];

		$disabled = '';
		if($this->renderReadonly || $config['readOnly'])  {
			$disabled = ' disabled="disabled"';
		}

		$selItems = $this->initItemArray($PA['fieldConf']);
		if (!count($selItems))  {
			$selItems[]=array('','');
		}

		$elName =  'document.' . $this->formName . '[\'' . $PA['itemFormElName'] . '\']';
		$cols = $config['cols'];
		if ($cols>1) {
			$markers = array(
				'###ITEMFORMEL_CHECK_LABEL###' => htmlspecialchars($PA['itemFormElLabel']),
			);
			$sub_tmpl = $this->cObj->getSubpart($template, '###CHECK_ITEM###');
			for ($c=0; $c<$cols; $c++) {
				$onclick = $elName . '.value=this.checked?
					(' . $elName . '.value|' . pow(2, $c) . '):
					(' . $elName . '.value&' . (pow(2, count($selItems)) - 1 - pow(2, $c)) . ');'
				;
				$onclick = 'onclick="' . $onclick . '"';
				$sub_markers = array(
					'###ITEMFORMEL_LABEL###' => htmlspecialchars($selItems[$c][0]),
					'###ITEMFORMEL_ID###' => $PA['itemFormElID'] . '-checkboxfield' . '_' . $c,
					'###ITEMFORMEL_NAME###' => $PA['itemFormElName'] . '[' . $c . ']',
					'###ITEMFORMEL_CHECKED###' => (($value & pow(2, $c)) ? ' checked="checked"' : ''),
					'###ITEMFORMEL_ONCHANGE###' => $onclick,
					'###ITEMFORMEL_DISABLED###' => $disabled,
				);
				$items .= $this->cObj->substituteMarkerArray($sub_tmpl, $sub_markers);
			}
			$content = $this->cObj->substituteMarkerArrayCached($template, $markers, array('###CHECK_ITEM###' => $items));
		} else {
			$template = $this->cObj->getSubpart($template, '###CHECK_ITEM###');
			$onclick = $elName . '.value=this.checked?1:0;';
				$onclick = 'onclick="' . $onclick . '"';
			$markers = array(
				'###ITEMFORMEL_CHECK_LABEL###' => '',
				'###ITEMFORMEL_LABEL###' => htmlspecialchars($PA['itemFormElLabel']),
				'###ITEMFORMEL_ID###' => $PA['itemFormElID'] . '-checkboxfield_0',
				'###ITEMFORMEL_NAME###' => $PA['itemFormElName'] . '[0]',
				'###ITEMFORMEL_CHECKED###' => ($value)? 'checked="checked"' : '',
				'###ITEMFORMEL_ONCHANGE###' => $onclick,
				'###ITEMFORMEL_DISABLED###' => $disabled,
			);
			$content = $this->cObj->substituteMarkerArray($template, $markers);
		}
		if (!$disabled) {
			$content .= '<input type="hidden" name="' . $PA['itemFormElName'] . '" value="' . htmlspecialchars($value) . '" />';
		}
		return $content;
	}

	/**
	 * Render field type radio
	 *
	 * @param	string		$field	The field name
	 * @param	array		$row	The record to edit from the database table
	 * @param	array		$PA		Array of parameters
	 * @return	string		The HTML code for table field
	 */
	public function getSingleFormField_typeRadio( $field, $row, &$PA) {
		$table = $PA['table'];
		$config = $PA['fieldConf']['config'];
		$template = $PA['template'];

		$disabled = '';
		if($this->renderReadonly || $config['readOnly'])  {
			$disabled = ' disabled="disabled"';
		}

		$selItems = $this->initItemArray($PA['fieldConf']);

		// TODO : implements 'itemsProcFunc'
		// if ($config['itemsProcFunc']) {
			// $selItems = $this->procItems($selItems, $PA['fieldTSConfig']['itemsProcFunc.'], $config, $table, $row, $field);
		// }

		$markers = array(
			'###ITEMFORMEL_RADIO_LABEL###' => htmlspecialchars($PA['itemFormElLabel']),
		);
		$sub_tmpl = $this->cObj->getSubpart($template, '###RADIO_ITEM###');
		for ($c = 0; $c < count($selItems); $c++) {
			$checked = ($selItems[$c][1] == $PA['itemFormElValue']) ? ' checked="checked"' : '';
			if ($PA['submitted_form'])
				$checked = ($selItems[$c][1] == $PA['submitted_itemFormElValue']) ? ' checked="checked"' : '';
			$sub_markers = array(
				'###ITEMFORMEL_LABEL###' => htmlspecialchars($selItems[$c][0]),
				'###ITEMFORMEL_ID###' => $PA['itemFormElID'] . '-radiofield' . '_' . $c,
				'###ITEMFORMEL_NAME###' => $PA['itemFormElName'],
				'###ITEMFORMEL_VALUE###' => $selItems[$c][1],
				'###ITEMFORMEL_CHECKED###' => $checked,
				'###ITEMFORMEL_ONCHANGE###' => '',
				'###ITEMFORMEL_DISABLED###' => $disabled,
			);
			$items .= $this->cObj->substituteMarkerArray($sub_tmpl, $sub_markers);
		}
		$content = $this->cObj->substituteMarkerArrayCached($template, $markers, array('###RADIO_ITEM###' => $items));

		return $content;
	}


	/**
	 * Render field type select
	 *
	 * @param	string		$field	The field name
	 * @param	array		$row	The record to edit from the database table
	 * @param	array		$PA		Array of parameters
	 * @return	string		The HTML code for table field
	 */
	public function getSingleFormField_typeSelect($field, $row, &$PA) {
		// Todo : hook
		// Comme ça même pour des formulaires plus complexe on peut continuer à utiliser l'extension
		// Ici on aura un simple SELECT avec la liste de tous les choix
		return '<p style="color:#ff0000">Type "select" is not implemented.</p>';
	}

	/**
	 * Render field type group
	 *
	 * @param	string		$field	The field name
	 * @param	array		$row	The record to edit from the database table
	 * @param	array		$PA		Array of parameters
	 * @return	string		The HTML code for table field
	 */
	public function getSingleFormField_typeGroup($field, $row, &$PA) {
		// Init:
		$conf = $PA['fieldConf'];
		$config = $PA['fieldConf']['config'];
		$internal_type = $config['internal_type'];
		$show_thumbs = $config['show_thumbs'];
		$size = intval($config['size']);
		$maxitems = t3lib_div::intInRange($config['maxitems'],0);
		if (!$maxitems) $maxitems = 100000;
		$minitems = t3lib_div::intInRange($config['minitems'],0);

		$disabled = '';
		if($this->renderReadonly || $config['readOnly'])  {
			$disabled = ' disabled="disabled"';
		}

		switch((string)$config['internal_type']) {
			case 'file_reference' :
				$template = '<p style="color:#ff0000">internal_type "file_reference" for type "group" is not implemented.</p>';
				break;
			case 'file' :
				$template = $this->cObj->getSubpart($PA['template'], '###SUBPART_GROUPFILE##');
				$groupFileAllowedWrap = ($conf['allowed_stdWrap.'])? $conf['allowed_stdWrap.'] : $this->groupFileAllowedWrap;
				$groupFileDisallowedWrap = ($conf['disallowed_stdWrap.'])? $conf['disallowed_stdWrap.'] : $this->groupFileDisallowedWrap;
				$separator = $conf['separator']? $conf['separatorExtFile']: ' ';
				$maxGroupFileSizeWrap = ($conf['max_size_stdWrap.'])? $conf['max_size_stdWrap.'] : $this->maxGroupFileSizeWrap;

				$allowed = '';
				if ($config['allowed']) {
					$allowed = t3lib_div::trimExplode(',', strtoupper($config['allowed']), true);
					$allowed = $this->cObj->stdWrap(implode($separator, $allowed), $groupFileAllowedWrap);
				}

				$disallowed = '';
				if ($config['disallowed']) {
					$disallowed = t3lib_div::trimExplode(',', strtoupper($config['disallowed']), true);
					$disallowed = $this->cObj->stdWrap(implode($separator, $disallowed), $groupFileDisallowedWrap);
				}

				if (isset($conf['show_thumbs']))
					$show_thumbs = $conf['show_thumbs'];

				$files = t3lib_div::trimExplode(',', $PA['itemFormElValue'], true);

				$markers = array(
					'###ITEMFORMEL_ID###' => uniqid('tx-icstcafeadmin-groupfilefield-'),
					'###ITEMFORMEL_LABEL###' => htmlspecialchars($PA['itemFormElLabel']),
					'###ITEMFORMEL_HIDDENNAME###' => $PA['itemFormElName'],
					'###ITEMFORMEL_HIDDENVALUE###' => htmlspecialchars(implode(',', $files)),
					'###ITEMFORMEL_ALLOWED_LABEL###' => '',
					'###ITEMFORMEL_ALLOWED###' => $allowed,
					'###ITEMFORMEL_DISALLOWED_LABEL###' => '',
					'###ITEMFORMEL_DISALLOWED###' => $disallowed,
					'###ITEMFORMEL_MAXSIZE_LABEL###' => '',
					'###ITEMFORMEL_MAXSIZE###' => $this->cObj->stdWrap($config['max_size'], $this->maxGroupFileSizeWrap),
				);

				$subTmpl['ITEMFORMEL_ADD'] = $this->cObj->getSubpart($this->templateCode, '###ITEMFORMEL_ADD###');
				$markerArray['ITEMFORMEL_ADD'] = array(
					'###ITEMFORMEL_ADD_NAME###' => $PA['itemFormElName'] . '[file]',
				);

				if (count($files) >= $config['maxitems']) {
					$outputFileAdd = $this->pibase->pi_getLL('files_number_max', $GLOBALS['TSFE']->sL($this->icstcafeadminLL . 'files_number_max'), true);
				} else {
					$outputFileAdd =  $this->cObj->substituteMarkerArray($subTmpl['ITEMFORMEL_ADD'], $markerArray['ITEMFORMEL_ADD']);
				}

				$outputFileDelete = '';
				if (!empty($files)) {
					$subTmpl['ITEMFORMEL_EXIST'] = $this->cObj->getSubpart($this->templateCode, '###ITEMFORMEL_EXIST###');
					foreach ($files as $file) {
						$uniqid = uniqid();

						if ($show_thumbs) {
							$width = $conf['maxGroupFileWidth']? $conf['maxGroupFileWidth'] : $this->maxGroupFileWidth;
							$width = $conf['maxGroupFileHeight']? $conf['maxGroupFileHeight'] : $this->maxGroupFileHeight;

							$imgResource = $this->cObj->getImgResource( $config['uploadfolder'] . '/' . $file, array('width'=>$width, 'height'=>$height) );
							$origFile_explode = explode('/', $imgResource['origFile']);
							$imgAlt = $field . ' ' . $origFile_explode[count($origFile_explode)-1];
							$imgTitle = $field . ' ' . $origFile_explode[count($origFile_explode)-1];
							$elDelete =  '<img src="' . $imgResource[3] . '" alt="' . $imgAlt . '" title="' . $imgTitle . '" />';
						} else {
							$elDelete = $file;
						}

						$markerArray['ITEMFORMEL_EXIST'] = array(
							'###ITEMFORMEL_EXIST_DELETE###' => $elDelete,
							'###ITEMFORMEL_EXIST_INDICE###' => $uniqid,
							'###ITEMFORMEL_EXIST_DELETE_NAME###' => $PA['itemFormElName'] . '[' . $uniqid . ']',
							'###ITEMFORMEL_EXIST_DELETE_VALUE###' => htmlspecialchars($file),
							'###ITEMFORMEL_EXIST_DELETE_LABEL###' => $this->pibase->pi_getLL('filedelete', $GLOBALS['TSFE']->sL($this->icstcafeadminLL . 'filedelete'), true),
						);
						$outputFileDelete .= $this->cObj->substituteMarkerArray($subTmpl['ITEMFORMEL_EXIST'], $markerArray['ITEMFORMEL_EXIST']);
					}
				}

				$template = $this->cObj->substituteSubpart($template, '###ITEMFORMEL_EXIST###', $outputFileDelete);
				$template = $this->cObj->substituteSubpart($template, '###ITEMFORMEL_ADD###', $outputFileAdd);
				$template = $this->cObj->substituteMarkerArray($template, $markers);

				break;
			case 'folder' :
				$template = '<p style="color:#ff0000">internal_type "folder" for type "group" is not implemented.</p>';
				break;
			case 'db' :
				$template = 'p style="color:#ff0000">internal_type "db" for type "group" is not implemented.</p>';
				break;
		}

		return $template;
	}


	/**
	 * Handler for unknown types.
	 *
	 * @param	string		$field	The field name
	 * @param	array		$row	The record to edit from the database table
	 * @param	array		$PA		Array of parameters
	 * @return	string		The HTML code for table field
	 */
	public function getSingleFormField_typeUnknown($field, $row, &$PA) {

	$subpartArray = array();
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['additionalSingleMarkers'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['additionalSingleMarkers'] as $_classRef) {
				$_procObj = & t3lib_div::getUserObj($_classRef);
				$_procObj->additionalFieldFormMarkers($markers, $content, $template, $row, $lConf, $this);
			}
		}
		$template = $this->cObj->substituteSubpartArray($template, $subpartArray);
		return '<p style="color:#ff0000">Unknown type: ' . $PA['fieldConf']['config']['type'] . '</p>';
	}

	/*******************************************************
	 * Item-array manipulation functions (check/select/radio)
	 *
	 *******************************************************/

	 /**
 * @param	array		$items		The array of items (label,value,icon)
 * @param	array		$iArray		The "itemsProcFunc." from fieldTSconfig of the field.
 * @param	array		$config		The config array for the field.
 * @param	string		$table		Table name
 * @param	array		$row		Record row
 * @param	string		$field		Field name
 * @return	items
 */
	function procItems($items, $iArray, $config, $table, $row, $field) {
		$params = array();
		$params['items'] = &$items;
		$params['config'] = $config;
		$params['TSconfig'] = $iArray;
		$params['table'] = $table;
		$params['row'] = $row;
		$params['field'] = $field;

		t3lib_div::callUserFunction($config['itemsProcFunc'], $params, $this);
		return $items;
	}

	/**
	 * Init items array
	 *
	 * @param	array		$fieldValue		The "columns" array for the field (from TCA)
	 * @return	mixed		$items
	 */
	protected function initItemArray($fieldValue)     {
		$items = array();
		if (is_array($fieldValue['config']['items']))   {
			foreach ($fieldValue['config']['items'] as $itemValue) {
				$items[] = array($GLOBALS['TSFE']->sL($itemValue[0]), $itemValue[1], $itemValue[2]);
			}
		}
		return $items;
	}

	/*******************************************************
	 * Control and process form	 - Process on DB
	 *
	 *******************************************************/

	/**
	 * Controls fields
	 *
	 * @param	array		$field	Field name
	 * @param	array		$evals	Field's evals
	 * @param	string		$value	Field's value
	 * @param	array		$lConf	Configuration
	 * @return	boolean		"true" wether control pass, otherwise "false"
	 */
	protected function controlField($field=null, $evals=null, $value, $lConf) {
		$control = true;

		foreach ($evals as $eval) {
			switch ($eval) {
				case 'required':
					if (!$value) {
						$control = false;
						$message = $lConf['controlFields.'][$field . '.']['message.']['required']? $lConf['controlFields.'][$field . '.']['message.']['required']: '';
						$message = $message? $message: $this->pibase->pi_getLL('ctrl_msg_' . $field . '_required', $this->pibase->pi_getLL('ctrl_required', $GLOBALS['TSFE']->sL($this->icstcafeadminLL . 'ctrl_required'), true), true);
						$this->ctrl_messages[$field] = $message;
						if ($lConf['controlFields.']['breakControl'])
							return false;
					}
					break;
				case 'date':	// date (day-month-year)
					if ($value && !strtotime($value)) {
						$control = false;
						$message = $lConf['controlFields.'][$field . '.']['message.']['date']? $lConf['controlFields.'][$field . '.']['message.']['date']: '';
						$message = $message? $message: $this->pibase->pi_getLL('ctrl_msg_' . $field . '_date', $this->pibase->pi_getLL('ctrl_date', $GLOBALS['TSFE']->sL($this->icstcafeadminLL . 'ctrl_date'), true), true);
						$this->ctrl_messages[$field] = $message;
						if ($lConf['controlFields.']['breakControl'])
							return false;
					}
					break;
				case 'datetime':// date + time
					if ($value && !strtotime($value)) {
						$control = false;
						$message = $lConf['controlFields.'][$field . '.']['message.']['datetime']? $lConf['controlFields.'][$field . '.']['message.']['datetime']: '';
						$message = $message? $message: $this->pibase->pi_getLL('ctrl_msg_' . $field . '_datetime', $this->pibase->pi_getLL('ctrl_datetime', $GLOBALS['TSFE']->sL($this->icstcafeadminLL . 'ctrl_datetime'), true), true);
						$this->ctrl_messages[$field] = $message;
						if ($lConf['controlFields.']['breakControl'])
							return false;
					}
					break;
				case 'time':	// time (hours, minutes)
					if ($value && !strtotime($value)) {
						$control = false;
						$message = $lConf['controlFields.'][$field . '.']['message.']['time']? $lConf['controlFields.'][$field . '.']['message.']['time']: '';
						$message = $message? $message: $this->pibase->pi_getLL('ctrl_msg_' . $field . '_time', $this->pibase->pi_getLL('ctrl_time', $GLOBALS['TSFE']->sL($this->icstcafeadminLL . 'ctrl_time'), true), true);
						$this->ctrl_messages[$field] = $message;
						if ($lConf['controlFields.']['breakControl'])
							return false;
					}
					break;
				case 'timesec':	// time + sec
					if ($value && !strtotime($value)) {
						$control = false;
						$message = $lConf['controlFields.'][$field . '.']['message.']['timesec']? $lConf['controlFields.'][$field . '.']['message.']['timesec']: '';
						$message = $message? $message: $this->pibase->pi_getLL('ctrl_msg_' . $field . '_timesec', $this->pibase->pi_getLL('ctrl_timesec', $GLOBALS['TSFE']->sL($this->icstcafeadminLL . 'ctrl_timesec'), true), true);
						$this->ctrl_messages[$field] = $message;
						if ($lConf['controlFields.']['breakControl'])
							return false;
					}
					break;
				case 'year':
					if ($value && !strtotime('01-01-' . $value)) {
						$control = false;
						$message = $lConf['controlFields.'][$field . '.']['message.']['year']? $lConf['controlFields.'][$field . '.']['message.']['year']: '';
						$message = $message? $message: $this->pibase->pi_getLL('ctrl_msg_' . $field . '_year', $this->pibase->pi_getLL('ctrl_year', $GLOBALS['TSFE']->sL($this->icstcafeadminLL . 'ctrl_year'), true), true);
						$this->ctrl_messages[$field] = $message;
						if ($lConf['controlFields.']['breakControl'])
							return false;
					}
					break;
				case 'int':
					if ($value && !is_numeric($value)) {
						$control = false;
						$message = $lConf['controlFields.'][$field . '.']['message.']['int']? $lConf['controlFields.'][$field . '.']['message.']['int']: '';
						$message = $message? $message: $this->pibase->pi_getLL('ctrl_msg_' . $field . '_int', $this->pibase->pi_getLL('ctrl_int', $GLOBALS['TSFE']->sL($this->icstcafeadminLL . 'ctrl_int'), true), true);
						$this->ctrl_messages[$field] = $message;
						if ($lConf['controlFields.']['breakControl'])
							return false;
					}
					break;
				case 'double2':
					if ($value && !is_numeric($value)) {
						$control = false;
						$message = $lConf['controlFields.'][$field . '.']['message.']['double']? $lConf['controlFields.'][$field . '.']['message.']['double']: '';
						$message = $message? $message: $this->pibase->pi_getLL('ctrl_msg_' . $field . '_double', $this->pibase->pi_getLL('ctrl_double', $GLOBALS['TSFE']->sL($this->icstcafeadminLL . 'ctrl_double'), true), true);
						$this->ctrl_messages[$field] = $message;
						if ($lConf['controlFields.']['breakControl'])
							return false;
					}
					break;
				case 'alphanum':
					if ($value && preg_match('[^a-zA-Z0-9]+', $value)) {
						$control = false;
						$message = $lConf['controlFields.'][$field . '.']['message.']['alphanum']? $lConf['controlFields.'][$field . '.']['message.']['alphanum']: '';
						$message = $message? $message: $this->pibase->pi_getLL('ctrl_msg_' . $field . '_alphanum', $this->pibase->pi_getLL('ctrl_alphanum', $GLOBALS['TSFE']->sL($this->icstcafeadminLL . 'ctrl_alphanum'), true), true);
						$this->ctrl_messages[$field] = $message;
						if ($lConf['controlFields.']['breakControl'])
							return false;
					}
					break;
				case 'upper':
					// Nothing to do
					break;
				case 'lower':
					// Nothing to do
					break;
				case 'nospace':
					// Nothing to do
					break;
				case 'password':
					// Nothing to do
					break;
				case 'md5':
					// Nothing to do
					break;
				// case 'unique':
					// break;
				// case 'uniqueInPid':
					// break;
				default:
					break;
			}
		}

		return $control;
	}

	/**
	 * Process fields
	 *
	 * @param	string		$table		The tablename
	 * @param	string		$field		Field name
	 * @param	string		$type		Field type
	 * @param	array		$evals		Field evals
	 * @param	string		$value		Field value
	 * @param	int		$control	Control flag
	 * @return	mixed		The processed value
	 */
	protected function process_fieldValue($field, $type, array $evals, $value, $lastValue) {
		foreach ($evals as $eval) {
			switch ($eval) {
				case 'required':
					if (empty($value)) {
						$this->errors[] = $this->pibase->pi_getLL('error_requiredField',  $GLOBALS['TSFE']->sL($this->icstcafeadminLL . 'error_requiredField', true));
					}
					break;
				case 'date':
					if ($value) {
						if (preg_match( '`^\d{1,2}-\d{1,2}-\d{4}$`' , $value)) {
							$date = strtotime($value);
						}
						if ($date) {
							$value = $date;
						} else {
							$date = preg_replace('<[^0-9]>', '', $value);
							if (!$date) {
								$value = '0';
							} else {
								$dateArray = str_split($date, 2);
								$d = $dateArray[0];
								$m = $dateArray[1]? $dateArray[1]: date('m');
								$dateArray = str_split($date, 4);
								$y = $dateArray[1]? $dateArray[1]: date('Y');
								$value = mktime(0,0,0,$m,$d,$y);
							}
						}
					}
					break;
				case 'datetime':
					if ($value) {
						if (preg_match( '`^\d{1,2}:\d{1,2} \d{1,2}-\d{1,2}-\d{4}$`' , $value)) {
							$date = strtotime($value);
						}
						if ($date) {
							$value = $date;
						} else {
							$date = preg_replace('<[^0-9]>', '', $value);
							if (!$date) {
								$value = '0';
							} else {
								$dateArray = str_split($date, 2);
								$h = $dateArray[0];
								$i = $dateArray[1];
								$d = $dateArray[2];
								$m = $dateArray[3]? $dateArray[3]: date('m');
								$dateArray = str_split($date, 8);
								$y = $dateArray[1]? $dateArray[1]: date('Y');
								$value = mktime($h,$i,0,$m,$d,$y);
							}
						}
					}
					break;
				case 'time':
					if ($value) {
						if (preg_match('`^\d{1,2}:\d{1,2}$`', $value)) {
							$time = strtotime($value);
						}
						if ($time) {
							$value = $time;
						} else {
							$time = preg_replace('<[^0-9]>', '', $value);
							$timeArray = str_split($time, 2);
							$h = $timeArray[0];
							$i = $timeArray[1];
							$value = mktime($h,$i);
						}
					}
					break;
				case 'timesec':
					if ($value) {
						if (preg_match('`^\d{1,2}:\d{1,2}:\d{1,2}$`', $value)) {
							$time = strtotime($value);
						}
						if ($time) {
							$value = $time;
						} else {
							$time = preg_replace('<[^0-9]>', '', $value);
							$timeArray = str_split($time, 2);
							$h = $timeArray[0];
							$i = $timeArray[1];
							$s = $timeArray[2];
							$value = mktime($h,$i,$s);
						}
					}
					break;
				case 'year':
					if ($value) {
						$date = preg_replace('<[^0-9]>', '', $value);
						$value = strtotime('01-01-' . $date);
					}
					break;
				case 'int':
					$value = intval($value);
					break;
				case 'double2':
					$value = floatval($value);
					break;
				case 'alphanum':
					if ($value) {
						if (preg_match('`[^a-zA-Z0-9]+`', $value)) {
							$value = preg_replace('<[^a-zA-Z0-9]>', '', $value);
						}
					}
					break;
				case 'upper':
					if ($value)
						$value = strtoupper($value);
					break;
				case 'lower':
					if ($value)
						$value = strtolower($value);
					break;
				case 'nospace':
					if ($value)
						$value = str_replace(' ', '', $value);
					break;
				case 'password':
					// Nothing to do
					break;
				case 'md5':
					if ($value && ($value != $lastValue)) {
						$value = md5($value);
					}
					break;
				default:
			}
		}

		return $value;
	}

	/**
	 * Prepares JS control field
	 *
	 * @param	string		$field		The fieldname
	 * @param	array		$evalList	The eval list
	 * @param	string		$inputId	The input ID
	 * @param	mixed		$message	The error message
	 * @return	void
	 */
	protected function makeJSControlField($field, $evalList, $inputId, $message) {
		$this->jsControl[$field] = array(
			'id' => $inputId,
			'eval' => implode(',', $evalList),
		);
		foreach ($evalList as $eval) {
			switch($eval) {
				case 'required':
					$msg_required = $message['required']? $message['required']: '';
					$msg_required = $msg_required? $msg_required: $this->pibase->pi_getLL('ctrl_msg_' . $field . '_required', $this->pibase->pi_getLL('ctrl_required', $GLOBALS['TSFE']->sL($this->icstcafeadminLL . 'ctrl_required'), true), true);
					$this->jsControl[$field]['msg_required'] = utf8_encode(htmlspecialchars($msg_required));
				break;

				case 'date':
					$msg_date = $message['date']? $message['date']: '';
					$msg_date = $msg_date? $msg_date: $this->pibase->pi_getLL('ctrl_msg_' . $field . '_date', $this->pibase->pi_getLL('ctrl_date', $GLOBALS['TSFE']->sL($this->icstcafeadminLL . 'ctrl_date'), true), true);
					$this->jsControl[$field]['msg_date'] = utf8_encode(htmlspecialchars($msg_date));
				break;

				case 'datetime':
					$msg_datetime = $message['datetime']? $message['datetime']: '';
					$msg_datetime = $msg_datetime? $msg_datetime: $this->pibase->pi_getLL('ctrl_msg_' . $field . '_datetime', $this->pibase->pi_getLL('ctrl_datetime', $GLOBALS['TSFE']->sL($this->icstcafeadminLL . 'ctrl_datetime'), true), true);
					$this->jsControl[$field]['msg_datetime'] = utf8_encode(htmlspecialchars($msg_datetime));
				break;

				case 'time':
					$msg_time = $message['time']? $message['time']: '';
					$msg_time = $msg_time? $msg_time: $this->pibase->pi_getLL('ctrl_msg_' . $field . '_time', $this->pibase->pi_getLL('ctrl_time', $GLOBALS['TSFE']->sL($this->icstcafeadminLL . 'ctrl_time'), true), true);
					$this->jsControl[$field]['msg_time'] = utf8_encode(htmlspecialchars($msg_time));
				break;

				case 'timesec':
					$msg_timesec = $message['timesec']? $message['timesec']: '';
					$msg_timesec = $msg_timesec? $msg_timesec: $this->pibase->pi_getLL('ctrl_msg_' . $field . '_timesec', $this->pibase->pi_getLL('ctrl_timesec', $GLOBALS['TSFE']->sL($this->icstcafeadminLL . 'ctrl_timesec'), true), true);
					$this->jsControl[$field]['msg_timesec'] = utf8_encode(htmlspecialchars($msg_timesec));
				break;

				case 'year':
					$msg_year = $message['year']? $message['year']: '';
					$msg_year = $msg_year? $msg_year: $this->pibase->pi_getLL('ctrl_msg_' . $field . '_year', $this->pibase->pi_getLL('ctrl_year', $GLOBALS['TSFE']->sL($this->icstcafeadminLL . 'ctrl_year'), true), true);
					$this->jsControl[$field]['msg_year'] = utf8_encode(htmlspecialchars($msg_year));
				break;

				case 'int':
					$msg_int = $message['int']? $message['int']: '';
					$msg_int = $msg_int? $msg_int: $this->pibase->pi_getLL('ctrl_msg_' . $field . '_int', $this->pibase->pi_getLL('ctrl_int', $GLOBALS['TSFE']->sL($this->icstcafeadminLL . 'ctrl_int'), true), true);
					$this->jsControl[$field]['msg_int'] = utf8_encode(htmlspecialchars($msg_int));
				break;

				case 'double2':
					$msg_double = $message['double']? $message['double']: '';
					$msg_double = $msg_double? $msg_double: $this->pibase->pi_getLL('ctrl_msg_' . $field . '_double', $this->pibase->pi_getLL('ctrl_double', $GLOBALS['TSFE']->sL($this->icstcafeadminLL . 'ctrl_double'), true), true);
					$this->jsControl[$field]['msg_double'] = utf8_encode(htmlspecialchars($msg_double));
				break;

				case 'alphanum':
					$msg_alphanum = $message['alphanum']? $message['alphanum']: '';
					$msg_alphanum = $msg_alphanum? $msg_alphanum: $this->pibase->pi_getLL('ctrl_msg_' . $field . '_alphanum', $this->pibase->pi_getLL('ctrl_alphanum', $GLOBALS['TSFE']->sL($this->icstcafeadminLL . 'ctrl_alphanum'), true), true);
					$this->jsControl[$field]['msg_alphanum'] = utf8_encode(htmlspecialchars($msg_alphanum));
				break;

				default:
					// TODO : add hook here
			}
		}
	}

	/**
	 * Save record in DB
	 *
	 * @param	string		$table	 	The tablename
	 * @param	array		$fields		Array of fields to update
	 * @param	array		$dataArray	Array of data
	 * @param	int		$id			ID to update or insert
	 * @param	boolean		$new		A flag to specify wether record must be inserted or updated
	 * @return	boolean		If record is created "TRUE" otherwise "FALSE"
	 */
	protected function saveDB($table=null, $fields=null, $dataArray=null,$id=null, $new=false) {
		if (!$table || !is_array($fields) || empty($fields) || !is_array($dataArray) || empty($dataArray) || !$id)
			return false;

		if ($new) { // Insert new record
			$result = $this->cObj->DBgetInsert(
				$table,
				$id,
				$dataArray,
				implode(',', $fields),
				true
			);

		} else {	// Update record
			$result = $this->cObj->DBgetUpdate(
				$table,
				$id,
				$dataArray,
				implode(',', $fields),
				true
			);
		}

		return $result;
	}

	/**
	 * Delete record
	 *
	 * @param	string		$table	The tablename
	 * @param	int		$rowUid	The record's uid
	 * @return	mixed		Result from handler
	 */
	public function deleteRecord($table, $rowUid) {
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
		return $this->cObj->DBgetUpdate(
			$table,
			$rowUid,
			array('hidden' => '1'),
			'hidden',
			true
		);
	}

	/*******************************************************
	 * JS functions
	 *
	 *******************************************************/

	/**
	 * Include lib tablefilter JS
	 *
	 * @return	void
	 */
	private static function includeLibTableFilter() {
		if (self::$tableFilterIncluded)
			return;
		$tags = array();
		$file = t3lib_div::resolveBackPath($GLOBALS['TSFE']->tmpl->getFileName('EXT:ics_tcafe_admin/lib/TableFilter/tablefilter.js'));
		$tags[] = '	<script src="' . htmlspecialchars($file) . '" type="text/javascript"></script>' . PHP_EOL;
		$GLOBALS['TSFE']->additionalHeaderData['tableFilter'] = implode('', $tags);
		self::$tableFilterIncluded = true;
	}

	/**
	 * Include lib tablefilter css
	 *
	 * @return	void
	 */
	private static function includeLibTableFilterCss() {
		$file = t3lib_div::resolveBackPath($GLOBALS['TSFE']->tmpl->getFileName('EXT:ics_tcafe_admin/lib/TableFilter/filtergrid.css'));
		$tag = '<link rel="stylesheet" type="text/css"  href="' . htmlspecialchars($file) . '" media="all" />';
		$GLOBALS['TSFE']->additionalHeaderData['tableFilterCss'] = $tag;
	}

	/**
	 * Include tablefilter js
	 *
	 * @param	string		$jsId	The js id
	 * @param	array		$PA		The conf array
	 * @param	array		$fields	The fields array
	 * @return	void
	 */
	private function includeTableFilterJS($jsId, $PA=null, $fields=null) {
		$props = array();

		// Retrieves cols properties
		$cols = t3lib_div::trimExplode(',', $PA['props.']['cols'], true);
		if (is_array($cols) && !empty($cols)) {
			foreach ($cols as $col) {
				list($col, $property) = $col;
				if ($col)
					$props[$col] = '"' . $property . '"';
			}
		}
		foreach ($fields as $idx=>$field) {
			if ($PA['fields.'][$field . '.']['tableFilter'])
				$props['col_' . $idx] = 'col_' . $idx . ': "' . $PA['fields.'][$field . '.']['tableFilter'] . '"';
		}

		// Retrieves other properties
		if ($PA['props.']['props'])
			$props[] = $PA['props.']['props'];

		if (empty($props)) {
			$js = '<script language="javascript" type="text/javascript">
				jQuery(document).ready(
					function(){
						if(document.getElementById("' . $jsId . '")){
							setFilterGrid("' . $jsId . '");
						}
					}
				);
			</script>';
		} else {
			$js = '<script language="javascript" type="text/javascript">
				jQuery(document).ready(
					function(){
						if(document.getElementById("' . $jsId . '")){
							' . $jsId . '_props = {' . implode(',', $props) . '};
							setFilterGrid("' . $jsId . '", ' . $jsId . '_props);
						}
					}
				);
			</script>';
		}

		$GLOBALS['TSFE']->additionalHeaderData[$jsId . '_filter'] = $js;
	}

	/**
	 * Include form js
	 *
	 * @param	boolean		$controlForm	To control form with javascript
	 * @param	array		$fields			The form fields
	 * @param	array		$jsControl		The jsControl
	 * @return	void
	 */
	private static function includeFormJS($controlForm, $fields, $jsControl) {
		if (self::$formJSIncluded)
			return;
		$file = t3lib_div::resolveBackPath($GLOBALS['TSFE']->tmpl->getFileName('EXT:ics_tcafe_admin/res/form.js'));
		$tag = '	<script src="' . htmlspecialchars($file) . '" type="text/javascript"></script>' . PHP_EOL;
		if ($controlForm) {
			$tag .= '	<script type="text/javascript">var control = new controlForm("' . implode(',', $fields) . '", ' . json_encode($jsControl) . ')</script>' . PHP_EOL;
		}
		$GLOBALS['TSFE']->additionalHeaderData['tcafeadmin_formFields'] = $tag;
		self::$formJSIncluded = true;
	}

	/*******************************************************
	 * Other functions
	 *
	 *******************************************************/

	/**
	 * Retrieves table fields name
	 *
	 * @param	string		$table The table name
	 * @return	mixed
	 */
	private function getTableFields($table=null) {
		if (!$table)
			return null;

		t3lib_div::loadTCA($table);
		return array_keys($GLOBALS['TCA'][$table]['columns']);
	}

	/**
	 * Retrieves label fields
	 *
	 * @param	string		$table
	 * @param	mixed		$fields
	 * @param	array		$labelFields
	 * @return	mixed
	 */
	private function mergeLabelFields($table=null, $fields=null, $labelFields=null) {
		if (!$table)
			return $labelFields;

		t3lib_div::loadTCA($table);
		$fields = $fields? $fields: array_keys($GLOBALS['TCA'][$table]['columns']);
		$fields = !is_array($fields)? t3lib_div::trimExplode(',', $fields): $fields;
		$labels = array();
		foreach ($fields as $field){
			if (!$labelFields[$field]) {
				$labels[$field] = $GLOBALS['TSFE']->sL($GLOBALS['TCA'][$table]['columns'][$field]['label']);
			} else {
				$labels[$field] = $labelFields[$field];
			}
		}
		return $labels;
	}

	/**
	 * Get other display
	 *
	 * @param	string		$table		The table name
	 * @param	mixed		$fields		List of fields
	 * @param	array		$labelFields	An associative array with key=>value where key is the field name and value the label
	 * @param	array		$lConf		The plugin's configuration
	 * @return	mixed		The content or false
	 */
	protected function switchDisplay($table=null, $fields=null, $labelFields=null, $lConf=null) {
		if (isset($this->pibase->piVars['codes'])) {
			$fields = $fields? $fields: $this->getTableFields($table);
			$fields = !is_array($fields)? t3lib_div::trimExplode(',', $fields): $fields;
			$labelFields = $this->mergeLabelFields($table, implode(',', $fields), $labelFields);
			$codes = t3lib_div::trimExplode(',', $this->pibase->piVars['codes'], true);
			unset($this->pibase->piVars['codes']);
			foreach ($codes as $theCode) {
				$theCode = (string) strtoupper(trim($theCode));
				switch ($theCode) {
					case 'LIST':
						$content .= $this->displayList($table, $fields, $labelFields, $lConf);
					break;

					case 'SINGLE':
						if ($this->pibase->piVars['showUid']) {
							$storage = '\'' . implode('\',\'',$this->pidStorage) . '\'';
							$requestFields = array_merge(array('uid'), $fields);
							$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
								implode(',', $requestFields),
								$table,
								'deleted=0 AND pid IN(' . $storage . ') AND uid=' . $this->pibase->piVars['showUid'],
								'',
								'',
								'1'
							);
							if (is_array($rows) && !empty($rows)) {
								$content .= $this->displaySingle($table, $fields, $rows[0], $labelFields, $lConf);
							}
						}
					break;

					case 'EDIT':
						if ($this->pibase->piVars['showUid']) {
							$storage = '\'' . implode('\',\'',$this->pidStorage) . '\'';
							$requestFields = array_merge(array('uid'), $fields);
							$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
								implode(',', $requestFields),
								$table,
								'deleted=0 AND pid IN(' . $storage . ') AND uid=' . $this->pibase->piVars['showUid'],
								'',
								'',
								'1'
							);
							if (is_array($rows) && !empty($rows)) {
								$content .= $this->displayEdit($table, $fields, $rows[0], $labelFields, $lConf);
							}
						}
					break;

					case 'NEW':
						$content .= $this->displayNew($table, $fields, $labelFields, $lConf);
					break;

					case 'DELETE':
						$this->deleteRecord($table, $this->pibase->piVars['rowUid']);
					break;

					case 'HIDE':
						$this->hideRecord($table, $this->pibase->piVars['rowUid']);
					break;
				}
			}

			return $content;
		}

		return false;
	}


	/**
	 * Retrieves
	 *
	 * @return	[type]		...
	 */
	public function getErrors() {
		return '<p class="process_error">' . implode('</p>' .chr(10) . '<p class="process_error">', $this->errors) . '</p>';
	}

}