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

if (!defined('EQDKP_INC')){
	header('HTTP/1.0 404 Not Found'); exit;
}

if (!class_exists('mmo_realmstatus')){
	include_once(registry::get_const('root_path').'portal/realmstatus/realmstatus.class.php');
}


/*+----------------------------------------------------------------------------
  | swtor_realmstatus
  +--------------------------------------------------------------------------*/
if (!class_exists('swtor_realmstatus')){
	class swtor_realmstatus extends mmo_realmstatus{
		/**
		* __dependencies
		* Get module dependencies
		*/
		public static function __shortcuts(){
			$shortcuts = array('puf' => 'urlfetcher', 'env' => 'environment');
			return array_merge(parent::$shortcuts, $shortcuts);
		}

		/* Game name */
		protected $game_name = 'swtor';

		/* URL to load serverstatus from */
		private $status_url = 'http://www.swtor.com/server-status';

		/* cache time in seconds default 30 minutes = 1800 seconds */
		private $cachetime = 1800;

		/* Array with all servers */
		private $server_list = array();

		/* image path */
		private $image_path;


		/**
		* Constructor
		*/
		public function __construct($moduleID){
			$this->moduleID = $moduleID;

			// call base constructor
			parent::__construct();

			// set image path
			$this->image_path = $this->env->link.'portal/realmstatus/swtor/images/';

			// read in the server status
			$this->loadStatus();
		}

		/**
		* checkServer
		* Check if specified server is up/down/unknown
		*
		* @param  string  $servername  Name of server to check
		*
		* @return string ('up', 'down', 'unknown')
		*/
		public function checkServer($servername){
			// get server data
			$servername = trim($servername);
			$servername = html_entity_decode($servername, ENT_QUOTES);
			$serverdata = $this->getServerData($servername);

			// get status
			switch (strtolower($serverdata['status'])){
				case 'up':		return 'up';  break;
				case 'down':	return 'down'; break;
				default:		return 'unknown'; break;
			}
		}

		/**
		* getOutput
		* Get the portal output for all servers
		*
		* @param  array  $servers  Array of server names
		*
		* @return string
		*/
		protected function getOutput($servers){
			// set output
			$output = '';

			// loop through the servers and collect server data
			$swtor_servers = array();
			if (is_array($servers)){
				foreach($servers as $servername){
					// get server data
					$servername = trim($servername);
					$servername = html_entity_decode($servername, ENT_QUOTES);
					$serverdata = $this->getServerData($servername);

					// get status
					switch (strtolower($serverdata['status'])){
						case 'up':		$status = 'online';  break;
						case 'down':	$status = 'offline'; break;
						case 'booting':	$status = 'booting'; break;
						default:		$status = 'unknown'; break;
					}

					// get server type
					
						$type = $serverdata['type'];

						// set country
						$country_flag = $this->getCountryFlag($serverdata['region']);
						$country_title = $serverdata['region'] == 'us' ? 'US' : 'EU';
						$country_div = '<img src="'.$this->env->link.'images/flags/'.$country_flag.'.svg" alt="'.$country_title.'" class="country-flag" style="max-height:24px;" title="'.$country_title.'"/>';


					$output .= '<div class="rs_swtor_name nowrap">'.$servername.' - '.$country_div.' '.$country_title.'</div>
								<div class="rs_swtor_status rs_swtor_'.$status.'"></div>';
				}
			}
			return $output;
		}

		/**
		* outputCSS
		* Output CSS
		*/
		protected function outputCSS(){
			$style = '.rs_swtor_status {
				padding-left:  10px;
				padding-top:   159px;
				padding-right: 10px;
				margin-top:    2px;
				margin-bottom: 2px;
			}

			.rs_swtor_online {
				background: url("'.$this->image_path.'online.png") no-repeat scroll 0 0 transparent;
			}

			.rs_swtor_offline {
				background: url("'.$this->image_path.'offline.png") no-repeat scroll 0 0 transparent;
			}

			.rs_swtor_booting {
				background: url("'.$this->image_path.'booting.png") no-repeat scroll 0 0 transparent;
			}

			.rs_swtor_unknown {
				background: url("'.$this->image_path.'searching.png") no-repeat scroll 0 0 transparent;
			}

			.rs_swtor_detail {
				padding:    5px 10px;
				margin:     3px;
			}

			.rs_swtor_country, .rs_swtor_type {
				display: inline;
			}

			.rs_swtor_name {
				margin-top: 1px;
				}';

			// add css
			$this->tpl->add_css($style);
		}

		/**
		* getServerData
		* Gets the data for the specified server
		*
		* @param  string  $servername  Name of the server to get data of
		*
		* @return array(status, population, type, timezone, language, region)
		*/
		private function getServerData($servername){
			$name = trim($servername);

			if (isset($this->server_list[$name]))
				return $this->server_list[$name];

			return array(
				'status'		=> 'unknown',
				'population'	=> -1,
				'type'			=> '',
				'timezone'		=> 'unknown',
				'language'		=> 'unknown',
				'region'		=> 'unknown',
			);
		}

		/**
		* loadStatus
		* Load status from either the pdc or from website
		*/
		private function loadStatus(){
			// try to load data from cache
			$this->server_list = $this->pdc->get('portal.module.realmstatus.swtor', false, true);
			if (!$this->server_list){
				// none in cache or outdated, load from website
				$this->server_list = $this->loadServerStatus();
				// store loaded data within cache
				if (is_array($this->server_list)){
					$this->pdc->put('portal.module.realmstatus.swtor', $this->server_list, $this->cachetime, false, true);
				}
			}
		}

		/**
		* loadServerStatus
		* Load the status for all Star Wars The Old Republic servers
		*
		* @return array(status, population, type, timezone, language, region)
		*/
		private function loadServerStatus(){
			// reset output
			$servers = array();

			// set URL reader options
			$this->puf->checkURL_first = true;

			// load html page
			$html = $this->puf->fetch($this->status_url);
			
			if (!$html || empty($html))
				return $servers;
			
			$dom = DOMDocument::loadHTML($html);

			
			$finder = new DomXPath($dom);
			$classname = "serverBody";
			$nodes = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");
			
			$servers = array();
			
			foreach ($nodes as $key => $node) {
				$status = $node->getAttribute('data-status');
				$status = strtolower($status);
				$childs = $node->childNodes;
				$i = 0;
				foreach($childs as $child){
					$i++;
					if($i == 4) {
						$servername = $child->textContent;
						
						$servers[$servername] = array(
								'status'		=> $status,
								'region'		=> ($key > 3) ? 'eu' : 'us',
						);
						
						break;
					}
				}
			}		
			
			
			if (!count($servers)) return array();

			return $servers;
		}

		/**
		* getCountryFlag
		* Gets the country flag image
		*
		* @param  string  $server_language  Language of server
		*
		* @return  string
		*/
		private function getCountryFlag($server_language){
			// return pvp status
			$language = strtolower($server_language);
			switch ($language){
				case 'eu':	return 'eu';
				case 'us':		return 'us';
			}
			return '';
		}
	}
}
?>