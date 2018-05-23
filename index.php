<?php
session_start();
$titulo = isset($_GET['pag']) ? $_GET['pag']." - CAC" : 'Sistema CAC';//versao reduzida if
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
    <!-- estiliza o seletor link href="bootstrap3.3.7/select/css/bootstrap-select.min.css" rel="stylesheet" -->
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
    <!-- estiliza o seletor script src="bootstrap3.3.7/select/js/bootstrap-select.min.js"></script -->
    <script src="js/jsonParser.js"></script>
    <!-- Aqui recebemos as msg do PHP e inserimos numa variavel JS chamada mensagem -->
    <script type="application/x-javascript">
        var mensagem =
        <?php
        if(isset($_SESSION['MSG'])){
            echo  '\''.$_SESSION['MSG'].'\'';
            $_SESSION['MSG'] = null;//apos mostrar msg devemos remover
        }else{
            echo '\'{"tipo":"erro","desc":" "}\'';
        } ?>;
    </script>
</head>
<?php
//aqui recebemos por get a pagina de conteudo escolhida
$opcao = isset($_GET['pag']) ? $_GET['pag'] : 'Login';
//TODO: verificar User Agente - evitar que código de estilo quebre

if(isset($_SESSION['LOGADO']) and $_SESSION['LOGADO'] == true){
    //pessoa logada tem essas opcoes de pagina
    switch ($opcao) {
        //A string do case se torna o Titulo da pagina
        case 'Login'://se a pessoa ja esta logada nao precisa de login novamente
        case 'DashBoard':
            include "view/dashboard.html";
            break;
        //Infra
        case 'Infraestrutura':
            include "view/infraestrutura/gerenciar_infraestrutura.html";
            break;
        case 'Cad.Predio':
            include "view/infraestrutura/cadPredio.html";
            break;
        case 'Cad.Sala':
            include "view/infraestrutura/cadSala.html";
            break;
        case 'Edit.Predio':
            include "view/infraestrutura/editPredio.html";
            break;
        case 'Edit.Sala':
            include "view/infraestrutura/editSala.html";
            break;
        //Oficina
        case 'Oficinas':
            include "view/oficina/gerenciar_oficina.html";
            break;
        case 'Cad.Oficina':
            include "view/oficina/cadOficina.html";
            break;
        case 'Edit.Oficina':
            include "view/oficina/edtOficina.html";
            break;
        //Turma
        case 'Turmas':
            include "view/turma/gerenciar_turma.html";
            break;
        case 'Cad.Turma':
            include "view/turma/cadTurma.html";
            break;
        case 'Edit.Turma':
            include "view/turma/editTurma.html";
            break;
        //Usuario
        case 'Usuarios':
            include "view/usuario/gerenciar_usuario.html";
            break;
        case 'Cad.Pessoa':
            include "view/usuario/cadPessoa.html";
            break;
        //Alunos
        case 'Alunos':
            include "view/aluno/gerenciar_aluno.html";
            break;
        case 'Cad.Aluno':
            include "view/aluno/inserir_aluno_em_turma.html";
            break;
        //Relatorios
        case 'Relatorios':
            include "view/relatorio/gerenciar_relatorio.html";
            break;
        default:
            include "view/404.html";
            break;
    }
}else{
    //Pessoa nao logada tem essas opcoes de pagina
    switch ($opcao) {
        case 'Login':
            include "view/login.html";
            break;
        case 'Cad.Candidato':
            include "view/usuario/cadCandidato.html";
            break;
	    case 'Testes':
		    include "view/Testes.html";
		    break;
        default:
            header("Location: ?pag=Login");
            break;
    }
}

?>
</html>
