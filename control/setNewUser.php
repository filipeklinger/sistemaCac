<?php
/**
 * Created by PhpStorm.
 * User: filipe
 * Date: 28/04/18
 * Time: 15:39
 */
include_once '../model/DatabaseOpenHelper.php';
class newUser{
    private $db;
    //variaveis
    private $num_cadastros;
    public function __construct(){
        $this->db = new Database();
    }

    public function setUser(){
        //aqui iteramos sobre os dados de usuarios enviados
        for($i=0;$i<sizeof($this->num_cadastros);$i++){
            //TODO verificar como serão entregues os dados para iterar
        }
    }
}