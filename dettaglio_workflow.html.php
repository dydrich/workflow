<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <title>gestione workflow</title>
    <link rel="stylesheet" href="../../font-awesome/css/font-awesome.min.css">
    <link href='https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,300,400italic,600,600italic,700,700italic,900,200' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="../../css/general.css" type="text/css" />
    <link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/reg.css" type="text/css" />
    <link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/jquery-ui.min.css" type="text/css" media="screen,projection" />
    <script type="text/javascript" src="../../js/jquery-2.0.3.min.js"></script>
    <script type="text/javascript" src="../../js/jquery-ui-1.10.3.custom.min.js"></script>
    <script type="text/javascript" src="../../js/page.js"></script>
    <script type="text/javascript">
        $(function() {
            load_jalert();
            setOverlayEvent();
            $("#buttonset").buttonset();

            $('#reg').on('click', function (event) {
                event.preventDefault();
                save_data();
            });
        });

        var save_data = function(){
            var url = "workflow_manager.php";

            $.ajax({
                type: "POST",
                url: url,
                data: $('#my_form').serialize(true),
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
                            document.location.href = "workflow.php";
                        }, 2500);
                    }
                }
            });
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
        <form id="my_form" method="post" style="margin-top: 30px; text-align: left; width: 660px; margin-left: auto; margin-right: auto">
	    <table style="width: 580px; margin-right: auto; margin-left: auto">
	        <tr>
	            <td style="width: 150px; padding-left: 10px; ">Flusso</td>
	            <td style="width: 430px; ">
	            	<input class="form_input" type="text" name="nome_flusso" id="nome_flusso" style="width: 430px" value="<?php if(isset($flow)) print $flow['richiesta']  ?>"  />
	            </td>
	        </tr>
	        <tr>
	            <td style="width: 150px; padding-left: 10px">Step</td>
	            <td style="width: 30px; ">
	            	<input class="form_input" type="text" name="num_step" id="num_step" style="width: 30px" value="<?php if(isset($flow)) print $flow['num_step'] ?>" />
	            </td>
	        </tr>
	        <tr>
	        	<td style="width: 150px; padding-left: 10px">Permessi</td>
	            <td style="width: 430px" id="buttonset">
	                <?php
                    $_g = null;
					if(isset($_POST['gruppi'])) {
						$_g = $_POST['gruppi'];
					}
					else if(isset($flow)) {
						$_g = $flow['gruppi'];
					}
	                while($_uf = $res_gruppi->fetch_assoc()){
	                    $checked = "";
	                    if(isset($_REQUEST['id']) && $_REQUEST['id'] != 0){
	                        if(intval($_uf['permessi'])&$_g) {
								$checked = "checked";
							}
	                    }
	                ?>
	                <input type="checkbox" style="margin: auto" value="<?php echo $_uf['permessi'] ?>" name="gruppi[]" id="gr_<?php echo $_uf['permessi'] ?>" <?php echo $checked ?> />
                    <label for="gr_<?php echo $_uf['permessi'] ?>"><?php echo $_uf['nome'] ?></label>
	                <?php } ?>
	            </td>
	        </tr>
            <tr>
                <td colspan="2">
                    <input type="hidden" name="_i" id="_i" value="<?php echo $_i ?>">
                    <input type="hidden" name="action" id="action" value="<?php echo $action ?>">
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: right; margin-right: 50px; padding-top: 20px">
                    <a href="#" id="reg" class="material_link">Registra</a>
                </td>
            </tr>
	    </table>
        </form>
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