<?php
error_reporting(1);
session_start();

require_once('include.php');
include "class/Ranking.class.php";
include "class/nome_patente.php";

$result_p = pg_query("SELECT * FROM accounts WHERE rank<52 and player_name<>''");
$num_players = pg_num_rows($result_p);

$result1 = pg_query("SELECT * FROM accounts WHERE online='t'");
$num_rows1 = pg_num_rows($result1);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <title>AzurePB - Point Blank Private Server</title>
    <link href="http://fonts.googleapis.com/css?family=Oswald:400,700,300" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="stylesheets/main.css" />
    <link rel="stylesheet" href="stylesheets/devices.css" />
    <link rel="stylesheet" href="stylesheets/post.css" />
    <link rel="stylesheet" href="stylesheets/botoes.css" />
    <link rel="stylesheet" href="stylesheets/paralax_slider.css" />
    <link rel="stylesheet" href="stylesheets/jquery.fancybox.css?v=2.1.2" type="text/css" media="screen" />
    <script src="stylesheets/js/jquery-1.11.0.min.js"></script>
    <script src="stylesheets/js/lightbox.min.js"></script>
    <script type="text/javascript" src="stylesheets/js/html5lightbox/jquery.js"></script>
    <script type="text/javascript" src="stylesheets/js/html5lightbox/html5lightbox.js"></script>
    <script src="https://www.google.com/recaptcha/api.js"></script>
    <script src="stylesheets/js/cycle.js" type="text/javascript"></script>
    <script type="text/javascript" src="stylesheets/javascript/jquery.min.js"></script>
    <link type="text/css" href="stylesheets/mypb2.css" rel="stylesheet" />
    <script type="text/javascript" src="stylesheets/javascript/mypb.js"></script>
    <link href='http://fonts.googleapis.com/css?family=Open Sans:400,300,700' rel='stylesheet' type='text/css'>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js"></script>

    </script>
</head>

