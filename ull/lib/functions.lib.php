<?php
/**
 * Checks if product with certain $id has the extrafield $cod_scs filled or not
*/
function is_financed($id){
	global $db;
	$sql = "SELECT cod_scs";
	$sql.= " FROM ".MAIN_DB_PREFIX."product_extrafields";
	$sql.= " WHERE fk_object = ".$id." AND cod_scs IS NOT NULL";
	$result = $db->query($sql);
	if ($result){
		$num = $db->num_rows($result);
		if ($num) return 1;
		else return 0;
	}
	else return -1;
}

function check_finance($id){
	global $db;
	$sql = "SELECT p.price_ttc as price, e.cod_scs as code, s.finance as finance, s.user_contrib as user_contrib, s.description as description";
	$sql.= " FROM ".MAIN_DB_PREFIX."product as p";
	$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."product_extrafields as e";
	$sql.= " ON p.rowid = e.fk_object";
	$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."seguridad_social  as s";
	$sql.= " ON e.cod_scs = s.id";
	$sql.= " WHERE p.rowid = ".$id." AND e.cod_scs IS NOT NULL ";
	$result = $db->query($sql);
	if ($result){
		$objp = $db->fetch_object($result);
		$array = array(
				"code" => $objp->code,
				"finance" => $objp->finance,
				"user_contrib" => $objp->user_contrib,
				"description" => $objp->description,
				"price" => $objp->price,
				);
		return $array;		
	}
	else return -1;
}

function is_renewable($object){
	global $db;
	$productid = $object->fk_product;
	$sql = "SELECT s.renov as renew";
	$sql.= " FROM ".MAIN_DB_PREFIX."facturedet as d";
	$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."product_extrafields as e";
	$sql.= " ON d.fk_product = e.fk_object";
	$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."seguridad_social as s";
	$sql.= " ON e.cod_scs = s.id";
	$sql.= " WHERE d.fk_product = ".$productid." AND s.renov IS NOT NULL ";
	$result = $db->query($sql);
	if ($result){
		$objp = $db->fetch_object($result);
		return $objp->renew;
	}
	else return 0;
}

function change_desc($object){
global $db;
	$sql = "UPDATE ".MAIN_DB_PREFIX."facturedet";
	$sql.= " SET description = '".$db->escape($object->desc);
	$sql.= "' WHERE rowid = ".$object->rowid;
	$result = $db->query($sql);
	return $sql;
}



function composerenewemail($name, $mail, $itemname, $renewdate){
$message="Estimado {$name}, \n El material ortopédico {$itemname} obtenido a través de nosotros podrá ser renovado a partir de {$renewdate}";
	$mailto = new CMailFile(
		$subject="Próxima renovación de su material ortopédico",
		$mail,
		$replyto="Administración <admin@aracelireymundo.com>",
		$message
	);
	if ($mailto->sendfile()) return 1;
	else	return 0;
	
}

function deleterenew($id){
global $db;
	$sql = "DELETE * FROM ".MAIN_DB_PREFIX."renewals";
	$sql.= " WHERE id =".$id.")";
	$resql = $db->query($sql);
	if ($resql)
		return 1;
	else return -1;
}
/**
*Obtains bought items that have to be reminded to renew (supposed to be called from cronjob)
*/
function get_renewal(){
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
		}
	}
	
	else return -1;
	
}

function addmonthstodate($date,$months){
	$monthToAdd = $months;

	$d1 = $date;

	$year = $d1->format('Y');
	$month = $d1->format('n');
	$day = $d1->format('d');

	$year += floor($monthToAdd/12);
	$monthToAdd = $monthToAdd%12;
	$month += $monthToAdd;
	if($month > 12) {
		$year ++;
		$month = $month % 12;
		if($month === 0)
		    $month = 12;
	}

	if(!checkdate($month, $day, $year)) {
		$d2 = DateTime::createFromFormat('Y-n-j', $year.'-'.$month.'-1');
		$d2->modify('last day of');
	}else {
		$d2 = DateTime::createFromFormat('Y-n-d', $year.'-'.$month.'-'.$day);
	}
	$d2->setTime($d1->format('H'), $d1->format('i'), $d1->format('s'));
	return $d2;
}

/**
* Adds a new line into renewal list
*/

function set_renewal($id,$invoice_date,$renov){
global $db;
	$date = new DateTime("@$invoice_date");
	$date = addmonthstodate($date,$renov);
	$sql = "INSERT INTO ".MAIN_DB_PREFIX."renewals";
	$sql.= " VALUES (".$id.",'".$date->format('Y-m-d')."')";
	$result = $db->query($sql);
	if ($result) return 1;
	else return -1;
}

function factura_scs ($id){
global $db;
	$sql = "SELECT scs";
	$sql.= " FROM ".MAIN_DB_PREFIX."facture_extrafields";
	$sql.= " WHERE fk_object = ".$id;
	$result = $db->query($sql);
	if ($result){
		$objp = $db->fetch_object($result);
		return $objp->scs;
	}
}


