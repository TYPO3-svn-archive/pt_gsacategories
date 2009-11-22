<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');


/******************************************************************************************************************************
 * Categories table
 ******************************************************************************************************************************/

$TCA['tx_ptgsacategories_cat'] = array (
	'ctrl' => $TCA['tx_ptgsacategories_cat']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'hidden,starttime,endtime,title,description,image,parentcats,childcats,articles,visible'
	),
	'feInterface' => $TCA['tx_ptgsacategories_cat']['feInterface'],
	'columns' => array (
		'hidden' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		'starttime' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.starttime',
			'config'  => array (
				'type'     => 'input',
				'size'     => '8',
				'max'      => '20',
				'eval'     => 'date',
				'default'  => '0',
				'checkbox' => '0'
			)
		),
		'endtime' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.endtime',
			'config'  => array (
				'type'     => 'input',
				'size'     => '8',
				'max'      => '20',
				'eval'     => 'date',
				'checkbox' => '0',
				'default'  => '0',
				'range'    => array (
					'upper' => mktime(0, 0, 0, 12, 31, 2020),
					'lower' => mktime(0, 0, 0, date('m')-1, date('d'), date('Y'))
				)
			)
		),
		'title' => Array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:pt_gsacategories/locallang_db.xml:tx_ptgsacategories_cat.title',		
			'config' => Array (
				'type' => 'input',	
				'size' => '30',
			)
		),
		'description' => Array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:pt_gsacategories/locallang_db.xml:tx_ptgsacategories_cat.description',		
			'config' => Array (
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
				'wizards' => Array(
					'_PADDING' => 2,
					'RTE' => array(
						'notNewRecords' => 1,
						'RTEonly' => 1,
						'type' => 'script',
						'title' => 'Full screen Rich Text Editing|Formatteret redigering i hele vinduet',
						'icon' => 'wizard_rte2.gif',
						'script' => 'wizard_rte.php',
					),
				),
			)
		),
		'image' => Array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:pt_gsacategories/locallang_db.xml:tx_ptgsacategories_cat.image',		
			'config' => Array (
				'type' => 'group',
				'internal_type' => 'file',
				'allowed' => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],	
				'max_size' => 1000,	
				'uploadfolder' => 'uploads/tx_ptgsacategories',
				'size' => 1,	
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'parentcats' => Array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:pt_gsacategories/locallang_db.xml:tx_ptgsacategories_cat.parentcats',		
			'config' => Array (
				'type' => 'inline',
				'foreign_table' => 'tx_ptgsacategories_cat_cat_rel',
				'foreign_field' => 'childcat_uid',
				'foreign_sortby' => 'childcat_sort',
				'foreign_label' => 'parentcat_uid',
		
		        'foreign_unique' => 'parentcat_uid',
		
		        /* zeigt sonst alles doppelt an
    				'symmetric_field' => 'parentcat_uid',
    				'symmetric_sortby' => 'parentcat_sort',
    				'symmetric_label' => 'childcat_uid',
				*/
				'maxitems' => 1000,
		        'appearance' => array (
                    'collapseAll' => 0,
		            'useSortable' => 1,
		        ),
			)
		),
		'childcats' => Array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:pt_gsacategories/locallang_db.xml:tx_ptgsacategories_cat.childcats',		
			'config' => Array (
				'type' => 'inline',
				'foreign_table' => 'tx_ptgsacategories_cat_cat_rel',
				'foreign_field' => 'parentcat_uid',
				'foreign_sortby' => 'parentcat_sort',
				'foreign_label' => 'childcat_uid',
		
				'foreign_unique' => 'childcat_uid',
		
				/* zeigt sonst alles doppelt and
    				'symmetric_field' => 'childcat_uid',
    				'symmetric_sortby' => 'childcat_sort',
    				'symmetric_label' => 'parentcat_uid',
				*/
		
				'maxitems' => 1000,
		        'appearance' => array (
                    'collapseAll' => 0,
		            'useSortable' => 1,
		        ),
			)
		),
		// TODO: (ry44) convert to irre field (pointing to cached articles records) or hide
		'articles' => Array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:pt_gsacategories/locallang_db.xml:tx_ptgsacategories_cat.articles',		
			'config' => Array (
				'type'     => 'input',
				'size'     => '4',
				'max'      => '4',
				'eval'     => 'int',
				'checkbox' => '0',
				'range'    => Array (
					'upper' => '1000',
					'lower' => '10'
				),
				'default' => 0
			)
		),
		'invisible' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:pt_gsacategories/locallang_db.xml:tx_ptgsacategories_cat.invisible',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 'hidden;;1;;1-1-1, title;;;;2-2-2, description;;;richtext[paste|bold|italic|underline|formatblock|class|left|center|right|orderedlist|unorderedlist|outdent|indent|link|image]:rte_transform[mode=ts];3-3-3, image, parentcats, childcats, invisible')
	),
	'palettes' => array (
		'1' => array('showitem' => 'starttime, endtime')
	)
);