<body>
    <div id="footer_image">
        <div id="main_wrapper">
            <div id="logo"> <a href="index.php"><img alt="alt_example" style="margin-left: 25%;margin-bottom:-5%" src="img/Logo-topo.png" /></a>
                <div id="social_ctn">
                </div>
            </div>
            <div id="main_in">
                <div id="menu_wrapper">
                    <div id="menu_left"></div>
                    <ul id="menu">
                        <li><a href="index.php">Home</a></li>
						<li><a href="/forum">Fórum</a></li>
                        <li><a href="?pg=ranking">Ranking</a>
                            <ul>
                                <li><a href="?pg=ranking">Ranking players</a></li>
                                <li class="drop_last"><a href="?pg=clan">Ranking Clan</a></li>
                            </ul>
                        </li>
                        <?php if (isset($_SESSION['username'])) { ?>
                            <li><a href="?pg=downloads">Downloads</a></li>
                            <li><a href="?pg=perfil">Painel do player</a>
                                <ul>
                                    <li><a href="?pg=changepw">Trocar Senha</a></li>
                                    <li><a href="?pg=changemail">Trocar Email</a></li>
                                    <li class="drop_last"><a href="./suporte">Suporte</a></li>
                                    
                                </ul>
                            </li>
                            
                        <?php } else { ?>
                            <li><a href="?pg=downloads">Downloads</a></li>
                            <li><a href="./cadastrar">Cadastrar</a></li>
                        <?php } ?>
                        
                        <li><a href="#">Regras</a>
                            <ul>
                                <li><a href="?pg=camp">Camp Regras</a></li>
                                <li class="drop_last"><a href="?pg=regras">Regras Gerais</a></li>
                            </ul>
                        </li>
                    </ul>
                    <a href="#" id="pull">Menu</a>
                    <div id="menu_right"></div>
                </div>

                <?php
                function limitarTexto($texto, $limite)
                {
                    $texto = substr($texto, 0, strrpos(substr($texto, 0, $limite), ' ')) . '...';
                    return $texto;
                }
                ?>
                <div id="main_news_wrapper">
                    <div id="row">
                        <!-- Left wrapper Start -->
                        <?php
                        if (@$_GET['pg'] == "") {
                            $_GET['pg'] = 'home';
                        }
                        switch ($_GET['pg']) {
                            case 'home':
                                include 'paginas/home.php';
                                break;
                            case 'vip':
                                include 'paginas/vip.php';
                                break;
                            case 'ativarpin':
                                include 'paginas/ativapin.php';
                                break;
                            case 'changepw':
                                include 'paginas/changepassword.php';
                                break;
                            case 'changemail':
                                include 'paginas/changeemail.php';
                                break;
                            case 'ranking':
                                include 'paginas/ranking.php';
                                break;
                            case 'clan':
                                include 'paginas/clan.php';
                                break;
                            case 'getcash':
                                include 'paginas/getcash.php';
                                break;
                            case 'ler_noticia':
                                include 'paginas/noticia.php';
                                break;
                            case 'allnews':
                                include 'paginas/allnews.php';
                                break;
                            case 'keyid':
                                include 'paginas/keyid.php';
                                break;
                            case 'camp':
                                include 'paginas/campregras.php';
                                break;
                            case 'perfil':
                                include 'paginas/perfil.php';
                                break;
                            case 'regras':
                                include 'paginas/regrasgerais.php';
                                break;
                            case 'downloads':
                                include 'paginas/downloads.php';
                                break;
                        }
                        ?>
                        <!-- Left wrapper end -->

                        <!-- Right wrapper Start -->
                        <div id="right_wrapper">
                            <?php if (!isset($_SESSION['username'])) { ?>
                                <div class="normal">
                                    <div class="header">Login</div>
                                    <div id="search">
                                        <form action="login.php" method="post">
                                          <input id="login" name="username" type="text" required="" class="id off" placeholder="Informe seu login" maxlength="16" onfocus="this.className='id off';if(this.value=='usuario'){this.value='';}">
                                            <input id="pass" name="password" type="password" required="" class="id off" placeholder="Digite sua senha" maxlength="16" onfocus="this.className='id off';if(this.value=='senha'){this.value='';}">
                                           <br>

											<span><a id="cop_text2" href="recuperar.php" style="padding-left: 18px;">Esqueci minha senha</a></span><br />
                                            <button style="margin-left: 230px;margin-top: -93px;margin-bottom: 31px;width: 80px;height: 74px;" type="submit" name="submit" class="read_more2">Logar</button>
                                        </form>
                                    </div>
                                </div>
                            <?php } else {
                                $inicio = $_SESSION['username'];
                                $rank = pg_query("SELECT * FROM accounts WHERE login = '$inicio'");
                                $ranking = pg_fetch_assoc($rank);

                                $rank = new Ranking();
                            ?>
                                <div class="normal">
                                    <div class="header">Minha conta</div>
                                    <ul class="blockLogin2S1">

                                        <li style="margin-top: 6px;">
                                            <span><?php if (isset($ranking)){ echo "<img src='Ranking/PAT/" . $ranking['rank'] . ".gif' width='20' />"; }?></span>
                                            <span style="font-family: monospace;vertical-align: super;margin-left: 5px;font-size: 14px;color: rgb(255, 255, 255);margin-bottom:-20px;"><?php echo "" . $ranking['player_name'] . ""; ?>
                                                <span style="margin-left: 75px;"><br>
                                                
                                                    <a href="logout.php" target="_self" style="text-decoration: none;"><button style="margin: -24px 0px 2px  170px;width: 74px;margin-left:69%;margin-top:-20px;" class="read_more">Sair</button></a>
                                                    <?php if ($ranking['rank'] > 52) { ?>
                                                    <a href="/painel_de_adm/" target="_self" style="text-decoration: none;"><button style="margin: -24px 0px 2px  170px;width: 74px;margin-top:1px;" class="read_more">Painel de ADM</button></a>
                        <?php } ?>
                                                </span>
                                            </span>
                                        </li>
                                    </ul>
                                    <li class="blcell3">
                                        <dl class="blockLogin2S2">
                                            <dt>Rank</dt>
                                            <dd><span style="color:#a93232;"><?php $rank->nome($ranking['rank']); ?></span></dd>
                                        </dl>
                                        <dl class="blockLogin2S2">
                                            <dt>Clan</dt>
                                            <dd><span style="color:White;"><?php $rank->clan($ranking['clan_id']); ?></span></dd>
                                        </dl>
                                        <dl class="blockLogin2S2">
                                            <dt>Cash</dt>
                                            <dd><span style="color:gold;"><?php echo "" . str_replace(",", ".", number_format($ranking['money'])) . ""; ?></span></dd>
                                        </dl>
                                        <dl class="blockLogin2S2">
                                            <dt>Gold</dt>
                                            <dd><span style="color:lime;"><?php echo "" . str_replace(",", ".", number_format($ranking['gp'])) . ""; ?></span></dd>
                                        </dl>
                                        <dl class="blockLogin2S2">
                                            <dt>Conta</dt>
                                            <dd><span style="color:white;"><?php $rank->tipodeconta($ranking['access_level']); ?></span></dd>
                                        </dl>
										<dl class="blockLogin2S2">
                                            <dt>Token</dt>
                                            <dd style="text-transform:none">  <a onclick="CopyToken();" style="color:white;"><?php echo $ranking['token']; ?></a></dd>
                                        </dl>
										
                                    </li>
                                </div>
                            <?php } ?>
                           
                            <div class="normal">
                                <div class="header">Status do servidor</div>
                                <li class="blcell3" style="width: 320px;">
                                    <dl class="blockLogin2S2" style="width: 320px;">
                                        <dt style="width: 150px;"><img src="https://i.imgur.com/pJsumlp.png" title="Servidor Brasileiro" width="20">
                                            Game Server</dt>
                                        <dd><span style="color:yellow;">
                                                <b>Brasil</b></span>
                                        </dd>
                                    </dl>

                                    <dl class="blockLogin2S2" style="width: 320px;">
                                        <dt style="width: 150px;"><img src="img/confirm.png" title="Total de contas ativas" width="16"> Contas Ativas</dt>
                                        <dd><span style="color:gold;"><?php echo $num_players; ?></span></dd>
                                    </dl>
                                    <dl class="blockLogin2S2" style="width: 320px;">
                                        <dt style="width: 150px;"><img src="img/statistic.png" title="Total de players online" width="16"> Players online</dt>
                                        <dd><span style="color:gold;"><?php echo $num_rows1; ?></span></dd>
                                    </dl>
                                    <dl class="blockLogin2S2" style="width: 320px;">
                                        <dt style="width: 150px;"><img src="img/discord.png" title="Total de players online" width="16"> Discord oficial</dt>
                                        <dd><span><a href="https://discord.gg/mGR7Zq" target="_blank"  style="color:gold;text-decoration: none;"> Clique aqui</a></span></dd>
                                    </dl>                                    
                                    <br><br><br>Evento de Bônus por partida
                                    <dl class="blockLogin2S2" style="width: 320px;">
                                        <dt style="width: 150px;"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/99/Star_icon_stylized.svg/1077px-Star_icon_stylized.svg.png" title="Bônus de experiência dado por partida" width="16"> Bônus Exp</dt>
                                        <dd><span style="color:white;"> 5.000%</span></dd>
                                    </dl>

                                    <dl class="blockLogin2S2" style="width: 320px;">
                                        <dt style="width: 150px;"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/99/Star_icon_stylized.svg/1077px-Star_icon_stylized.svg.png" title="Bônus de gold dado por partida" width="16"> Bônus Gold</dt>
                                        <dd><span style="color:white;"> 300%</span></dd>
                                    </dl>
                                    


                                    <!-- *************** PHP RankUP *************** -->

                                </li>
                            </div>
                            
                            <div class="normal">

                                <div class="header">Jogue agora</div>
                                <br>
                                <center><a href="https://azurepb.net/?pg=downloads"><img src="img/baixar.png" onmouseover="this.src='img/baixar2.png';" onmouseout="this.src='img/baixar.png';"></a></center><br>
                            </div>
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>
                <div class="bottom_shadow"></div>
            </div>

            <div id="footer">
                <div class="row">
                    <div class="footer_widget">
                        <div class="header"><a href="#">SOBRE NÓS</a></div>
                        <div class="body">
                                O AzurePB surgiu por volta de 2017 em um projeto entre 2 amigos, o servidor no meio do caminho foi descontinuado e agora estamos de volta.</p>
                            <img alt="alt_example" src="img/Logo-topo.png" style="margin:8px 0px 0px 55px;width:214px;" />
                        </div>
                    </div>
                    <div class="divider_footer"></div>

                    <div id="latest_media">
                        <div class="header"><a href="#">SCREENSHOTS</a></div>
                        <div class="body">
                            <ul id="l_media_list">
                                <li><a class="shadowbox" href="http://i.imgur.com/Ue5ZN1H.jpg" rel="gallery"><img alt="alt_example" src="http://i.imgur.com/Ue5ZN1H.jpg" /></a></li>
                                <li><a class="shadowbox" href="http://i.imgur.com/bq8qgvT.jpg" rel="gallery"><img alt="alt_example" src="http://i.imgur.com/bq8qgvT.jpg" /></a></li>
                                <li><a class="shadowbox" href="http://i.imgur.com/5puVkoF.jpg" rel="gallery"><img alt="alt_example" src="http://i.imgur.com/5puVkoF.jpg" /></a></li>
                                <li><a class="shadowbox" href="http://i.imgur.com/59ubdhm.jpg" rel="gallery"><img alt="alt_example" src="http://i.imgur.com/59ubdhm.jpg" /></a></li>
                                <li><a class="shadowbox" href="http://i.imgur.com/ZokLZ9K.jpg" rel="gallery"><img alt="alt_example" src="http://i.imgur.com/ZokLZ9K.jpg" /></a></li>
                                <li><a class="shadowbox" href="http://i.imgur.com/Lrz3Kiu.jpg" rel="gallery"><img alt="alt_example" src="http://i.imgur.com/Lrz3Kiu.jpg" /></a></li>
                                <li><a class="shadowbox" href="http://i.imgur.com/hFKmrep.jpg" rel="gallery"><img alt="alt_example" src="http://i.imgur.com/hFKmrep.jpg" /></a></li>
                                <li><a class="shadowbox" href="http://i.imgur.com/7rl5St1.jpg" rel="gallery"><img alt="alt_example" src="http://i.imgur.com/7rl5St1.jpg" /></a></li>
                                <li><a class="shadowbox" href="http://i.imgur.com/xCitd2H.jpg" rel="gallery"><img alt="alt_example" src="http://i.imgur.com/xCitd2H.jpg" /></a></li>
                                <li><a class="shadowbox" href="http://i.imgur.com/AURfUuJ.jpg" rel="gallery"><img alt="alt_example" src="http://i.imgur.com/AURfUuJ.jpg" /></a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="clear"></div>
                </div>
            </div>
            <!--********************************************* Footer end *********************************************-->
            <div class="clear"></div>
            <!--********************************************* Main_in end *********************************************-->
        </div>
    </div>
    <!--********************************************* Main wrapper end *********************************************-->

    <script src="http://code.jquery.com/jquery-latest.js" type="text/javascript"></script>
    <script src="stylesheets/javascript/jquery.carouFredSel-6.1.0.js" type="text/javascript"></script>
    <script src="stylesheets/javascript/jquery.cslider.js" type="text/javascript"></script>
    <script src="stylesheets/javascript/modernizr.custom.28468.js" type="text/javascript"></script>
    <script src="stylesheets/javascript/getTweet.js" type="text/javascript"></script>
    <script src="stylesheets/javascript/jquery.fancybox.js?v=2.1.3" type="text/javascript"></script>



    <!--******* Javascript Code for the menu *******-->

    <script type="text/javascript">
        $(document).ready(function() {
            $('#menu li').bind('mouseover', openSubMenu);
            $('#menu > li').bind('mouseout', closeSubMenu);

            function openSubMenu() {
                $(this).find('ul').css('visibility', 'visible');
            };

            function closeSubMenu() {
                $(this).find('ul').css('visibility', 'hidden');
            };
        });
    </script>

    <script type="text/javascript">
        $(function() {
            var pull = $('#pull');
            menu = $('ul#menu');

            $(pull).on('click', function(e) {
                e.preventDefault();
                menu.slideToggle();
            });

            $(window).resize(function() {
                var w = $(window).width();
                if (w > 767 && $('ul#menu').css('visibility', 'hidden')) {
                    $('ul#menu').removeAttr('style');
                };
                var menu = $('#menu_wrapper').width();
                $('#pull').width(menu - 20);
            });
        });
    </script>

    <script type="text/javascript">
        $(function() {
            var menu = $('#menu_wrapper').width();
            $('#pull').width(menu - 20);
        });
    </script>

    <script src="http://code.jquery.com/jquery-latest.js" type="text/javascript"></script>
    <script src="stylesheets/javascript/jquery.carouFredSel-6.1.0.js" type="text/javascript"></script>
    <script src="stylesheets/javascript/jquery.cslider.js" type="text/javascript"></script>
    <script src="stylesheets/javascript/jquery.bxSlider.min.js" type="text/javascript"></script>
    <script src="stylesheets/javascript/getTweet.js" type="text/javascript"></script>
    <script src="stylesheets/javascript/jquery.fancybox.js?v=2.1.3" type="text/javascript"></script>

    <!--******* Javascript Code for the Main banner *******-->
    <script type="text/javascript">
        jQuery(function($) {
            $('#homepage-carousel').bxSlider({
                'prev': false,
                'next': false,
                mode: 'fade',
                pager: true
            });
            $('.homepage-news-item').show();
            var x = $('div.tabs');
            $.each(x, function(i) {
                var f = i1;
                $('.pager-', f).empty();
                $(this).appendTo('.pager-', f);
            });
        });
    </script>

    <!--******* Javascript Code for the Main banner mobile version *******-->
    <script type="text/javascript">
        $(function() {

            $('#da-slider').cslider({
                autoplay: true,
                bgincrement: 450
            });

        });
   
	function CopyToken(){
		console.log("Função Startada");
    var copyText = document.getElementById("tokenHidden");
    var textArea = document.createElement("textarea");
    textArea.value = copyText.textContent;
    document.body.appendChild(textArea);
    textArea.select();
    document.execCommand("Copy");
    textArea.remove();
}
	}
	</script>
</body>

</html>