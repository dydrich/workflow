<?php
/**
 * Created by PhpStorm.
 * User: riccardo
 * Date: 4/17/17
 * Time: 3:09 PM
 */
if ($_SESSION['wflow_office'] === 1) {
	$sel_open_requests = "SELECT rb_w_richieste.*, data1 FROM rb_w_richieste, rb_w_dati_richiesta 
					  WHERE rb_w_richieste.id_richiesta = rb_w_dati_richiesta.id_richiesta 
					  AND COALESCE(ufficio, 1) = ".$_SESSION['wflow_office']." 
					  AND (COALESCE(stato, 0) <> 3 AND COALESCE(stato, 0) <> 4 AND COALESCE(stato, 0) <> 5) 
					  AND data1 >= NOW() 
					  ORDER BY data1 DESC";
}
else if ($_SESSION['wflow_office'] === 2) {
    // ds
	$sel_open_requests = "SELECT rb_w_richieste.*, data1 FROM rb_w_richieste, rb_w_dati_richiesta 
					  WHERE rb_w_richieste.id_richiesta = rb_w_dati_richiesta.id_richiesta 
					  AND (COALESCE(ufficio, 1) = ".$_SESSION['wflow_office']." OR stato = 8)
					  AND (COALESCE(stato, 0) <> 3 AND COALESCE(stato, 0) <> 4 AND COALESCE(stato, 0) <> 5) 
					  AND data1 >= NOW() 
					  ORDER BY data1 DESC";
}
else {
	$sel_open_requests = "SELECT rb_w_richieste.*, data1 FROM rb_w_richieste, rb_w_dati_richiesta 
					  WHERE rb_w_richieste.id_richiesta = rb_w_dati_richiesta.id_richiesta 
					  AND (COALESCE(stato, 0) <> 3 AND COALESCE(stato, 0) <> 4 AND COALESCE(stato, 0) <> 5) 
					  AND data1 >= NOW() 
					  ORDER BY data1 DESC";
}

$open_requests = [];
$permessi_giornalieri_ata = $permessi_orari_ata = $permessi_giornalieri_doc = $permessi_orari_doc = $teachers = $ata = 0;
$res = $db->executeQuery($sel_open_requests);
if ($res->num_rows > 0) {
	while ($row = $res->fetch_assoc()) {
		$open_requests[$row['id_richiesta']] = $row;
		if ($row['workflow'] == 1) {
		    if ($row['area'] == 'teachers') {
				$permessi_giornalieri_doc++;
				$teachers++;
            }
            else {
				$permessi_giornalieri_ata++;
				$ata++;
            }
		}
		if ($row['workflow'] == 2) {
			if ($row['area'] == 'teachers') {
				$permessi_orari_doc++;
				$teachers++;
			}
			else {
				$permessi_orari_ata++;
				$ata++;
			}
		}
	}
}
?>
<div class="welcome">
	<p id="w_head" style="margin-bottom: 0; background-image: none">
		<i class="fa fa-id-card-o" style="position: relative; left: -30px; font-size: 1.4em"></i>
		<span style="position: relative; left: -20px" class="material_label normal">Pratiche personale docente aperte</span>
	</p>
	<p class="w_text" style="margin-top: 10px">
		<a href="richieste.php?idw=1&area=teachers" class="<?php if ($permessi_giornalieri_doc > 0) echo "attention" ?>">Permessi giornalieri: <?php echo $permessi_giornalieri_doc ?></a>
	</p>
	<p class="w_text" style="margin: 0">
		<a href="richieste.php?idw=2&area=teachers" class="<?php if ($permessi_orari_doc > 0) echo "attention" ?>">Permessi orari: <?php echo $permessi_orari_doc ?></a>
	</p>
</div>
<div class="welcome">
	<p id="w_head" style="margin-bottom: 0; background-image: none">
		<i class="fa fa-id-card" style="position: relative; left: -30px; font-size: 1.4em"></i>
		<span style="position: relative; left: -20px" class="material_label normal">Pratiche personale ATA aperte</span>
	</p>
	<p class="w_text" style="margin-top: 10px">
		<a href="richieste.php?idw=1&area=ata" class="<?php if ($permessi_giornalieri_ata > 0) echo "attention" ?>">Permessi giornalieri: <?php echo $permessi_giornalieri_ata ?></a>
	</p>
	<p class="w_text" style="margin-top: 0">
		<a href="richieste.php?idw=2&area=ata" class="<?php if ($permessi_orari_ata > 0) echo "attention" ?>">Permessi orari: <?php echo $permessi_orari_ata ?></a>
	</p>
</div>
