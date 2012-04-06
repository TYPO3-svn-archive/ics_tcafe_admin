#
# Table structure for table 'tx_icstcafeadmin_testFields'
#
CREATE TABLE tx_icstcafeadmin_testFields (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	required_input tinytext,
	date_input int(11) DEFAULT '0' NOT NULL,
	datetime_input int(11) DEFAULT '0' NOT NULL,
	time_input int(11) DEFAULT '0' NOT NULL,
	timesec_input int(11) DEFAULT '0' NOT NULL,
	year_input int(11) DEFAULT '0' NOT NULL,
	int_input int(11) DEFAULT '0' NOT NULL,
	double_input double(11,2) DEFAULT '0.00' NOT NULL,
	alphanum_input tinytext,
	upper_input tinytext,
	lower_input tinytext,
	nospace_input tinytext,
	password_input tinytext,
	md5_input tinytext,
	textfield text,
	checkboxes int(11) DEFAULT '0' NOT NULL,
	checkfield tinyint(3) DEFAULT '0' NOT NULL,
	radiofield int(11) DEFAULT '0' NOT NULL,
	selectfield text,
	selectfield_ft text,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);