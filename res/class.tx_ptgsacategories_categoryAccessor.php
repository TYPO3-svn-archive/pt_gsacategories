<?php
/***************************************************************
 *  Copyright notice
 *  
 *  (c) 2007 Fabrizio Branca (branca@punkt.de)
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
 * Inclusion of TYPO3 resources
 */
require_once t3lib_extMgm::extPath('pt_tools') . 'res/abstract/class.tx_pttools_iSingleton.php'; // interface for Singleton design pattern
require_once t3lib_extMgm::extPath('pt_tools') . 'res/staticlib/class.tx_pttools_debug.php'; // debugging class with trace() function
require_once t3lib_extMgm::extPath('pt_tools') . 'res/objects/class.tx_pttools_exception.php'; // general exception class
require_once t3lib_extMgm::extPath('pt_tools') . 'res/staticlib/class.tx_pttools_div.php'; // general helper library class
require_once t3lib_extMgm::extPath('pt_tools') . 'res/staticlib/class.tx_pttools_assert.php'; 



/**
 * Accessor methods for categories
 * 
 * $Id: class.tx_ptgsacategories_categoryAccessor.php,v 1.16 2008/09/11 09:53:44 ry44 Exp $
 * 
 * @author      Fabrizio Branca <branca@punkt.de>
 * @since       2007-11-19
 * @package     TYPO3
 * @subpackage  tx_ptgsacategories
 */
class tx_ptgsacategories_categoryAccessor implements tx_pttools_iSingleton {
    /**
     * Constants
     */
    const DB_TABLE_CAT = 'tx_ptgsacategories_cat';
    const DB_TABLE_CAT_CAT_REL = 'tx_ptgsacategories_cat_cat_rel';
    const DB_TABLE_CAT_ART_REL = 'tx_ptgsacategories_cat_art_rel';
    
    /**
     * @var tx_ptgsacategories_categoryAccessor       Singleton unique instance
     */
    private static $uniqueInstance = NULL;
    
    /**
     * @var t3lib_db	database object
     */
    protected $dbObj;
    
    /**
     * @var array		configuration array
     */
    protected $conf;
    
    /**
     * @var int
     */
    protected $storagePid;



    /***************************************************************************
     *   CONSTRUCTOR & OBJECT HANDLING METHODS
     **************************************************************************/
    
    /**
     * Returns a unique instance (Singleton) of the object. Use this method instead of the private/protected class constructor.
     *
     * @param   void
     * @return  tx_ptgsacategories_categoryAccessor      unique instance of the object (Singleton) 
     * @global     
     * @author  Fabrizio Branca <branca@punkt.de>
     * @since   2007-11-19
     */
    public static function getInstance() {

        if (self::$uniqueInstance === NULL) {
            $className = __CLASS__;
            self::$uniqueInstance = new $className();
        }
        return self::$uniqueInstance;
    }
    
    
    
    /**
     * Private constructor
     * 
     * @param 	void
     * @return 	void
     * @author	Fabrizio Branca <branca@punkt.de>
     * @since	2008-05-27
     */
    private function __construct() {
    	$this->dbObj = $GLOBALS['TYPO3_DB'];
    	$extConfArray = tx_pttools_div::returnExtConfArray('pt_gsacategories');
		$this->conf = tx_pttools_div::returnTyposcriptSetup($extConfArray['tsConfigurationPid'], 'plugin.tx_ptgsacategories_pi1.');
		tx_pttools_assert::isArray($this->conf,	array('message' => 'No configuration found!'));

		$this->storagePid = tx_pttools_div::getPid($this->conf['storagePid']);
    }



    /**
     * Disable cloning
     * 
     * @param   void
     * @return  void
     * @author  Fabrizio Branca <branca@punkt.de>
     * @since   2007-11-19
     */
    public final function __clone() {

        trigger_error('Clone is not allowed for ' . get_class($this) . ' (Singleton)', E_USER_ERROR);
    
    }



