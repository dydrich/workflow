<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>Richiesta permesso</title>
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

            $('#new_perm').on('click', function (event) {
                event.preventDefault();
                save_data();
            });

            $('#data').datepicker({
                dateFormat: "dd/mm/yy"
            });

            $('#save_button').on('click', function (event) {
               event.preventDefault();
               save_data();
            });
        });

        var save_data = function(){
            var url = "request_manager.php";
            $.ajax({
                type: "POST",
                url: url,
                dataType: 'json',
                data: {action: 'save_data', idr: <?php echo $_GET['idr'] ?>, code: $('#code').text(), reason: $('#type').val(), date: $('#data').val(), req_type: <?php echo $request['workflow'] ?>},
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
                        window.setTimeout(function () {
                            window.document.location = "permessi_giornalieri.php";
                        }, 2500);
                    }
                }
            });
        };
	</script>
    <style>
        td {height: 25px}
    </style>
</head>
<body>
<?php include "../../../intranet/".$_SESSION['__area__']."/header.php" ?>
<?php include "../../../intranet/".$_SESSION['__area__']."/navigation.php" ?>
<div id="main">
	<div id="right_col">
		<?php include $_SESSION['__area__']."_menu.php" ?>
	</div>
	<div id="left_col">
        <form id="req_form" action="req_manager" style="width: 75%;">
            <table style="width: 90%; margin: auto">
                <tr class="bottom_decoration" >
                    <td style="width: 35%">
                        <label for="code">Codice pratica</label>
                    </td>
                    <td style="width: 65%" class="_bold">
                        <span id="code"><?php echo $request['codice_pratica'] ?></span>
                    </td>
                </tr>
                <tr>
                    <td style="width: 35%">
                        <label for="data">Data permesso</label>
                    </td>
                    <td style="width: 65%">
						<input type="text" id="data" name="data" style="width: 95%" value="<?php if($req_data != null) echo format_date($req_data['data1'], SQL_DATE_STYLE, IT_DATE_STYLE, "/"); ?>" />
                    </td>
                </tr>
                <tr>
                    <td style="width: 35%">
                        <label for="type">Tipo di permesso</label>
                    </td>
                    <td style="width: 65%">
                        <select id="type" name="type" style="width: 95%">
                            <option value="0">.</option>
                            <?php
                            while ($r = $res_t->fetch_assoc()) {
								?>
                                <option value="<?php echo $r['id'] ?>" <?php if($req_data != null && $req_data['intero1'] == $r['id']) echo 'selected' ; ?>><?php echo $r['motivo'] ?></option>
								<?php
							}
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td colspan="2"> </td>
                </tr>
                <tr>
                    <td colspan="2" class="_right">
                        <a href="#" id="save_button" class="material_link">Invia richiesta</a>
                    </td>
                </tr>
            </table>
        </form>
		<p class="spacer"></p>
	</div>
</div>
<?php include "../../../intranet/".$_SESSION['__area__']."/footer.php" ?>
<div id="drawer" class="drawer" style="display: none; position: absolute">
	<div style="width: 100%; height: 430px">
		<div class="drawer_link"><a href="../../../intranet/teachers/index.php"><img src="../../../images/6.png" style="margin-right: 10px; position: relative; top: 5%" />Home</a></div>
		<div class="drawer_link"><a href="../../../intranet/teachers/profile.php"><img src="../../../images/33.png" style="margin-right: 10px; position: relative; top: 5%" />Profilo</a></div>
		<div class="drawer_link"><a href="../../modules/documents/load_module.php?module=docs&area=teachers"><img src="../../../images/11.png" style="margin-right: 10px; position: relative; top: 5%" />Documenti</a></div>
		<?php if(is_installed("com")){ ?>
			<div class="drawer_link"><a href="<?php echo $_SESSION['__path_to_root__'] ?>modules/communication/load_module.php?module=com&area=teachers"><img src="../../../images/57.png" style="margin-right: 10px; position: relative; top: 5%" />Comunicazioni</a></div>
		<?php } ?>
		<?php if ($_SESSION['__user__']->hasConnectedAccounts()) {
			$acc = $_SESSION['__user__']->getConnectedAccounts();
			foreach ($acc as $ca) {
				$mat = $db->executeCount("SELECT rb_materie.materia FROM rb_materie, rb_docenti WHERE rb_docenti.materia = id_materia AND id_docente = $ca");
				?>
				<div class="drawer_link">
					<a href="<?php echo $_SESSION['__path_to_root__'] ?>admin/sudo_manager.php?action=sudo&area=3&uid=<?php echo $ca ?>">
						<img src="../../../images/14.png" style="margin-right: 10px; position: relative; top: 5%"/>
						Cambia utente (<?php echo $mat ?>)</a>
				</div>
				<?php
			}
		}
		?>
	</div>
	<?php if (isset($_SESSION['__sudoer__'])): ?>
		<div class="drawer_lastlink"><a href="<?php echo $_SESSION['__path_to_root__'] ?>admin/sudo_manager.php?action=back"><img src="../../../images/14.png" style="margin-right: 10px; position: relative; top: 5%" />DeSuDo</a></div>
	<?php endif; ?>
	<div class="drawer_lastlink"><a href="../../../shared/do_logout.php"><img src="../../../images/51.png" style="margin-right: 10px; position: relative; top: 5%" />Logout</a></div>
</div>
<div id="adm_pwd" style="display: none">
	<p>
		<label for="pass" class="material_label">Inserisci la password</label>
		<input type="password" class="material_input" id="pass" name="pass" style="width: 200px" />
	</p>
	<p style="margin-top: 45px; text-align: right">
		<a href="#" id="su_go" class="material_link">SuperUser</a>
	</p>
</div>
</body>
</html>