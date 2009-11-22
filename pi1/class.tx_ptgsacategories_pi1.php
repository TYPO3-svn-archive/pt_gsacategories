<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2007-2008 Fabrizio Branca <branca@punkt.de>
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

require_once (PATH_tslib . 'class.tslib_pibase.php');
require_once t3lib_extMgm::extPath('pt_gsacategories') . 'res/class.tx_ptgsacategories_category.php';
require_once t3lib_extMgm::extPath('pt_gsacategories') . 'res/class.tx_ptgsacategories_categoryCollection.php';
require_once t3lib_extMgm::extPath('pt_gsacategories') . 'res/class.tx_ptgsacategories_categoryAccessor.php';
require_once t3lib_extMgm::extPath('pt_tools') . 'res/staticlib/class.tx_pttools_div.php';
require_once t3lib_extMgm::extPath('pt_gsashop') . 'res/class.tx_ptgsashop_articleFactory.php';



/**
 * Plugin 'GSA Categories: Category Menu' for the 'pt_gsacategories' extension.
 *
 * @author	Fabrizio Branca <branca@punkt.de>
 * @package	TYPO3
 * @subpackage	tx_ptgsacategories
 */
class tx_ptgsacategories_pi1 extends tslib_pibase {

	/**
	 * @var string	prefix id
	 */
	public $prefixId = 'tx_ptgsacategories_pi1'; // Same as class name

	/**
	 * @var string	relative script path
	 */
	public $scriptRelPath = 'pi1/class.tx_ptgsacategories_pi1.php'; // Path to this script relative to the extension dir.

	/**
	 * @var string	extension key
	 */
	public $extKey = 'pt_gsacategories'; // The extension key.

	/**
	 * @var tslib_cObj current cObj
	 */
	public $cObj;

	/**
	 * @var int		selected value
	 */
	protected $selectedValue;
	
	/**
	 * @var array	Rootline of selected value
	 */
	protected $rootlineArray = null;



