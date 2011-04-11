<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1'] = 'layout,select_key,pages,recursive';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1'] = 'pi_flexform';
t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:'.$_EXTKEY.'/pi1/flexform_ds.xml');


t3lib_extMgm::addPlugin(array('LLL:EXT:pt_gsacategories/locallang_db.xml:tt_content.list_type_pi1', $_EXTKEY.'_pi1'),'list_type');

t3lib_extMgm::addStaticFile($_EXTKEY,'static/general/','GSA Categories: General Settings');
t3lib_extMgm::addStaticFile($_EXTKEY,'static/catDataProvider/','GSA Categories: Category DataProvider for the GSA Article list plugin');
t3lib_extMgm::addStaticFile($_EXTKEY,'static/catmenu/','GSA Categories: Category Menu');

// add folder type and icon
if (t3lib_div::compat_version('4.4')) {
	t3lib_SpriteManager::addTcaTypeIcon('pages', 'contains-gsacat', '../typo3conf/ext/pt_gsacategories/icon_tx_ptgsacategories_sysfolder_cat.png');
} else {
	$ICON_TYPES['gsacat'] = array('icon' => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_ptgsacategories_sysfolder_cat.png');
}
$TCA['pages']['columns']['module']['config']['items'][] = array('LLL:EXT:pt_gsacategories/locallang_db.xml:tx_ptgsacategories_cat', 'gsacat');


t3lib_extMgm::allowTableOnStandardPages('tx_ptgsacategories_cat');

$TCA['tx_ptgsacategories_cat'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:pt_gsacategories/locallang_db.xml:tx_ptgsacategories_cat',
		'label'     => 'title',
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY crdate',
		'delete' => 'deleted',
		'enablecolumns' => array (
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca_pt_gsacategories.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_ptgsacategories_cat.png',
	),
	'feInterface' => array (
		'fe_admin_fieldList' => 'hidden, starttime, endtime, title, description, image, parentcats, childcats, articles, invisible',
	)
);


t3lib_extMgm::allowTableOnStandardPages('tx_ptgsacategories_cat_art_rel');

$TCA['tx_ptgsacategories_cat_art_rel'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:pt_gsacategories/locallang_db.xml:tx_ptgsacategories_cat_art_rel',
		'label'     => 'uid',
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'hideTable' => true,
		'default_sortby' => 'ORDER BY crdate',
		'delete' => 'deleted',
		'enablecolumns' => array (
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca_pt_gsacategories.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_ptgsacategories_cat_art_rel.gif',
	),
	'feInterface' => array (
		'fe_admin_fieldList' => 'hidden, starttime, endtime, cat_uid, art_uid, cat_sort, art_sort',
	)
);


t3lib_extMgm::allowTableOnStandardPages('tx_ptgsacategories_cat_cat_rel');

$TCA['tx_ptgsacategories_cat_cat_rel'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:pt_gsacategories/locallang_db.xml:tx_ptgsacategories_cat_cat_rel',
		'label'     => 'uid',
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'hideTable' => true,
		'default_sortby' => 'ORDER BY crdate',
		'delete' => 'deleted',
		'enablecolumns' => array (
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca_pt_gsacategories.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_ptgsacategories_cat.png',
	),
	'feInterface' => array (
		'fe_admin_fieldList' => 'hidden, starttime, endtime, parentcat_uid, childcat_uid, parentcat_sort, childcat_sort',
	)
);


?>