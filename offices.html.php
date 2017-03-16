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
    <script type="text/javascript">
        $(function(){
            load_jalert();
            setOverlayEvent();

            $('#off_detail').on('click', function (event) {
                event.preventDefault();
                document.location.href = 'office.php?id=0';
            });

            $('.mod').on('click', function (event) {
               event.preventDefault();
               id_off = $(this).data('id');
                document.location.href = 'office.php?id='+id_off;
            });

            $('a.del_link').click(function(event){
                event.preventDefault();
                office_to_del = $(this).data('office');
                j_alert("confirm", "Eliminare l'ufficio?");
                //del_user(strs[1]);
            });

            $('#okbutton').on('click', function (event) {
                event.preventDefault();
                del_office();
            });
        });
        var office_to_del = 0;
        var del_office = function(){
            $('#confirm').fadeOut(10);
            var url = "office_manager.php";

            $.ajax({
                type: "POST",
                url: url,
                data: {action: 2, id: office_to_del},
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
                        $('#row_'+office_to_del).hide();
                    }
                }
            });
        };
    </script>
    <title>Amministrazione</title>
</head>
<body>
<?php include "../../intranet/{$_SESSION['__mod_area__']}/header.php" ?>
<?php include "navigation.php" ?>
    <div id="main">
        <div id="right_col">
            <?php include "menu.php" ?>
        </div>
        <div id="left_col">
            <div style="position: absolute; top: 75px; left: 53%; margin-bottom: -5px" class="rb_button">
                <a href="#" id="off_detail">
                    <img src="../../images/39.png" style="padding: 12px 0 0 12px" />
                </a>
            </div>
            <div id="card_container" class="card_container" style="margin-top: 30px">
                <?php
                if (count($uffici) == 0) {
                    ?>
                    <div class="card">
                        <div class="card_title card_nocontent _bold _center normal">
							Nessun ufficio presente
                        </div>
                    </div>
                    <?php
                }
                else {
					foreach ($uffici as $k => $ufficio) {
						?>
                        <div class="card" id="row_<?php echo $k ?>">
                            <div class="card_title card_nocontent normal">
                                <a href="#" class="mod normal" data-id="<?php echo $k ?>">
                                    <?php echo $ufficio['nome'] ?>
                                </a>
                                <div style="float: right; margin-right: 20px; color: #1E4389">
                                    <a href="#" class="normal del_link" data-office="<?php echo $k ?>">
                                        <i class="fa fa-trash "></i>
                                    </a>
                                </div>
                            </div>
                        </div>
						<?php
					}
				}
                ?>
            </div>
        </div>
    </div>
	<?php include "../../intranet/{$_SESSION['__mod_area__']}/footer.php" ?>
    <div id="drawer" class="drawer" style="display: none; position: absolute">
        <div style="width: 100%; height: 430px">
            <div class="drawer_link"><a href="<?php echo $_SESSION['__modules__']['com']['path_to_root'] ?>intranet/<?php echo $_SESSION['__mod_area__'] ?>/index.php"><img src="../../images/6.png" style="margin-right: 10px; position: relative; top: 5%" />Home</a></div>
            <div class="drawer_link"><a href="<?php echo $_SESSION['__modules__']['com']['path_to_root'] ?>intranet/<?php echo $_SESSION['__mod_area__'] ?>/profile.php"><img src="../../images/33.png" style="margin-right: 10px; position: relative; top: 5%" />Profilo</a></div>
			<?php if (!$_SESSION['__user__'] instanceof ParentBean) : ?>
                <div class="drawer_link"><a href="<?php echo $_SESSION['__modules__']['com']['path_to_root'] ?>modules/documents/load_module.php?module=docs&area=<?php echo $_SESSION['__mod_area__'] ?>"><img src="<?php echo $_SESSION['__modules__']['wflow']['path_to_root'] ?>images/11.png" style="margin-right: 10px; position: relative; top: 5%" />Documenti</a></div>
			<?php endif; ?>
			<?php if(is_installed("com")){ ?>
                <div class="drawer_link"><a href="<?php echo $_SESSION['__modules__']['com']['path_to_root'] ?>modules/communication/load_module.php?module=com&area=<?php echo $_SESSION['__mod_area__'] ?>"><img src="<?php echo $_SESSION['__modules__']['wflow']['path_to_root'] ?>images/57.png" style="margin-right: 10px; position: relative; top: 5%" />Comunicazioni</a></div>
			<?php } ?>
        </div>
		<?php if (isset($_SESSION['__sudoer__'])): ?>
            <div class="drawer_lastlink"><a href="<?php echo $_SESSION['__modules__']['com']['path_to_root'] ?>admin/sudo_manager.php?action=back"><img src="<?php echo $_SESSION['__modules__']['wflow']['path_to_root'] ?>images/14.png" style="margin-right: 10px; position: relative; top: 5%" />DeSuDo</a></div>
		<?php endif; ?>
        <div class="drawer_lastlink"><a href="<?php echo $_SESSION['__modules__']['com']['path_to_root'] ?>shared/do_logout.php"><img src="<?php echo $_SESSION['__modules__']['wflow']['path_to_root'] ?>images/51.png" style="margin-right: 10px; position: relative; top: 5%" />Logout</a></div>
    </div>
</body>
</html>
