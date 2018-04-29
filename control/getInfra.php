<?php
/**
 * Created by PhpStorm.
 * User: filipe
 * Date: 28/04/18
 * Time: 18:50
 */

include_once '../model/DatabaseOpenHelper.php';
include 'constantes.php';
class getInfra{
    private $db;

    public function __construct(){
        $this->db = new Database();
    }

    public function getPredios(){
        return $this->db->select("id_predio,nome,localizacao,is_ativo","predio");
    }

    public function getSalas(){
        return "sala";
    }
}

//primeiro tipo da requisicao
$tipo = $_GET['tipo'];

$infra = new getInfra();
if($tipo == 'predio'){
    echo $infra->getPredios();
}else{
    echo $infra->getSalas();
}