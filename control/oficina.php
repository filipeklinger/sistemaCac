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
        $this->preRequisito = isset($_POST['pre_requisito']) ? $_POST['pre_requisito'] : INVALIDO;
        $this->inserOficina();
    }

    private function inserOficina(){
        $params = array($this->nome,$this->preRequisito);
        try {
            $this->db->insert("nome,pre_requisito", "oficina", $params);
        } catch (Exception $e) {
            echo $e;
        }
        $this->redireciona();
    }

    public function getOficina(){
        try {
            return $this->db->select("*", "oficina");
        } catch (Exception $e) {
            echo $e;
        }
    }

    private function redireciona(){
        //depois de inserir redirecionamos para a pagina de infra
        header("Location: ../index.php?pag=DashBoard");
    }
}