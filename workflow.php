<?php

include "../../lib/start.php";

check_session();
check_permission(ADM_PERM);

//$log = fopen("/tmp/mysql.log", "w+");

$sel_flow = "SELECT * FROM w_workflow";
$res_flow = $db->execute($sel_flow);

include "workflow.html.php";