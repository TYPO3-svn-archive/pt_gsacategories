<?php
/***************************************************************
 *  Copyright notice
 *  
 *  (c) 2007-2008 Fabrizio Branca (branca@punkt.de)
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

/** 
 * Category class
 *
 * $Id: class.tx_ptgsacategories_category.php,v 1.11 2008/05/28 12:26:18 ry44 Exp $
 *
 * @author  Fabrizio Branca <branca@punkt.de>
 * @since   2007-11-19
 */

require_once t3lib_extMgm::extPath('pt_tools') . 'res/staticlib/class.tx_pttools_debug.php'; // debugging class with trace() function
require_once t3lib_extMgm::extPath('pt_tools') . 'res/objects/class.tx_pttools_exception.php'; // general exception class
require_once t3lib_extMgm::extPath('pt_tools') . 'res/staticlib/class.tx_pttools_div.php'; // general helper library class

require_once t3lib_extMgm::extPath('pt_gsacategories') . 'res/class.tx_ptgsacategories_categoryAccessor.php';



/**
 * Category class for pt_gsashop articles
 *
 * @author      Fabrizio Branca <branca@punkt.de>
 * @since       2007-11-19
 * @package     TYPO3
 * @subpackage  tx_ptgsacategories
 */
class tx_ptgsacategories_category {
    
    /**
     * @var int	uid of the category
     */
    protected $uid;
    /**
     * @var string	title of the category
     */
    protected $title = '';
    /**
     * @var string	description of the category
     */
    protected $description = '';
    /**
     * @var string	path to the image of the category
     */
    protected $image = '';
    /**
     * @var tx_ptgsacategories_categoryCollection	collection of parent categories
     */
    protected $parentcats = NULL;
    /**
     * @var tx_ptgsacategories_categoryCollection	collection of child categories
     */
    protected $childcats = NULL;
    /**
     * @var tx_ptgsashop_articleCollection	collection of articles in this category
     */
    protected $articles = NULL;



    /**
     * Constructor
     *
     * @param 	int		(optional) uid
     * @param 	array	(optional) data array
     * @author	Fabrizio Branca <branca@punkt.de>
     * @since	2008-02
     */
    public function __construct($uid = '', $dataArr = array()) {

        if (!empty($uid)) {
            $this->loadSelf($uid);
        } elseif (!empty($dataArr)) {
            $this->setDataArray($dataArr);
        }
    }



    /**
     * Returns the property value
     *
     * @param 	void
     * @return 	int		uid
     * @author	Fabrizio Branca <branca@punkt.de>
     * @since	2008-02
     */
    public function get_uid() {

        return $this->uid;
    }



    /**
     * Sets the property value
     *
     * @param  	int		uid
     * @return 	void
     * @author	Fabrizio Branca <branca@punkt.de>
     * @since	2008-02
     */
    public function set_uid($uid) {

        $this->uid = $uid;
    }



    /**
     * Returns the property value
     *
     * @param 	void
     * @return 	string	title
     * @author	Fabrizio Branca <branca@punkt.de>
     * @since	2008-02
     */
    public function get_title() {

        return $this->title;
    }



    /**
     * Sets the property value
     *
     * @param  	string	title
     * @return 	void
     * @author	Fabrizio Branca <branca@punkt.de>
     * @since	2008-02
     */
    public function set_title($title) {

        $this->title = $title;
    }



    /**
     * Returns the property value
     *
     * @param 	void
     * @return 	string	description
     * @author	Fabrizio Branca <branca@punkt.de>
     * @since	2008-02
     */
    public function get_description() {

        return $this->description;
    }



    /**
     * Sets the property value
     *
     * @param  	string	description
     * @return 	void
     * @author	Fabrizio Branca <branca@punkt.de>
     * @since	2008-02
     */
    public function set_description($description) {

        $this->description = $description;
    }



    /**
     * Returns the property value
     *
     * @param 	void
     * @return 	string	image
     * @author	Fabrizio Branca <branca@punkt.de>
     * @since	2008-02
     */
    public function get_image() {

        return $this->image;
    }



    /**
     * Sets the property value
     *
     * @param  	string	image
     * @return 	void
     * @author	Fabrizio Branca <branca@punkt.de>
     * @since	2008-02
     */
    public function set_image($image) {

        $this->image = $image;
    }



