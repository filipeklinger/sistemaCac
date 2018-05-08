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
        $this->insertHorario();
        //redirecionando para pagina inicial
        $this->redireciona();
    }

    private function insertHorario(){
        $this->sala = isset($_POST['sala_id']) ? $_POST['sala_id'] : INVALIDO;
        if($this->sala != INVALIDO){
            $seg = isset($_POST['seg']) ? 1 : 0;
            $ter = isset($_POST['ter']) ? 1 : 0;
            $qua = isset($_POST['qua']) ? 1 : 0;
            $qui = isset($_POST['qui']) ? 1 : 0;
            $sex = isset($_POST['sex']) ? 1 : 0;


            $ano = date('Y')."";
            $columns = "ano,sala_id,segunda,terca,quarta,quinta,sexta,inicio,fim,turma_id";
            $params = array($ano,$this->sala,$seg,$ter,$qua,$qui,$sex,$this->hinic,$this->hfim,$this->turmaId);
            $this->db->insert($columns,"horario_turma_sala",$params);
        }


    }

    private function insertTurma(){
        $date = date('Y-m-d');
        $nome_turma = "t01-".date('Y');//nome automatico da turma
        $criacao_turma = $date."";
        $is_ativo = SIM;
        $columns = "criacao_turma,oficina_id,num_vagas,nome_turma,professor,is_ativo";
        $params = array($criacao_turma,$this->oficina,$this->vagas,$nome_turma,$this->prof,$is_ativo);
        print_r($params);
        $this->db->insert($columns,"turma",$params);
    }

    public function getTurmas(){

        try {
            $projection =
                "criacao_turma as criacao,oficina.nome as oficina,num_vagas as vagas,nome_turma as turma,".
                "pessoa.nome as professor,sala.nome as sala,segunda,terca,quarta,quinta,sexta,inicio,fim";
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