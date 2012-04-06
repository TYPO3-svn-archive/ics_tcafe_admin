<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 In cite Solution <technique@in-cite.net>
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
 * Class 'tx_icstcafeadmin_dynflex' for the 'ics_tcafe_admin' extension.
 * Generates dynamic flex.
 *
 * @author	Tsi YANG <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icstcafeadmin
 */
class tx_icstcafeadmin_dynflex {
	var $extKey        = 'ics_tcafe_admin';

	var	$fieldTypes = array('input', 'text', 'check', 'radio', 'select', 'group');

	/**
	 * Pre process flexform's field
	 *
	 * @param	string		$pi_table	Le nom de la table
	 * @param	string		$pi_field	Le nom du champ
	 * @param	array		$pi_row		l'enregistrement de la table à éditer
	 * @param	string		$pi_altName	Le nom alternatif du champ
	 * @param	integer		$pi_palette
	 * @param	string		$pi_extra
	 * @param	integer		$pi_pal
	 * @param	object		&$pi_tce
	 * @return	[type]		...
	 */
	function getSingleField_preProcess($pi_table, $pi_field, & $pi_row, $pi_altName, $pi_palette, $pi_extra, $pi_pal, &$pi_tce) {

		$list_types = array('ics_tcafe_admin_pi1');

		if (($pi_table != 'tt_content') || ($pi_field != 'pi_flexform') || ($pi_row['CType'] != 'list') || !in_array($pi_row['list_type'], $list_types))
			return;

		t3lib_div::loadTCA($pi_table);
		$conf = &$GLOBALS['TCA'][$pi_table]['columns'][$pi_field];
		$this->id = $pi_row['pid'];
		$flexData = (!empty($pi_row['pi_flexform'])) ? (t3lib_div::xml2array($pi_row['pi_flexform'])) : (array('data' => array()));

		if ($pi_row['list_type'] == 'ics_tcafe_admin_pi1') {
			if ($xmlFlex = $this->preProcess_icstcafeadmin_pi1($flexData))
				$conf['config']['ds']['ics_tcafe_admin_pi1,list'] = $xmlFlex;
		}
	}

	/**
	 * Pre process ics_tcafe_admin_pi1
	 *
	 * @param	array		$flexData	Flexform data
	 * @return	string		Xml flexform
	 */
	private function preProcess_icstcafeadmin_pi1($flexData = null) {
		if (!$flexData)
			return null;

		$file_ffds_pi1 = 'EXT:ics_tcafe_admin/flexform_ds_pi1.xml';

		$flex = '';
		if ($flex_tablefields = $this->ppFlex_tablefields($flexData, $file_ffds_pi1))
			$flex .= $flex_tablefields;

		if ($flex_fieldLabels = $this->ppFlex_fieldlabels($flexData, $file_ffds_pi1))
			$flex .= $flex_fieldLabels;

		return str_replace(
			'<!-- ###ADDITIONAL FLEX TABLEFIELDS### -->',
			$flex,
			file_get_contents(t3lib_div::getFileAbsFileName($file_ffds_pi1))
		);
	}

	/**
	 * Pre procces flexform tablefields
	 *
	 * @param	array		$flexData		Flexform data
	 * @param	string		$file_ffds_pi1	Flexform ds file
	 * @return	string		XML flexform of tablefields
	 */
	private function ppFlex_tablefields($flexData = null, $file_ffds_pi1 = null) {
		if (!$flexData || !$file_ffds_pi1)
			return null;

		$table = $flexData['data']['table']['lDEF']['tablename']['vDEF'];
		if (empty($table))
			return null;
		t3lib_div::loadTCA($table);

		$fields = $GLOBALS['TCA'][$table]['columns'];
		if (empty($fields))
			return null;

		$config['type'] = 'select';
		$config['items'] = array();
		foreach($fields as $fieldname=>$fieldrow){
			if (in_array($GLOBALS['TCA'][$table]['columns'][$fieldname]['config']['type'], $this->fieldTypes))
				$config['items'][] = array(
					'0' => $GLOBALS['TCA'][$table]['columns'][$fieldname]['label'],
					'1' => $fieldname
				);
		}
		$config['maxitems'] = count($fields);
		$config['size'] = count($fields);

		$flexArray = array(
			'TCEforms' => array(
				'label' => 'LLL:EXT:ics_tcafe_admin/locallang_flexform_pi1.xml:tablefields',
				'config' => $config,
			),
		);

		return t3lib_div::array2xml($flexArray, '', 0, 'fields');
	}

	/**
	 * Pre procces flexform fieldlabels
	 *
	 * @param	array		$flexData		Flexform data
	 * @param	string		$file_ffds_pi1	Flexform ds file
	 * @return	string		XML flexform of fieldlabels
	 */
	private function ppFlex_fieldlabels($flexData = null, $file_ffds_pi1 = null) {
		if (!$flexData || !$file_ffds_pi1)
			return null;

		$table = $flexData['data']['table']['lDEF']['tablename']['vDEF'];
		if (empty($table))
			return null;
		t3lib_div::loadTCA($table);

		$fields = $GLOBALS['TCA'][$table]['columns'];
		if (empty($fields))
			return null;

		$xmlFlex = '';
		foreach ($fields as $fieldname=>$fieldrow) {
			if (in_array($GLOBALS['TCA'][$table]['columns'][$fieldname]['config']['type'], $this->fieldTypes)) {
				$flexArray = array(
					'TCEforms' => array(
						'label' => sprintf($GLOBALS['LANG']->sL('LLL:EXT:ics_tcafe_admin/locallang_flexform_pi1.xml:fieldlabel'), $GLOBALS['LANG']->sL($GLOBALS['TCA'][$table]['columns'][$fieldname]['label'])),
						'config' => array(
							'type' => 'input',
							'size' => '20',
						),
					),
				);
				$xmlFlex .= t3lib_div::array2xml($flexArray, '', 0, $fieldname . 'Label');
			}
		}
		return $xmlFlex;
	}

	/**
	 * insert 'codes', found in the ['what_to_display'] array to the selector in the BE.
	 *
	 * @param	array		$config: extension configuration array
	 * @return	array		$config array with extra codes merged in
	 */
	function user_insertExtraCodes($config) {
		if(is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['what_to_display'])) {
			$config['items'] = array_merge($config['items'], $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['what_to_display']);
		}
		return $config;
	}


}
