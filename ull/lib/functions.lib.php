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
	$sql = "SELECT *";
	$sql.= " FROM ".MAIN_DB_PREFIX."facturedet as d";
	$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."product_extrafields as e";
	$sql.= " ON d.fk_product = e.fk_object";
	$sql.= " LEFT JOIN ".MAIN_DB_PREFIX. "seguridad_social as s";
	$sql.= " ON e.cod_scs = s.id";
	$sql.= " WHERE d.fk_product=".$productid." AND s.renew IS NOT NULL";
	$result = $db->query($sql);
	if ($result) return 1;
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
/**
*Obtains bought items that have to be reminded to renew (supposed to be called from cronjob)
*/
function get_renewal(){
global $db;
	$sql = "SELECT c.nom as nom, c.email as email, p.label as itemname, r.renew_date as renew_date";
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
				if (!$sendit) $err=1;
		}
		if ($err) setEventMessage("MALLLLLLL", 'errors');
		else setEventMessage("Todo bien", 'mesgs');
		return 1;
	}
	
	else return -1;
	
}

/**
* Adds a new line into renewal list
*/

function set_renewal($id,$renew_date){
global $db;
	$sql = "INSERT INTO ".MAIN_DB_PREFIX."renewals";
	$sql.= " VALUES (".$id.",".$renew_date.")";
	$result = $db->query($sql);
	if ($result) return 1;
	else return -1;
}





