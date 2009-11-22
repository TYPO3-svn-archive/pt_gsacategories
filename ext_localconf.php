<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

t3lib_extMgm::addPItoST43($_EXTKEY,'pi1/class.tx_ptgsacategories_pi1.php','_pi1','list_type',0);

t3lib_extMgm::addUserTSConfig('
	options.saveDocNew.tx_ptgsacategories_cat=1
	options.saveDocNew.tx_ptgsacategories_cat_art_rel=1
	options.saveDocNew.tx_ptgsacategories_cat_cat_rel=1
');

// Hooks:
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['pt_gsaadmin']['module2_hooks']['returnArticleForm_formAfterFirstSection'][] = 'EXT:pt_gsacategories/res/hooks/class.tx_ptgsacategories_ptgsaadmin_hooks.php:tx_ptgsacategories_ptgsaadmin_hooks->returnArticleForm_formAfterFirstSection';
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['pt_gsaadmin']['module2_hooks']['createArticleFromFormData_processRelatedData'][] =  'EXT:pt_gsacategories/res/hooks/class.tx_ptgsacategories_ptgsaadmin_hooks.php:tx_ptgsacategories_ptgsaadmin_hooks->createArticleFromFormData_processRelatedData';
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['pt_gsaadmin']['module2_hooks']['loadArticleDefaults'][] = 'EXT:pt_gsacategories/res/hooks/class.tx_ptgsacategories_ptgsaadmin_hooks.php:tx_ptgsacategories_ptgsaadmin_hooks->loadArticleDefaults';
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['pt_gsaadmin']['module2_hooks']['deleteArticle'][] = 'EXT:pt_gsacategories/res/hooks/class.tx_ptgsacategories_ptgsaadmin_hooks.php:tx_ptgsacategories_ptgsaadmin_hooks->deleteArticle';
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['pt_gsashop']['pi2_hooks']['displayArticleInfobox_MarkerArrayHook'][] = 'EXT:pt_gsacategories/res/hooks/class.tx_ptgsacategories_ptgsashop_displayArticleInfobox.php:tx_ptgsacategories_ptgsashop_displayArticleInfobox';

// Data Provider for pt_gsaarticlelist
if (t3lib_extMgm::isLoaded('pt_gsaarticlelist')) {
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][t3lib_extMgm::extPath('pt_gsaarticlelist').'res/class.tx_ptgsaarticlelist_getDataProvider.php']['availableDataProvider'][] = array('name' => 'LLL:EXT:pt_gsacategories/locallang_db.xml:labelDataProvider', 'path' => 'EXT:pt_gsacategories/res/hooks/class.tx_ptgsacategories_ptgsaarticlelist_catDataProvider.php:tx_ptgsacategories_ptgsaarticlelist_catDataProvider');
}

?>