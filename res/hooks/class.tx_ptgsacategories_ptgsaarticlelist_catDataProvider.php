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



require_once t3lib_extMgm::extPath('pt_gsacategories').'res/class.tx_ptgsacategories_category.php';
require_once t3lib_extMgm::extPath('pt_gsacategories').'res/class.tx_ptgsacategories_categoryCollection.php';
require_once t3lib_extMgm::extPath('pt_gsacategories').'res/class.tx_ptgsacategories_categoryAccessor.php';
require_once t3lib_extMgm::extPath('pt_gsacategories').'pi1/class.tx_ptgsacategories_pi1.php';
require_once t3lib_extMgm::extPath('pt_gsaarticlelist').'res/class.tx_ptgsaarticlelist_iDataProvider.php';  
require_once t3lib_extMgm::extPath('pt_gsashop').'res/class.tx_ptgsashop_articleAccessor.php';



/**
 * Class "tx_ptgsacategories_ptgsaarticlelist_catDataProvider"
 *
 * $Id: class.tx_ptgsacategories_ptgsaarticlelist_catDataProvider.php,v 1.13 2008/09/11 09:53:44 ry44 Exp $
 *  
 * @author	Fabrizio Branca <branca@punkt.de>
 * @since	2008-06-24
 */
class tx_ptgsacategories_ptgsaarticlelist_catDataProvider extends tx_ptgsaarticlelist_pi1 implements tx_ptgsaarticlelist_iDataProvider {
    
	/**
	 * @var array
	 */
    protected $catArray = array();
    
    /**
     * @var array
     */
    protected $urlParams;
    
    /**
     * @var tx_ptgsacategories_categoryAccessor
     */
    protected $catAccessor;
    
    /**
     * @var tx_ptgsashop_articleAccessor
     */
    protected $articleAccessor;
    
    /**
     * @var tx_ptgsaarticlelist_pi1	reference to the calling article list plugin object
     */
    protected $pObj;
    
    /**
     * @var int		selected value
     */
    protected $selectedValue;
    
    /**
     * @var array configuration array
     */
    public $conf;
    
    
    
    /**
     * Init function (for tx_ptgsaarticlelist_iDataProvider interface)
     *
     * @param   tx_ptgsaarticlelist_pi1     reference to the calling article list plugin
     * @author  Fabrizio Branca <branca@punkt.de>
     * @since   2008-01   
     */
    public function init(tx_ptgsaarticlelist_pi1 $ref) {
        
        $this->pObj = $ref;
        $this->conf = $this->pObj->conf['catDataProvider.'];
        
        $this->urlParams = t3lib_div::_GP('tx_ptgsacategories_pi1');
        $this->catAccessor = tx_ptgsacategories_categoryAccessor::getInstance();
        $this->articleAccessor = tx_ptgsashop_articleAccessor::getInstance();
        
        // pre-process configuration
        $this->conf['defaultCategory'] = $GLOBALS['TSFE']->cObj->stdWrap($this->conf['defaultCategory'], $this->conf['defaultCategory.']);
        $this->conf['showSubCategoryContent'] = $GLOBALS['TSFE']->cObj->stdWrap($this->conf['showSubCategoryContent'], $this->conf['showSubCategoryContent.']);
        $this->conf['orderBy'] = $GLOBALS['TSFE']->cObj->stdWrap($this->conf['orderBy'], $this->conf['orderBy.']);
        $this->conf['hidePassiveArticles'] = $GLOBALS['TSFE']->cObj->stdWrap($this->conf['hidePassiveArticles'], $this->conf['hidePassiveArticles.']);
        $this->conf['parameterName'] = $GLOBALS['TSFE']->cObj->stdWrap($this->conf['parameterName'], $this->conf['parameterName.']);
        
        if (TYPO3_DLOG) t3lib_div::devLog('Configuration for "tx_ptgsacategories_ptgsaarticlelist_catDataProvider"', 'pt_gsacategories', 1, $this->conf);
        
        
    	// parameterName fallback (e.g. "cat_uid // filter")
		foreach (t3lib_div::trimExplode('//', $this->conf['parameterName']) as $parameterName) {
			$this->selectedValue = $GLOBALS['TSFE']->cObj->getData('GPvar:'.$parameterName, array());
			if (!empty($this->selectedValue)) {
				break;
			}
		}
		
         if (empty($this->selectedValue)) {
        	if (!empty($this->conf['defaultCategory'])) {
        		$this->selectedValue = $this->conf['defaultCategory'];
        	}
        }
		
        $this->catArray = array(intval($this->selectedValue));
        $catObj = new tx_ptgsacategories_category(intval($this->selectedValue));
        
        if ($this->conf['showSubCategoryContent']) {
            foreach ($catObj->getCategoryTree() as $cat) {
                if (!in_array($cat['uid'], $this->catArray)) {
                    $this->catArray[] = $cat['uid'];    
                }
            }
        }
        
        // Add Parameters to the pager urls
        $this->pObj->conf['pager_typolink.']['additionalParams'] = $GLOBALS['TSFE']->cObj->stdWrap($this->pObj->conf['pager_typolink.']['additionalParams'], $this->pObj->conf['pager_typolink.']['additionalParams.']);
        $parameterValue = $GLOBALS['TSFE']->cObj->getData('GPvar:'.$this->conf['parameterName'], array());
        $this->pObj->conf['pager_typolink.']['additionalParams'] .= '&'.tx_ptgsacategories_pi1::convertPipedGpvarString($this->conf['parameterName']).'='.intval($parameterValue);
        
    }
        
    
    