	/**
	 * Preprocess configuration parameters
	 *
	 * @param 	void
	 * @return 	void
	 * @author	Fabrizio Branca <branca@punkt.de>
	 * @since	2008-05-15
	 */
	protected function init() {
		
		$this->conf['templateFile'] = $this->cObj->stdWrap($this->conf['templateFile'], $this->conf['templateFile.']);
		$this->conf['rootNodes'] = $this->cObj->stdWrap($this->conf['rootNodes'], $this->conf['rootNodes.']);
		$this->conf['rootNodesOrderBy'] = $this->cObj->stdWrap($this->conf['rootNodesOrderBy'], $this->conf['rootNodesOrderBy.']);
		$this->conf['nodesOrderBy'] = $this->cObj->stdWrap($this->conf['nodesOrderBy'], $this->conf['nodesOrderBy.']);
		$this->conf['hideFirstLevel'] = $this->cObj->stdWrap($this->conf['hideFirstLevel'], $this->conf['hideFirstLevel.']);
		$this->conf['firstLevel'] = $this->cObj->stdWrap($this->conf['firstLevel'], $this->conf['firstLevel.']);
		$this->conf['lastLevel'] = $this->cObj->stdWrap($this->conf['lastLevel'], $this->conf['lastLevel.']);
		$this->conf['parameterName'] = $this->cObj->stdWrap($this->conf['parameterName'], $this->conf['parameterName.']);
		$this->conf['pluginMode'] = $this->cObj->stdWrap($this->conf['pluginMode'], $this->conf['pluginMode.']);
		$this->conf['linkParameterName'] = $this->cObj->stdWrap($this->conf['linkParameterName'], $this->conf['linkParameterName.']);
		$this->conf['linkArticleParameterName'] = $this->cObj->stdWrap($this->conf['linkArticleParameterName'], $this->conf['linkArticleParameterName.']);
		$this->conf['articleTitleTemplate'] = $this->cObj->stdWrap($this->conf['articleTitleTemplate'], $this->conf['articleTitleTemplate.']);
		#$this->conf['renderSubs'] = 0;
		#$this->conf['maxSubLevel'] = 1;
		#$this->conf['startSubsOn'] = 2;
		#$this->conf['renderOnlySelectedSubs'] = 1;
		$this->conf['renderSubs'] = $this->cObj->stdWrap($this->conf['renderSubs'], $this->conf['renderSubs.']);
		$this->conf['maxSubLevel'] = $this->cObj->stdWrap($this->conf['maxSubLevel'], $this->conf['maxSubLevel.']);
		$this->conf['startSubsOn'] = $this->cObj->stdWrap($this->conf['startSubsOn'], $this->conf['startSubsOn.']);
		$this->conf['renderOnlySelectedSubs'] = $this->cObj->stdWrap($this->conf['renderOnlySelectedSubs'], $this->conf['renderOnlySelectedSubs.']);

		if (empty($this->conf['linkParameterName'])) {
			$this->conf['linkParameterName'] = array_shift(t3lib_div::trimExplode('//', $this->conf['parameterName']));
		}

		if (empty($this->conf['linkArticleParameterName'])) {
			$this->conf['linkArticleParameterName'] = array_shift(t3lib_div::trimExplode('//', $this->conf['parameterName']));
		}

	}



	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	public function main($content, $conf) {

		$this->conf = $conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->pi_USER_INT_obj = 1; // Configuring so caching is not expected. This value means that no cHash params are ever set. We do this, because it's a USER_INT object!

		tx_pttools_div::mergeConfAndFlexform($this, true);

		// parameterName fallback (e.g. "cat_uid // filter")
		
		
		foreach (t3lib_div::trimExplode('//', $this->conf['parameterName']) as $parameterName) {
			$this->selectedValue = $this->cObj->getData('GPvar:'. $parameterName, array());
			if (!empty($this->selectedValue)) {
				break;
			}
		}

		#$this->selectedValue = t3lib_div::_GET($this->extKey);

		// pre-process configuration values
		$this->init();

		$menuArray = array();
		$categoryClassName = t3lib_div::makeInstanceClassName('tx_ptgsacategories_category');

		switch ($this->conf['pluginMode']) {

			/**
			 * Simple breadcrumb menu
			 */
			case 'breadcrumb' : {
				$activeCat = new $categoryClassName($this->selectedValue); /* @var activeCat tx_ptgsacategories_category */
				$menuArray = $activeCat->getRootlineAsArray(0, $this->conf['firstLevel'] + ($this->conf['hideFirstLevel'] ? 1 : 0));
			} break;

			/**
			 * Bread crumb menu with current article as last element
			 */
			case 'breadcrumbarticle' : {
				$catArray = tx_ptgsacategories_categoryAccessor::getInstance()->selectCategoriesConnectionsForArticle($this->selectedValue);
				$activeCat = new $categoryClassName($catArray[0]); /* @var activeCat tx_ptgsacategories_category */
				$menuArray = $activeCat->getRootlineAsArray(0, $this->conf['firstLevel'] + ($this->conf['hideFirstLevel'] ? 1 : 0));
			} break;

			/**
			 * Tree menu of category hierarchy
			 */
			default: {
				$this->conf['pluginMode'] = 'default';
				$catCollection = t3lib_div::makeInstance('tx_ptgsacategories_categoryCollection');
				if (empty($this->conf['rootNodes'])) {
					$catCollection->getRoodnodes($this->conf['rootNodesOrderBy']);
				} else {
					foreach (t3lib_div::trimExplode(',', $this->conf['rootNodes']) as $catUid) {
						$catCollection->addItem(new $categoryClassName($catUid));
					}
				}
				foreach ($catCollection as $cat) { /* @var cat tx_ptgsacategories_category */
					$menuArray[] = $cat->getCategoryTreeAsNestedArray(NULL, 0, array(), $this->conf['nodesOrderBy']);
				}
			}
		}

		// do not link last item
		if (($this->conf['pluginMode'] == 'breadcrumb') && $this->conf['doNotLinkLastItem']) {
			$lastItemKey = end(array_keys($menuArray));
			$menuArray[$lastItemKey]['doNotLink'] = true;
		}

		// create last item for the selected article
		if ($this->conf['pluginMode'] == 'breadcrumbarticle') {
			// append link to article single view
			$articleObj = tx_ptgsashop_articleFactory::createArticle($this->selectedValue);

			$rplArray = array (
				'###ARTNO###' => $articleObj->get_artNo(),
				'###ID###' => $articleObj->get_id(),
				'###MATCH1###' => $articleObj->get_match1(),
				'###MATCH2###' => $articleObj->get_match2(),
				'###ALTTEXT###' => $articleObj->get_altText(),
				'###DESCRIPTION###' => $articleObj->get_description(),
			);

			$title = str_replace(array_keys($rplArray), $rplArray, $this->conf['articleTitleTemplate']);
			$title = $this->cObj->stdWrap($title, $this->conf['titleArticle_stdWrap.']);

			$menuArray[] = array( 'title' => $title, 'type' => 'article' );
		}

		// render the menu/breadcrumb
		$content = '';
		if (($this->conf['pluginMode'] == 'breadcrumb') || ($this->conf['pluginMode'] == 'breadcrumbarticle')) {
			$content .= $this->renderBreadCrumb($menuArray);
			$content = $GLOBALS['TSFE']->cObj->wrap($content, $this->conf['wrapBreadCrumbAll']);
		} else {
			$count = count($menuArray);
			foreach ($menuArray as $key => $item) {
				$content .= $this->renderMenu($item, $key, $count);
			}
			$wrapLevel = str_replace('###LEVEL###', 'root', $this->conf['wrapLevel']);
			$content = $GLOBALS['TSFE']->cObj->wrap($content, $wrapLevel);
		}
		
		$content = $GLOBALS['TSFE']->cObj->wrap($content, $this->conf['wrapAll']);

		return $this->conf['doNotWrapInBaseClass'] == 1 ? $content : $this->pi_wrapInBaseClass($content);
	}



