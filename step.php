<?php

include "../../lib/start.php";

check_session();
check_permission(ADM_PERM|SEG_PERM|DIR_PERM|DSG_PERM);

$sel_step = "SELECT id_step, descrizione 
			FROM rb_w_step 
			ORDER BY id_step";
$res_step = $db->execute($sel_step);

$drawer_label = "Gestione step";

include "step.html.php";
