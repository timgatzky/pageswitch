<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2010 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  Tim Gatzky 2012 
 * @author     Tim Gatzky <info@tim-gatzky.de>
 * @package    pageswitch 
 * @license    LGPL 
 * @filesource
 */


/**
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_page']['palettes']['regular'] = str_replace
(
	'type', 
	'type,sibling;', 
	$GLOBALS['TL_DCA']['tl_page']['palettes']['regular']
);

/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_page']['fields']['sibling'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_page']['sibling'],
	'exclude'                 => true,
	'inputType'               => 'select',
	'options_callback'        => array('tl_page_pageswitch', 'getPages'),
	'save_callback'				=> array(array('tl_page_pageswitch','saveBidirectual')),
	'eval'                    => array('tl_class'=>'w50'),
);



class tl_page_pageswitch extends Backend
{
	/**
	 * @var
	 */
	protected $tabIndex = 3;
	protected $arrPages = array();
	
	/**
	 * Save the current page as new sibling to sibling page
	 * @param variable
	 * @ param 
	 */
	
	public function saveBidirectual($varValue, DataContainer $dc)
	{
		if($GLOBALS['PAGESWITCH']['saveBidirectual'])
		{
			$arrSet = array('sibling'=>$dc->id);
			$objUpdate = $this->Database->prepare("UPDATE tl_page %s WHERE id=?")->set($arrSet)->execute($varValue);
		}
		return $varValue;
	}
	
	/**
	 * Get all pages
	 * @return array
	 */
	public function getPages(DataContainer $dc)
	{
		// get root pages
		$objRootPages = $this->Database->prepare("SELECT * FROM tl_page WHERE id!=? AND type='root' ORDER BY sorting")
						->execute($dc->activeRecord->id);
		if($objRootPages->numRows < 1)
		{
			return array();
		}
		
		$this->arrPages[] = '-';
		
		while($objRootPages->next())
		{
			$this->arrPages[$objRootPages->id] = $objRootPages->title.' (id:'.$objRootPages->id.')';
			$this->getPageList($objRootPages->id,0);
		}
		
		return $this->arrPages;
	}
	
	/**
	 * Create the page tree for the select field
	 * @param integer
	 * @param integer
	 * @return void
	 */
	protected function getPageList($intId,$level=-1)
	{
		$objPages = $this->Database->prepare("SELECT id, title FROM tl_page WHERE pid=? AND type != 'root' AND type != 'error_403' AND type != 'error_404' ORDER BY sorting")
								   ->execute($intId);
								   
		if ($objPages->numRows < 1)
		{
			return;
		}

		++$level;
		
		while ($objPages->next())
		{
			$label = str_repeat("&nbsp;", ($this->tabIndex * $level)) . $objPages->title;
			$this->arrPages[$objPages->id] = $label;
			
			// call recursiv
			$this->getPageList($objPages->id, $level);
		}
	}
	
	
}

?>