	/**
	 * Render menu recursively
	 *
	 * @param 	array 	root item to start with
	 * @param 	int		(optional) position of the item in its level (needed for optionSplit)
	 * @param 	int		(optional) total count of items on this level (needed for optionSplit)
	 * @return 	string	HTML output
	 * @author	Fabrizio Branca <branca@punkt.de>
	 * @since	2008-05-27
	 */
	#protected function renderMenu(array $item, $pos = 0, $count = 0) { (alt)
	protected function renderMenu(array $item, $pos = 0, $count = 0, $renderSub = 0) {

		/* Build up rootline */
		if (is_null($this->rootlineArray)) {
			$menuArray = array();
			$categoryClassName = t3lib_div::makeInstanceClassName('tx_ptgsacategories_category');
			$activeCat = new $categoryClassName($this->selectedValue); /* @var activeCat tx_ptgsacategories_category */
			$menuArray = $activeCat->getRootlineAsArray(0, $this->conf['firstLevel'] + ($this->conf['hideFirstLevel'] ? 1 : 0));
			$this->rootlineArray = array();
			foreach ($menuArray as $menuEntry) {
				$this->rootlineArray[] = $menuEntry['uid'];
			}
		}
		
		$output = '';
		
		// check if item level is lower than lastLevel, if not do nothing
		if ((empty($this->conf['lastLevel']) || $item['level'] <= $this->conf['lastLevel'])) {

			// check if item level is higher then firstLevel, if not go on with subitems...
			if ($item['level'] >= $this->conf['firstLevel'] + ($this->conf['hideFirstLevel'] ? 1 : 0)) {

				// generate typolink
				$item = $this->makeLink($item, $pos, $count);

			}

			// fetch the content of the subtree (if any subcategories exist) recursively
			$subLevelContent = '';
		
			if (($count = count($item['subcategories'])) > 0) {
				foreach ($item['subcategories'] as $key => $subitem) {
					
					if ($this->selectedValue == $item['uid'] || $renderSub || in_array($item['uid'], $this->rootlineArray) ) {
						$subLevelContent .= $this->renderMenu($subitem, $key, $count, 1);	// mark sub items as chosen subs
					} else {
						$subLevelContent .= $this->renderMenu($subitem, $key, $count, 0);	// mark sub items as non-chosen subs
					}
					
				}
				if (!empty($subLevelContent)) {
					$wrapLevel = str_replace('###LEVEL###', $item['level'], $this->conf['wrapLevel']);
					$subLevelContent = $GLOBALS['TSFE']->cObj->wrap($subLevelContent, $wrapLevel);
				}
			}

			$level = $item['level'];
			$renderSubs = $this->conf['renderSubs'];
			$maxSubLevel = $this->conf['maxSubLevel'];
			$startSubsOn = $this->conf['startSubsOn'];
			$renderOnlySelectedSubs = $this->conf['renderOnlySelectedSubs'];
			
			/**
			 * Dieser Schritt darf nur geschehen, wenn
			 * 
			 * 0. Der Level des letzten items kleiner ist, als startSub ODER
			 * 1. Man sich in einem gewählten sub befindet und der Level des letzten items kleiner ist, als startSub + maxSubLevel, ODER
			 * 2. renderSubs global auf 1 steht, ODER
			 * 3. item ist in rootline
			 */
			if ( $level < $startSubsOn ||																			//	See 0.
				 $renderOnlySelectedSubs == 1 && $renderSub == 1 && ( $level < $startSubsOn + $maxSubLevel ) ||		//  See 1.
				 $renderSubs == 1 ||  																				//  See 2.
				 in_array($item['uid'], $this->rootlineArray) 
				) {
				
				$output .= $item['optionSplittedItem'] . $subLevelContent;
	
				$wrapItemAndSub = str_replace('###LEVEL###', $item['level'], $this->conf['wrapItemAndSub']);
				$output = $GLOBALS['TSFE']->cObj->wrap($output, $wrapItemAndSub);
				
			}

		}

		return $output;
	}



