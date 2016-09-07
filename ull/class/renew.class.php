<?php
/* Copyright (C) 2007-2012  Laurent Destailleur <eldy@users.sourceforge.net>
 * Copyright (C) 2014       Juanjo Menent       <jmenent@2byte.es>
 * Copyright (C) 2015       Florian Henry       <florian.henry@open-concept.pro>
 * Copyright (C) 2015       Raphaël Doursenaud  <rdoursenaud@gpcsolutions.fr>
 * Copyright (C) ---Put here your own copyright and developer email---
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * \file    dev/skeletons/skeleton_class.class.php
 * \ingroup mymodule othermodule1 othermodule2
 * \brief   This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *          Put some comments here
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
require_once DOL_DOCUMENT_ROOT . '/ull/lib/functions.lib.php';
require_once DOL_DOCUMENT_ROOT .'/core/class/CMailFile.class.php';
//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

/**
 * Class Skeleton_Class
 *
 * Put here description of your class
 * @see CommonObject
 */
class Renew extends CommonObject
{
	/**
	 * @var string Id to identify managed objects
	 */
	public $element = 'skeleton';
	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'renewals';

	/**
	 * @var Skeleton_ClassLine[] Lines
	 */
	public $lines = array();

	public function __construct(DoliDB $db)
	{
		$this->db = $db;
		return 1;
	}
/**
*Obtains bought items that have to be reminded to renew (supposed to be called from cronjob)
*/
	public function get_renewal(){
	global $db;
		$sql = "SELECT c.nom as nom, c.email as email, p.label as itemname, r.renew_date as renew_date, r.id as rid";
		$sql.= " FROM ".MAIN_DB_PREFIX."renewals as r";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."facturedet as d";
		$sql.= " ON r.id = d.rowid";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."facture  as f";
		$sql.= " ON d.fk_facture = f.rowid";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."societe  as c";
		$sql.= " ON f.fk_soc = c.rowid";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."product  as p";
		$sql.= " ON d.fk_product = p.rowid";
		$sql.= " WHERE DATE_ADD(r.renew_date,INTERVAL 1 MONTH) > NOW()";
		$resql = $db->query($sql);
	
		if ($resql){
		 $numrows=$db->num_rows($resql);
		    $i=0;
		    $err=0;
		    while ($i < $numrows)
		    {
			$objp = $db->fetch_object($resql);
			$array = array(
					"itemname" => $objp->itemname,
					"renewdate" => $objp->renew_date,
					"clientname" => $objp->nom,
					"email" => $objp->email,				
					);
				
					$sendit = composerenewemail($array["clientname"], $array["email"], $array["itemname"], $array["renewdate"]);
					if ($sendit){
						deleterenew($objp->rid);
					}
					else return -1;
			}
		}
	
		else return -1;
	
	}
}
