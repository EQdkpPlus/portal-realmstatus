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

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

/*+----------------------------------------------------------------------------
| realmstatus_portal
+--------------------------------------------------------------------------*/
class realmstatus_portal extends portal_generic {

	protected static $path = 'realmstatus';
	protected static $data = array(
		'name'			=> 'Realmstatus Module',
		'version'		=> '2.0.9',
		'author'		=> 'Aderyn',
		'icon'			=> 'fa-desktop',
		'contact'		=> 'Aderyn@gmx.net',
		'description'	=> 'Show Realmstatus',
		'exchangeMod'	=> array('realmstatus'),
		'lang_prefix'	=> 'realmstatus_'
	);
	protected static $positions = array('left1', 'left2', 'right');
	protected $settings = array(
		'realm'  => array(
			'type'		=> 'text',
			'size'		=> '40',
		),
		'us' => array(
			'type'		=> 'radio',
		),
	);
	protected static $install  = array(
		'autoenable'		=> '0',
		'defaultposition'	=> 'right',
		'defaultnumber'		=> '5',
	);
	
	protected static $apiLevel = 20;

	public function get_settings($state) {
		// check ig gd lib is available, if so, make option to use available
		if (extension_loaded('gd') && function_exists('gd_info')){
			$gd_info = gd_info();
			$this->settings['gd'] = array(
				'type'		=> 'radio',
				'dir_lang'	=> sprintf($this->user->lang('realmstatus_f_gd'), $gd_info['GD Version']),
			);
		}
		return $this->settings;
	}

	/**
	* output
	* Returns the portal output
	*
	* @return string
	*/
	public function output() {
		// empty output as default
		$realmstatus = '';

		// try to load the status file for this game
		$game_name = strtolower($this->game->get_game());
		
		$status_file = $this->root_path.'portal/realmstatus/'.$game_name.'/status.class.php';
		if (file_exists($status_file)) {
			include_once($status_file);
			$class_name = $game_name.'_realmstatus';
			$status = registry::register($class_name, array($this->id));
			if ($status) $realmstatus .= $status->getPortalOutput();
			else $realmstatus .= '<div class="center">'.$this->user->lang('rs_game_not_supported').'</div>';
		} else {
			$realmstatus .= '<div class="center">'.$this->user->lang('rs_game_not_supported').'</div>';
		}

		// return the output for module manager
		return $realmstatus;
	}

}
?>