	/**
	 * Renders the breadcrumb array
	 *
	 * @param 	array	menuArray
	 * @return 	string	HTML Output
	 * @author	Fabrizio Branca <branca@punkt.de>
	 * @since	2008-05-28
	 */
	protected function renderBreadCrumb(array $menuArray) {
		$output = '';
		$count = count($menuArray);
		foreach ($menuArray as $key => $item) {
			if ($item['type'] == 'article') {
				$item = $this->makeArticleLink($item, $key, $count);
			} else {
				$item = $this->makeLink($item, $key, $count);
			}
			$output .= $item['optionSplittedItem'];
		}
		return $output;
	}



	/**
	 * Render article menu item
	 *
	 * @param 	array 	article item to render
	 * @param 	int		(optional) position of the item in its level (needed for optionSplit)
	 * @param 	int		(optional) total count of items on this level (needed for optionSplit)
	 * @return 	array	item, rendered content is in $item['optionSplittedItem']
	 * @author	Fabrizio Branca <branca@punkt.de>
	 * @since	2008-05-28
	 */
	protected function makeArticleLink(array $item, $pos = 0, $count = 0) {

		$typolinkConf = $this->conf['typolinkArticle.'];
		$typolinkConf['additionalParams'] = $this->cObj->stdWrap($this->conf['additionalParams'], $this->conf['additionalParams.']);
		$typolinkConf['additionalParams'] .= '&'.$this->convertPipedGpvarString($this->conf['linkArticleParameterName']).'=' . $this->selectedValue; // TODO: hash values!
		$item['title'] = $this->cObj->stdWrap($item['title'], $this->conf['title_stdWrap.']);
		$item['link'] = $this->cObj->typoLink($item['title'], $typolinkConf);

		$wrapBreadCrumbItem = str_replace('###LEVEL###', $item['level'], $this->conf['wrapBreadCrumbItem']);
		$wrapBreadCrumbItem_optionSplitted = $GLOBALS['TSFE']->tmpl->splitConfArray(array('wrapBreadCrumbItem' => $wrapBreadCrumbItem), $count);
		if (!$this->conf['doNotLinkLastItem']) {
			$item['optionSplittedItem'] = $GLOBALS['TSFE']->cObj->wrap($item['link'], $wrapBreadCrumbItem_optionSplitted[$pos]['wrapBreadCrumbItem']);
		} else {
			$item['optionSplittedItem'] = $GLOBALS['TSFE']->cObj->wrap($item['title'], $wrapBreadCrumbItem_optionSplitted[$pos]['wrapBreadCrumbItem']);
		}
		return $item;

	}

	