    /**
     * Select category by uid
     *
     * @param 	int		uid
     * @return 	mixed	data array or FALSE if no row
     * @author	Fabrizio Branca <branca@punkt.de>
     * @since 	2007-11-19
     */
    public function selectCategoryByUid($uid) {

        // query preparation
        $select = '*';
        $from = 'tx_ptgsacategories_cat';
        $where = 'uid = ' . intval($uid) . ' ' . tx_pttools_div::enableFields($from);
        $groupBy = '';
        $orderBy = '';
        $limit = '';
        
        // exec query using TYPO3 DB API
        $res = $this->dbObj->exec_SELECTquery($select, $from, $where, $groupBy, $orderBy, $limit);
        
        tx_pttools_assert::isMySQLRessource($res);
        
        $a_row = $this->dbObj->sql_fetch_assoc($res);
        
        $this->dbObj->sql_free_result($res);
        
        trace($a_row);
        return $a_row;
    }



    /**
     * Select all root categories (categories that have no parent categories)
     *
     * @param 	void
     * @param   string  field to order categories by (added by michael knoll)
     * @return 	array	array of record arrays
     * @author	Fabrizio Branca <branca@punkt.de>
     * @since	2007-12-17
     */
    public function selectRootCategories($orderBy = '') {

        // query preparation
        $select = '*';
        $from = 'tx_ptgsacategories_cat';
        $where = 'uid NOT in (SELECT childcat_uid from tx_ptgsacategories_cat_cat_rel WHERE true ' . tx_pttools_div::enableFields('tx_ptgsacategories_cat_cat_rel') . ')';
        $where .= ' ' . tx_pttools_div::enableFields($from);
        $groupBy = '';
        $orderBy = $orderBy == '' ? '' : $orderBy;
        $limit = '';
        
        // exec query using TYPO3 DB API
        $res = $this->dbObj->exec_SELECTquery($select, $from, $where, $groupBy, $orderBy, $limit);
        
        tx_pttools_assert::isMySQLRessource($res);
        
        $a_row = array();
        while ( ($item = $this->dbObj->sql_fetch_assoc($res)) == true ) {
            $a_row[] = $item;
        }
        $this->dbObj->sql_free_result($res);
        
        trace($a_row);
        return $a_row;
    }



    /**
     * Select parent categories
     * 
     * @param 	int		uid
     * @return	array	array of dataArrays
     * @throws	tx_pttools_exception	if query fails
     * @author 	Fabrizio Branca <branca@punkt.de>
     * @since	2008-01
     */
    public function selectParentCategories($uid) {

        // query preparation
        $select = 'cat.*';
        $from = 'tx_ptgsacategories_cat as cat, tx_ptgsacategories_cat_cat_rel as rel';
        $where = 'rel.childcat_uid = ' . intval($uid);
        $where .= ' AND rel.parentcat_uid = cat.uid';
        $where .= ' ' . tx_pttools_div::enableFields('tx_ptgsacategories_cat', 'cat');
        $where .= ' ' . tx_pttools_div::enableFields('tx_ptgsacategories_cat_cat_rel', 'rel');
        $groupBy = '';
        $orderBy = '';
        $limit = '';
        
        // exec query using TYPO3 DB API
        $res = $this->dbObj->exec_SELECTquery($select, $from, $where, $groupBy, $orderBy, $limit);
        
        tx_pttools_assert::isMySQLRessource($res);
        
        $a_row = array();
        while ( ($item = $this->dbObj->sql_fetch_assoc($res)) == true ) {
            $a_row[] = $item;
        }
        $this->dbObj->sql_free_result($res);
        
        trace($a_row);
        return $a_row;
    }



    /**
     * Method to innsert category Connecection in Database
     * 
     * @param   integer id of parent category
     * @param   integer id of child category
     * @return  integer id of inserted Record 
     * @author  Dorit Rottner <rottner@punkt.de>
     * @since   2007-12-11
     */
    public function insertInterCategoryConnection($parentUid, $childUid, $parentcatSort = 0, $childcatSort = 0) {

        trace('[METHOD] ' . __METHOD__);
        $row = array('uid' => 0, 'parentcat_uid' => $parentUid, 'childcat_uid' => $childUid, 'parentcat_sort' => $parentcatSort, 'childcat_sort' => $childcatSort);
        $row = tx_pttools_div::expandFieldValuesForQuery($row, true, 1);
        $uid = $this->insertRecord($row, self::DB_TABLE_CAT_CAT_REL);
        return $uid;
    }



