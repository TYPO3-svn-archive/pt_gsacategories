#
# Table structure for table 'tx_ptgsacategories_cat'
#
CREATE TABLE tx_ptgsacategories_cat (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	starttime int(11) DEFAULT '0' NOT NULL,
	endtime int(11) DEFAULT '0' NOT NULL,
	title tinytext DEFAULT '' NOT NULL,
	description text DEFAULT '' NOT NULL,
	image text DEFAULT '' NOT NULL,
	parentcats int(11) DEFAULT '0' NOT NULL,
	childcats int(11) DEFAULT '0' NOT NULL,
	articles int(11) DEFAULT '0' NOT NULL,
	invisible tinyint(4) DEFAULT '0' NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_ptgsacategories_cat_art_rel'
#
CREATE TABLE tx_ptgsacategories_cat_art_rel (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	starttime int(11) DEFAULT '0' NOT NULL,
	endtime int(11) DEFAULT '0' NOT NULL,
	cat_uid int(11) DEFAULT '0' NOT NULL,
	art_uid int(11) DEFAULT '0' NOT NULL,
	cat_sort int(11) DEFAULT '0' NOT NULL,
	art_sort int(11) DEFAULT '0' NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY cat_uid (cat_uid),
	KEY art_uid (art_uid)
);



#
# Table structure for table 'tx_ptgsacategories_cat_cat_rel'
#
CREATE TABLE tx_ptgsacategories_cat_cat_rel (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	starttime int(11) DEFAULT '0' NOT NULL,
	endtime int(11) DEFAULT '0' NOT NULL,
	parentcat_uid int(11) DEFAULT '0' NOT NULL,
	childcat_uid int(11)  DEFAULT '0' NOT NULL,
	parentcat_sort int(11) DEFAULT '0' NOT NULL,
	childcat_sort int(11) DEFAULT '0' NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY parentcat_uid (parentcat_uid),
	KEY childcat_uid (childcat_uid)
);
