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
 *   59: class tx_icstcafeadmin_CommonRenderer
 *   89:     function __construct($pi_base, $table, array $fields, array $fieldLabels, array $conf)
 *  112:     public function init()
 *  126:     protected function renderValue($field, $recordId, $value=null, $view='')
 *  172:     public function default_renderValue($field, $value=null, $view='')
 *  216:     protected function fetchInputFieldFormat($config)
 *  243:     public function handleFieldValue($recordId, $value=null, $config=null)
 *  273:     protected function  handleFieldValue_typeCheck($value=null, array $config)
 *  288:     protected function handleFieldValue_typeSelect($recordId, $value=null, array $config)
 *  365:     public function getMMLabels($recordId, array $config)
 *  410:     protected function handleFieldValue_typeGroup($recordId, $value=null, array $config)
 *  457:     public function sL($str)
 *  469:     public function getLL($key, $alternativeLabel= '', $hsc=false)
 *  479:     public function cObjDataActions($row=null)
 *  532:     public function renderBackLink($row=null)
 *
 * TOTAL FUNCTIONS: 14
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */


/**
 * Class 'tx_icstcafeadmin_CommonRenderer' for the 'ics_tcafe_admin' extension.
 * Render the list view
 *
 * @author	Tsi YANG <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icstcafeadmin
 */
class tx_icstcafeadmin_CommonRenderer {
	protected $pi_base;
	var $prefixId;
	var $extKey;
	var $conf;
	var $cObj;

	var $templateCode;

	protected $table;
	protected $fields;
	protected $fieldLabels;

	public static $allowedImgFileExtArray = array(
		'gif',
		'png',
		'jpeg',
		'jpg',
	);

