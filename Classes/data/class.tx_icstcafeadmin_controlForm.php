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
 *   49: class tx_icstcafeadmin_controlForm
 *   72:     function __construct($pi_base, $table, $recordId=0, array $fields, array $conf)
 *  103:     public function controlEntries()
 *  149:     public function controlEntry($field)
 *  315:     public function getNoCheckOnFields()
 *  324:     public function resetNoCheckOnFields()
 *
 * TOTAL FUNCTIONS: 5
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */


/**
 * Class 'tx_icstcafeadmin_controlForm' for the 'ics_tcafe_admin' extension.
 *
 * @author	Tsi YANG <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icstcafeadmin
 */
class tx_icstcafeadmin_controlForm{

	private $noCheckOnFields = array();	// Array of no checked field

	var $pi_base;
	var $prefixId;
	var $extKey;

	private $table;
	private $fields;
	private $row = null;

	/**
	 * Constructor
	 *
	 * @param	tx_icstcafeadmin_pi1		$pi_base: Instance of tx_icstcafeadmin_pi1
	 * @param	string		$table: The tablename
	 * @param	int		$row: The recordId
	 * @param	array		$fields: Array of fields
	 * @param	array		$pi_baseVars: $pi_base POST and GET incoming array
	 * @param	array		$lConf: Typoscript configuration
	 * @return	void
	 */
	function __construct($pi_base, $table, $recordId=0, array $fields, array $conf) {
		$this->pi_base = $pi_base;
		$this->prefixId = $pi_base->prefixId;
		$this->extKey = $pi_base->extKey;
		$this->conf = $conf;
		$this->cObj = $pi_base->cObj;
		$this->piVars = $pi_base->piVars;

		$this->table = $table;
		$this->fields = $fields;

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
	}

	/**
	 * Checks entries
	 *
	 * @return	boolean		Whether entries are checked, it returns true. Otherwise it returns false.
	 */
	public function controlEntries() {
		$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['ics_tcafe_admin']);

