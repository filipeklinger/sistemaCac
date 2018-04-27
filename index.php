<?php
session_start();
$titulo = isset($_GET['pag']) ? $_GET['pag'] : 'Sistema CAC';//versao reduzida if
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- As 3 meta tags acima *devem* vir em primeiro lugar dentro do `head`; qualquer outro conteúdo deve vir *após* essas tags -->
    <title><?php echo $titulo ?></title>

    <!-- Bootstrap -->
    <link href="bootstrap3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/cssPersonalizado.css" rel="stylesheet">
    <!-- HTML5 shim e Respond.js para suporte no IE8 de elementos HTML5 e media queries -->
    <!-- ALERTA: Respond.js não funciona se você visualizar uma página file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <!-- jQuery (obrigatório para plugins JavaScript do Bootstrap) -->
    <script src="bootstrap3.3.7/jquery.min.js"></script>
    <script src="js/jsonParser.js";></script>
</head>
<body>

<div class="container">
    <?php
    //aqui verificamos se a logica php nos retornou alguma mensagem
    if(isset($_SESSION['MSG_ERRO'])){
        echo "<div class=\"container  espaco_max_padding\">";
        echo "<div class='alert alert-danger alert-dismissable'>";
        echo "<a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>";
        echo "<strong>{$_SESSION['MSG_ERRO']}</strong>";
        echo "</div>";
        echo "</div>";
        $_SESSION['MSG_ERRO'] = NULL;//aqui resetamos a variavel para nao mostrar mensagem errada
    }
    if(isset($_SESSION['MSG_SUCESSO'])){
        echo "<div class=\"container espaco_max_padding\">";
        echo "<div class='alert alert-success alert-dismissable'>";
        echo "<a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>";
        echo "<span class='glyphicon glyphicon-ok'></span> ".$_SESSION['MSG_SUCESSO'];
        echo "</div>";
        echo "</div>";
        $_SESSION['MSG_SUCESSO'] = NULL;
    }

    //aqui recebemos por get a pagina de conteudo escolhida
    $opcao = isset($_GET['pag']) ? $_GET['pag'] : 'Login';

        switch($opcao){
            case 'Login':
            case 'Pesquisa':
                include "view/login.html";
                break;
            case 'DashBoard':
                include "view/dashboard.html";
                break;
            //TODO Incluir as paginas view aqui
            //A string do case se torna o Titulo da pagina
            default:
                include "view/404.html";
                break;
        }
    ?>

</div>




<!-- Inclui todos os plugins compilados do bootstrap (abaixo) -->
<script src="bootstrap3.3.7/js/bootstrap.min.js"></script>
</body>
</html>
