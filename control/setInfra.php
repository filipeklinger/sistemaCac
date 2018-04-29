<?php
/**
 * Created by Filipe
 * Date: 28/04/18
 * Time: 18:15
 */

include_once '../model/DatabaseOpenHelper.php';
include 'constantes.php';

class infra{
    private $db;

    public function __construct(){
        $this->db = new Database();
    }

    public function insertPredio(){
        $nome = isset($_POST['nome']) ? $_POST['nome'] : INVALIDO;
        $localizacao = isset($_POST['localizacao']) ? $_POST['localizacao'] : INVALIDO;
        $params = array($nome,$localizacao);
        $this->db->insert("nome,localizacao","predio",$params);
        $this->redireciona();
    }

    public function insertSala(){
        $predioId = isset($_POST['predio_id']) ? $_POST['predio_id'] : INVALIDO;;
        $nome = isset($_POST['nome']) ? $_POST['nome'] : INVALIDO;
        $is_ativo = 1;
        $params = array($predioId,$nome,$is_ativo);
        $this->db->insert("predio_id,nome,is_ativo","sala",$params);
        $this->redireciona();
    }

    private function redireciona(){
        //depois de inserir redirecionamos para a pagina de infra
        header("Location: ../index.php?pag=Infraestrutura");
    }
}
//recebe por GET o tipo da infraestrutura
$tipo = $_GET['tipo'];
$infra = new infra();
if($tipo == "predio"){
    $infra->insertPredio();
}else if($tipo == 2){
    $infra->insertSala();
}else{
    echo "Erro no tipo";
}

