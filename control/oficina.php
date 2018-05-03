<?php
/**
 * Created by PhpStorm.
 * User: filipe
 * Date: 29/04/18
 * Time: 15:24
 */

include_once '../model/DatabaseOpenHelper.php';
include_once 'constantes.php';

class oficina{
    private $db;
    private $nome,$preRequisito;
    public function __construct(){
        $this->db = new Database();
    }
    public function setOficina(){
        $this->nome = isset($_POST['nome']) ? $_POST['nome'] : INVALIDO;;
        $this->preRequisito = isset($_POST['pre_requisito']) ? $_POST['pre_requisito'] : INVALIDO;;
        $this->inserOficina();
    }

    private function inserOficina(){
        $this->getDados();
        $params = array($this->nome,$this->preRequisito);
        $this->db->insert("nome,pre_requisito","oficina",$params);
        $this->redireciona();
    }

    public function getOficina(){
        return $this->db->select("*","oficina");
    }

    private function redireciona(){
        //depois de inserir redirecionamos para a pagina de infra
        header("Location: ../index.php?pag=DashBoard");
    }
}