	/**
	 * Constructor
	 *
	 * @param	tx_icstcafeadmin_pi1		$pi_base: Instance of tx_icstcafeadmin_pi1
	 * @param	string		$table: The tablename
	 * @param	array		$fields: Fields names array
	 * @param	array		$fieldLabels: Associative array of fields labels like field=>labelfield
	 * @param	array		$conf: Typoscript configuration
	 * @return	void
	 */
	function __construct($pi_base, $table, array $fields, array $fieldLabels, array $conf) {
		$this->pi_base = $pi_base;
		$this->prefixId = $pi_base->prefixId;
		$this->extKey = $pi_base->extKey;
		$this->conf = $conf;
		$this->cObj = $pi_base->cObj;

		$this->templateCode = $pi_base->templateCode;
		$this->backPid = $pi_base->backPid;

		$this->table = $table;
		$this->fields = $fields;
		$this->fieldLabels = $fieldLabels;
		$this->storage = $pi_base->storage;

		$this->extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['ics_tcafe_admin']);
	}

	/**
	 * Initialize the renderer
	 *
	 * @return	void
	 */
	public function init() {
		$GLOBALS['TSFE']->includeTCA();
		t3lib_div::loadTCA($this->table);
	}

	/**
	 * Render value
	 *
	 * @param	string		$field: Field's name
	 * @param	int		$recordId: Record's id
	 * @param	mixed		$value: Field's value
	 * @param	string		$view: The display view
	 * @return	string		The value
	 */
	protected function renderValue($field, $recordId, $value=null, $view='') {
		if (!$field)
			throw new Exception('Field is not set on RenderValue.');

		$label = $GLOBALS['TCA'][$this->table]['ctrl']['label'];
		$config = $GLOBALS['TCA'][$this->table]['columns'][$field]['config'];

		$process = false;
		// Hook to render value
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['renderValue'])) {
			$conf = $this->conf;
			$conf['currentView'] = $view;
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['renderValue'] as $class) {
				$procObj = & t3lib_div::getUserObj($class);
				if ($process = $procObj->renderValue($this->pi_base, $this->table, $field, $this->fieldLabels, $value, $recordId, $conf, $this))
					break;
			}
		}
		if (!$process) {
			if ($field===$label && $view=='viewList') {
				$data = $this->cObjDataActions();
				$data['id'] = $recordId;
				$data['title'] = $this->handleFieldValue($recordId ,$value, $config);
				$data['table'] = $this->table;
				$data['PIDitemDisplay'] = $this->conf['view.']['PIDitemDisplay'];
				$cObj = t3lib_div::makeInstance('tslib_cObj');
				$cObj->start($data, $this->table);
				$cObj->setParent($this->cObj->data, $this->cObj->currentRecord);

				$value = $cObj->stdWrap('', $this->conf['defaultConf.']['label.']);
			}
			else {
				$value = $this->default_renderValue($field, $this->handleFieldValue($recordId ,$value, $config), $view);
			}
		}
		return $value;
	}

	/**
	 * Render value
	 *
	 * @param	string		$field: Field's name
	 * @param	mixed		$value: Field's value
	 * @param	string		$view: The display view
	 * @return	string		The value
	 */
	public function default_renderValue($field, $value=null, $view='') {
		$config = $GLOBALS['TCA'][$this->table]['columns'][$field]['config'];

		// Render value on field Typoscript configuration
		if ($this->conf['renderConf.'][$this->table . '.'][$field . '.'][$view . '.']) {
			$value = $this->cObj->stdWrap($value, $this->conf['renderConf.'][$this->table . '.'][$field . '.'][$view . '.']);
		}
		// Render value on TCA type Typoscript configuration
		else {
			switch ($config['type']) {
				case 'input':
					if ($format = $this->fetchInputFieldFormat($config)) {
						$value = $this->cObj->stdWrap($value, $this->conf['defaultConf.'][$format . '.']);
					}
					break;
				case 'text':
					$value = $this->cObj->stdWrap($value, $this->conf['defaultConf.']['text.'][$view . '.']);
					break;
				case 'check':
					$value = $this->cObj->stdWrap($value, $this->conf['defaultConf.']['check.'][$view . '.']);
					break;
				case 'select':
					$value = $this->cObj->stdWrap($value, $this->conf['defaultConf.']['select.'][$view . '.']);
					break;
				case 'group':
					// TODO : prendre en compte le maxitems
					if ($config['internal_type']==='file' && array_intersect(t3lib_div::trimExplode(',', $config['allowed'], true), self::$allowedImgFileExtArray)) {
						$value = $this->cObj->stdWrap($value, $this->conf['defaultConf.']['illustration.']);
					}
					break;
				default:
					// $value = htmlspecialchars($value);
			}
		}
		return $value;
	}


	/**
	 * Retrieves input field format
	 *
	 * @param	array		$config: Field's TCA configuration
	 * @return	string		The field format
	 */
	protected function fetchInputFieldFormat($config) {
		$evalList = t3lib_div::trimExplode(',', $config['eval'], true);
		if (in_array('date', $evalList))
			return 'date';
		if (in_array('datetime', $evalList))
			return 'datetime';
		if (in_array('time', $evalList))
			return 'time';
		if (in_array('timesec', $evalList))
			return 'timesec';
		if (in_array('year', $evalList))
			return 'year';

		if (in_array('password', $evalList))
			return 'password';

		return null;
	}

	/**
	 * Handles field's value
	 *
	 * @param	int		$recordId: Record's id
	 * @param	mixed		$value: Field's value
	 * @param	array		$config: Field's TCA configuration
	 * @return	string		the	value
	 */
	public function handleFieldValue($recordId, $value=null, $config=null) {
		if (!$config)
			// return htmlspecialchars($value);
			return $value;

		switch ($config['type']) {
			// case 'input': Nothing to do
			// case 'text': Nothing to do
			case 'check':
				$value = $this->handleFieldValue_typeCheck ($value, $config);
				break;
			case 'select':
				$value = $this->handleFieldValue_typeSelect ($recordId, $value, $config);
				break;
			case 'group':
				$value = $this->handleFieldValue_typeGroup ($recordId, $value, $config);
				break;
			default:
		}
		// return htmlspecialchars($value);
		return $value;
	}

	/**
	 * Handles check field's value
	 *
	 * @param	mixed		$value: Field's value
	 * @param	array		$config: Field's TCA configuration
	 * @return	string		The processed value
	 */
	protected function  handleFieldValue_typeCheck($value=null, array $config) {
		if (is_array($config['items'])) {
			// TODO : insert here code to process type check with several values
		}
		return $value;
	}

	/**
	 * Handles select field's value
	 *
	 * @param	int		$recordId: Record's id
	 * @param	mixed		$value: Field's value
	 * @param	array		$config: Field's TCA configuration
	 * @return	string		The processed value
	 */
	protected function handleFieldValue_typeSelect($recordId, $value=null, array $config) {
		if ($config['MM']) {
			if (!$value) {
				$value = '';
			}
			else {
				if ($GLOBALS['TCA'][$config['foreign_table']]['ctrl']['label'] != 'uid') {
					$labels = $this->getMMLabels($recordId, $config);
					if (!empty($labels))
						$value = implode(',', $labels);
				}
				else {
					$loadDBGroup = t3lib_div::makeInstance('FE_loadDBGroup');
					$loadDBGroup->start('', $config['foreign_table'], $config['MM'], $recordId, $this->table, $config);
					$options = array();
					foreach($loadDBGroup->itemArray as $item) {
						$options[] = $item['id'];
					}
					if (!empty($options))
						$value = implode(',', $options);
				}
			}
		}
		// foreign_table
		elseif ($config['foreign_table']) {
			if (!$value) {
				$value = '';
			} else {
				t3lib_div::loadTCA($config['foreign_table']);
				if ($label = $GLOBALS['TCA'][$config['foreign_table']]['ctrl']['label']) {
					// Get select fields
					$fields = array('uid', $label);
					// Get records
					$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
						implode(',', $fields),
						$config['foreign_table'],
						'uid IN( ' . $value . ')',
						'',
						'',
						'',
						'uid'
					);
					// Fetch labels
					if (is_array($rows) && !empty($rows)) {
						$keys = t3lib_div::trimExplode(',', $value, true);
						$labels = array();
						foreach ($keys as $key) {
							$labels[] = $rows[$key][$label];
						}
						$value = implode(',', $labels);
					}
				}
			}
		}
		// itemsProcFunc
		elseif ($config['itemsProcFunc']) {
			// TODO : To implemented
		}
		else {
			$keys = t3lib_div::trimExplode(',', $value, true);
			$labels = array();
			foreach ($keys as $key) {
				$labels[] = $this->sL($config['items'][$key][0]);
			}
			$value = implode(',', $labels);
		}

		return $value;
	}

	/**
	 * Retrieves MM result
	 *
	 * @param	int		$recordId: Record's id
	 * @param	array		$config: Field's TCA configuration
	 * @return	mixed		Labels array
	 */
	public function getMMLabels($recordId, array $config) {
		t3lib_div::loadTCA($config['foreign_table']);

		// Get select fields
		$fields = array('`'.$config['foreign_table'].'`.`uid` as ft_uid');
		$label = $GLOBALS['TCA'][$config['foreign_table']]['ctrl']['label'];
		$fields[] = '`'.$config['foreign_table'].'`.`'.$label.'` as ft_label';
		$uidLocal_field = 'uid_local';
		$uidForeign_field = 'uid_foreign';
		$localTable = $this->table;
		$foreignTable = $config['foreign_table'];
		$sorting = '`'.$config['MM'].'`.`sorting`';
		if ($config['MM_opposite_field']) {
			$uidLocal_field = 'uid_foreign';
			$uidForeign_field = 'uid_local';
			$localTable = $config['foreign_table'];
			$foreignTable = $this->table;
			$sorting = '`'.$config['MM'].'`.`sorting_foreign`';
		}
		$addWhere_tablenames = ' AND (`'.$config['MM'].'`.`tablenames` = \'' . $this->table . '\' || `'.$config['MM'].'`.`tablenames` = \'\')';
		// Get records
		$result = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query(
			implode(',', $fields),
			$localTable,
			$config['MM'],
			$foreignTable,
			' AND `'.$config['MM'].'`.`'.$uidLocal_field.'` = ' . $recordId . $addWhere_tablenames,
			'',
			$sorting
		);
		$labels = array();
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result)) {
			$labels[] = $row['ft_label'];
		}
		return $labels;
	}

	/**
	 * Handles group field's value
	 *
	 * @param	int		$recordId: Record's id
	 * @param	mixed		$value: Field's value
	 * @param	array		$config: Field's TCA configuration
	 * @return	string		The processed value
	 */
	protected function handleFieldValue_typeGroup($recordId, $value=null, array $config) {
		switch ($config['internal_type']) {
			// case 'file': Nothing do
			case 'db':
				if ($config['MM']) {
					$allowedRelationTables = t3lib_div::trimExplode(',', $config['allowed']);
					$labels = array();
					foreach ($allowedRelationTables as $table) {
						t3lib_div::loadTCA($table);
						// Get select fields
						$label = $GLOBALS['TCA'][$table]['ctrl']['label'];
						$fields = array('`'.$table.'`.`uid` as ft_uid');
						if ($label != 'uid')
							$fields[] = '`'.$table.'`.`'.$label.'` as ft_label';

						$addWhere_tablenames = ' AND ' . $config['MM'] . '.tablenames = \'' . $table . '\'';
						// Get records
						$result = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query(
							implode(',', $fields),
							$this->table,
							$config['MM'],
							$table,
							' AND `'.$table.'`.`deleted` =0 AND `'.$config['MM'].'`.`uid_local` = ' . $recordId . $addWhere_tablenames,
							'',
							'`'.$config['MM'].'`.`sorting`'
						);
						// Fetch labels

						while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result)) {
							$labels[] = $row['ft_label'];
						}
					}
					if (!empty($labels))
						$value = implode(',', $labels);
				}
				break;
			default:
		}
		return $value;
	}

	/**
	 * Fetches language label for key
	 *
	 * @param	string		$str: Language label reference, eg. 'LLL:EXT:lang/locallang_core.php:labels.blablabla'
	 * @return	string		The value of the label, fetched for the current backend language
	 */
	public function sL($str) {
		return $GLOBALS['TSFE']->sL($str);
	}

	/**
	 * Returns the localized label of the LOCAL_LANG key, $key Notice that for debugging purposes prefixes for the output values can be set with the internal vars ->LLtestPrefixAlt and ->LLtestPrefix
	 *
	 * @param	string		The key from the LOCAL_LANG array for which to return the value.
	 * @param	string		Alternative string to return IF no value is found set for the key, neither for the local language nor the default.
	 * @param	boolean		If TRUE, the output label is passed through htmlspecialchars()
	 * @return	string		The value from LOCAL_LANG.
	 */
	public function getLL($key, $alternativeLabel= '', $hsc=false) {
		return $this->pi_base->pi_getLL($key, $alternativeLabel, $hsc);
	}

	/**
	 * Retrieves data array for TCAFE actions
	 *
	 * @param	array		$row: The record row
	 * @return	mixed		Tha data array
	 */
	public function cObjDataActions($row=null) {
		$GLOBALS['TSFE']->includeTCA();
		t3lib_div::loadTCA($this->table);
		$fields = $this->fields;
		$fields[] = 'uid';
		// TODO : revoir cette partie de code: label, label_alt, label_alt_force
		if ($GLOBALS['TCA'][$this->table]['ctrl']['label_alt'])
			$fields[] = $GLOBALS['TCA'][$this->table]['ctrl']['label_alt'];
		array_unique($fields);
		$id = $this->pi_base->showUid? $this->pi_base->showUid: $this->pi_base->newUid;
		$id = $id? $id: $row['uid'];
		$row = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow(
			implode(',', $fields),
			$this->table,
			'uid=' . $id
		);
		$label = $row[$GLOBALS['TCA'][$this->table]['ctrl']['label']];
		$label = $label? $label: $row[$GLOBALS['TCA'][$this->table]['ctrl']['label_alt']];
		$data = array(
			'id' => $row['uid'],
			'newId' => 'New'.uniqid(),
			'table' => $this->table,
			'fields' => implode(',', $this->fields),
			'hidden' => $row['hidden'],
			'PIDitemDisplay' => $this->conf['view.']['PIDitemDisplay'],
			'withDataItemDisplay' => $this->conf['view.']['withDataItemDisplay'],
			'PIDeditItem' => $this->conf['view.']['PIDeditItem'],
			'withDataEditItem' => $this->conf['view.']['withDataEditItem'],
			'PIDnewItem' => $this->conf['view.']['PIDnewItem'],
			'withDataNewItem' => $this->conf['view.']['withDataNewItem'],
			'label' => $label,
			'backPid' => $this->backPid,
			'crit_mode' => $this->pi_base->piVars['mode'],
			'crit_table' => $this->pi_base->piVars['table'],
			'crit_showUid' => $this->pi_base->piVars['showUid'],
			'crit_fields' => $this->pi_base->piVars['fields'],
		);
		// Hook to retrieves more data
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['actions_additionnalDataArray'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['actions_additionnalDataArray'] as $class) {
				$procObj = & t3lib_div::getUserObj($class);
				if ($procObj->actions_additionnalDataArray($data, $this->table, $row, $this->conf, $this))
					break;
			}
		}
		return $data;
	}

	/**
	 * Render back link
	 *
	 * @param	array		$row: the row
	 * @return	string		The content
	 */
	public function renderBackLink($row=null) {
		$data = array(
			'backPid' => $this->backPid,
			'crit_mode' => $this->pi_base->piVars['mode'],
			'crit_table' => $this->pi_base->piVars['table'],
			'crit_showUid' => $this->pi_base->piVars['showUid'],
			'crit_fields' => $this->pi_base->piVars['fields'],
		);
		if ($criteria = $this->pi_base->piVars['criteria']) {
			$data['mode'] = $criteria['mode'];
			$data['table'] = $criteria['table'];
			$data['showUid'] = $criteria['showUid'];
			$data['fields'] = $criteria['fields'];
		}

		// Hook to retrieves more data
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['backlink_additionnalDataArray'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['backlink_additionnalDataArray'] as $class) {
				$procObj = & t3lib_div::getUserObj($class);
				$procObj->backlink_additionnalDataArray($data, $this->table, $row, $this->conf, $this);
			}
		}
		$cObj = t3lib_div::makeInstance('tslib_cObj');
		$cObj->start($data, 'TCAFE_Admin_backlink');
		$cObj->setParent($this->cObj->data, $this->cObj->currentRecord);
		return $cObj->stdWrap('', $this->conf['renderOptions.']['backlink.']);
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_tcafe_admin/pi1/class.tx_icstcafeadmin_CommonRenderer.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_tcafe_admin/pi1/class.tx_icstcafeadmin_CommonRenderer.php']);
}

?>