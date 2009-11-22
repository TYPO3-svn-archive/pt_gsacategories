<?php

########################################################################
# Extension Manager/Repository config file for ext: "pt_gsacategories"
#
# Auto generated 11-11-2008 16:02
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'GSA Categories',
	'description' => 'Adds categories support for GSA (pt_gsashop)',
	'category' => 'General Shop Applications',
	'author' => 'Fabrizio Branca',
	'author_email' => 'branca@punkt.de',
	'shy' => '',
	'dependencies' => 'cms,pt_gsashop,pt_tools,jquery,pt_gsaadmin,smarty',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'alpha',
	'internal' => '',
	'uploadfolder' => 1,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author_company' => 'punkt.de GmbH',
	'version' => '0.0.2',
	'constraints' => array(
		'depends' => array(
			'cms' => '',
			'php' => '5.1.0-0.0.0',
			'typo3' => '4.1.0-0.0.0',
			'pt_gsashop' => '0.10.1-',
			'pt_tools' => '0.3.0-',
			'jquery' => '1.2.2',
			'pt_gsaadmin' => '0.0.4',
			'smarty' => '1.2.6-',
		),
		'conflicts' => array(
		),
		'suggests' => array(
			'PEAR HTML_QuickForm_advmultiselect (THIS IS JUST A HINT, please ignore if your server is correctly configured)' => '',
			'pt_gsaarticlelist' => '',
		),
	),
	'_md5_values_when_last_written' => 'a:35:{s:9:"ChangeLog";s:4:"e8d4";s:10:"README.txt";s:4:"ee2d";s:21:"ext_conf_template.txt";s:4:"b0a7";s:12:"ext_icon.gif";s:4:"4546";s:17:"ext_localconf.php";s:4:"ab17";s:14:"ext_tables.php";s:4:"fedf";s:14:"ext_tables.sql";s:4:"6357";s:31:"icon_tx_ptgsacategories_cat.png";s:4:"1798";s:39:"icon_tx_ptgsacategories_cat_art_rel.gif";s:4:"475a";s:41:"icon_tx_ptgsacategories_sysfolder_cat.png";s:4:"33f9";s:16:"locallang_db.xml";s:4:"9f6d";s:24:"tca_pt_gsacategories.php";s:4:"e1df";s:37:"res/HTML_Quickform_advmultiselect.css";s:4:"c294";s:41:"res/class.tx_ptgsacategories_category.php";s:4:"c757";s:49:"res/class.tx_ptgsacategories_categoryAccessor.php";s:4:"c494";s:51:"res/class.tx_ptgsacategories_categoryCollection.php";s:4:"cd8c";s:38:"res/tx_ptgsadmin_module2_categories.js";s:4:"11ef";s:24:"res/samples/sortmenu.txt";s:4:"fa62";s:28:"res/tmpl/articlebox.tpl.html";s:4:"159e";s:55:"res/hooks/class.tx_ptgsacategories_ptgsaadmin_hooks.php";s:4:"bda0";s:71:"res/hooks/class.tx_ptgsacategories_ptgsaarticlelist_catDataProvider.php";s:4:"6c9e";s:70:"res/hooks/class.tx_ptgsacategories_ptgsashop_displayArticleInfobox.php";s:4:"9524";s:28:"static/catmenu/constants.txt";s:4:"d9ca";s:24:"static/catmenu/setup.txt";s:4:"bb17";s:28:"static/general/constants.txt";s:4:"d9ca";s:24:"static/general/setup.txt";s:4:"c9a3";s:36:"static/catDataProvider/constants.txt";s:4:"d9ca";s:32:"static/catDataProvider/setup.txt";s:4:"b402";s:36:"pi1/class.tx_ptgsacategories_pi1.php";s:4:"2473";s:19:"pi1/flexform_ds.xml";s:4:"7b65";s:17:"pi1/locallang.xml";s:4:"96ea";s:21:"pi1/locallang_tca.xml";s:4:"6f33";s:14:"doc/DevDoc.txt";s:4:"30f9";s:19:"doc/wizard_form.dat";s:4:"0bb4";s:20:"doc/wizard_form.html";s:4:"33d0";}',
	'suggests' => array(
	),
);

?>