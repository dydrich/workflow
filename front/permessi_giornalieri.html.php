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

            $('#new_perm').on('click', function (event) {
				event.preventDefault();
				save_data();
            });

            $('.upd_req').on('click', function (event) {
               event.preventDefault();
               idr = $(this).data('id');
               status = $(this).data('status');
               req_status = $(this).data('req_status');
               if (status === 'open') {
                   if (req_status == 1 || req_status == 0) {
                       document.location.href = "permesso_giornaliero.php?idr="+idr;
                   }
                   else {
                       j_alert("error", "Modifica non permessa");
                   }
               }
            });

            $('a.del_link').click(function(event){
                event.preventDefault();
                req_to_del = $(this).data('id');
                j_alert("confirm", "Eliminare la richiesta?");
                //del_user(strs[1]);
            });

            $('#okbutton').on('click', function (event) {
                event.preventDefault();
                del_req();
            });
        });

        var save_data = function(){
            var url = "create_permission_uniqid.php";
            $.ajax({
                type: "POST",
                url: url,
                data: {idw: <?php echo $_SESSION['wflow_type'] ?>},
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
                        window.setTimeout(function () {
							window.document.location = "permesso_giornaliero.php?idr="+json.requestID;
                        }, 2500);
                    }
                }
            });
        };

        var del_req = function(){
            $('#confirm').fadeOut(10);
            var url = "request_manager.php";

            $.ajax({
                type: "POST",
                url: url,
                data: {action: 'del_request', idr: req_to_del},
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
                        j_alert("error", json.message);
                        console.log(json.dbg_message);
                    }
                    else {
                        j_alert("alert", json.message);
                        $('#row'+req_to_del).hide();
                    }
                }
            });
        };

	</script>
</head>
<body>
<?php include "../../../intranet/".$_SESSION['__area__']."/header.php" ?>
<?php include "navigation.php" ?>
<div id="main">
	<div id="right_col">
		<?php include "front_menu.php" ?>
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
			    $state = "open";
			    if ($row['data1'] < date('Y-m-d')) {
			        $state = "closed";
                }
                setlocale(LC_TIME, 'it_IT.utf8');
				$starting_day = strftime("%A %d %B %Y", strtotime($row['data1']));
				$ending_day = strftime("%A %d %B %Y", strtotime($row['data2']));
				$number_of_days = $row['intero2'];
				?>
				<div class="card" id="row<?php echo $row['id_richiesta'] ?>" style="<?php if($state === 'closed') echo "background-color: #EEEEEE"; ?>">
					<div class="card_title">
                        <a href="#" class="upd_req" data-id="<?php echo $row['id_richiesta'] ?>" data-req_status="<?php echo $row['stato'] ?>" data-status="<?php echo $state ?>">
                            Richiesta del <?php echo format_date(substr($row['data_ora'], 0, 10), SQL_DATE_STYLE, IT_DATE_STYLE, "/") ?> - Prot.: <?php echo $row['protocollo'] ?>
                        </a>
                        <?php if($state == 'open'): ?>
                        <div style="float: right; margin-right: 20px; color: #1E4389">
                            <a href="#" class="normal del_link" data-id="<?php echo $row['id_richiesta'] ?>">
                                <i class="fa fa-trash "></i>
                            </a>
                        </div>
                    <?php endif; ?>
					</div>
					<div class="card_varcontent">
                        <?php if ($number_of_days == 1): ?>
						Giorno richiesto: <strong><?php echo $starting_day ?></strong>
                        <?php else : ?>
                            Giorni richiesti: <strong><?php echo $number_of_days ?>, da <?php echo $starting_day ?> a <?php echo $ending_day ?></strong>
                        <?php endif; ?>
                        <div style="float: right; margin-right: 20px; color: #1E4389">
                            Stato: <span class="_bold"><?php echo strtoupper($_SESSION['statuses'][$row['stato']]['nome']) ?></span>
                        </div>
                        <div style="margin-top: 5px; margin-bottom: 5px">Codice pratica: <?php echo $row['codice_pratica'] ?></div>
					</div>
				</div>
				<?php
			}
		?>
		</div>
		<?php
		}
		?>
		<div class="normal" style="width: 90%; margin-left: 50px; margin-top: 30px">
			<a href="#" id="new_perm">
				<i class="fa fa-question-circle accent_color" style="margin-right: 10px; font-size: 1.3em"></i>
				Nuova richiesta
			</a>
		</div><p class="spacer"></p>
	</div>
</div>
<?php include "../../../intranet/".$_SESSION['__area__']."/footer.php" ?>
<div id="drawer" class="drawer" style="display: none; position: absolute">
	<div style="width: 100%; height: 430px">
		<div class="drawer_link"><a href="../../../intranet/<?php echo $_SESSION['__mod_area__'] ?>/index.php"><img src="../../../images/6.png" style="margin-right: 10px; position: relative; top: 5%" />Home</a></div>
		<div class="drawer_link"><a href="../../../intranet/<?php echo $_SESSION['__mod_area__'] ?>/profile.php"><img src="../../../images/33.png" style="margin-right: 10px; position: relative; top: 5%" />Profilo</a></div>
		<div class="drawer_link"><a href="../../modules/documents/load_module.php?module=docs&area=<?php echo $_SESSION['__mod_area__'] ?>"><img src="../../../images/11.png" style="margin-right: 10px; position: relative; top: 5%" />Documenti</a></div>
		<?php if(is_installed("com")){ ?>
			<div class="drawer_link"><a href="<?php echo $_SESSION['__path_to_root__'] ?>modules/communication/load_module.php?module=com&area=<?php echo $_SESSION['__mod_area__'] ?>"><img src="../../../images/57.png" style="margin-right: 10px; position: relative; top: 5%" />Comunicazioni</a></div>
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
</body>
</html>
