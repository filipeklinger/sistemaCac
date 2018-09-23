<?php
/**
 * Created by PhpStorm.
 * User: filipe
 * Date: 20/09/18
 * Time: 19:27
 */
if ( session_status() !== PHP_SESSION_ACTIVE ) {
    session_start();
}
include 'control/search.php';

$sch = new search();
try {
    $registros = $sch->getDataFromDuplicados();
} catch (Exception $e) {
    echo $e;
}
?>
<html>
<head>
    <title>Duplicador Killer</title>
    <!-- Bootstrap Reorganizado Duplicador Killer-->
    <link href="../bootstrap3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <!-- jQuery (obrigatório para plugins JavaScript do Bootstrap) -->
    <script src="../bootstrap3.3.7/jquery.min.js" ></script>
    <!-- Inclui todos os plugins compilados do bootstrap (abaixo) -->
    <script src="../bootstrap3.3.7/js/bootstrap.min.js"></script>



</head>
<body>
<div class="container">
    <h3 class="text-center">Registros duplicados no sistema: <?php echo sizeof($registros); ?></h3>
    <?php
    if(isset($_SESSION['MENSAG'])){
        echo $_SESSION['MENSAG'];
        unset($_SESSION['MENSAG']);
    }
    ?>
    <form onsubmit="return warning(this);" method="post" action="control/destroy.php">
        <button class="btn btn-warning center-block">ELIMINAR</button>

    <table class="table table-striped">
        <thead>
            <th>ID</th>
            <th>Documento</th>
            <th>Nome</th>
            <th>Esta em turma</th>
            <th>Tem Login</th>
            <th>Excluir</th>
        </thead>
        <tbody>
            <?php
                for($i=0;$i<sizeof($registros);$i++){
                    echo $registros[$i];
                }
                if(sizeof($registros) == 0){
                    echo "<tr>
                            <td> Parabens!! </td>
                            <td> Nenhum Registro duplicado </td>
                            <td>  </td>
                            <td>  </td>
                            <td> </td>";

                }
            ?>
        </tbody>
    </form>
    </table>
</div>
<script type="application/javascript">
    function warning(form) {
        return confirm("Essa ação é irreverssível, tem ceteza ?");
    }
</script>
</body>
</html>


