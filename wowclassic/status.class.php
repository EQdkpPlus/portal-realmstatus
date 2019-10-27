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
	header('HTTP/1.0 404 Not Found');exit;
}

if (!class_exists('mmo_realmstatus')){
	include_once(registry::get_const('root_path').'portal/realmstatus/realmstatus.class.php');
}

/*+----------------------------------------------------------------------------
  | wow_realmstatus
  +--------------------------------------------------------------------------*/
if (!class_exists('wowclassic_realmstatus')){
	class wowclassic_realmstatus extends mmo_realmstatus{

		/* Game name */
		protected $game_name = 'wowclassic';

		/* The style for output */
		private $style;
	
		protected $moduleID = 0;
		
		/* cache time in seconds default 10 minutes = 600 seconds */
		private $cachetime = 600;

		/**
		* Constructor
		*/
		public function __construct($moduleID){
			$this->moduleID = $moduleID;

			// call base constructor
			parent::__construct();

			// init the styles class
			$this->initStyle();
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
			// try to load xml string from cache
			$status = $this->pdc->get('portal.module.realmstatus.wowclassic.'.$servername, false, true);
			if ($status === null){
				// none in cache or outdated, load from website
				$status = $this->loadStatus($servername);
				if ($status !== false){
					// store loaded data within cache
					$this->pdc->put('portal.module.realmstatus.wowclassic.'.$servername, $status, $this->cachetime, false, true);
				}else{
					$status = 'unknown';
				}
			}
			return $status;
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
			// get realms array
			$realms = array();
			foreach ($servers as $realm){
				$realm = trim($realm);
				$realm = html_entity_decode($realm, ENT_QUOTES);
				$realmdata = $this->loadStatus($realm);
				$realms[$realm] = $realmdata;
			}

			// get output from style
			$output = $this->style->output($realms);
			return $output;
		}

		/**
		* outputCSS
		* Output CSS
		*/
		protected function outputCSS(){
			$this->style->outputCssStyle();
		}

		
		/**
		 * loadStatus
		 * Load status from either the pdc or from codemasters website
		 *
		 * @param  string  $servername  Name of server to check
		 *
		 * @return string ('up', 'down', 'unknown')
		 */
		private function loadStatus($servername){
			$arrRealms = $this->pdc->get('portal.module.realmstatus.wowclassic.json', false, true);
			if ($arrRealms === null){
				$strRealmLocation = $this->config->get('uc_server_loc');
				
				switch($strRealmLocation){
					case 'us':  $strData = '{"operationName":"GetRealmStatusData","variables":{"input":{"compoundRegionGameVersionSlug":"classic-us"}},"extensions":{"persistedQuery":{"version":1,"sha256Hash":"b668cd9bb522d534eb99fcacc46ccbe3824b98dc10b61a08d4ae149bee9caa70"}}}';
						break;
						
					case 'kr':	$strData = '{"operationName":"GetRealmStatusData","variables":{"input":{"compoundRegionGameVersionSlug":"classic-kr"}},"extensions":{"persistedQuery":{"version":1,"sha256Hash":"b668cd9bb522d534eb99fcacc46ccbe3824b98dc10b61a08d4ae149bee9caa70"}}}';
						break;
						
					case 'tw':	$strData = '{"operationName":"GetRealmStatusData","variables":{"input":{"compoundRegionGameVersionSlug":"tw"}},"extensions":{"persistedQuery":{"version":1,"sha256Hash":"b668cd9bb522d534eb99fcacc46ccbe3824b98dc10b61a08d4ae149bee9caa70"}}}';
						break;
					
					default:
					case 'eu': $strData = '{"operationName":"GetInitialRealmStatusData","variables":{"input":{"compoundRegionGameVersionSlug":"classic-eu"}},"extensions":{"persistedQuery":{"version":1,"sha256Hash":"7b3ba73c1458c52eec129aaf0c64d8be62f5496754f1143f8147da317fdd2417"}}}';
						break;
				}
				
				
				
				$mixResult = register('urlfetcher')->post('https://worldofwarcraft.com/graphql', $strData, 'application/json');
				
				if($mixResult){
					$arrJson = json_decode($mixResult, true);
					$arrRealms = $arrJson['data']['Realms'];
					
					$this->pdc->put('portal.module.realmstatus.wowclassic.json', $arrRealms, $this->cachetime, false, true);
				}
			}
			
			foreach($arrRealms as $arrRealmData){
				if($arrRealmData['name'] == $servername){
					return array(
							'type'			=> ($arrRealmData['type']['slug'] == 'rp') ? 'roleplaying' : $arrRealmData['type']['slug'],
							'queue'			=> '',
							'status'		=> $arrRealmData['online'],
							'population'	=> (strlen($arrRealmData['population']['slug'])) ? $arrRealmData['population']['slug'] : 'unknown',
							'name'			=> $servername,
							'slug'			=> $arrRealmData['slug'],
					);
				}
			}
			
			return array(
					'type'			=> 'error',
					'queue'			=> '',
					'status'		=> -1,
					'population'	=> 'error',
					'name'			=> $servername,
					'slug'			=> $servername,
			);
		}

		/**
		* initStyle
		* Initialize the styles classes
		*/
		private function initStyle(){
			$file_style_normal = $this->root_path.'portal/realmstatus/wowclassic/styles/wowstatus.style_normal.class.php';
			include_once($file_style_normal);

			$this->style = registry::register('wowstatus_style_normal');
		}
	}
}
?>