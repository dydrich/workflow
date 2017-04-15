<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>Area docenti</title>
	<link rel="stylesheet" href="<?php echo $_SESSION['__path_to_root__'] ?>font-awesome/css/font-awesome.min.css">
	<link href='https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,300,400italic,600,600italic,700,700italic,900,200' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="<?php echo $_SESSION['__path_to_root__'] ?>css/general.css" type="text/css" media="screen,projection" />
	<link rel="stylesheet" href="<?php echo $_SESSION['__path_to_root__'] ?>css/site_themes/<?php echo getTheme() ?>/reg.css" type="text/css" media="screen,projection" />
	<link rel="stylesheet" href="<?php echo $_SESSION['__path_to_root__'] ?>css/site_themes/<?php echo getTheme() ?>/jquery-ui.min.css" type="text/css" media="screen,projection" />
	<script type="text/javascript" src="<?php echo $_SESSION['__path_to_root__'] ?>js/jquery-2.0.3.min.js"></script>
	<script type="text/javascript" src="<?php echo $_SESSION['__path_to_root__'] ?>js/jquery-ui-1.10.3.custom.min.js"></script>
	<script type="text/javascript" src="<?php echo $_SESSION['__path_to_root__'] ?>js/page.js"></script>
	<script type="text/javascript" src="<?php echo $_SESSION['__path_to_root__'] ?>js/md5-min.js"></script>
	<script type="text/javascript">
        $(function(){
            load_jalert();
            setOverlayEvent();
        });
	</script>
