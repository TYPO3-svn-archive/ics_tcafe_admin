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
 *   57: class tx_icstcafeadmin_CommonRenderer
 *   85:     function __construct($pi, tslib_cObj $cObj, $table, array $lConf)
 *  102:     public function init()
 *  116:     protected function renderValue($field, $recId, $value=null, $view='')
 *  142:     public function default_renderValue($field, $value=null, $view='')
 *  185:     protected function fetchInputFieldFormat($config)
 *  212:     public function handleFieldValue($recId, $value=null, $config=null)
 *  240:     protected function  handleFieldValue_typeCheck($value=null, array $config)
 *  255:     protected function handleFieldValue_typeSelect($recId, $value=null, array $config)
 *  321:     protected function getMMRecords($recId, array $config)
 *  365:     protected function handleFieldValue_typeGroup($recId, $value=null, array $config)
 *  412:     protected function sL($str)
 *  424:     protected function getLL($key, $alternativeLabel= '', $hsc=false)
 *
 * TOTAL FUNCTIONS: 12
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
	protected $pi;
	protected $templateCode;
	protected $prefixId;
	protected $conf;
	protected $cObj;

	protected $table;

	public static $allowedImgFileExtArray = array(
		'gif',
		'png',
		'jpeg',
		'jpg',
	);

	/**
	 * Constructor
	 *
	 * @param	tx_icstcafeadmin_pi1		$pi: Instance of tx_icstcafeadmin_pi1
	 * @param	tslib_cObj		$cObj: tx_icstcafeadmin_pi1 cObj
	 * @param	string		$table: The tablename
	 * @param	array		$fields: Fields anmes array
	 * @param	array		$fieldLabels: Associative array of fields labels like field=>labelfield
	 * @param	string		$templateCode: The template code
	 * @param	array		$lConf: Typoscript configuration
	 * @return	void
	 */
	function __construct($pi, tslib_cObj $cObj, $table, array $lConf) {
		$this->pi = $pi;
		$this->prefixId = $pi->prefixId;
		$this->extKey = $pi->extKey;

		$this->table = $table;

		$this->cObj = $cObj;
		$this->templateCode = $pi->templateCode;
		$this->conf = $lConf;
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
	 * @param	int		$recId: Record's id
	 * @param	mixed		$value: Field's value
	 * @param	string		$view: The display view
	 * @return	string		The value
	 */
	protected function renderValue($field, $recId, $value=null, $view='') {
		if (!$field)
			throw new Exception('Field is not set on RenderValue.');

		$label = $GLOBALS['TCA'][$this->table]['ctrl']['label'];
		$config = $GLOBALS['TCA'][$this->table]['columns'][$field]['config'];
		
		// Hook to render value
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['renderValue'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['renderValue'] as $class) {
				$procObj = & t3lib_div::getUserObj($class);
				$value = $procObj->renderValue($this, $this->table, $recId, $field, $value, $config, $view);
			}
		}
		elseif ($field===$label && $view=='viewList') {
			$data = array(
				'id' => $recId,
				'title' => $this->handleFieldValue($recId ,$value, $config),
			);
			$cObj = t3lib_div::makeInstance('tslib_cObj');
			$cObj->start($data, $this->table);
			$cObj->setParent($this->cObj->data, $this->cObj->currentRecord);

			$value = $cObj->stdWrap('', $this->conf['defaultConf.']['label.']);
		}
		else {
			$value = $this->default_renderValue($field, $this->handleFieldValue($recId ,$value, $config), $view);
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
		$value = htmlspecialchars($value);
		$config = $GLOBALS['TCA'][$this->table]['columns'][$field]['config'];

		// Render value on field Typoscript configuration
		if ($this->conf['renderConf.'][$field . '.'][$view . '.']) {
			$value = $this->cObj->stdWrap($value, $this->conf['renderConf.'][$field . '.'][$view . '.']);
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
	 * @param	int		$recId: Record's id
	 * @param	mixed		$value: Field's value
	 * @param	array		$config: Field's TCA configuration
	 * @return	string		the	value
	 */
	public function handleFieldValue($recId, $value=null, $config=null) {
		if (!$config)
			return htmlspecialchars($value);

		switch ($config['type']) {
			// case 'input': Nothing to do
			// case 'text': Nothing to do
			case 'check':
				$value = $this->handleFieldValue_typeCheck ($value, $config);
				break;
			case 'select':
				$value = $this->handleFieldValue_typeSelect ($recId, $value, $config);
				break;
			case 'group':
				$value = $this->handleFieldValue_typeGroup ($recId, $value, $config);
				break;
			default:
		}
		return htmlspecialchars($value);
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
	 * @param	int		$recId: Record's id
	 * @param	mixed		$value: Field's value
	 * @param	array		$config: Field's TCA configuration
	 * @return	string		The processed value
	 */
	protected function handleFieldValue_typeSelect($recId, $value=null, array $config) {
		if ($config['MM']) {
			if (!$value) {
				$value = '';
			}
			else {
				$result = $this->getMMRecords($recId, $config);
				// Fetch labels
				$labels = array();
				while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result)) {
					$labels[] = $row['ft_label'];
				}
				if (!empty($labels))
					$value = implode(',', $labels);
			}
		}
		// foreign_table
		elseif ($config['foreign_table']) {
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
	 * @param	int		$recId: Record's id
	 * @param	array		$config: Field's TCA configuration
	 * @return	resource		pointer MySQL result pointer / DBAL object
	 */
	protected function getMMRecords($recId, array $config) {
		t3lib_div::loadTCA($config['foreign_table']);

		// Get select fields
		$label = $GLOBALS['TCA'][$config['foreign_table']]['ctrl']['label'];
		$fields = array('`'.$config['foreign_table'].'`.`uid` as ft_uid');
		if ($label != 'uid')
			$fields[] = '`'.$config['foreign_table'].'`.`'.$label.'` as ft_label';

		$addWhere_tablenames = ' AND (`'.$config['MM'].'`.`tablenames` = \'' . $this->table . '\' || `'.$config['MM'].'`.`tablenames` = \'\')';

		// Get records
		if ($config['MM_opposite_field']) {
			$result = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query(
				implode(',', $fields),
				$config['foreign_table'],
				$config['MM'],
				$this->table,
				' AND `'.$config['MM'].'`.`uid_foreign` = ' . $recId . $addWhere_tablenames,
				'',
				'`'.$config['MM'].'`.`sorting_foreign`'
			);
		} else {
			$result = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query(
				implode(',', $fields),
				$this->table,
				$config['MM'],
				$config['foreign_table'],
				' AND `'.$config['MM'].'`.`uid_local` = ' . $recId . $addWhere_tablenames,
				'',
				'`'.$config['MM'].'`.`sorting`'
			);
		}
		return $result;
	}

	/**
	 * Handles group field's value
	 *
	 * @param	int		$recId: Record's id
	 * @param	mixed		$value: Field's value
	 * @param	array		$config: Field's TCA configuration
	 * @return	string		The processed value
	 */
	protected function handleFieldValue_typeGroup($recId, $value=null, array $config) {
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
							' AND `'.$table.'`.`deleted` =0 AND `'.$config['MM'].'`.`uid_local` = ' . $recId . $addWhere_tablenames,
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
	protected function sL($str) {
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
	protected function getLL($key, $alternativeLabel= '', $hsc=false) {
		return $this->pi->pi_getLL($key, $alternativeLabel, $hsc);
	}


}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_tcafe_admin/pi1/class.tx_icstcafeadmin_CommonRenderer.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_tcafe_admin/pi1/class.tx_icstcafeadmin_CommonRenderer.php']);
}

?>