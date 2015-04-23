<?php

	/**
	 * This file is part of an ADDON for use with WebsiteBaker CMS
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
		const PAGE_VISIBILITY_PUBLIC = "public";
		const PAGE_VISIBILITY_REGISTERED  = "registered";
		const PAGE_VISIBILITY_PRIVATE  = "private";
		const PAGE_VISIBILITY_HIDDEN  = "hidden";
		const PAGE_VISIBILITY_NONE  = "none";
		const PAGE_TRAIL = "page_trail";
		const COUNT_CHILDREN = "count_children";
		
		const PAGE_ADMIN_GROUPS = 'admin_groups';
		const PAGE_ADMIN_USERS  = 'admin_users';
		const PAGE_VIEW_GROUPS  = 'viewing_groups';
		const PAGE_VIEW_USERS   = 'viewing_users';
		
		private $levelID = 0;
		private $parentID = 0;
		private $menuID = 1;
		private $navigationCode = "";
		private $naviTitleOption = self::PAGE_TITLE;
		private $furtherNavigationOption = array();
		private $formatCode = "[li][a][at][/a]";
		private $currentClassName = "current";
		private $firstLevelIDName = "";
		private $actualVisibilityOption = array("public");
		private $isUserAllowedToSeeActualPage = false;
		
		protected $oDb  = null;
		protected $oApp = null;
		
		public function __construct(wb $oApplication, database $database) {
			$this->oApp = $oApplication;
			$this->oDb  = $database;
		}
		
		public function debug($obj, $printAsVarDump) {
			ob_start();
			if($printAsVarDump) {
				var_dump($obj);
			} else {
				print_r($obj);
			}
			$result = ob_get_clean();
			echo '<pre style="margin-top: 100px">'.$result.'</pre>';
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

			//$sql = 'SELECT * FROM '.TABLE_PREFIX.'pages WHERE '.self::PAGE_LEVEL.' = '.$this->levelID.' AND '.self::PAGE_VISIBILITY.' = "public" AND '.self::PAGE_MENU.' = '.$this->menuID.' ORDER BY '.self::PAGE_POSITION.' ASC';
			$sql = 'SELECT * FROM '.TABLE_PREFIX.'pages WHERE '.self::PAGE_LEVEL.' = '.$this->levelID.' AND '.self::PAGE_MENU.' = '.$this->menuID.' ORDER BY '.self::PAGE_POSITION.' ASC';
			$result = $this->oDb->query($sql);
			
			if($this->levelID == 0) {
				if($this->firstLevelIDName == "") $this->firstLevelIDName = 'menu-id-'.$this->menuID.'';
				$this->navigationCode .= '<ul id="'.$this->firstLevelIDName .'" class="page-level-'.$this->levelID.'">';
			} else {
				$this->navigationCode .= '<ul class="page-level-'.$this->levelID.'">';
			}
			
			while($row = $result->fetchRow()) {

				// Access-Control Check
				if ($this->oApp->is_authenticated()) {
				
					// current user is logged in
					if (
						// is user in a group which is listed in PAGE_VIEW_GROUPS ?
						$this->oApp->ami_group_member($row[self::PAGE_VIEW_GROUPS]) ||
						// is user in the list of PAGE_VIEW_USERS ?
						$this->oApp->is_group_match($this->oApp->get_user_id(), $row[self::PAGE_VIEW_USERS]) ||
						// is user in a group which is listed in PAGE_ADMIN_GROUPS ?
						$this->oApp->ami_group_member($row[self::PAGE_ADMIN_GROUPS]) ||
						// is user in the list of PAGE_ADMIN_USERS ?
						$this->oApp->is_group_match($this->oApp->get_user_id(), $row[self::PAGE_ADMIN_USERS])
					) {
						// ok, current user (privat user) got access to public, registered and privat
						// Other Visibility options like "HIDDEN" and "NONE" will be ignored for the whitelist.
						// The reason why the visibility option "NONE" is treated like the option "HIDDEN" (temporary): http://bit.ly/1OL2bhx
						$this->actualVisibilityOption = array(self::PAGE_VISIBILITY_PUBLIC, self::PAGE_VISIBILITY_REGISTERED, self::PAGE_VISIBILITY_PRIVATE);
					} 
				} else {
					// current user (public user) got access to public, registered
					// Other Visibility options like "PRIVATE", "HIDDEN" and "NONE" will be ignored for the whitelist
					// The reason why the visibility option "NONE" is treated like the option "HIDDEN" (temporary): http://bit.ly/1OL2bhx
					$this->actualVisibilityOption = array(self::PAGE_VISIBILITY_PUBLIC, self::PAGE_VISIBILITY_REGISTERED);
				}
				
				// Is the current user allowed to see the actual page?
				$this->isUserAllowedToSeeActualPage = in_array(self::getVisibilityOfPageID($row[self::PAGE_ID]), $this->actualVisibilityOption);
				
				// If the user is not allowed to see this page skip the other statements inside the loop
				if(!$this->isUserAllowedToSeeActualPage) continue;
				
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
				
				$liStartTag = '<li data-visibility="'.self::getVisibilityOfPageID($row[self::PAGE_ID]).'" class="'.$liClass.'" id="page-id-'.$row[self::PAGE_ID].'">';
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
