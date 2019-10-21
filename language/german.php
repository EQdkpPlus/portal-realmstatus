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

// Title
$lang['realmstatus']				= 'Serverstatus';
$lang['realmstatus_name']			= 'Serverstatus';
$lang['realmstatus_desc']			= 'Den aktuellen Status des Servers anzeigen';

//  Settings
$lang['realmstatus_f_realm']		= 'Liste von Servern';
$lang['realmstatus_f_help_realm']	= 'Bei mehreren Servern müssen diese durch Komma getrennt angegeben werden.';
$lang['realmstatus_f_us']			= 'Handelt es sich um einen US Server?';
$lang['realmstatus_f_help_us']		= 'Diese Einstellung hat nur Auswirkungen wenn als Spiel RIFT oder WoW eingestellt ist.';
$lang['realmstatus_f_gd']			= 'GD Lib (%s) erkannt. GD Lib Version verwenden?';
$lang['realmstatus_f_help_gd']		= 'Diese Einstellung hat nur Auswirkungen wenn als Spiel WoW eingestellt ist.';

// Portal Modul
$lang['rs_no_realmname']			= 'Kein Server angegeben';
$lang['rs_realm_not_found']			= 'Server nicht gefunden';
$lang['rs_game_not_supported']		= 'Der Serverstatus wird für das Spiel nicht unterstützt';
$lang['rs_unknown']					= 'Unbekannt';
$lang['rs_realm_status_error']		= "Fehler beim Ermitteln des Serverstatus für %1\$s";
$lang['rs_loading']					= 'Lade Status...';
$lang['rs_loading_error']			= 'Fehler beim Laden des Status.';

$lang['realmstatus_wow_population_new'] = 'Empfohlen';
$lang['realmstatus_wow_population_low'] = 'Niedrig';
$lang['realmstatus_wow_population_medium'] = 'Mittel';
$lang['realmstatus_wow_population_high'] = 'Hoch';

?>
