-- 
-- Table `tl_page`
-- 

CREATE TABLE `tl_page` (
  `sibling` int(10) unsigned NOT NULL default '0',
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Table `tl_module`
-- 

CREATE TABLE `tl_module` (
  `pageswitch_template` varchar(32) NOT NULL default '',
) ENGINE=MyISAM DEFAULT CHARSET=utf8;