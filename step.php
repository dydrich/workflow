<?php

include "../../lib/start.php";

check_session();
check_permission(ADM_PERM);

//$log = fopen("/tmp/mysql.log", "w+");

$sel_step = "SELECT id_step, descrizione, ufficio, nome FROM w_step, w_uffici WHERE ufficio = id_ufficio ORDER BY id_step";
$res_step = $db->execute($sel_step);

include "step.html.php";
