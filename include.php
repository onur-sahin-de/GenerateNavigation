<?php

	/**
	 * This file is part of an ADDON for use with WebsiteBaker CMS & LEPTON CMS Core.
	 * This ADDON is released under the GNU GPL.
	 * Additional license terms can be seen in the info.php of this module.
	 *
	 * @module          generate_navigation
	 * @author          Onur Sahin, www.onur-sahin.de
	 * @copyright       2015 Onur Sahin, www.onur-sahin.de
	 * @link            www.onur-sahin.de/downloads/generate-navigation
	 * @license         http://www.gnu.org/licenses/gpl.html
	 * @license_terms   please see info.php of this module
	 *
	 */
	 
	/**
	 * This file is the main core of the module GenerateNavigation.
	 */
	 

	class GenerateNavigation {
		
		const PAGE_TITLE = "page_title";
		const MENU_TITLE = "menu_title";
		const PAGE_ID = "page_id";
		const PAGE_PARENT = "parent";
		const PAGE_TARGET = "target";
		const PAGE_LINK = "link";
		const PAGE_LEVEL = "level";
		const PAGE_MENU = "menu";
		const PAGE_POSITION = "position";
		const PAGE_VISIBILITY = "visibility";
		const PAGE_TRAIL = "page_trail";
		const COUNT_CHILDREN = "count_children";
		
		private $levelID = 0;
		private $parentID = 0;
		private $menuID = 1;
		private $navigationCode = "";
		private $naviTitleOption = self::PAGE_TITLE;
		private $furtherNavigationOption = array();
		private $formatCode = "[li][a][at][/a]";
		private $currentClassName = "current";
		private $firstLevelIDName = "";
		
		protected $oDb  = null;
		protected $oApp = null;
		
		public function __construct(wb $oApplication, database $database) {
			$this->oApp = $oApplication;
			$this->oDb  = $database;
		}
		
		public function setMenuID($getMenuID) {
			$this->menuID = $getMenuID;
		}
		
		public function setLevelID($getLevelID) {
			$this->levelID = $getLevelID;
		}
		
		public function setCurrentClassName($getCurrentClassName) {
			$this->currentClassName = $getCurrentClassName;	
		}
		
		public function setNaviTitleOption($getNaviTitleOption) {
			$this->naviTitleOption = $getNaviTitleOption;
		}
		
		public function setFurtherNavigationOption($getFurtherNavigationOption) {
			$this->furtherNavigationOption = $getFurtherNavigationOption;
		}
		
		public function setFormatCode($getFormatCode) {
			$this->formatCode = $getFormatCode;
		}
		
		public function setFirstLevelIDName($getFirstLevelIDName) {
			$this->firstLevelIDName = $getFirstLevelIDName;
		}
		
		public function getVisibilityOfPageID($getPageID) {
			$sql = 'SELECT * FROM '.TABLE_PREFIX.'pages WHERE '.self::PAGE_ID.' = '.$getPageID.'';
			$result = $this->oDb->query($sql)->fetchRow();
			return $result[self::PAGE_VISIBILITY];
		}
		
		private function hasChildren($getPageID) {
			$sql = 'SELECT count('.self::PAGE_ID.') AS '.self::COUNT_CHILDREN.' FROM '.TABLE_PREFIX.'pages where '.self::PAGE_PARENT.' = '.$getPageID.' AND '.self::PAGE_VISIBILITY.' = "public" ';
			$result = $this->oDb->query($sql)->fetchRow();
			return $result[self::COUNT_CHILDREN] > 0;
		}
		
		private function generateNavigationCode() {
			
			$liClass = "";
			$aClass = "";
			$furtherNavigationOptionContent = "";

			$sql = 'SELECT * FROM '.TABLE_PREFIX.'pages WHERE '.self::PAGE_LEVEL.' = '.$this->levelID.' AND '.self::PAGE_VISIBILITY.' = "public" AND '.self::PAGE_MENU.' = '.$this->menuID.' ORDER BY '.self::PAGE_POSITION.' ASC';
			$result = $this->oDb->query($sql);
			
			if($this->levelID == 0) {
				if($this->firstLevelIDName == "") $this->firstLevelIDName = 'menu-id-'.$this->menuID.'';
				$this->navigationCode .= '<ul id="'.$this->firstLevelIDName .'" class="page-level-'.$this->levelID.'">';
			} else {
				$this->navigationCode .= '<ul class="page-level-'.$this->levelID.'">';
			}
			
			while($row = $result->fetchRow()) {
				//echo $row[self::PAGE_ID];
				//echo self::getVisibilityOfPageID($row[self::PAGE_ID])."<br>";
				if(PAGE_ID == $row[self::PAGE_ID]) {
					$liClass = "".$this->currentClassName." page-parent-".$row[self::PAGE_PARENT];
				} else {
					$liClass = "page-parent-".$row[self::PAGE_PARENT];
				}
			
				if(self::hasChildren($row[self::PAGE_ID])) {
					$liClass .= " has-children";	
					$aClass .= "has-parent";
				} else {
					$aClass = "";
				}
				
				if(array_key_exists($row[self::PAGE_ID], $this->furtherNavigationOption)) {
					$furtherNavigationOptionContent = $this->furtherNavigationOption[$row[self::PAGE_ID]];
				} else {
					$furtherNavigationOptionContent = "";
				}
				
				$liStartTag = '<li class="'.$liClass.'" id="page-id-'.$row[self::PAGE_ID].'">';
				$liEndTag = '</li>';
				$aStartTag = '<a class="'.$aClass.'" target="'.$row[self::PAGE_TARGET].'" href="'.WB_URL.''.PAGES_DIRECTORY.''.$row[self::PAGE_LINK].''.PAGE_EXTENSION.'">';
				$aText = $row[$this->naviTitleOption];
				$aEndTag = '</a>';
				$fno = $furtherNavigationOptionContent;
				
				$formatCodeToSearch = array("[li]", "[a]", "[at]", "[/a]", "[fno]");
				$formatCodeToReplace = array($liStartTag, $aStartTag, $aText, $aEndTag, $fno);
				$this->navigationCode .= str_replace($formatCodeToSearch, $formatCodeToReplace, $this->formatCode);

				if(self::hasChildren($row[self::PAGE_ID])) {
					$this->levelID++;
					$this->parentID = $row[self::PAGE_ID];
					self::generateNavigationCode();
				} else {
					$this->navigationCode .= '</li>';
				}
				
			}	
			
			$this->navigationCode .= '</ul></li>';
			
		}
		
		private function reset() {
			$this->navigationCode = "";
			$this->levelID = 0;
			$this->parentID = 0;
		}
		
		public function getNavigationCode() {
			self::generateNavigationCode();
			$navigationCodeWithoutLastLI = preg_replace('#</li>\\s*$#', '', $this->navigationCode);
			self::reset();
			return $navigationCodeWithoutLastLI;
		}
		
		public function printNavigation() {
			echo self::getNavigationCode();
		}
		
		public function printBootstrapNavigation() {
			
			$search = array("page-level-0", "has-children", "has-parent", "class=\"dropdown-toggle");
			$replace = array("nav navbar-nav", "dropdown", "dropdown-toggle", "data-toggle=\"dropdown\" class=\"dropdown-toggle");
			$result = str_replace($search, $replace, self::getNavigationCode());	
			
			$searchPattern = '/page-level-[0-9]+/';
			$replacementForSearchPattern = 'dropdown-menu';
			$result = preg_replace($searchPattern, $replacementForSearchPattern, $result);
			
			echo $result;
			
		}
		
		
	}

?>
