<?php 
/***************************************************************
*  Copyright notice
*  
*  (c) 2008 Fabrizio Branca (branca@punkt.de)
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is 
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
* 
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
* 
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

require_once 'HTML/QuickForm/advmultiselect.php'; // PEAR HTML_Quickform_advmultiselect: Advanced mutliselecet element for HTML_Quickform (see http://www.laurent-laville.org/?module=pear&desc=qfams)

require_once t3lib_extMgm::extPath('pt_gsacategories').'res/class.tx_ptgsacategories_category.php';
require_once t3lib_extMgm::extPath('pt_gsacategories').'res/class.tx_ptgsacategories_categoryCollection.php';
require_once t3lib_extMgm::extPath('pt_gsacategories').'res/class.tx_ptgsacategories_categoryAccessor.php';  



/**
 * Extending the article gui in pt_gsaadmin
 * 
 * $Id: class.tx_ptgsacategories_ptgsaadmin_hooks.php,v 1.7 2008/05/28 12:26:18 ry44 Exp $
 * 
 * @author	Fabrizio Branca <branca@punkt.de>
 * @since	2008-03-11
 */
class tx_ptgsacategories_ptgsaadmin_hooks extends tx_ptgsaadmin_submodules {
    
    /**
     * This is called when the article data is loaded (e.g. to add addtional data, that is used in the form later)
     *
     * $params['articleDataArr']    article data array
     * $params['articleObj']        tx_ptgsashop_baseArticle
     * 
     * @param   array   array of parameters   
     * @param   tx_ptgsaadmin_module2   calling module object
     * @author  Fabrizio Branca <branca@punkt.de>
     * @since   2008-02-07
     */
    public function loadArticleDefaults(&$params, &$ref){
        $catAccessor = tx_ptgsacategories_categoryAccessor::getInstance();
        $params['articleDataArr']['catselect'] = $catAccessor->selectCategoriesConnectionsForArticle($params['articleObj']->get_id());
    }
    
    
    
    /**
     * $params is an empty array (use processRelatedData if you need the article's uid)
     *
     * @param array $params
     * @param tx_ptgsaadmin_module2 $ref
     */
    public function createArticleFromFormData_processRelatedData(&$params, &$ref){
        $catAccessor = tx_ptgsacategories_categoryAccessor::getInstance();
        try{
            $catArray = is_array(t3lib_div::GPvar('catselect')) ? t3lib_div::GPvar('catselect') : array();
            $catAccessor->connectArticleToCategories($params['articleUid'], $catArray);
        } catch (tx_pttools_exception $excObj) {
            $excObj->handleException();
        }
    }
    
    
    
