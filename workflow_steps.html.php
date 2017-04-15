<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title><?php print $_SESSION['__config__']['intestazione_scuola'] ?>:: dettaglio workflow</title>
	<link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:400,300,400italic,600,600italic,700,700italic,900,200' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="../../font-awesome/css/font-awesome.min.css">
	<link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/reg.css" type="text/css" media="screen,projection" />
	<link rel="stylesheet" href="../../css/general.css" type="text/css" media="screen,projection" />
	<link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/jquery-ui.min.css" type="text/css" media="screen,projection" />
	<script type="text/javascript" src="../../js/jquery-2.0.3.min.js"></script>
	<script type="text/javascript" src="../../js/jquery-ui-1.10.3.custom.min.js"></script>
	<script type="text/javascript" src="../../js/page.js"></script>
	<script>
        $(function(){
            load_jalert();
            setOverlayEvent();

            $('#new_flow').on('click', function (event) {
                event.preventDefault();
                flow_detail(0);
            });

            $('.steps').on('click', function (event) {
                event.preventDefault();
                w_id = $(this).data('id');
            });
			<?php
			for ($kx = 1; $kx <= $wflow['num_step']; $kx++) {
			?>
            $('#buttonset<?php echo $kx ?>').buttonset();
			<?php
			}
			?>

            $('.save_link').on('click', function (event) {
               _step = $(this).data("step");
               insert_step(_step);
            });
        });

        var insert_step = function(step){
            var url = "workflow_manager.php";

            $.ajax({
                type: "POST",
                url: url,
                data: $('#form'+step).serialize(true),
                dataType: 'json',
                error: function() {
                    j_alert("error", "Errore di trasmissione dei dati");
                },
                succes: function() {

                },
                complete: function(data){
                    r = data.responseText;
                    if(r == "null"){
                        return false;
                    }
                    var json = $.parseJSON(r);
                    if (json.status == "kosql"){
                        sqlalert();
                        console.log(json.dbg_message);
                    }
                    else if(json.status == "ko") {
                        j_alert("error", "Impossibile completare l'operazione richiesta. Riprovare tra qualche secondo o segnalare l'errore al webmaster");
                        return;
                    }
                    else {
                        j_alert("alert", json.message);
                        $('#step'+step).css({backgroundColor: '#EEEEEE'});
                    }
                }
            });
        };

        var flow_detail = function (w_id) {
            document.location.href = 'dettaglio_workflow.php?id='+w_id;
        };

	</script>
</head>
<body>
<?php include "../../intranet/{$_SESSION['__mod_area__']}/header.php" ?>
<?php include "navigation.php" ?>
<div id="main">
	<div id="right_col">
		<?php include "menu.php" ?>
	</div>
	<div id="left_col">
		<?php
        $steps = $workflow->getSteps();
		for ($i = 0; $i < $wflow['num_step']; $i++) {
			?>
		<fieldset style="width: 85%; margin: auto" id="step<?php echo $i + 1 ?>">
            <legend>Step <?php echo $i + 1 ?></legend>
            <form id="form<?php echo $i + 1 ?>" action="workflow_manager.php" method="post" class="no_border">
			<table style="width: 75%; margin: auto">
				<tr>
					<td style="width: 40%">
						<label for="num">Ordine</label>
					</td>
					<td style="width: 60%">
						<select id="num" name="num" style="width: 90%">
							<?php
							for ($x = 1; $x <= $wflow['num_step']; $x++) {
							?>
							<option value="<?php echo $x ?>" <?php if (($i + 1) == $x) echo "selected" ?>><?php echo $x ?></option>
							<?php
							}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td style="width: 40%">
						<label for="step">Tipo step</label>
					</td>
					<td style="width: 60%">
						<select id="step" name="step" style="width: 90%">
							<?php
							$res_steps->data_seek(0);
							while ($row = $res_steps->fetch_assoc()) {
								?>
							<option value="<?php echo $row['id_step'] ?>" <?php if(isset($steps[$i+1]) && $steps[$i+1]['step_type'] == $row['id_step']) echo "selected"; ?>><?php echo $row['descrizione'] ?></option>
								<?php
							}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td style="width: 40%">
						<label for="office">Ufficio</label>
					</td>
					<td style="width: 60%">
						<select id="office" name="office" style="width: 90%">
							<?php
							$res_offices->data_seek(0);
							while ($office = $res_offices->fetch_assoc()) {
								?>
								<option value="<?php echo $office['id_ufficio'] ?>" <?php if(isset($steps[$i+1]) && $steps[$i+1]['office'] == $office['id_ufficio']) echo "selected"; ?>><?php echo $office['nome'] ?></option>
								<?php
							}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td style="width: 40%">
						<label for="status">Stato iniziale</label>
					</td>
					<td style="width: 60%">
						<select id="status" name="status" style="width: 90%">
							<?php
							$res_status->data_seek(0);
							while ($status = $res_status->fetch_assoc()) {
								?>
								<option value="<?php echo $status['id_status'] ?>" <?php if(isset($steps[$i+1]) && $steps[$i+1]['status'] == $status['id_status']) echo "selected"; ?>><?php echo $status['nome'] ?></option>
								<?php
							}
							?>
						</select>
					</td>
				</tr>
                <tr>
                    <td style="width: 40%">
                        <label for="status">Stati in uscita</label>
                    </td>
                    <td style="width: 60%" id="buttonset<?php echo $i + 1 ?>">
							<?php
							$res_status->data_seek(0);
							while ($status = $res_status->fetch_assoc()) {
							    $checked = '';
							    if (isset($steps[$i+1]) && in_array($status['id_status'], $steps[$i+1]['statuses'])) {
							        $checked = 'checked';
                                }
								?>
                                <input type="checkbox" name="final_statuses_step<?php echo $i + 1 ?>[]" id="st<?php echo $i + 1 ?>fs_<?php echo $status['id_status'] ?>" value="<?php echo $status['id_status'] ?>" <?php echo $checked; ?> />
                                <label for="st<?php echo $i + 1 ?>fs_<?php echo $status['id_status'] ?>" style="font-size: 0.8em"><?php echo $status['nome'] ?></label>
								<?php
							}
							?>
                    </td>
                </tr>
				<tr>
					<td colspan="2">
						&nbsp;<input type="hidden" name="action" value="add_step" />
                        <input type="hidden" name="_i" value="<?php echo $id_workflow ?>" />
					</td>
				</tr>
				<tr>
					<td colspan="2" class="_right">
						<a href="#" class="material_link save_link" data-step="<?php echo $i + 1 ?>" style="margin-right: 6%">Registra step <?php echo $i + 1 ?></a>
					</td>
				</tr>
			</table>
            </form>
		</fieldset>
			<?php
		}
		?>
		<p class="spacer"></p>
	</div>
</div>
<?php include "../../intranet/{$_SESSION['__mod_area__']}/footer.php" ?>
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