    /**
     * Get article collection for a page (for tx_ptgsaarticlelist_iDataProvider interface)
     *
     * @param   int     offset
     * @param   int     row cound
     * @return  tx_ptgsashop_articleCollection
     * @author  Fabrizio Branca <branca@punkt.de>
     * @since   2008-01
     */
    public function getArticleCollectionForPage($offset, $rowcount) {
        
        $artColl = new tx_ptgsashop_articleCollection();
        
        trace($this->catArray, 0, '==> $this->catArray');
        
        $limit = (($offset!='') ? $offset.',' : '') . $rowcount;
         
        $articleUids = $this->catAccessor->selectArticlesInCategories($this->catArray);
        // echo 'Sortierung nach '.  $this->pObj->conf['catDataProvider.']['orderBy'];
        // $GLOBALS['trace']=1;
        trace($articleUids, 0, '==> $articleUids');
        
        if (count($articleUids)>0) {
            $where = 'art.NUMMER in ('.implode(',',$articleUids).')';
            
            if ($this->conf['hidePassiveArticles']) { // TODO: dokumentieren!
            	$where .= ' AND art.PASSIV = 0';
            }
            
            
            $articleUids = $this->articleAccessor->selectOnlineArticles($this->conf['orderBy'], $limit, '', $where, false);
                    
            foreach ($articleUids as $art) {
            // echo $art_uid.'<br />';
    	        $artColl->addItem( tx_ptgsashop_articleFactory::createArticle(  
                            $art['NUMMER'], 
                            $this->pObj->customerObj->get_priceCategory(), 
                            $this->pObj->customerObj->get_gsaMasterAddressId(), 
                            1, 
                            '',  
                            $this->pObj->conf['display_img']
                        )
    	        ); 
            }
        }    
             
        return $artColl;
    }
    
    
    
    /**
     * Get article count (for tx_ptgsaarticlelist_iDataProvider interface)
     *
     * @param   void
     * @return  int     amount of total articles
     * @author  Fabrizio Branca <branca@punkt.de>
     * @since   2008-01
     */
    public function getArticleCount() {
        
        $articleUids = $this->catAccessor->selectArticlesInCategories($this->catArray);
        
        if (count($articleUids)>0) {
            $where = 'art.NUMMER in ('.implode(',',$articleUids).')';
            
            if ($this->conf['hidePassiveArticles']) { // TODO: dokumentieren!
                $where .= ' AND art.PASSIV = 0';
            }
            
            return $this->articleAccessor->selectOnlineArticlesQuantity('', $where);
        } else {
            return 0;
        }
        
    }
    
    
    
    /**
     * Get Headline (for tx_ptgsaarticlelist_iDataProvider interface)
     *
     * @return  string  headline
     * @author  Fabrizio Branca <branca@punkt.de>
     * @since	2008-02-15
     */
    public function getHeadline() {
        $catObj = new tx_ptgsacategories_category(intval($this->selectedValue));
        
        if (empty($this->selectedValue)){
            $headline = $this->ll('catDataProvider_noCategorySelected');
        } else {
            if ($this->conf['showSubCategoryContent'] && count($this->catArray) > 1) {
                $headline = sprintf($this->ll('catDataProvider_ArticlesInCategoryAndSubcategories'), $catObj->get_title());
            } else {
                $headline = sprintf($this->ll('catDataProvider_ArticlesInCategory'), $catObj->get_title());
            }
        }
        return $headline;
    }
    
    
    
    /**
     * Helper function: Get language label
     *
     * @param   string  key
     * @return  string  label
     * @author	Fabrizio Branca <branca@punkt.de>
     * @since	2008-02-15
     */
    public function ll($key) {
    	return $this->pObj->pi_getLL($key);
    }
    
} // end class


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pt_gsacategories/res/hooks/class.tx_ptgsacategories_ptgsaarticlelist_catDataProvider.php']) {
    include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pt_gsacategories/res/hooks/class.tx_ptgsacategories_ptgsaarticlelist_catDataProvider.php']);
}
?>