    /**
     * This is called when an article was deleted (e.g. for cleaning up all related data)
     * 
     * $params['articleUid']   uid of the deleted article
     *
     * @param   array   array of parameters   
     * @param   tx_ptgsaadmin_module2   calling module object
     * @author  Fabrizio Branca <branca@punkt.de>
     * @since   2008-02-07
     */   
    public function deleteArticle(&$params, &$ref){
        $catAccessor = tx_ptgsacategories_categoryAccessor::getInstance();
        $catAccessor->deleteAllCategoryConnectionsForArticle($params['articleUid']);
    }
    

    
    /**
     * This is called while building the form (after the first button row)
     * 
     * $params['formObj']              HTML_Quickform object
     * $params['defaultsDataArr']   default data array
     * $params['tceformsObj']       t3lib_TCEforms object
     * $params['table']             Name of the virtual_table
     * $params['row']               virtual row for tceforms
     *
     * @param   array   array of parameters   
     * @param   tx_ptgsaadmin_module2   calling module object
     * @author  Fabrizio Branca <branca@punkt.de>
     * @since   2008-02-07
     */
    public function returnArticleForm_formAfterFirstSection(&$params, &$ref){
         
        $catAccessor = tx_ptgsacategories_categoryAccessor::getInstance();
        $rootCats = $catAccessor->selectRootCategories();
        
        $catList = array();

        foreach ($rootCats as $rootCat){
            $cat = new tx_ptgsacategories_category($rootCat['uid']);
            foreach ($cat->getCategoryTree() as $catEntry){
                while (isset($catList[(string)$catEntry['uid']])){
                    list($uid, $counter) = explode('_', $catEntry['uid']);
                    if (is_array($params['defaultsDataArr']['catselect'])){
                        if (in_array($catEntry['uid'], $params['defaultsDataArr']['catselect'])) {
                            $params['defaultsDataArr']['catselect'][] = $uid .'_'. ($counter+1);
                        }
                    }
                    $catEntry['uid'] = $uid .'_'. ($counter+1);
                }
                $catList[(string)$catEntry['uid']] = '<span class="points">'.str_repeat("..", $catEntry['level']).'</span> '. $catEntry['title'];                    
            }
        }            
        
        
        $params['formObj']->addElement('header', 'artHeader3', $ref->ll('artForm_header6'));

        $ams =& $params['formObj']->addElement('advmultiselect', 'catselect', $ref->ll('artForm_catselect')); /* @var $ams HTML_QuickForm_advmultiselect */
        
        // rootlines
        $tmp = array();
        $rootlines = array();
        $titlelookup = array();
        if (is_array($params['defaultsDataArr']['catselect'])){
            foreach ($params['defaultsDataArr']['catselect'] as $key){
                $tmpCat = new tx_ptgsacategories_category($key);
                foreach ($tmpCat->getRootlines() as $rl){
                    if (!in_array($rl, $tmp)) {
                        $tmp[] = $rl;
                        $tmp2 = array();
                        foreach(explode(',',$rl) as $key => $catuid){
                            if (empty($titlelookup[$catuid])) {
                                $catObj = new tx_ptgsacategories_category($catuid);
                                $titlelookup[$catuid] = $catObj->get_title();
                                $titlelookup[$catuid] = (in_array($catuid, $params['defaultsDataArr']['catselect'])) ? '<span class="tx_ptgsacategories_selected">'.$titlelookup[$catuid].'</span>' : '<span class="tx_ptgsacategories_notselected">'.$titlelookup[$catuid].'</span>';
                            }
                            $tmp2[] = $titlelookup[$catuid]; 
                        }
                        $rootlines[] = implode(' > ', $tmp2);
                    }
                }
            }
        }
        sort($rootlines);
        $rootlinesinfo = '<div class="tx_ptgsacategories_categories">'.implode('<br />', $rootlines).'</div>';

        $ams->setElementTemplate($rootlinesinfo.'{selected}');
        
        $ref->doc->inDocStylesArray['HTML_Quickform_advmultiselect.css'] = '@import url("../../../../'.t3lib_extMgm::extRelPath('pt_gsacategories').'res/HTML_Quickform_advmultiselect.css");';
        
        $jsfile = $GLOBALS['BACK_PATH'].t3lib_extMgm::extRelPath('jquery').'src/jquery.js';
        $ref->jsArray['jquery.js'] = '<script type="text/javascript" src="'.$jsfile.'"></script>'; 
        
        $jsfile = $GLOBALS['BACK_PATH'].t3lib_extMgm::extRelPath('pt_gsacategories').'res/tx_ptgsadmin_module2_categories.js';
        $ref->jsArray['tx_ptgsadmin_module2_categories.js'] = '<script type="text/javascript" src="'.$jsfile.'"></script>'; 

        foreach ($catList as $key => $value){
            if (!empty($params['defaultsDataArr'])) {
                $ams->addOption($value, $key, in_array($key, (array)$params['defaultsDataArr']['catselect']) ? 'checked="checked"' : '');
            } else {
                $ams->addOption($value, $key);
            }
        }
    }
    
    
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pt_gsacategories/res/hooks/class.tx_ptgsacategories_ptgsaadmin_hooks.php']) {
    include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pt_gsacategories/res/hooks/class.tx_ptgsacategories_ptgsaadmin_hooks.php']);
}
?>