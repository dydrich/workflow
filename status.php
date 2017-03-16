<?php

include "../../lib/start.php";

check_session();
check_permission(ADM_PERM|SEG_PERM|DIR_PERM|DSG_PERM);

/*
$sel_status = "SELECT id_status, rb_w_status.nome, permessi, rb_w_uffici.nome AS uff, id_ufficio
			  FROM rb_w_status, rb_w_uffici 
			  WHERE permessi&codice_permessi 
			  ORDER BY id_status, id_ufficio";
*/
$sel_status = "SELECT * FROM rb_w_status ORDER BY id_status";
$res_status = $db->execute($sel_status);
$status = [];
while($row = $res_status->fetch_assoc()) {
	$status[$row['id_status']] = $row;
	$status[$row['id_status']]['uffici'] = [];
	$sel_uff = "SELECT id_ufficio, nome FROM rb_w_uffici WHERE codice_permessi&{$row['permessi']}";
	$res_uff = $db->executeQuery($sel_uff);
	while ($r = $res_uff->fetch_assoc()) {
		$status[$row['id_status']]['uffici'][$r['id_ufficio']] = $r['nome'];
	}
}

$drawer_label = "Gestione degli stati della pratica";

include "status.html.php";