<?php
/**
 * Created by PhpStorm.
 * User: riccardo
 * Date: 3/22/17
 * Time: 8:47 PM
 */
require_once "../../../lib/start.php";

check_session();
check_permission(DOC_PERM|ATA_PERM|SEG_PERM|DSG_PERM);

$_SESSION['__path_to_root__'] = "../../../";
$_SESSION['__path_to_reg_home__'] = "../";

$user = $_SESSION['__user__']->getUid();

$navigation_label = "registro elettronico ";
$drawer_label = "Segreteria";

include "front.html.php";