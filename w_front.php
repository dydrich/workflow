<?php
/**
 * Created by PhpStorm.
 * User: riccardo
 * Date: 4/22/17
 * Time: 4:30 PM
 */
$sel_closed_requests = "SELECT rb_w_richieste.*, data1 FROM rb_w_richieste, rb_w_dati_richiesta 
					  WHERE rb_w_richieste.id_richiesta = rb_w_dati_richiesta.id_richiesta 
					  AND richiedente = {$_SESSION['__user__']->getUid()}
					  AND data1 >= NOW() 
					  ORDER BY data1 ASC";
$res = $db->executeQuery($sel_closed_requests);
$accepted = $rejected = $waiting = 0;
if ($res->num_rows > 0) {
	while ($row = $res->fetch_assoc()) {
		if ($row['stato'] == 3) {
			$accepted++;
		}
		else if ($row['stato'] == 4) {
			$rejected++;
		}
		else {
			$waiting++;
		}
	}
	?>
	<div class="welcome">
		<p id="w_head" style="margin-bottom: 0; background-image: none">
			<i class="fa fa-id-card-o" style="position: relative; left: -30px; font-size: 1.4em"></i>
			<span style="position: relative; left: -20px" class="_bold">Permessi</span>
		</p>
		<?php if ($accepted > 0): ?>
		<p class="w_text" style="margin-top: 10px">
			<a href="../../modules/workflow/load_module.php?module=wflow&area=teachers&page=front" class="attention">
				Hai <?php echo $accepted ?> richieste approvate
			</a>
		</p>
		<?php endif; ?>
		<?php if ($rejected > 0): ?>
			<p class="w_text" style="margin-top: 0">
				<a href="../../modules/workflow/load_module.php?module=wflow&area=teachers&page=front" class="attention">
					Hai <?php echo $rejected ?> richieste non approvate
				</a>
			</p>
		<?php endif; ?>
		<?php if ($waiting > 0): ?>
			<p class="w_text" style="margin-top: 0">
				<a href="../../modules/workflow/load_module.php?module=wflow&area=teachers&page=front" class="attention">
					Hai <?php echo $waiting ?> richieste in attesa di approvazione
				</a>
			</p>
		<?php endif; ?>
	</div>

	<?php
}
?>