	/**
	 * Render single menu item
	 *
	 * @param 	array 	item to render
	 * @param 	int		(optional) position of the item in its level (needed for optionSplit)
	 * @param 	int		(optional) total count of items on this level (needed for optionSplit)
	 * @return 	array	item, rendered content is in $item['optionSplittedItem']
	 * @author	Fabrizio Branca <branca@punkt.de>
	 * @since	2008-05-27
	 */
	protected function makeLink(array $item, $pos = 0, $count = 0) {

		$typolinkConf = $this->conf['typolink.'];
		$typolinkConf['additionalParams'] = $this->cObj->stdWrap($this->conf['additionalParams'], $this->conf['additionalParams.']);
		$typolinkConf['additionalParams'] .= '&'.$this->convertPipedGpvarString($this->conf['linkParameterName']).'=' . $item['uid'];

		// adding parameters for nice urls
		if ($this->conf['addParametersForPath']) {
			$categoryClassName = t3lib_div::makeInstanceClassName('tx_ptgsacategories_category');
			$tmpCat = new $categoryClassName($item['uid']); /* @var $tmpCat tx_ptgsacategories_category */
			$counter = -1;
			foreach (array_reverse($tmpCat->getRootlineAsArray(0, $this->conf['firstLevel'] + ($this->conf['hideFirstLevel'] ? 1 : 0) )) as $step) {
				if ($step['uid'] != $item['uid']) {
					$typolinkConf['additionalParams'] .= '&'.$this->convertPipedGpvarString($this->conf['linkParameterName'].$counter).'=' . $step['uid'];
					$counter--;
				}
			}
		}

		$item['title'] = $this->cObj->stdWrap($item['title'], $this->conf['title_stdWrap.']);

		if (!$item['doNotLink']) {
			$item['link'] = $this->cObj->typoLink($item['title'], $typolinkConf);
		} else {
			$item['link'] = $item['title'];
		}

		$item['active'] = ($item['uid'] == $this->selectedValue);
		if (($this->conf['pluginMode'] == 'breadcrumb') || ($this->conf['pluginMode'] == 'breadcrumbarticle')) {
			$wrapBreadCrumbItem = str_replace('###LEVEL###', $item['level'], $this->conf['wrapBreadCrumbItem']);
			$wrapBreadCrumbItem_optionSplitted = $GLOBALS['TSFE']->tmpl->splitConfArray(array('wrapBreadCrumbItem' => $wrapBreadCrumbItem), $count);
			$item['optionSplittedItem'] = $GLOBALS['TSFE']->cObj->wrap($item['link'], $wrapBreadCrumbItem_optionSplitted[$pos]['wrapBreadCrumbItem']);
		} elseif ($item['active']) {
			$wrapActiveItem = str_replace('###LEVEL###', $item['level'], $this->conf['wrapActiveItem']);
			$wrapActiveItem_optionSplitted = $GLOBALS['TSFE']->tmpl->splitConfArray(array('wrapActiveItem' => $wrapActiveItem), $count);
			$item['optionSplittedItem'] = $GLOBALS['TSFE']->cObj->wrap($item['link'], $wrapActiveItem_optionSplitted[$pos]['wrapActiveItem']);
		} else {
			$wrapItem = str_replace('###LEVEL###', $item['level'], $this->conf['wrapItem']);
			$wrapItem_optionSplitted = $GLOBALS['TSFE']->tmpl->splitConfArray(array('wrapItem' => $wrapItem), $count);
			$item['optionSplittedItem'] = $GLOBALS['TSFE']->cObj->wrap($item['link'], $wrapItem_optionSplitted[$pos]['wrapItem']);
		}

		return $item;
	}



	/**
	 * Convert piped GPvar to "additionaParams" string
	 * TODO: move to pt_tools?
	 *
	 * @param 	string	GPvar string (e.g. tx_ptgsacategories|cat_uid)
	 * @return 	string	parameter name (e.g. tx_ptgsacategories[cat_uid])
	 * @author	Fabrizio Branca <branca@punkt.de>
	 * @since	2008-04-28
	 */
	public static function convertPipedGpvarString($gpvarstring) {
		$returnString = '';
		foreach (t3lib_div::trimExplode('|', $gpvarstring) as $part) {
			if ($returnString == '') {
				$returnString .= $part;
			} else {
				$returnString .= '['. $part . ']';
			}
		}
		return $returnString;
	}

} // class end




if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pt_gsacategories/pi1/class.tx_ptgsacategories_pi1.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pt_gsacategories/pi1/class.tx_ptgsacategories_pi1.php']);
}

?>