    /**
     * Method to store Record in Database
     * 
     * @param   array   contains data to be stored in Database
     * @return  int		id of stored database Record
     * @author  Dorit Rottner <rottner@punkt.de>, Fabrizio Branca <branca@punkt.de>
     * @since   2007-12-11
     */
    public function storeCategory(array $dataArr) {

        trace('[METHOD] ' . __METHOD__);
        trace($dataArr, 0, '$dataArr');
        
        $row = array();
        $row['uid'] = intval($dataArr['uid']);
        $row['title'] = $this->dbObj->quoteStr($dataArr['title'], self::DB_TABLE_CAT);
        $row['description'] = $this->dbObj->quoteStr($dataArr['description'], self::DB_TABLE_CAT);
        $row['image'] = $this->dbObj->quoteStr($dataArr['image'], self::DB_TABLE_CAT);
        $row['parentcats'] = intval($dataArr['parentcats']);
        $row['childcats'] = intval($dataArr['childcats']);
        $row['articles'] = intval($dataArr['articles']);
        
        if (intval($dataArr['uid']) == 0) {
            $row = tx_pttools_div::expandFieldValuesForQuery($row, true, 1);
            $dataArr['uid'] = $this->insertRecord($row, self::DB_TABLE_CAT);
        } else {
            $row = tx_pttools_div::expandFieldValuesForQuery($row, self::DB_TABLE_CAT);
            $this->updateRecord($row);
        }
        return $dataArr['uid'];
    }



    /**
     * Method to insert Record in Database
     * 
     * @param	array    contains data to insert
     * @param 	string   name of Database Table 
     * @return 	integer  uid after Insert statement 
     * @author  Dorit Rottner <rottner@punkt.de>
     * @since   2007-12-11
     */
    public function insertRecord($row, $from) {

        trace('[METHOD] ' . __METHOD__);
        
        trace($row, 0, '$row');
        $res = $GLOBALS['TYPO3_DB']->exec_INSERTquery($from, $row);
        
        tx_pttools_assert::isMySQLRessource($res);
        
        trace($res);
        
        return $GLOBALS['TYPO3_DB']->sql_insert_id();
    }



    /**
     * Method to update Record in Database
     * 
     * @param   array   which contains the Data to be updated
     * @return  integer uid of updates Record 
     * @author  Dorit Rottner <rottner@punkt.de>
     * @since   2007-12-11
     */
    public function updateRecord($row) {

        trace('[METHOD] ' . __METHOD__);
        $where = 'uid =' . intval($row['uid']);
        $res = $GLOBALS['TYPO3_DB']->exec_UPDATEquery(self::DB_TABLE_CAT, $where, $row);
        
        tx_pttools_assert::isMySQLRessource($res);
        
    }



    /**
     * Select child categories
     *
     * @param   int     uid of the parent category
     * @param   string  DB field to order nodes by (added by Michael Knoll)
     * @return  array   array of rows
     * @author  Fabrizio Branca <branca@punkt.de>
     * @since   2007-12
     */
    public function selectChildCategories($uid, $nodesOrderBy = '') {

        // query preparation
        $select = 'tx_ptgsacategories_cat.*';
        $from = 'tx_ptgsacategories_cat, tx_ptgsacategories_cat_cat_rel';
        $where = 'tx_ptgsacategories_cat_cat_rel.parentcat_uid = ' . intval($uid);
        $where .= ' AND tx_ptgsacategories_cat_cat_rel.childcat_uid = tx_ptgsacategories_cat.uid';
        $where .= tx_pttools_div::enableFields('tx_ptgsacategories_cat');
        $where .= tx_pttools_div::enableFields('tx_ptgsacategories_cat_cat_rel');
        $groupBy = '';
        $orderBy = $nodesOrderBy == '' ? 'tx_ptgsacategories_cat_cat_rel.parentcat_sort' : $nodesOrderBy;
        $limit = '';
        
        // exec query using TYPO3 DB API
        $res = $this->dbObj->exec_SELECTquery($select, $from, $where, $groupBy, $orderBy, $limit);
        
        tx_pttools_assert::isMySQLRessource($res);
        
        while ( ($item = $this->dbObj->sql_fetch_assoc($res)) == true ) {
            $a_row[] = $item;
        }
        $this->dbObj->sql_free_result($res);
        
        return $a_row;
    }



