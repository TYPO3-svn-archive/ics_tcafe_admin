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
 *   64:     function __construct($pi, $table, $row, array $fields, array $piVars, array $lConf)
 *   78:     public function controlEntries()
 *   93:     public function controlEntry($field)
 *  187:     public function getNoCheckFields()
 *  196:     public function resetNoCheckFields()
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

	private $noCheckFields = array();	// Array of no checked field

	/**
	 * Constructor
	 *
	 * @param	tx_icstcafeadmin_pi1		$pi: Instance of tx_icstcafeadmin_pi1
	 * @param	string		$table: The tablename
	 * @param	array		$row: The row
	 * @param	array		$fields: Array of fields
	 * @param	array		$piVars: $pi POST and GET incoming array
	 * @param	array		$lConf: Typoscript configuration
	 * @return	void
	 */
	function __construct($pi, $table, $row, array $fields, array $piVars, array $lConf) {
		$this->table = $table;
		$this->row = $row;
		$this->fields = $fields;

		$this->piVars = $piVars;
		$this->conf = $lConf;
	}

	/**
	 * Checks entries
	 *
	 * @return	boolean		Whether entries are checked, it returns true. Otherwise it returns false.
	 */
	public function controlEntries() {
		$control = true;
		foreach ($this->fields as $field) {
			if (!$this->controlEntry($field)&& $this->conf['controlEntries.']['breakControl'])
				break;
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
		$GLOBALS['TSFE']->includeTCA();
		t3lib_div::loadTCA($this->table);
		$config = $GLOBALS['TCA'][$this->table]['columns'][$field]['config'];
		$evals = t3lib_div::trimExplode(',', $config['eval'], true);
		if ($this->conf['controlEntries.'][$field.'.']['eval.'])
			$evals = array_merge(t3lib_div::trimExplode(',', $this->conf['controlEntries.'][$field.'.']['eval.']), $evals);

		$value = $this->piVars[$field];
		$control = true;
		foreach ($evals as $eval) {
			switch ($eval) {
				case 'required':
					if (!$value) {
						$control = false;
						if ($this->conf['controlEntries.']['breakControl'])
							return false;
					}
					break;
				case 'date':
					if ($value && !strtotime($value)) {
						$control = false;
						if ($this->conf['controlEntries.']['breakControl'])
							return false;
					}
					break;
				case 'datetime':// date + time
					if ($value && !strtotime($value)) {
						$control = false;
						if ($this->conf['controlEntries.']['breakControl'])
							return false;
					}
					break;
				case 'time':	// time (hours, minutes)
					if ($value && !strtotime($value)) {
						$control = false;
						if ($this->conf['controlEntries.']['breakControl'])
							return false;
					}
					break;
				case 'timesec':	// time + sec
					if ($value && !strtotime($value)) {
						$control = false;
						if ($this->conf['controlEntries.']['breakControl'])
							return false;
					}
					break;
				case 'year':
					if ($value && !strtotime('01-01-' . $value)) {
						$control = false;
						if ($this->conf['controlEntries.']['breakControl'])
							return false;
					}
					break;
				case 'int':
					if ($value && !is_numeric($value)) {
						$control = false;
						if ($this->conf['controlEntries.']['breakControl'])
							return false;
					}
					break;
				case 'double2':
					if ($value && !is_numeric($value)) {
						$control = false;
						if ($this->conf['controlEntries.']['breakControl'])
							return false;
					}
					break;
				case 'alphanum':
					if ($value && preg_match('[^a-zA-Z0-9]+', $value)) {
						$control = false;
						if ($this->conf['controlEntries.']['breakControl'])
							return false;
					}
					break;
				default:
					// Hook on controlEntry
					if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['controlEntry'])) {
						foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['controlEntry'] as $class) {
							$procObj = & t3lib_div::getUserObj($class);
							$control = $procObj->controlEntry($field, $value, $evals, $this, $this->conf);
						}
					}
				}
		}
		if (!$control)
			$this->noCheckFields[] = $field;
		return $control;
	}

	/**
	 * Retrieves noCheckFields
	 *
	 * @return	mixed		Array of fields not checked
	 */
	public function getNoCheckFields() {
		return $this->noCheckFields;
	}

	/**
	 * Reset noCheckFields
	 *
	 * @return	void
	 */
	public function resetNoCheckFields() {
		$this->noCheckFields = array();
	}

}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_tcafe_admin/pi1/class.tx_icstcafeadmin_controlForm.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_tcafe_admin/pi1/class.tx_icstcafeadmin_controlForm.php']);
}

?>