#
# Table structure for table 'tx_blogexample_domain_model_blog'
#
CREATE TABLE tx_hbook_domain_model_post (
	uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,

	content text NOT NULL,
	author varchar(255) DEFAULT '' NOT NULL,
	email varchar(255) DEFAULT '' NOT NULL,
	email_visible tinyint(1) DEFAULT '0' NOT NULL,
	ip_address int(11) unsigned DEFAULT '2130706433' NOT NULL,
	approved tinyint(1) DEFAULT '1' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY list (pid,deleted,hidden,approved)
);

CREATE TABLE tx_hbook_domain_model_smilie (
	uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,

	code varchar(16) default '' NOT NULL,
	image tinytext NOT NULL

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY list (pid,deleted,hidden),
	KEY code (code)
);