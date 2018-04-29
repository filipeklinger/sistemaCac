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
    <link href="bootstrap3.3.7/select/css/bootstrap-select.min.css" rel="stylesheet">
    <link href="css/cssPersonalizado.css" rel="stylesheet">
    <!-- HTML5 shim e Respond.js para suporte no IE8 de elementos HTML5 e media queries -->
    <!-- ALERTA: Respond.js não funciona se você visualizar uma página file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <!-- jQuery (obrigatório para plugins JavaScript do Bootstrap) -->
    <script src="bootstrap3.3.7/jquery.min.js"></script>
    <!-- Inclui todos os plugins compilados do bootstrap (abaixo) -->
    <script src="bootstrap3.3.7/js/bootstrap.min.js"></script>
    <script src="bootstrap3.3.7/select/js/bootstrap-select.min.js"></script>
    <script src="js/jsonParser.js";></script>
</head>
<?php
//aqui recebemos por get a pagina de conteudo escolhida
$opcao = isset($_GET['pag']) ? $_GET['pag'] : 'Login';

switch ($opcao) {
    case 'Login':
        include "view/login.html";
        break;
    case 'Infraestrutura':
        include "view/infraestrutura.html";
        break;
    case 'Cad.Predio':
        include "view/cadPredio.html";
        break;
    case 'Cad.Sala':
        include "view/cadSala.html";
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
</html>
