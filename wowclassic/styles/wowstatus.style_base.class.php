<?php
/*	Project:	EQdkp-Plus
 *	Package:	Realm Status Portal Module
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2015 EQdkp-Plus Developer Team
 *
 *	This program is free software: you can redistribute it and/or modify
 *	it under the terms of the GNU Affero General Public License as published
 *	by the Free Software Foundation, either version 3 of the License, or
 *	(at your option) any later version.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU Affero General Public License for more details.
 *
 *	You should have received a copy of the GNU Affero General Public License
 *	along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

if (!defined('EQDKP_INC'))
{
  header('HTTP/1.0 404 Not Found');exit;
}


/*+----------------------------------------------------------------------------
  | wowstatus_style_base
  +--------------------------------------------------------------------------*/
if (!class_exists("wowstatus_style_base"))
{
  abstract class wowstatus_style_base extends gen_class
  {
    /**
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     * output
     * Get the WoW Realm Status output
     *
     * @param  array  $realms  Array with Realmnames => Realmdata
     *
     * @return  string
     */
    public abstract function output($realms);

    /**
     * outputCssStyle
     * Output the CSS Style
     */
    public abstract function outputCssStyle();

  }
}

?>