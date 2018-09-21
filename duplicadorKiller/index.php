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
?>
<html>
<head>
    <title>duplicador Killer</title>
    <!-- Bootstrap -->
    <link href="../bootstrap3.3.7/css/bootstrap.min.css" rel="stylesheet">


</head>
<body>
<h3>Registros duplicados no sistema</h3>
<?php
if(isset($_SESSION['MENSAG'])){
    echo $_SESSION['MENSAG'];
    unset($_SESSION['MENSAG']);
}
?>
<form method="post" action="control/destroy.php">
    <button type="submit">ELIMINAR</button>

<table class="table">
    <thead>
        <th>ID</th>
        <th>Documento</th>
        <th>Nome</th>
        <th>Esta em turma</th>
        <th>Excluir</th>
    </thead>
    <tbody>
        <?php
        try {
            echo $sch->getDataFromDuplicados();
        } catch (Exception $e) {
            echo $e;
        }
        ?>
    </tbody>
</form>
</table>
</body>
</html>


