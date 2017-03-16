<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <title><?php print $_SESSION['__config__']['intestazione_scuola'] ?>:: gestione workflow</title>
    <link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:400,300,400italic,600,600italic,700,700italic,900,200' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="../../font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/reg.css" type="text/css" media="screen,projection" />
    <link rel="stylesheet" href="../../css/general.css" type="text/css" media="screen,projection" />
    <link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/jquery-ui.min.css" type="text/css" media="screen,projection" />
    <script type="text/javascript" src="../../js/jquery-2.0.3.min.js"></script>
    <script type="text/javascript" src="../../js/jquery-ui-1.10.3.custom.min.js"></script>
    <script type="text/javascript" src="../../js/page.js"></script>
    <script>
        $(function() {
            load_jalert();
            setOverlayEvent();

            $('#reg').on('click', function (event) {
                event.preventDefault();
                save_data();
            });

            $('#nome_status').focus();
        });

        var save_data = function(){
            var url = "status_manager.php";

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
                            document.location.href = "status.php";
                        }, 2500);
                    }
                }
            });
        }
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
        <form id="my_form" method="post" style="margin-top: 30px; text-align: left; width: 460px; margin-left: auto; margin-right: auto">
            <table style="width: 400px; margin-left: auto; margin-right: auto; margin-top: 30px; margin-bottom: 5px">
	        <tr>
	            <td style="width: 100px; padding-left: 10px;">Nome</td>
	            <td style="width: 250px; ">
	            	<input class="form_input" type="text" name="nome_status" id="nome_status" style="width: 250px" value="<?php if(isset($status)) print $status ?>"  />
	            </td>
	        </tr>
	        <tr>
	        	<td align="left" style="width: 100px; padding-left: 10px">Permessi</td>
	            <td style="width: 250px">
	                <?php
	                while($_uf = $res_uffici->fetch_assoc()){
	                    $checked = "";
	                    if(isset($_GET['id']) && $_GET['id'] != 0){
	                        if($_uf['codice_permessi']&$perms)
	                        //if($user['gruppi']&$gr['codice'])
	                            $checked = "checked";
	                    }
	                ?>
	                <input class="form_input" type="checkbox" style="margin: auto" value="<?php print $_uf['codice_permessi'] ?>" id="permessi[]" name="permessi[]" <?php print $checked ?> />&nbsp;&nbsp;&nbsp;
                        <?php print $_uf['nome'] ?>
                        <br />
                    <?php } ?>
	            </td>
	        </tr>
                <tr>
                    <td colspan="2">&nbsp;
                        <input type="hidden" name="action" id="action" value="<?php echo $action ?>">
                        <input type="hidden" name="id" id="id" value="<?php echo $_REQUEST['id'] ?>">
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