/******************************************************************************************************************************
 * Categories articles asymmetric m:n relation table
 ******************************************************************************************************************************/

$TCA['tx_ptgsacategories_cat_art_rel'] = array (
	'ctrl' => $TCA['tx_ptgsacategories_cat_art_rel']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'hidden,starttime,endtime,cat_uid,art_uid,cat_sort,art_sort'
	),
	'feInterface' => $TCA['tx_ptgsacategories_cat_art_rel']['feInterface'],
	'columns' => array (
		'hidden' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		'starttime' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.starttime',
			'config'  => array (
				'type'     => 'input',
				'size'     => '8',
				'max'      => '20',
				'eval'     => 'date',
				'default'  => '0',
				'checkbox' => '0'
			)
		),
		'endtime' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.endtime',
			'config'  => array (
				'type'     => 'input',
				'size'     => '8',
				'max'      => '20',
				'eval'     => 'date',
				'checkbox' => '0',
				'default'  => '0',
				'range'    => array (
					'upper' => mktime(0, 0, 0, 12, 31, 2020),
					'lower' => mktime(0, 0, 0, date('m')-1, date('d'), date('Y'))
				)
			)
		),
		'cat_uid' => Array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:pt_gsacategories/locallang_db.xml:tx_ptgsacategories_cat_art_rel.cat_uid',		
			'config' => Array (
				'type' => 'group',	
				'internal_type' => 'db',	
				'allowed' => 'tx_ptgsacategories_cat',	
				'size' => 1,	
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'art_uid' => Array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:pt_gsacategories/locallang_db.xml:tx_ptgsacategories_cat_art_rel.art_uid',		
			'config' => Array (
				'type'     => 'input',
				'size'     => '4',
				'max'      => '4',
				'eval'     => 'int',
				'checkbox' => '0',
				'range'    => Array (
					'upper' => '1000',
					'lower' => '10'
				),
				'default' => 0
			)
		),
		'cat_sort' => Array (		
			'config' => Array (
				'type'     => 'passthrough',
			)
		),
		'art_sort' => Array (
			'config' => Array (
				'type'     => 'passthrough',
			)		
		),
	),
	'types' => array (
		'0' => array('showitem' => 'cat_uid, art_uid, cat_sort, art_sort')
	),
);



/******************************************************************************************************************************
 * Categories symmetric m:n relation table
 ******************************************************************************************************************************/

$TCA['tx_ptgsacategories_cat_cat_rel'] = array (
	'ctrl' => $TCA['tx_ptgsacategories_cat_cat_rel']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'hidden,starttime,endtime,parentcat_uid,childcat_uid,parentcat_sort,childcat_sort'
	),
	'feInterface' => $TCA['tx_ptgsacategories_cat_cat_rel']['feInterface'],
	'columns' => array (
		'hidden' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		'starttime' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.starttime',
			'config'  => array (
				'type'     => 'input',
				'size'     => '8',
				'max'      => '20',
				'eval'     => 'date',
				'default'  => '0',
				'checkbox' => '0'
			)
		),
		'endtime' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.endtime',
			'config'  => array (
				'type'     => 'input',
				'size'     => '8',
				'max'      => '20',
				'eval'     => 'date',
				'checkbox' => '0',
				'default'  => '0',
				'range'    => array (
					'upper' => mktime(0, 0, 0, 12, 31, 2020),
					'lower' => mktime(0, 0, 0, date('m')-1, date('d'), date('Y'))
				)
			)
		),
		'parentcat_uid' => Array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:pt_gsacategories/locallang_db.xml:tx_ptgsacategories_cat_cat_rel.parentcat_uid',		
			'config' => Array (
				'type' => 'select',
				'foreign_table' => 'tx_ptgsacategories_cat',
				'maxitems' => 1,
			)
		),
		'childcat_uid' => Array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:pt_gsacategories/locallang_db.xml:tx_ptgsacategories_cat_cat_rel.childcat_uid',		
			'config' => Array (
				'type' => 'select',
				'foreign_table' => 'tx_ptgsacategories_cat',
				'maxitems' => 1,
			)
		),
		'parentcat_sort' => Array (		
			'config' => Array (
				'type'     => 'passthrough',
			)
		),
		'childcat_sort' => Array (		
			'config' => Array (
				'type'     => 'passthrough',
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 'parentcat_uid, childcat_uid, parentcat_sort, childcat_sort')
	),
);

?>