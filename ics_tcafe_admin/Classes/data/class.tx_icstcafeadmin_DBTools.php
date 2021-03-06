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
 *   60: class tx_icstcafeadmin_DBTools
 *   81:     function __construct($pi_base, $conf)
 *   98:     public function process_valuesToDB($table, $recordId=0, array $fields, array $values)
 *  115:     public function process_valueToDB($table, $recordId=0, $field, $value)
 *  160:     public function renderField_config_evals($table, $field, $recordId=0, $value, array $evals)
 *  243:     private function process_dateToDB($table, $field, $value)
 *  284:     private function process_datetimeToDB($table, $field, $value)
 *  326:     private function process_timeToDB($value)
 *  348:     private function process_timesecToDB($value)
 *  375:     public function renderField_text($table, $field, $recordId=0, $value, $config)
 *  403:     public function renderField_check($table, $field, $recordId=0, $value, $config)
 *  426:     public function renderField_select($table, $field, $recordId=0, $value, $config)
 *  487:     function renderField_group_parseFiles($table, $field, $recordId=0, array $value, $config, $uploadfolder=null, $basename=true)
 *  560:     public function getGroup_files()
 *  569:     public function getSelect_MM()
 *
 * TOTAL FUNCTIONS: 14
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

require_once(t3lib_extMgm::extPath('rtehtmlarea').'pi2/class.tx_rtehtmlarea_pi2.php');
/**
 * Class 'tx_icstcafeadmin_DBTools' for the 'ics_tcafe_admin' extension.
 *
 * This class process value to DB.
 *
 * @author	Tsi YANG <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icstcafeadmin
 */
class tx_icstcafeadmin_DBTools {
	protected $pi_base;
	var $prefixId;
	var $extKey;
	var $conf;
	var $cObj;

	private $group_files = array(
		'deletedFiles' => array(),	// Associative array of field type "group", internal type "file" where key/value are fieldname/array of files
		'newFiles' => array()		// Associative array of field type "group", internal type "file" where key/value are fieldname/new files
	);

	private $select_MM = array();	// Associative array of field type "select" with MM relation where key/value are fieldname/array of foreign table uids

	/**
	 * Constructor
	 *
	 * @param	tx_icstcafeadmin_pi1		$pi_base: Instance of tx_icstcafeadmin_pi1
	 * @param	array		$conf: Typoscript configuration
	 * @return	void
	 */
	function __construct($pi_base, $conf) {
		$this->pi_base = $pi_base;
		$this->prefixId = $pi_base->prefixId;
		$this->extKey = $pi_base->extKey;
		$this->conf = $conf;
		$this->cObj = $pi_base->cObj;
	}

	/**
	 * Process values to DB
	 *
	 * @param	string		$table: The table name
	 * @param	int		$recordId: The record id
	 * @param	string		$field: Array of fields name
	 * @param	mixed		$value: The entry value
	 * @return	mixed		The data array with values processed where key/value pairs are fieldnames/values
	 */
	public function process_valuesToDB($table, $recordId=0, array $fields, array $values) {
		$data = array();
		foreach ($fields as $field) {
			$dateArray[$field] = $this->process_valueToDB($table, $recordId, $field, $values[$field]);
		}
		return $dateArray;
	}

