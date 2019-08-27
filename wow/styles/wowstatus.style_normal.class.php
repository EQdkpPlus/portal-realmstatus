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
  header('HTTP/1.0 404 Not Found');
  exit;
}


if (!class_exists('wowstatus_style_base'))
{
  include_once(registry::get_const('root_path').'portal/realmstatus/wow/styles/wowstatus.style_base.class.php');
}

/*+----------------------------------------------------------------------------
  | wowstatus_style_normal
  +--------------------------------------------------------------------------*/
if (!class_exists("wowstatus_style_normal"))
{
  class wowstatus_style_normal extends wowstatus_style_base
  {

    /* Base image path */
    private $image_path;

    /**
     * Constructor
     */
    public function __construct()
    {
      // call base constructor
      parent::__construct();

      // set image path
      $this->image_path = $this->env->link.'portal/realmstatus/wow/images/normal/';
    }

    /**
     * output
     * Get the WoW Realm Status output
     *
     * @param  array  $realms  Array with Realmnames => Realmdata
     *
     * @return  string
     */
    public function output($realms)
    {
      // set output
      $output = '';

      // process all realms
      if (is_array($realms))
      {
        foreach ($realms as $realmname => $realmdata)
        {
          // set "tr" div
          $output .= '<div class="tr">';
          // output status
          switch ((int)$realmdata['status'])
          {
            case 1:
              $output .= '<div class="td"><img src="'.$this->image_path.'up.png" alt="Online" title="'.$realmname.'" /></div>';
              break;
            case 0:
              $output .= '<div class="td"><img src="'.$this->image_path.'down.png" alt="Offline" title="'.$realmname.'" /></div>';
              break;
            default:
              $output .= '<div class="td"><i class="fa fa-lg fa-question" title="'.$realmname.' ('.$this->user->lang('rs_unknown').')" /></i></div>';
              break;
          }

          // output realm name
          $output .= '<div class="td">'.$realmname.'</div>';

          // output server type
          switch (strtolower($realmdata['type']))
          {
            case 'roleplaying':
              $output .= '<div class="td rs_wow_rp">RP</div>';
              break;
          }

          // close "tr" div
          $output .= '</div>';
        }
      }

      return $output;
    }

    /**
     * outputCssStyle
     * Output the CSS Style
     */
    public function outputCssStyle()
    {
      $style = '.rs_wow_pve, .rs_wow_rppvp {
                  color: #EBDBA2;
                }

                .rs_wow_pvp {
                  color: #CC3333;
                }

                .rs_wow_rp {
                  color: #33CC33;
                }';

      // add css
      $this->tpl->add_css($style);
    }

  }
}

?>