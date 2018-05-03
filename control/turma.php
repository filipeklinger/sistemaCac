<?php
/**
 * Created by Filipe
 * Date: 03/05/18
 * Time: 17:02
 */

include_once '../model/DatabaseOpenHelper.php';
include_once 'constantes.php';

class turma{
    private $db;

    public function __construct(){
        $this->db = new Database();
    }

    public function setTurma(){
        $criacao_turma = "";//data de criacao da turma (data de hoje do sistema)
        $oficina_id = "";//identificador das oficinas ja cadastradas
        $num_vagas = "";//inteiro
        $nome_turma = "";//nome automatico
        $professor = "";//Identificador de um professor já cadastrado
        $is_ativo = SIM;
        //$this->redireciona();
    }

    public function getTurmas(){
        return $this->db->select("criacao_turma,oficina_id,num_vagas,nome_turma,professor","turma");
    }

    private function redireciona(){
        //depois de inserir redirecionamos para a pagina de infra
        header("Location: ../index.php?pag=DashBoard");
    }
}