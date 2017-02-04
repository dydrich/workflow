<?php

include "../../lib/start.php";

check_session();
check_permission(ADM_PERM);

//$log = fopen("/tmp/mysql.log", "w+");

$sel_status = "SELECT id_status, w_status.nome, permessi, w_uffici.nome AS uff, id_ufficio FROM w_status, w_uffici WHERE permessi&codice_permessi ORDER BY id_status, id_ufficio";
$res_status = $db->execute($sel_status);

include "status.html.php";