		$control = true;
		foreach ($this->fields as $field) {
			$controlEntry = true;
			// Hook on controlEntry
			if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['controlEntry'])) {
				foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['controlEntry'] as $class) {
					$procObj = & t3lib_div::getUserObj($class);
					if ($process = $procObj->controlEntry($this->pi_base, $this->table, $field, $value, $this->recordId, $this->conf, $this, $controlEntry))
						break;
				}
			}
			if (!$process)
				$controlEntry = $this->controlEntry($field);
			if ($control)
				$control = $controlEntry;

			if ($extConf['debug.']['formControllerDebug']) {
				$tables = t3lib_div::trimExplode(',', $extConf['debug.']['tables'], true);
				if (empty($tables) || in_array($this->table, $tables)) {
					t3lib_div::devLog('Form controller (controlEntries)', 'ics_tcafe_admin', 0, array('Field'=>$field, 'Control'=>$controlEntry));
				}
			}

			if (!$control && $this->conf['controlEntries.']['breakControl']) {
				break;
			}
		}
		// Hook on extra_controlEntries
		if ($control && is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['extra_controlEntries'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['extra_controlEntries'] as $class) {
				$procObj = & t3lib_div::getUserObj($class);
				$procObj->extra_controlEntries($control, $this->table, $this->row, $this->pi_base, $this->conf, $this);
			}
		}
		return $control;
	}

	/**
	 * Checks entry
	 *
	 * @param	string		$field: The field name
	 * @return	boolean		Whether entry is checked, it returns true. Otherwise it returns false.
	 */
	public function controlEntry($field) {
		$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['ics_tcafe_admin']);

		$GLOBALS['TSFE']->includeTCA();
		t3lib_div::loadTCA($this->table);
		$config = $GLOBALS['TCA'][$this->table]['columns'][$field]['config'];
		$evals = t3lib_div::trimExplode(',', $config['eval'], true);
		if ($config['type']=='select' && $config['minitems']>0)
			$evals[] = 'required';
		if ($this->conf['controlEntries.'][$this->table.'.'][$field.'.']['eval'])
			$evals = array_merge(t3lib_div::trimExplode(',', $this->conf['controlEntries.'][$this->table.'.'][$field.'.']['eval']), $evals);

		if ($extConf['debug.']['formControllerDebug']) {
			$tables = t3lib_div::trimExplode(',', $extConf['debug.']['tables'], true);
			if (empty($tables) || in_array($this->table, $tables)) {
				t3lib_div::devLog('Form controller (controlEntry)', 'ics_tcafe_admin', 0, array('Evals'=>$evals));
			}
		}

		$value = $this->piVars[$field];
		$control = true;
		foreach ($evals as $eval) {
			switch ($eval) {
				case 'required':
					if ($extConf['debug.']['formControllerDebug']) {
						$tables = t3lib_div::trimExplode(',', $extConf['debug.']['tables'], true);
						if (empty($tables) || in_array($this->table, $tables)) {
							t3lib_div::devLog('Form controller (controlEntry)', 'ics_tcafe_admin', 0, array('Pass eval'=>'required'));
						}
					}
					if (!$value) {
						$control = false;
						if ($this->conf['controlEntries.']['breakControl'])
							return false;
					}
					break;
				case 'date':
					if ($extConf['debug.']['formControllerDebug']) {
						$tables = t3lib_div::trimExplode(',', $extConf['debug.']['tables'], true);
						if (empty($tables) || in_array($this->table, $tables)) {
							t3lib_div::devLog('Form controller (controlEntry)', 'ics_tcafe_admin', 0, array('Pass eval'=>'date'));
						}
					}
					if ($value && !strtotime($value)) {
						$control = false;
						if ($this->conf['controlEntries.']['breakControl'])
							return false;
					}
					break;
				case 'datetime':// date + time
					if ($extConf['debug.']['formControllerDebug']) {
						$tables = t3lib_div::trimExplode(',', $extConf['debug.']['tables'], true);
						if (empty($tables) || in_array($this->table, $tables)) {
							t3lib_div::devLog('Form controller (controlEntry)', 'ics_tcafe_admin', 0, array('Pass eval'=>'datetime'));
						}
					}
					if ($value && !strtotime($value)) {
						$control = false;
						if ($this->conf['controlEntries.']['breakControl'])
							return false;
					}
					break;
				case 'time':	// time (hours, minutes)
					if ($extConf['debug.']['formControllerDebug']) {
						$tables = t3lib_div::trimExplode(',', $extConf['debug.']['tables'], true);
						if (empty($tables) || in_array($this->table, $tables)) {
							t3lib_div::devLog('Form controller (controlEntry)', 'ics_tcafe_admin', 0, array('Pass eval'=>'time'));
						}
					}
					if ($value && !strtotime($value)) {
						$control = false;
						if ($this->conf['controlEntries.']['breakControl'])
							return false;
					}
					break;
				case 'timesec':	// time + sec
					if ($extConf['debug.']['formControllerDebug']) {
						$tables = t3lib_div::trimExplode(',', $extConf['debug.']['tables'], true);
						if (empty($tables) || in_array($this->table, $tables)) {
							t3lib_div::devLog('Form controller (controlEntry)', 'ics_tcafe_admin', 0, array('Pass eval'=>'timesec'));
						}
					}
					if ($value && !strtotime($value)) {
						$control = false;
						if ($this->conf['controlEntries.']['breakControl'])
							return false;
					}
					break;
				case 'year':
					if ($extConf['debug.']['formControllerDebug']) {
						$tables = t3lib_div::trimExplode(',', $extConf['debug.']['tables'], true);
						if (empty($tables) || in_array($this->table, $tables)) {
							t3lib_div::devLog('Form controller (controlEntry)', 'ics_tcafe_admin', 0, array('Pass eval'=>'year'));
						}
					}
					if ($value && !strtotime('01-01-' . $value)) {
						$control = false;
						if ($this->conf['controlEntries.']['breakControl'])
							return false;
					}
					break;
				case 'int':
					if ($extConf['debug.']['formControllerDebug']) {
						$tables = t3lib_div::trimExplode(',', $extConf['debug.']['tables'], true);
						if (empty($tables) || in_array($this->table, $tables)) {
							t3lib_div::devLog('Form controller (controlEntry)', 'ics_tcafe_admin', 0, array('Pass eval'=>'int'));
						}
					}
					if ($value && !is_numeric($value)) {
						$control = false;
						if ($this->conf['controlEntries.']['breakControl'])
							return false;
					}
					break;
				case 'double2':
					if ($extConf['debug.']['formControllerDebug']) {
						$tables = t3lib_div::trimExplode(',', $extConf['debug.']['tables'], true);
						if (empty($tables) || in_array($this->table, $tables)) {
							t3lib_div::devLog('Form controller (controlEntry)', 'ics_tcafe_admin', 0, array('Pass eval'=>'double2'));
						}
					}
					if ($value && !is_numeric($value)) {
						$control = false;
						if ($this->conf['controlEntries.']['breakControl'])
							return false;
					}
					break;
				case 'alphanum':
					if ($extConf['debug.']['formControllerDebug']) {
						$tables = t3lib_div::trimExplode(',', $extConf['debug.']['tables'], true);
						if (empty($tables) || in_array($this->table, $tables)) {
							t3lib_div::devLog('Form controller (controlEntry)', 'ics_tcafe_admin', 0, array('Pass eval'=>'alphanum'));
						}
					}
					if ($value && preg_match('[^a-zA-Z0-9]+', $value)) {
						$control = false;
						if ($this->conf['controlEntries.']['breakControl'])
							return false;
					}
					break;
				default:
					// Hook on extra eval entry
					if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['extra_evalEntry'])) {
						foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['extra_evalEntry'] as $class) {
							$procObj = & t3lib_div::getUserObj($class);
							if ($process = $procObj->extra_evalEntry($this->pi_base, $this->table, $field, $value, $this->recordId, $this->conf, $this, $control)) {
								if ($extConf['debug.']['formControllerDebug']) {
									$tables = t3lib_div::trimExplode(',', $extConf['debug.']['tables'], true);
									if (empty($tables) || in_array($this->table, $tables)) {
										t3lib_div::devLog('Form controller (controlEntry)', 'ics_tcafe_admin', 0, array('Pass eval'=>'extra'));
									}
								}
								break;
							}
						}
					}
			}
		}
		if (!$control)
			$this->noCheckOnFields[] = $field;
		return $control;
	}

	/**
	 * Retrieves noCheckOnFields
	 *
	 * @return	mixed		Array of fields not check on
	 */
	public function getNoCheckOnFields() {
		return $this->noCheckOnFields;
	}

	/**
	 * Reset noCheckOnFields
	 *
	 * @return	void
	 */
	public function resetNoCheckOnFields() {
		$this->noCheckOnFields = array();
	}

}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_tcafe_admin/pi1/class.tx_icstcafeadmin_controlForm.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_tcafe_admin/pi1/class.tx_icstcafeadmin_controlForm.php']);
}

?>