    /**
     * Get parent categories
     *
     * @param 	void
     * @return 	tx_ptgsacategories_categoryCollection
     * @author	Fabrizio Branca <branca@punkt.de>
     * @since	2008-02
     */
    public function get_parentcats() {

        if (empty($this->parentcats)) {
            $dataArr = tx_ptgsacategories_categoryAccessor::getInstance()->selectParentCategories($this->uid);
            $this->parentcats = t3lib_div::makeInstance('tx_ptgsacategories_categoryCollection');
            $categoryClassName = get_class($this);
            if (is_array($dataArr)) {
	            foreach ($dataArr as $data) {
	                $this->parentcats->addItem(new $categoryClassName('', $data));
	            }
            }
        }
        return $this->parentcats;
    }



    /**
     * Get child categories
     *
     * @param 	string   DB Field to order categories by (added by Michael Knoll)
     * @return 	tx_ptgsacategories_categoryCollection
     * @author	Fabrizio Branca <branca@punkt.de>
     * @since	2008-02
     */
    public function get_childcats($nodesOrderBy = '') {

        if (empty($this->parentcats)) {
            $dataArr = tx_ptgsacategories_categoryAccessor::getInstance()->selectChildCategories($this->uid, $nodesOrderBy);
            $this->childcats = t3lib_div::makeInstance('tx_ptgsacategories_categoryCollection');
            $categoryClassName = get_class($this);
            if (is_array($dataArr)) {
                foreach ($dataArr as $data) {
                    $this->childcats->addItem(new $categoryClassName('', $data));
                }
            }
        }
        return $this->childcats;
    }



    /**
     * Returns a list of rootlines
     *
     * @return 	array	array of rootlines (comma separeted lists of category uids from this node to the top)
     * @throws 	tx_pttools_exception if a loop is found in the category graph
     * @author	Fabrizio Branca <branca@punkt.de>
     */
    public function getRootlines() {

        $rootlines = array();
        /* @var $cat tx_ptgsacategories_category */
        foreach ($this->get_parentcats() as $cat) {
            foreach ($cat->getRootlines() as $rl) {
                if (!in_array($this->get_uid(), explode(',', $rl))) {
                    $rootlines[] = $rl . ',' . $this->get_uid();
                    // array_unshift($rootlines, $rl.','.$this->get_uid());
                } else {
                    throw new tx_pttools_exception('Loop in category graph!');
                }
            }
        }
        return (!empty($rootlines)) ? $rootlines : array($this->get_uid());
    }
    
    
    
    /**
	 * Get rootline as array
	 * 
	 * @param 	int		id of the rootline (if categories are a graph, there may be more rootlines)
	 * @param 	int		startlevel
	 * @return 	array	array of uid/level/title records
	 * @author	Fabrizio Branca <branca@punkt.de>
	 * @since	2008-03-19
	 */
    public function getRootlineAsArray($i = 0, $startLevel = 0) {
    	$rootLines = $this->getRootlines();
    	$breadcrumb = array();
    	$level = 0;
    	$categoryClassName = get_class($this);
    	foreach (t3lib_div::trimExplode(',', $rootLines[$i]) as $cat_uid) {
    		if ($level >= $startLevel) {
	    		$tmpCat = new $categoryClassName($cat_uid); /* @var tmpCat tx_ptgsacategories_category */
	    		$breadcrumb[] = array('uid' => $tmpCat->uid, 'level' => $level, 'title' => $tmpCat->title);
    		}
    		$level++;
    	}
    	return $breadcrumb;
    }



    /**
     * Set data array to properties
     *
     * @param 	array data array
     * @return	void
     * @author	Fabrizio Branca <branca@punkt.de>
     * @since	2008-02
     */
    public function setDataArray(array $dataArray) {

        foreach ($dataArray as $propertyname => $pvalue) {
            $setter = 'set_' . $propertyname;
            if (method_exists($this, $setter)) {
                $this->$setter($pvalue);
            }
        }
    }



    /**
     * Get data array from properties
     *
     * @param 	void
     * @return 	array	data array
     * @author	Fabrizio Branca <branca@punkt.de>
     * @since	2008-02
     */
    protected function getDataArray() {

        $dataArray = array();
        // TODO: liefert das auch die properties von erbenden Klassen? (ry44)
        foreach (array_keys(get_class_vars(__CLASS__)) as $propertyname) {
            $getter = 'get_' . $propertyname;
            if (method_exists($this, $getter)) {
                $dataArray[$propertyname] = $this->$getter();
            }
        }
        return $dataArray;
    }



    /**
     * Load values from database
     *
     * @param	int		uid
     * @return 	void
     * @author	Fabrizio Branca <branca@punkt.de>
     * @since	2008-02
     */
    public function loadSelf($uid) {

        $dataArr = tx_ptgsacategories_categoryAccessor::getInstance()->selectCategoryByUid($uid);
        if ($dataArr) {
            $this->setDataArray($dataArr);
        }
    }



