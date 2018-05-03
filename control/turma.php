<?php
/**
 * Created by Filipe
 * Date: 03/05/18
 * Time: 17:02
 */

include_once '../model/DatabaseOpenHelper.php';

class turma{
    private $db;

    public function __construct(){
        $this->db = new Database();
    }

    public function setTurma(){
        $this->redireciona();
    }

    public function getTurmas(){
        return "turma.....";
    }

    private function redireciona(){
        //depois de inserir redirecionamos para a pagina de infra
        header("Location: ../index.php?pag=DashBoard");
    }
}