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

function change_desc($object){
global $db;
	$sql = "UPDATE ".MAIN_DB_PREFIX."facturedet";
	$sql.= " SET description = '".$db->escape($object->desc);
	$sql.= "' WHERE rowid = ".$object->rowid;
	$result = $db->query($sql);
	return $sql;
}

