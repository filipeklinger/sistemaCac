<?php
/**
 * Created by Filipe
 * Date: 03/05/18
 * Time: 17:02
 */

include_once '../model/DatabaseOpenHelper.php';
include_once 'constantes.php';
include_once 'mensagem.php';

class turma{
    private $db;
    private $oficina,$hinic,$hfim,$vagas,$prof,$sala,$turmaId;

    public function __construct(){
        $this->db = new Database();
    }

    /**
     * @throws Exception
     */
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
            try {
                if($this->db->insert($columns, "horario_turma_sala", $params)){
                    new mensagem(SUCESSO,"Cadastrado com sucesso");
                }else{
                    new mensagem(INSERT_ERRO,"Erro ao cadastrar");
                }
            } catch (Exception $e) {
                new mensagem(ERRO,"Erro: ".$e);
            }
        }


    }

    /**
     * @throws Exception
     */
    private function insertTurma(){
        $date = date('Y-m-d');
        //-------------------criacao do nome----------------------------------------------------------------------------
        $tumasCadastradas = json_decode($this->getTurmaByOficinaId($this->oficina));
        $numTurma = sizeof($tumasCadastradas)+1;//aqui setamos um numero para a turma de acordo com as tumas ja cadastradas para uma determinada oficina
        //$nomeOficina = strtoupper((json_decode($this->getOficinaById($this->oficina)))[0]->nome);

        $nome_turma = "T0".$numTurma."-".date('Y');//nome automatico da turma
        //--------------------------------------------------------------------------------------------------------------
        $criacao_turma = $date."";
        $is_ativo = SIM;
        $columns = "criacao_turma,oficina_id,num_vagas,nome_turma,professor,is_ativo";
        $params = array($criacao_turma,$this->oficina,$this->vagas,$nome_turma,$this->prof,$is_ativo);
        print_r($params);
        try {
            if($this->db->insert($columns, "turma", $params)){
                new mensagem(SUCESSO,"Turma cadastrada com sucesso");
            }
        } catch (Exception $e) {
            new mensagem(ERRO,"Erro: ".$e);
        }
    }

    /**
     * @param $oficinaId Integer
     * @return string
     * @throws Exception
     */
    private function getTurmaByOficinaId($oficinaId){
        return $this->db->select("id_turma,oficina_id,num_vagas,nome_turma,professor,is_ativo","turma","oficina_id = ? and is_ativo = ?",array($oficinaId,1));
    }

    public function getTurmasAtivas(){

        try {
            $projection =
                "predio.nome as predio,criacao_turma as criacao,oficina.nome as oficina,num_vagas as vagas,nome_turma as turma,".
                "pessoa.nome as professor,sala.nome as sala,segunda,terca,quarta,quinta,sexta,TIME_FORMAT(inicio, '%H:%ih') AS inicio,TIME_FORMAT(fim, '%H:%ih') AS fim";
            $table ="(pessoa,oficina,turma,sala,predio)";
            $joinClause = " LEFT JOIN horario_turma_sala ON id_turma = turma_id";

            $whereClause = "professor=id_pessoa and id_oficina=oficina_id and sala_id = id_sala and predio_id = id_predio and turma.is_ativo = ?";
            $whereArgs = array(SIM);
            return $this->db->select($projection,$table.$joinClause , $whereClause,$whereArgs);
        } catch (Exception $e) {
            new mensagem(ERRO,"Erro: ".$e);
            return "";
        }
    }
    public function getTurmas(){
        //aqui mostramos todas as turmas ativas ou nao
        try {
            $projection =
                "criacao_turma as criacao,oficina.nome as oficina,num_vagas as vagas,nome_turma as turma,".
                "pessoa.nome as professor,sala.nome as sala,segunda,terca,quarta,quinta,sexta,TIME_FORMAT(inicio, '%H:%ih') AS inicio,TIME_FORMAT(fim, '%H:%ih') AS fim, turma.is_ativo as ativo";
            $table ="(pessoa,oficina,turma,sala)";
            $joinClause = " LEFT JOIN horario_turma_sala ON id_turma = turma_id";

            $whereClause = "professor=id_pessoa and id_oficina=oficina_id and (sala_id = id_sala or sala_id = null)";
            return $this->db->select($projection,$table.$joinClause , $whereClause);
        } catch (Exception $e) {
            new mensagem(ERRO,"Erro: ".$e);
            return "";
        }
    }

    public function getHorariosBySalaId($identificador){
        $projection = "oficina.nome as oficina,segunda,terca,quarta,quinta,sexta,TIME_FORMAT(inicio, '%H:%ih') AS inicio,TIME_FORMAT(fim, '%H:%ih') AS fim";
        try {
            return $this->db->select($projection, "horario_turma_sala,turma,oficina", "sala_id = ? and turma_id = id_turma and oficina_id=id_oficina", array($identificador),"inicio");
        } catch (Exception $e) {
            new mensagem(ERRO,"Erro: ".$e);
            return "";
        }
    }

    /**
     * @param $oficinaId
     * @return string
     * @throws Exception
     */
    private function getOficinaById($oficinaId){
        return $this->db->select("nome","oficina","id_oficina = ?",array($oficinaId));

    }
    private function redireciona(){
        //depois de inserir redirecionamos para a pagina de infra
        header("Location: ../index.php?pag=DashBoard");
    }
}