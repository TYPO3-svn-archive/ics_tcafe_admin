<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA['tx_icstcafeadmin_testFields'] = array (
	'ctrl' => $TCA['tx_icstcafeadmin_testFields']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'hidden,required_input,date_input,datetime_input,time_input,timesec_input,year_input,int_input,double_input,alphanum_input,upper_input,lower_input,nospace_input,password_input,md5_input,textfield,checkboxes,checkfield,radiofield,selectfield,test'
	),
	'feInterface' => $TCA['tx_icstcafeadmin_testFields']['feInterface'],
	'columns' => array (
		'hidden' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		'required_input' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_tcafe_admin/locallang_db.xml:tx_icstcafeadmin_testFields.required_input',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'required',
			)
		),
		'date_input' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_tcafe_admin/locallang_db.xml:tx_icstcafeadmin_testFields.date_input',		
			'config' => array (
				'type'     => 'input',
				'size'     => '8',
				'max'      => '20',
				'eval'     => 'date',
				'checkbox' => '0',
				'default'  => '0'
			)
		),
		'datetime_input' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_tcafe_admin/locallang_db.xml:tx_icstcafeadmin_testFields.datetime_input',		
			'config' => array (
				'type'     => 'input',
				'size'     => '12',
				'max'      => '20',
				'eval'     => 'datetime',
				'checkbox' => '0',
				'default'  => '0'
			)
		),
		'time_input' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_tcafe_admin/locallang_db.xml:tx_icstcafeadmin_testFields.time_input',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'time',
			)
		),
		'timesec_input' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_tcafe_admin/locallang_db.xml:tx_icstcafeadmin_testFields.timesec_input',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'timesec',
			)
		),
		'year_input' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_tcafe_admin/locallang_db.xml:tx_icstcafeadmin_testFields.year_input',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',	
				'max' => '4',	
				'eval' => 'year',
			)
		),
		'int_input' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_tcafe_admin/locallang_db.xml:tx_icstcafeadmin_testFields.int_input',		
			'config' => array (
				'type'     => 'input',
				'size'     => '4',
				'max'      => '4',
				'eval'     => 'int',
				'checkbox' => '0',
				'range'    => array (
					'upper' => '1000',
					'lower' => '10'
				),
				'default' => 0
			)
		),
		'double_input' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_tcafe_admin/locallang_db.xml:tx_icstcafeadmin_testFields.double_input',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'double2',
			)
		),
		'alphanum_input' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_tcafe_admin/locallang_db.xml:tx_icstcafeadmin_testFields.alphanum_input',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'alphanum',
			)
		),
		'upper_input' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_tcafe_admin/locallang_db.xml:tx_icstcafeadmin_testFields.upper_input',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'upper',
			)
		),
		'lower_input' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_tcafe_admin/locallang_db.xml:tx_icstcafeadmin_testFields.lower_input',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'lower',
			)
		),
		'nospace_input' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_tcafe_admin/locallang_db.xml:tx_icstcafeadmin_testFields.nospace_input',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'nospace',
			)
		),
		'password_input' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_tcafe_admin/locallang_db.xml:tx_icstcafeadmin_testFields.password_input',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'required,password',
			)
		),
		'md5_input' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_tcafe_admin/locallang_db.xml:tx_icstcafeadmin_testFields.md5_input',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'md5',
			)
		),
		'textfield' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_tcafe_admin/locallang_db.xml:tx_icstcafeadmin_testFields.textfield',		
			'config' => array (
				'type' => 'text',
				'cols' => '30',	
				'rows' => '5',
			)
		),
		'checkboxes' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_tcafe_admin/locallang_db.xml:tx_icstcafeadmin_testFields.checkboxes',		
			'config' => array (
				'type' => 'check',
				'cols' => 4,
				'items' => array (
					array('LLL:EXT:ics_tcafe_admin/locallang_db.xml:tx_icstcafeadmin_testFields.checkboxes.I.0', ''),
					array('LLL:EXT:ics_tcafe_admin/locallang_db.xml:tx_icstcafeadmin_testFields.checkboxes.I.1', ''),
					array('LLL:EXT:ics_tcafe_admin/locallang_db.xml:tx_icstcafeadmin_testFields.checkboxes.I.2', ''),
					array('LLL:EXT:ics_tcafe_admin/locallang_db.xml:tx_icstcafeadmin_testFields.checkboxes.I.3', ''),
				),
			)
		),
		'checkfield' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_tcafe_admin/locallang_db.xml:tx_icstcafeadmin_testFields.checkfield',		
			'config' => array (
				'type' => 'check',
				'default' => 1,
			)
		),
		'radiofield' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_tcafe_admin/locallang_db.xml:tx_icstcafeadmin_testFields.radiofield',		
			'config' => array (
				'type' => 'radio',
				'items' => array (
					array('LLL:EXT:ics_tcafe_admin/locallang_db.xml:tx_icstcafeadmin_testFields.radiofield.I.0', '0'),
					array('LLL:EXT:ics_tcafe_admin/locallang_db.xml:tx_icstcafeadmin_testFields.radiofield.I.1', '1'),
					array('LLL:EXT:ics_tcafe_admin/locallang_db.xml:tx_icstcafeadmin_testFields.radiofield.I.2', '2'),
					array('LLL:EXT:ics_tcafe_admin/locallang_db.xml:tx_icstcafeadmin_testFields.radiofield.I.3', '3'),
				),
			)
		),
		'selectfield' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_tcafe_admin/locallang_db.xml:tx_icstcafeadmin_testFields.selectfield',		
			'config' => array (
				'type' => 'select',
				'items' => array (
					array('',0),
					array('LLL:EXT:ics_tcafe_admin/locallang_db.xml:tx_icstcafeadmin_testFields.selectfield.I.0', 1),
					array('LLL:EXT:ics_tcafe_admin/locallang_db.xml:tx_icstcafeadmin_testFields.selectfield.I.1', 2),
					array('LLL:EXT:ics_tcafe_admin/locallang_db.xml:tx_icstcafeadmin_testFields.selectfield.I.2', 3),
					array('LLL:EXT:ics_tcafe_admin/locallang_db.xml:tx_icstcafeadmin_testFields.selectfield.I.3', 4),
				),
				'size' => 10,	
				'maxitems' => 100,
			)
		),
		'selectfield_ft' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_tcafe_admin/locallang_db.xml:tx_icstcafeadmin_testFields.selectfield_ft',		
			'config' => array (
				'type' => 'select',	
				'foreign_table' => 'fe_users',	
				'foreign_table_where' => 'ORDER BY fe_users.uid',	
				'size' => 5,	
				'minitems' => 0,
				'maxitems' => 100,
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 'hidden;;1;;1-1-1, required_input, date_input, datetime_input, time_input, timesec_input, year_input, int_input, double_input, alphanum_input, upper_input, lower_input, nospace_input, password_input, md5_input, textfield, checkboxes, checkfield, radiofield, selectfield, selectfield_ft')
	),
	'palettes' => array (
		'1' => array('showitem' => '')
	)
);
?>