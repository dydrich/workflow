<?php
/**
 * Created by PhpStorm.
 * User: riccardo
 * Date: 4/30/17
 * Time: 11:48 AM
 * segnalazione pratiche urgenti
 */
if ($_SESSION['wflow_office'] === 1) {
	$sel_open_requests = "SELECT rb_w_richieste.*, data1 FROM rb_w_richieste, rb_w_dati_richiesta 
					  WHERE rb_w_richieste.id_richiesta = rb_w_dati_richiesta.id_richiesta 
					  AND COALESCE(ufficio, 1) = ".$_SESSION['wflow_office']." 
					  AND (COALESCE(stato, 0) <> 3 AND COALESCE(stato, 0) <> 4 AND COALESCE(stato, 0) <> 5) 
					  AND (DATE(data1) BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 5 DAY)) 
					  ORDER BY data1 DESC";
}
else if ($_SESSION['wflow_office'] === 2) {
	// ds
	$sel_open_requests = "SELECT rb_w_richieste.*, data1 FROM rb_w_richieste, rb_w_dati_richiesta 
					  WHERE rb_w_richieste.id_richiesta = rb_w_dati_richiesta.id_richiesta 
					  AND (COALESCE(ufficio, 1) = ".$_SESSION['wflow_office']." OR stato = 8)
					  AND (COALESCE(stato, 0) <> 3 AND COALESCE(stato, 0) <> 4 AND COALESCE(stato, 0) <> 5) 
					   AND (DATE(data1) BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 5 DAY)) 
					  ORDER BY data1 DESC";
}
else {
	$sel_open_requests = "SELECT rb_w_richieste.*, data1 FROM rb_w_richieste, rb_w_dati_richiesta 
					  WHERE rb_w_richieste.id_richiesta = rb_w_dati_richiesta.id_richiesta 
					  AND (COALESCE(stato, 0) <> 3 AND COALESCE(stato, 0) <> 4 AND COALESCE(stato, 0) <> 5) 
					   AND (DATE(data1) BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 5 DAY)) 
					  ORDER BY data1 DESC";
}
$res = $db->executeQuery($sel_open_requests);
if ($res->num_rows > 0) {
	?>
	<div class="welcome">
		<p id="w_head" class="attention" style="margin-bottom: 0; background-image: none">
			<i class="fa fa-warning" style="position: relative; left: -30px; font-size: 1.4em"></i>
			<span style="position: relative; left: -20px" class="_bold">Urgente</span>
		</p>
		<p class="w_text attention _bold" style="margin-top: 10px">
			<a href="../../modules/workflow/load_module.php?module=wflow&area=manager&page=index" class="attention">Sono presenti delle pratiche in scadenza</a>
		</p>
	</div>

	<?php
}
?>