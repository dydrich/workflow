<?php

include "../../lib/start.php";

check_session();
check_permission(ADM_PERM|SEG_PERM|DIR_PERM|DSG_PERM);

$sel_flow = "SELECT * FROM rb_w_workflow";
$res_flow = $db->execute($sel_flow);

$drawer_label = "Gestione flussi di lavorazione";

include "workflow.html.php";