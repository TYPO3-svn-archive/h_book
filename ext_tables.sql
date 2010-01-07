#
# Table structure for table 'tx_hbook_entries'
#
CREATE TABLE tx_hbook_entries (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	author tinytext NOT NULL,
	text text NOT NULL,
	comment text NOT NULL,
	ip varchar(15) DEFAULT '' NOT NULL,
	email tinytext NOT NULL,
	homepage tinytext NOT NULL,
	location tinytext NOT NULL,
	icq varchar(9) DEFAULT '' NOT NULL,
	msn varchar(128) DEFAULT '' NOT NULL,
	aim varchar(128) DEFAULT '' NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_hbook_ips'
#
CREATE TABLE tx_hbook_ips (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	endtime int(11) DEFAULT '0' NOT NULL,
	ip varchar(15) DEFAULT '' NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_hbook_checkhash'
#
CREATE TABLE tx_hbook_checkhash (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	hash varchar(8) DEFAULT '',
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);