	/**
	 * Process values to DB
	 *
	 * @param	string		$table: The table name
	 * @param	int		$recordId: The record id
	 * @param	string		$field: The field name
	 * @param	mixed		$value: The entry value
	 * @return	mixed		The processed value
	 */
	public function process_valueToDB($table, $recordId=0, $field, $value) {
		$GLOBALS['TSFE']->includeTCA();
		t3lib_div::loadTCA($table);
		$config = $GLOBALS['TCA'][$table]['columns'][$field]['config'];

		$process = false;
		// Hook on process_valueToDB
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['process_valueToDB'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['process_valueToDB'] as $class) {
				$procObj = & t3lib_div::getUserObj($class);
				if ($process = $procObj->process_valueToDB($this->pi_base, $table, $field, $value, $recordId, $this->conf, $this))
					break;
			}
		}
		if (!$process) {
			$evals = t3lib_div::trimExplode(',', $config['eval'], true);
			if (!empty($evals)) {
				$value = $this->renderField_config_evals($table, $field, $recordId, $value, $evals);
			}
			elseif ($config['type']=='check') {
				$value = $this->renderField_check($table, $field, $recordId, $value, $config);
			}
			elseif ($config['type']=='select') {
				$value = $this->renderField_select($table, $field, $recordId, $value, $config);
			}
			elseif($config['type']=='group' && $config['internal_type']=='file') {
				$value = $this->renderField_group_parseFiles($table, $field, $recordId, $value, $config);
			}
			elseif ($config['type']=='text'){
				$value = $this->renderField_text($table, $field, $recordId, $value, $config);
			}
		}
		return $value;
	}

	/**
	 * Process value switch evals
	 *
	 * @param	string		$table: The table name
	 * @param	string		$field: The field name
	 * @param	array		$recordId: The record id
	 * @param	mixed		$value: The value to process
	 * @param	array		$evals: The TCA eval
	 * @return	mixed		The value processing
	 */
	public function renderField_config_evals($table, $field, $recordId=0, $value, array $evals) {
		foreach ($evals as $eval) {
			switch ($eval) {
				case 'required':
					if (empty($value))
						throw new Exception('Required field ' . $field . ' must not be empty.');
					break;
				case 'date':
					$value = $this->process_dateToDB($table, $field, $value);
					break;
				case 'datetime':
					$value = $this->process_datetimeToDB($table, $field, $value);
					break;
				case 'time':
					$value = $this->process_timeToDB($value);
					break;
				case 'timesec':
					$value = $this->process_timesecToDB($value);
					break;
				case 'year':
					if ($value) {
						$date = preg_replace('<[^0-9]>', '', $value);
						$value = strtotime('01-01-' . $date);
					}
					$value = intval($value);
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
					$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
						$field,
						$table,
						'deleted = 0 AND uid=' . $recordId,
						'',
						'',
						'1'
					);
					if (is_array($rows) && !empty($rows))
						$row = $rows[0];
					if ($value && ($value != $row[$field])) {
						$value = md5($value);
					}
					break;
				default:
			}
		}
		return $value;
	}

	/**
	 * Process date to DB
	 *
	 * @param	string		$table: The tablename
	 * @param	string		$field: The field name
	 * @param	mixed		$value: The value to process
	 * @return	int		The processed value
	 */
	private function process_dateToDB($table, $field, $value) {
		if (!$value)
			return 0;
		$process = false;
		// Hook on process_dateToDB
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['process_dateToDB'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['process_dateToDB'] as $class) {
				$procObj = & t3lib_div::getUserObj($class);
				if ($process = $procObj->process_dateToDB($this->pi_base, $table, $field, $value, $this->conf, $this))
					break;
			}
		}
		if (!$process) {
			if (preg_match( '`^\d{1,2}-\d{1,2}-\d{4}$`' , $value) && ($date = strtotime($value))) {
				$value = $date;
			}
			else {
				if ($date = preg_replace('<[^0-9]>', '', $value)) {
					$dateArray = str_split($date, 2);
					$d = $dateArray[0];
					$m = $dateArray[1]? $dateArray[1]: date('m');
					$dateArray = str_split($date, 4);
					$y = $dateArray[1]? $dateArray[1]: date('Y');
					$value = mktime(0,0,0,$m,$d,$y);
				}
				else {
					$value = 0;
				}
			}
		}
		return intval($value);
	}

	/**
	 * Process datetime to DB
	 *
	 * @param	string		$table: The tablename
	 * @param	string		$field: The field name
	 * @param	mixed		$value: The value to process
	 * @return	int		The processed value
	 */
	private function process_datetimeToDB($table, $field, $value) {
		if (!$value)
			return 0;

		$process = false;
		// Hook on process_datetimeToDB
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['process_datetimeToDB'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['process_datetimeToDB'] as $class) {
				$procObj = & t3lib_div::getUserObj($class);
				if ($process = $procObj->process_datetimeToDB($this->pi_base, $table, $field, $value, $this->conf, $this))
					break;
			}
		}
		if(!$process) {
			if (preg_match( '`^\d{1,2}-\d{1,2}-\d{4} \d{1,2}:\d{1,2}$`' , $value) && ($date = strtotime($value))) {
				$value = $date;
			}
			else {
				if ($date = preg_replace('<[^0-9]>', '', $value)) {
					$dateArray = str_split($date, 2);
					$h = $dateArray[0];
					$i = $dateArray[1];
					$d = $dateArray[2];
					$m = $dateArray[3]? $dateArray[3]: date('m');
					$dateArray = str_split($date, 8);
					$y = $dateArray[1]? $dateArray[1]: date('Y');
					$value = mktime($h,$i,0,$m,$d,$y);
				}
				else {
					$value = 0;
				}
			}
		}
		return intval($value);
	}

	/**
	 * Process time to DB
	 *
	 * @param	mixed		$value: The value to process
	 * @return	int		The processed value
	 */
	private function process_timeToDB($value) {
		if (!$value)
			return 0;
		if (preg_match('`^\d{1,2}:\d{1,2}$`', $value) && ($time = strtotime($value))) {
			$value = $time;
		}
		else {
			$time = preg_replace('<[^0-9]>', '', $value);
			$timeArray = str_split($time, 2);
			$h = $timeArray[0];
			$i = $timeArray[1];
			$value = mktime($h,$i);
		}
		return intval($value);
	}

	/**
	 * Process time to DB
	 *
	 * @param	mixed		$value: The value to process
	 * @return	int		The processed value
	 */
	private function process_timesecToDB($value) {
		if (!$value)
			return 0;
		if (preg_match('`^\d{1,2}:\d{1,2}:\d{1,2}$`', $value) && ($time = strtotime($value))) {
			$value = $time;
		}
		else {
			$time = preg_replace('<[^0-9]>', '', $value);
			$timeArray = str_split($time, 2);
			$h = $timeArray[0];
			$i = $timeArray[1];
			$s = $timeArray[2];
			$value = mktime($h,$i,$s);
		}
		return intval($value);
	}

	/**
	 * Process select value to DB
	 *
	 * @param	string		$table: The table name
	 * @param	string		$field: The fieldname
	 * @param	array		$recordId: The record id
	 * @param	mixed		$value: The value to process
	 * @param	array		$config: TCA field config
	 * @return	int
	 */
	public function renderField_text($table, $field, $recordId=0, $value, $config) {
		if (!$value)
			$value = '';
		$process = false;
		// Hook on renderField_text
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['renderField_text'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['renderField_text'] as $class) {
				$procObj = & t3lib_div::getUserObj($class);
				if ($process = $procObj->renderField_text($this->pi_base, $table, $field, $value, $recordId, $this->conf, $this))
					break;
			}
		}
		if(!$process) {
			if (isset($config['wizards']['RTE'])) {
				if(!$this->RTEObj)  $this->RTEObj = t3lib_div::makeInstance('tx_rtehtmlarea_pi2');
				if($this->RTEObj->isAvailable()) {
					$this->thePidValue = $GLOBALS['TSFE']->id;
					$pageTSConfig = $GLOBALS['TSFE']->getPagesTSconfig();
					$RTEsetup = $pageTSConfig['RTE.'];
					$this->thisConfig = $RTEsetup['default.'];
					$this->thisConfig = $this->thisConfig['FE.'];
					$dataArray = array();
					$value = $this->RTEObj->transformContent(
						'db',
						$value,
						$table,
						$field,
						$dataArray,
						$this->specConf,
						$this->thisConfig,
						'',
						$this->thePidValue
					);
				}
				else {
					$value = htmlspecialchars($value);
				}
			}
			else {
				$value = htmlspecialchars($value);
			}
			
		}
		return $value;
	}

	/**
	 * Process check value to DB
	 *
	 * @param	string		$table: The table name
	 * @param	string		$field: The fieldname
	 * @param	array		$recordId: The record id
	 * @param	mixed		$value: The value to process
	 * @param	array		$config: TCA field config
	 * @return	int
	 */
	public function renderField_check($table, $field, $recordId=0, $value, $config) {
		if ($config['cols'] && $config['cols']>1) {
			// TODO: Implements form section and implements this
		}
		else {
			if ($config['items']) {
				if (is_array($value)) {
					$v_checked = $value;
					$value = 0;
					foreach ($v_checked as $check) {
						$value = $value + intval($check);
					}
				}
			}
			else {
				if ($value[0]) {
					$value = 1;
				}
				else {
					$value = 0;
				}
			}
		}
		return $value;
	}

	/**
	 * Process select value to DB
	 *
	 * @param	string		$table: The table name
	 * @param	string		$field: The fieldname
	 * @param	array		$recordId: The record id
	 * @param	mixed		$value: The value to process
	 * @param	array		$config: TCA field config
	 * @return	int
	 */
	public function renderField_select($table, $field, $recordId=0, $value, $config) {
		if (!$value)
			$value = null;
		$process = false;
		// Hook on renderField_select
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['renderField_select'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['renderField_select'] as $class) {
				$procObj = & t3lib_div::getUserObj($class);
				if ($process = $procObj->renderField_select($this->pi_base, $table, $field, $value, $recordId, $this->conf, $this))
					break;
			}
		}
		if(!$process) {
			if ($config['maxitems'] <= 1 && $config['renderMode'] !== 'tree') {
				if ($config['MM']) {
					if ($value) {
						$this->select_MM[$field] = array($value);
						$value = 1;
					}
					else {
						$this->select_MM[$field] = null;
					}
				}
				else {	// $config['items']
					// nothing to do;
				}
			// } elseif (!strcmp($config['renderMode'], 'checkbox')) {
				// TODO : Implements Checkbox renderMode
			// } elseif (!strcmp($config['renderMode'], 'singlebox')) {
				// TODO : Implements Single selector box renderMode
			// } elseif (!strcmp($config['renderMode'], 'tree')) { //
				// TODO : Implements Tree renderMode
			}
			else {	// Multiple checkbox or select multiple
				if ($config['MM']) {
					if ($value) {
						$elem = array_slice($value, 0, 1);
						if (is_numeric($elem[0])) {	// Case: select multiple
							$this->select_MM[$field] = array_values($value);
						}
						else {	// Case: checkbox
							$this->select_MM[$field] = array_keys($value);
						}
						
						$value = count($value);
					}
					else {
						$this->select_MM[$field] = null;
					}
				}
				else {
					if ($value)
						$value = implode(',', array_keys($value));
				}
			}
		}
		return $value;
	}

	/**
	 * Upload file
	 *
	 * @param	string		$table: The table name
	 * @param	string		$field: The fieldname
	 * @param	array		$recordId: The record id
	 * @param	array		$value: The value to process
	 * @param	array		$config: TCA field config
	 * @param	string		$uploadfolder: The upload folder whether diffrent of TCA uploadfolder
	 * @param	boolean		$basename: Filename is basename or path. Default is basename.
	 * @return	void
	 */
	function renderField_group_parseFiles($table, $field, $recordId=0, array $value, $config, $uploadfolder=null, $basename=true) {
		if (!$uploadfolder)
			$uploadfolder = $config['uploadfolder']? $config['uploadfolder'].'/': '';

		$files = t3lib_div::trimExplode(',', $value['files'], true);
		if (is_array($value) && !empty($value)) {
			$pFiles = $value;
			unset($pFiles['files']);
			$fileToDeleteArray = array_intersect($files, array_values($pFiles));
			$files = array_diff($files, array_values($pFiles));
			foreach ($fileToDeleteArray as $file) {
				if ($file) {
					if ($basename)
						$unlink = t3lib_div::getFileAbsFileName($uploadfolder . $file);
					else
						$unlink = t3lib_div::getFileAbsFileName($file);
					@unlink($unlink);
					$this->group_files['deletedFiles'][$field][] = $file;
				}
			}
		}

		if ($_FILES[$this->prefixId]['tmp_name'][$field]['file']) {
			if (!is_uploaded_file($_FILES[$this->prefixId]['tmp_name'][$field]['file'])) {
				throw new Exception('File ' . $_FILES[$this->prefixId]['tmp_name'][$field]['file'] . ' can not be uploaded.');
			}
			elseif ($_FILES[$this->prefixId]['error'][$field]['file'] != UPLOAD_ERR_OK) {
				throw new Exception('Error on upload file ' . $_FILES[$this->prefixId]['tmp_name'][$field]['file']);
			}
			elseif ($_FILES[$this->prefixId]['size'][$field]['file'] > ($config['max_size'] * 1024)) {
				throw new Exception('File size is higher than maxsize.');
			}
			else {
				$newFile = basename(t3lib_div::fixWindowsFilePath($_FILES[$this->prefixId]['name'][$field]['file']));

				$filefunc = t3lib_div::makeInstance('t3lib_basicFileFunctions');
				$newFile = $filefunc->cleanFileName($newFile);
				if ($uploadfolder)
					$newFile = $filefunc->getUniqueName($newFile, t3lib_div::getFileAbsFileName($uploadfolder));
				$newFile = basename($newFile);

				$ext = '';
				$allowed = t3lib_div::trimExplode(',', $config['allowed'], true);
				$disallowed = t3lib_div::trimExplode(',', $config['disallowed'], true);

				if (strrpos($newFile, '.') !== false)
					$ext = strtolower(substr($newFile, strrpos($newFile, '.') + 1));

				if (in_array($ext, $allowed) || !in_array($ext, $disallowed)) {
					if (move_uploaded_file($_FILES[$this->prefixId]['tmp_name'][$field]['file'], t3lib_div::getFileAbsFileName($uploadfolder . $newFile))) {
						if ($basename) {
							$files[] = $newFile;
							$this->group_files['newFile'][$field] = $newFile;
						}
						else {
							$files[] = $uploadfolder.$newFile;
							$this->group_files['newFile'][$field] = $uploadfolder.$newFile;
						}
					}
				}
				else {
					throw new Exception('File extension ' . $ext . ' is not allowed.');
				}
			}
		}
		return implode(',', $files);
	}

	/**
	 * Retrieves group files
	 *
	 * @return	mixed		Array of group files with deletedFiles where key/value pairs are fieldnames/array(files) and newFile where key/value pairs are fieldnames/files
	 */
	public function getGroup_files() {
		return $this->group_files;
	}

	/**
	 * Retrieves foreign table uids of MM relation
	 *
	 * @return	mixed		Associative array of field type "select" with MM relation where key/value are fieldname/array of foreign table uids
	 */
	public function getSelect_MM() {
		return $this->select_MM;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_tcafe_admin/pi1/class.tx_icstcafeadmin_DBTools.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_tcafe_admin/pi1/class.tx_icstcafeadmin_DBTools.php']);
}

?>