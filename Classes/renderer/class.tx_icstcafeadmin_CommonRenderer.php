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
 *   52: class tx_icstcafeadmin_CommonRenderer
 *   73:     function __construct($pi, tslib_cObj $cObj, $table, array $lConf)
 *   90:     public function init()
 *  102:     protected function renderValue($field, $value=null, $view='')
 *  128:     public function default_renderValue($field, $value=null, $view='')
 *  163:     private function fetchInputFieldFormat($config)
 *  187:     private function handleFieldValue($value=null, array $config)
 *  210:     private function  handleFieldValue_typeCheck($value=null, array $config)
 *
 * TOTAL FUNCTIONS: 7
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
		t3lib_div::loadTCA($this->table);
	}

	/**
	 * Render value
	 *
	 * @param	string		$field: Field's name
	 * @param	mixed		$value: Field's value
	 * @param	string		$view: The display view
	 * @return	string		The value
	 */
	protected function renderValue($field, $value=null, $view='') {
		if (!$field)
			throw new Exception('Field is not set on RenderValue.');

		$config = $GLOBALS['TCA'][$this->table]['columns'][$field]['config'];

		// Hook to render value
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['renderValue'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['renderValue'] as $class) {
				$procObj = & t3lib_div::getUserObj($class);
				$value = $procObj->renderValue($this, $this->table, $field, $this->handleFieldValue($value, $config), $config, $view);
			}
		} else {
			$value = $this->default_renderValue($field, $this->handleFieldValue($value, $config), $view);
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
	private function fetchInputFieldFormat($config) {
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
	}

	/**
	 * Handles field's value
	 *
	 * @param	mixed		$value: Field's value
	 * @param	array		$config: Field's TCA configuration
	 * @return	string		the	value
	 */
	private function handleFieldValue($value=null, array $config) {
		switch ($config['type']) {
			// case 'input': Nothing to do
			// case 'text': Nothing to do
			case 'check':
				$value = $this->handleFieldValue_typeCheck ($value, $config);
				break;
			case 'select':
				$value = $this->handleFieldValue_typeSelect ($value, $config);
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
	private function  handleFieldValue_typeCheck($value=null, array $config) {
		if (is_array($config['items'])) {
			// TODO : insert here code to process type check with several values
		}
		return $value;
	}
	
	/**
	 * Handles select field's value
	 *
	 * @param	mixed		$value: Field's value
	 * @param	array		$config: Field's TCA configuration
	 * @return	string		The processed value
	 */
	private function handleFieldValue_typeSelect($value=null, array $config) {
		// foreign_table
		// MM*
		
		if (false) {
		} 
		elseif ($config['MM']) {
		}
		elseif ($config['foreign_table']) {
			
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
	
	private function sL($str) {
		return $GLOBALS['LANG']->sL($str);
	}
	


}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_tcafe_admin/pi1/class.tx_icstcafeadmin_CommonRenderer.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_tcafe_admin/pi1/class.tx_icstcafeadmin_CommonRenderer.php']);
}

?>