    /**
     * Stores itself to the database
     *
     * @param 	bool	(optional) update related categories, default = false
     * @return 	int		uit of the inserted/updated record
     * @author	Fabrizio Branca <branca@punkt.de>
     * @since	2008-02
     */
    public function storeSelf($updateRelatedCats = false) {

        $catAccessor = tx_ptgsacategories_categoryAccessor::getInstance();
        $catId = $catAccessor->storeCategory($this->getDataArray());
        $this->set_uid($catId);
        
        // TODO: test for infinite loops!
        if ($updateRelatedCats) {
            foreach ($this->get_parentcats() as $parentCat) { /* @var $parentCat tx_ptgsacategories_category */
                $parentCatId = $parentCat->storeSelf();
                $catAccessor->insertInterCategoryConnection($parentCatId, $this->uid);
            }
            
            foreach ($this->get_childcats() as $childCat) { /* @var $childCat tx_ptgsacategories_category */
                $childCatid = $childCat->storeSelf();
                $catAccessor->insertInterCategoryConnection($this->uid, $childCatid);
            }
        }
        return $this->uid;
    }



    /**
     * Get complete category tree as plain array
     *
     * @param 	tx_ptgsacategories_category 	(optional) start categorie, default=NULL means that the category itself is the starting point
     * @param 	int								(optional) level, used for recursion
     * @return 	array							array of uid/level/title records
     * @author	Fabrizio Branca <branca@punkt.de>
     * @since	2008-02
     * @deprecated 
     */
    public function getCategoryTree(tx_ptgsacategories_category $cat = NULL, $level = 0) {

        if ($cat == NULL) {
            $cat = $this;
        }
        $tree[] = array('uid' => $cat->uid, 'level' => $level, 'title' => $cat->title);
        
        if ($level < 10) { // TODO: richtig auf zyklen überprüfen!        
            foreach ($cat->get_childcats() as $cat2) { /* @var $cat2 tx_ptgsacategories_category */
                $tree2 = $this->getCategoryTree($cat2, $level + 1);
                $tree = array_merge($tree, $tree2);
            }
        }
        
        return $tree;
    }
    
    
    
    /**
     * Get complete category tree as nested array (using depth-first search with cycle checking)
     *
     * @param	int						(optional) stop level, default is NULL (runs up to the leaves)
     * @param 	int						(optional) current level, only for internal purpose
     * @param 	array					(optional) list of visited category uids, only for internal purpose (needed for cycle checking!) 
     * @return 	array					nested array of uid/level/title/subcategories records (value of "subcategories" is an array of uid/level/title/subcategories records)
     * @throws	tx_pttools_exception			when finding a cycle in category tree
     * @author	Fabrizio Branca <branca@punkt.de>
     * @since	2008-04-28
     */
    public function getCategoryTreeAsNestedArray($stopLevel = NULL, $currentLevel = 0, $visitedCategories = array(), $nodesOrderBy = '') {

        // check for cycles
        if (!in_array($this->uid, $visitedCategories)) {
        	$visitedCategories[] = $this->uid;
        } else {
        	throw new tx_pttools_exception('Cycle found in category graph with category uid "'.$this->uid.'"!');
        }
        
        $nestedArray = array('uid' => $this->uid, 'title' => $this->title, 'level' => $currentLevel);
        
        if (is_null($stopLevel) || $stopLevel > $currentLevel) {
        	$nestedArray['subcategories'] = array();
	        foreach ($this->get_childcats($nodesOrderBy) as $cat2) { /* @var $cat2 tx_ptgsacategories_category */
	       		$nestedArray['subcategories'][] = $cat2->getCategoryTreeAsNestedArray($stopLevel, $currentLevel + 1, $visitedCategories, $nodesOrderBy);
	        }
        }
        
        return $nestedArray;
    }
    
    
    
} // end class


/*******************************************************************************
 *   TYPO3 XCLASS INCLUSION (for class extension/overriding)
 ******************************************************************************/
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pt_gsacategories/res/class.tx_ptgsacategories_category.php']['XCLASS']['ext/pt_gsacategories/res/class.tx_ptgsacategories_category.php']) {
    include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pt_gsacategories/res/class.tx_ptgsacategories_category.php']['XCLASS']['ext/pt_gsacategories/res/class.tx_ptgsacategories_category.php']);
    
}

?>