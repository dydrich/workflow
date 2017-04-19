<?php
/**
 * Created by PhpStorm.
 * User: riccardo
 * Date: 4/19/17
 * Time: 6:23 PM
 */
$sel_open_requests = "SELECT rb_w_richieste.*, data1 FROM rb_w_richieste, rb_w_dati_richiesta 
					  WHERE rb_w_richieste.id_richiesta = rb_w_dati_richiesta.id_richiesta 
					  AND COALESCE(ufficio, 1) = ".$_SESSION['wflow_office']." 
					  AND (COALESCE(stato, 0) <> 3 AND COALESCE(stato, 0) <> 4 AND COALESCE(stato, 0) <> 5) 
					  AND data1 >= NOW() 
					  ORDER BY data1 DESC";
$res = $db->executeQuery($sel_open_requests);
if ($res->num_rows > 0) {
?>
	<div class="welcome">
		<p id="w_head" style="margin-bottom: 0; background-image: none">
			<i class="fa fa-id-card-o" style="position: relative; left: -30px; font-size: 1.4em"></i>
			<span style="position: relative; left: -20px" class="_bold">Pratiche in corso</span>
		</p>
		<p class="w_text" style="margin-top: 10px">
			<a href="../../modules/workflow/load_module.php?module=wflow&area=manager&page=index" class="attention">Hai <?php echo $res->num_rows ?> pratiche aperte</a>
		</p>
	</div>

<?php
}
?>