</head>
<body>
<?php include "../../intranet/".$_SESSION['__area__']."/header.php" ?>
<?php include "navigation.php" ?>
<div id="main">
	<div id="right_col">
		<?php include "menu.php" ?>
	</div>
	<div id="left_col">
		<?php
		if($res_perms->num_rows < 1){
			?>
			<div class="_bold _center" style="width: 90%; margin: 30px auto; font-size: 1.1em">Nessuna richiesta in archivio</div>
			<?php
		}
		else {
			?>
			<div class="card_container">
				<?php
				while ($row = $res_perms->fetch_assoc()) {
					$w = new \eschool\Workflow($row['id_workflow'], null, null, null, new MySQLDataLoader($db));
					if ($row['id_workflow'] == 1) {
						$dpd = new \eschool\DailyPermitAdministrativeRequest($row['id_richiesta'], $row['codice_pratica'], $row['id_workflow'], new MySQLDataLoader($db), $w);
					}
					else {
						$dpd = new \eschool\LeaveAdministrativeRequest($row['id_richiesta'], $row['codice_pratica'], $row['id_workflow'], new MySQLDataLoader($db), $w);
					}

					$current_step = $dpd->getCurrentStep();
					if (!$current_step) {
						$now = 0;
					}
					else {
						$now = $current_step['step'];
					}
					$can_advance = true;
					$ns = $w->getNextStep($now);

					if ($_SESSION['wflow_office'] != $ns['office']) {
						$can_advance = false;
					}

					setlocale(LC_TIME, 'it_IT.utf8');
					$giorno_str = strftime("%A %d %B %Y", strtotime($row['data1']));

					$user = $db->executeCount("SELECT CONCAT_WS(' ', cognome, nome) FROM rb_utenti WHERE uid = {$row['richiedente']}");
					$step_req = $db->executeQuery("SELECT * FROM rb_w_step_richieste WHERE id_richiesta = {$row['id_richiesta']} ORDER BY step DESC");
					$steps = $db->executeQuery("SELECT step, ufficio, ordine, status, descrizione FROM rb_w_step_workflow, rb_w_step 
											  	WHERE id_workflow = {$row['id_workflow']} 
											  	AND id_step = step");
					?>
					<div class="card" id="row<?php echo $row['id_richiesta'] ?>">
						<div class="card_title">
							<?php echo $user ?> (<?php if($row['id_workflow'] < 3) echo "docente"; else echo "ata" ?>) - <?php echo $giorno_str ?>
						</div>
						<div class="card_varcontent">
							Protocollo: <strong><?php echo $row['protocollo'] ?></strong>
							<div style="float: right; margin-right: 20px">Codice pratica: <?php echo $row['codice_pratica'] ?></div>
							<?php if($_REQUEST['idw'] == 1 || $_REQUEST['idw'] == 3): ?>
								<div style="margin-top: 5px; margin-bottom: 5px">Motivo: <span class="_bold"><?php echo $row['motivo'] ?></span></div>
							<?php endif; ?>
							<?php if($_REQUEST['idw'] == 2 || $_REQUEST['idw'] == 4){
								$leave = new \eschool\Leave($row['id_richiesta'], $row['codice_pratica'], $row['id_workflow'], new MySQLDataLoader($db));
								?>
								<div style="margin-top: 5px; margin-bottom: 5px">
									Ore di permesso richieste: <span class="_bold"><?php echo $leave->getNumberOfHours() ?></span>
									<span style="margin-left: 2px">dalle <?php echo substr($leave->getEnter(), 0, 5) ?></span> alle <?php echo substr($leave->getExit(), 0, 5) ?>
								</div>
							<?php } ?>
							<div>
								Stato: <span class="_bold"><?php echo $_SESSION['statuses'][$row['stato']]['nome'] ?></span>
							</div>
						</div>
					</div>
					<?php
				}
				?>
			</div>
			<?php
		}
		?>
		<p class="spacer"></p>
	</div>
</div>
<?php include "../../intranet/".$_SESSION['__area__']."/footer.php" ?>
<div id="drawer" class="drawer" style="display: none; position: absolute">
	<div style="width: 100%; height: 430px">
		<div class="drawer_link"><a href="<?php echo $_SESSION['__modules__']['wflow']['path_to_root'] ?>intranet/<?php echo $_SESSION['__mod_area__'] ?>/index.php"><img src="../../images/6.png" style="margin-right: 10px; position: relative; top: 5%" />Home</a></div>
		<div class="drawer_link"><a href="<?php echo $_SESSION['__modules__']['wflow']['path_to_root'] ?>intranet/<?php echo $_SESSION['__mod_area__'] ?>/profile.php"><img src="../../images/33.png" style="margin-right: 10px; position: relative; top: 5%" />Profilo</a></div>
		<?php if (!$_SESSION['__user__'] instanceof ParentBean) : ?>
			<div class="drawer_link"><a href="<?php echo $_SESSION['__modules__']['wflow']['path_to_root'] ?>modules/documents/load_module.php?module=docs&area=<?php echo $_SESSION['__mod_area__'] ?>"><img src="<?php echo $_SESSION['__modules__']['wflow']['path_to_root'] ?>images/11.png" style="margin-right: 10px; position: relative; top: 5%" />Documenti</a></div>
		<?php endif; ?>
		<?php if(is_installed("com")){ ?>
			<div class="drawer_link"><a href="<?php echo $_SESSION['__modules__']['wflow']['path_to_root'] ?>modules/communication/load_module.php?module=com&area=<?php echo $_SESSION['__mod_area__'] ?>"><img src="<?php echo $_SESSION['__modules__']['wflow']['path_to_root'] ?>images/57.png" style="margin-right: 10px; position: relative; top: 5%" />Comunicazioni</a></div>
		<?php } ?>
	</div>
	<?php if (isset($_SESSION['__sudoer__'])): ?>
		<div class="drawer_lastlink"><a href="<?php echo $_SESSION['__modules__']['wflow']['path_to_root'] ?>admin/sudo_manager.php?action=back"><img src="<?php echo $_SESSION['__modules__']['wflow']['path_to_root'] ?>images/14.png" style="margin-right: 10px; position: relative; top: 5%" />DeSuDo</a></div>
	<?php endif; ?>
	<div class="drawer_lastlink"><a href="<?php echo $_SESSION['__modules__']['wflow']['path_to_root'] ?>shared/do_logout.php"><img src="<?php echo $_SESSION['__modules__']['wflow']['path_to_root'] ?>images/51.png" style="margin-right: 10px; position: relative; top: 5%" />Logout</a></div>
</div>
</body>
</html>