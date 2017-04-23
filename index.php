<?php

include "../../lib/start.php";

check_session();
check_permission(ADM_PERM|SEG_PERM|DSG_PERM|DIR_PERM);

$_SESSION['__path_to_root__'] = "../../";
$_SESSION['__path_to_reg_home__'] = "./";

$drawer_label = "Gestione segreteria";

include "index.html.php";