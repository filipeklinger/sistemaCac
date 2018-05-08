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
    private $oficina,$hinic,$hfim,$vagas,$prof,$sala,$turmaId;

    public function __construct(){
        $this->db = new Database();
    }

    public function setTurma(){
        $this->oficina = $_POST['oficina_id'];
        $this->prof = $_POST['prof_id'];
        $this->vagas = $_POST['vagas'];
        $this->hinic = $_POST['horario_inic'];
        $this->hfim = $_POST['horario_fim'];
        //inserindo turma
        $this->insertTurma();
        $this->turmaId = $this->db->getLastId();
        //recuperando os horarios definidos
        $this->formHorarios();
        //redirecionando para pagina inicial
        $this->redireciona();
    }

    private function formHorarios(){
        $this->sala = isset($_POST['sala_id']) ? $_POST['sala_id'] : INVALIDO;
        if($this->sala != INVALIDO){
            $seg = isset($_POST['seg']) ? $_POST['seg'] : false;
            $ter = isset($_POST['ter']) ? $_POST['ter'] : false;
            $qua = isset($_POST['qua']) ? $_POST['qua'] : false;
            $qui = isset($_POST['qui']) ? $_POST['qui'] : false;
            $sex = isset($_POST['sex']) ? $_POST['sex'] : false;

            if($seg != false) $this->insertHorario(2);
            if($ter != false) $this->insertHorario(3);
            if($qua != false) $this->insertHorario(4);
            if($qui != false) $this->insertHorario(5);
            if($sex != false) $this->insertHorario(6);
        }


    }

    private function insertHorario($diaSemana){
        $ano = date('Y')."";
        $params = array($ano,$this->sala,$diaSemana,$this->hinic,$this->hfim,$this->turmaId);
        $this->db->insert("ano,sala_id,dia_semana,inicio,fim,turma_id","horario_turma_sala",$params);

    }

    private function insertTurma(){
        $date = date('Y-m-d');
        $nome_turma = "t01-".date('Y');//nome automatico da turma
        $criacao_turma = $date."";
        $is_ativo = SIM;
        $params = array($criacao_turma,$this->oficina,$this->vagas,$nome_turma,$this->prof,$is_ativo);
        print_r($params);
        $this->db->insert("criacao_turma,oficina.nome,num_vagas,nome_turma,professor,is_ativo","turma",$params);
    }

    public function getTurmas(){

        try {
            $projection ="criacao_turma as criacao,oficina.nome as oficina,num_vagas as vagas,nome_turma as turma,pessoa.nome as professor,sala.nome as sala,dia_semana,inicio,fim";
            $table ="(pessoa,oficina,turma,sala)";
            $joinClause = " LEFT JOIN horario_turma_sala ON id_turma = turma_id";

            $whereClause = "professor=id_pessoa and id_oficina=oficina_id and (sala_id = id_sala or sala_id = null)";

            return $this->db->select($projection,$table.$joinClause , $whereClause);
        } catch (Exception $e) {
            return $e;
        }
    }

    private function redireciona(){
        //depois de inserir redirecionamos para a pagina de infra
        header("Location: ../index.php?pag=DashBoard");
    }
}