<?php

include "../../lib/start.php";

check_session();
check_permission(ADM_PERM|SEG_PERM|DIR_PERM|DSG_PERM);

$sel_uffici = "SELECT * FROM rb_w_uffici ORDER BY id_ufficio DESC";
$res_uffici = $db->executeQuery($sel_uffici);
$array_id = array();
$uffici = [];
while($ufficio = $res_uffici->fetch_assoc()){
    array_push($array_id, $ufficio['id_ufficio']);
    $uffici[$ufficio['id_ufficio']] = $ufficio;
}
$stringa_uffici = join(",", $array_id);

$drawer_label = "Elenco uffici";

include "offices.html.php";