    /**
     * Connect article to categories (and remove old entries)
     *
     * @param   int     gsa uid of the article
     * @param   array   array of category uids
     * @author  Fabrizio Branca <branca@punkt.de>
     * @since   2007-12
     */
    public function connectArticleToCategories($gsaUid, array $catArray) {

        // preprocess catArray
        $catList = array(); // cat = 5 and cat = 5_1 are the same categories!
        foreach ($catArray as $catUid) {
            list ($catUid) = explode('_', $catUid);
            if (!in_array($catUid, $catList)) {
                $catList[] = $catUid;
            }
        }
        
        $oldCatArray = $this->selectCategoriesConnectionsForArticle($gsaUid);
        
        // delete category connections that don't exist in catArray
        foreach ($oldCatArray as $oldCatUid) {
            if (!in_array($oldCatUid, $catList)) {
                $this->deleteCategoryConnectionForArticle($gsaUid, $oldCatUid, false);
            }
        }
        
        // insert category connections that don't exist in oldCatArray
        foreach ($catList as $catUid) {
            if (!in_array($catUid, $oldCatArray)) {
                $this->insertCategoryConnectionForArticle($gsaUid, $catUid);
            }
        }
    }



    /**
     * Select all category connections for a given article
     *
     * @param 	int		gsa uid of the article
     * @return 	array	array of uids of the categories the article is in
     * @author	Fabrizio Branca <branca@punkt.de>
     * @since	2008-02
     */
    public function selectCategoriesConnectionsForArticle($gsaUid, $orderBy = 'title') {

        // query preparation
        $select = 'catart.cat_uid';
        $from = 'tx_ptgsacategories_cat_art_rel as catart, tx_ptgsacategories_cat as cat';
        $where = 'catart.art_uid = ' . intval($gsaUid);
        $where .= ' AND catart.cat_uid = cat.uid';
        $where .= ' ' . tx_pttools_div::enableFields('tx_ptgsacategories_cat_art_rel', 'catart');
        $where .= ' ' . tx_pttools_div::enableFields('tx_ptgsacategories_cat', 'cat');
        $orderBy = !empty($orderBy) ? 'cat.'.$this->dbObj->quoteStr($orderBy, 'tx_ptgsacategories_cat') : '' ;
        $groupBy = '';
        $limit = '';
        
        // exec query using TYPO3 DB API
        $res = $this->dbObj->exec_SELECTquery($select, $from, $where, $groupBy, $orderBy, $limit);
        
        tx_pttools_assert::isMySQLRessource($res);
        
        $rows = array();
        while (($item = $this->dbObj->sql_fetch_assoc($res)) == true) {
            $rows[] = $item['cat_uid'];
        }
        $this->dbObj->sql_free_result($res);
        
        return $rows;
    }



    /**
     * Delete category connections for an article 
     *
     * @param 	int		gsa uid of the article
     * @param 	int		category uid 
     * @param 	bool	(optional) only mark as deleted, default = true
     * @author	Fabrizio Branca <branca@punkt.de>
     * @since	2008-02
     */
    public function deleteCategoryConnectionForArticle($gsaUid, $catUid, $onlymarkasdeleted = true) {

        $table = 'tx_ptgsacategories_cat_art_rel';
        $where = 'cat_uid=' . intval($catUid) . ' AND art_uid=' . intval($gsaUid);
        if ($onlymarkasdeleted) {
            $res = $this->dbObj->exec_UPDATEquery($table, $where, array('deleted' => 1));
        } else {
            $res = $this->dbObj->exec_DELETEquery($table, $where);
        }
        tx_pttools_assert::isMySQLRessource($res);
    }



