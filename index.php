<?php

include "../../lib/start.php";

check_session();
check_permission(ADM_PERM);

$drawer_label = "Gestione segreteria";

include "index.html.php";