    /**
     * Delete all category connections for an article
     *
     * @param 	int		gsa uid of the article
     * @param 	bool	(optional) only mark as deleted, default = true
     * @author	Fabrizio Branca <branca@punkt.de>
     * @since	2008-02
     */
    public function deleteAllCategoryConnectionsForArticle($gsaUid, $onlymarkasdeleted = true) {

        $catArray = $this->selectCategoriesConnectionsForArticle($gsaUid);
        foreach ($catArray as $catUid) {
            $this->deleteCategoryConnectionForArticle($gsaUid, $catUid, $onlymarkasdeleted);
        }
    }



    /**
     * Insert category connection
     *
     * @param 	int 	gsa uid of the article
     * @param 	int		category uid
     * @return 	int		uid of the new inserted cat_art_rel record
     * @author	Fabrizio Branca <branca@punkt.de>
     * @since	2008-02
     */
    public function insertCategoryConnectionForArticle($gsaUid, $catUid) {

        $insertFieldsArr = array();
        
        // query preparation
        $table = 'tx_ptgsacategories_cat_art_rel';
        $insertFieldsArr['pid'] = intval($this->storagePid);
        $insertFieldsArr['tstamp'] = time();
        $insertFieldsArr['crdate'] = time();
        $insertFieldsArr['cat_uid'] = intval($catUid);
        $insertFieldsArr['art_uid'] = intval($gsaUid);
        // $insertFieldsArr['cat_sort']        = 0;
        // $insertFieldsArr['art_sort']        = 0;
        

        // exec query using TYPO3 DB API
        $res = $this->dbObj->exec_INSERTquery($table, $insertFieldsArr);
        
        tx_pttools_assert::isMySQLRessource($res);
        
        $lastInsertedId = $this->dbObj->sql_insert_id();
        
        trace($lastInsertedId);
        return $lastInsertedId;
    }



    /**
     * Return all articles in a category
     *
     * @param 	array	array of category uids
     * @param 	string	(optional) limit statement
     * @param	string	(optional) orderby statement
     * @return 	array	array of art_uids
     * @author	Fabrizio Branca <branca@punkt.de>
     * @since	2008-02
     */
    public function selectArticlesInCategories(array $categories, $limit = '', $orderBy = '') {

        array_walk($categories, 'intval');
        
        // query preparation
        $select = 'art_uid';
        $from = 'tx_ptgsacategories_cat_art_rel';
        $where = 'cat_uid in (' . implode(',', $categories) . ')';
        $where .= ' ' . tx_pttools_div::enableFields($from);
        $groupBy = '';
        
        // exec query using TYPO3 DB API
        $res = $this->dbObj->exec_SELECTquery($select, $from, $where, $groupBy, $orderBy, $limit);
        
        tx_pttools_assert::isMySQLRessource($res);
        
        $rows = array();
        while ( ($item = $this->dbObj->sql_fetch_assoc($res)) == true ) {
            $rows[] = $item['art_uid'];
        }
        $this->dbObj->sql_free_result($res);
        
        return $rows;
    }



    /**
     * Return the total count of all articles in the categories
     *
     * @param 	array	array of category uids 
     * @return 	int		amount of articles
     * @author	Fabrizio Branca <branca@punkt.de>
     * @since	2008-02
     */
    public function selectArticlesInCategoriesCount(array $categories) {

        array_walk($categories, 'intval');
        
        // query preparation
        $select = 'count(*) as qty ';
        $from = 'tx_ptgsacategories_cat_art_rel';
        $where = 'cat_uid in (' . implode(',', $categories) . ')';
        $where .= ' ' . tx_pttools_div::enableFields($from);
        $groupBy = '';
        $orderBy = '';
        $limit = '';
        
        // exec query using TYPO3 DB API
        $res = $this->dbObj->exec_SELECTquery($select, $from, $where, $groupBy, $orderBy, $limit);
        
        tx_pttools_assert::isMySQLRessource($res);
        
        $item = $this->dbObj->sql_fetch_assoc($res);
        
        $this->dbObj->sql_free_result($res);
        
        return $item['qty'];
    }

} // end class


/*******************************************************************************
 *   TYPO3 XCLASS INCLUSION (for class extension/overriding)
 ******************************************************************************/
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pt_gsacategories/res/class.tx_ptgsacategories_categoryAccessor.php']) {
    include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pt_gsacategories/res/class.tx_ptgsacategories_categoryAccessor.php']);
}

?>