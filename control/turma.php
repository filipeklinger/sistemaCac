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
    private $seg,$ter,$qua,$qui,$sex;

    public function __construct(){
        $this->db = new Database();
    }

    /**
     * @throws Exception
     */
    public function setTurma(){
        $this->getCommonData();
        //inserindo turma
        $this->insertTurma();
        $this->turmaId = $this->db->getLastId();
        //recuperando os horarios definidos
        $this->insertHorario();
        //redirecionando para pagina inicial
        $this->redireciona();
    }

    /**
     * CommonData são os dados que são utilizados no insert e no update
     */
    private function getCommonData(){
        $this->oficina = $_POST['oficina_id'];
        $this->prof = $_POST['prof_id'];
        $this->vagas = $_POST['vagas'];
        $this->hinic = $_POST['horario_inic'];
        $this->hfim = $_POST['horario_fim'];

        $this->sala = isset($_POST['sala_id']) ? $_POST['sala_id'] : INVALIDO;
        //horario
        $this->seg = isset($_POST['seg']) ? 1 : 0;
        $this->ter = isset($_POST['ter']) ? 1 : 0;
        $this->qua = isset($_POST['qua']) ? 1 : 0;
        $this->qui = isset($_POST['qui']) ? 1 : 0;
        $this->sex = isset($_POST['sex']) ? 1 : 0;
    }

    /**
     * Popula a Tabela de Turmas
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
        try {
            if($this->db->insert($columns, "turma", $params)){
                new mensagem(SUCESSO,"Turma cadastrada com sucesso");
            }
        } catch (Exception $e) {
            new mensagem(ERRO,"Erro: ".$e);
        }
    }

    /**
     * Popula a tabela horario_turma_sala
     * @throws Exception
     */
    private function insertHorario(){
        if ($this->sala != INVALIDO) {
            $ano = date('Y') . "";
            $columns = "ano,sala_id,segunda,terca,quarta,quinta,sexta,inicio,fim,turma_id";
            $params = array($ano, $this->sala, $this->seg, $this->ter, $this->qua, $this->qui, $this->sex, $this->hinic, $this->hfim, $this->turmaId);
            if ($this->db->insert($columns, "horario_turma_sala", $params)) {
                new mensagem(SUCESSO, "Cadastrado com sucesso");
            } else {
                new mensagem(INSERT_ERRO, "Erro ao cadastrar");
            }
        }
    }


    /**
     * @param $oficinaId Integer
     * @return string
     * @throws Exception
     */
    private function getTurmaByOficinaId($oficinaId){
        $projection = "id_turma,oficina_id,num_vagas,nome_turma,professor,is_ativo";
        return $this->db->select($projection,"turma","oficina_id = ? and is_ativo = ?",array($oficinaId,1));
    }

    public function getTurmasAtivas(){

        try {
            $projection =
                "id_turma,predio.nome as predio,criacao_turma as criacao,oficina.nome as oficina,num_vagas as vagas,nome_turma as turma,".
                //selecionando vagas disponiveis
                "(SELECT count(*) as n from aluno_turma where aluno_turma.turma_id = id_turma and lista_espera = 0) as ocupadas,".
                "pessoa.nome as professor,sala.nome as sala,segunda,terca,quarta,quinta,sexta,TIME_FORMAT(inicio, '%H:%ih') AS inicio,TIME_FORMAT(fim, '%H:%ih') AS fim";
            $table ="(pessoa,oficina,turma,sala,predio)";
            $joinClause = " LEFT JOIN horario_turma_sala ON id_turma = turma_id";

            $whereClause = "professor=id_pessoa and id_oficina=oficina_id and sala_id = id_sala and predio_id = id_predio and turma.is_ativo = ?";
            $whereArgs = array(SIM);//Ativo = Sim
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
                "turma.id_turma,criacao_turma as criacao,oficina.nome as oficina,num_vagas as vagas,nome_turma as turma,".
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
     * @param $turmaId
     * @return string
     * @throws Exception
     */
    public function getTurmaById($turmaId){
        $columns =
            "turma.num_vagas,turma.nome_turma,turma.professor,turma.is_ativo,turma.oficina_id,"./* turma */
            "ano,segunda,terca,quarta,quinta,sexta,inicio,fim,"./* horario_turma_sala */
            "horario_turma_sala.sala_id,"./* sala */
            "sala.predio_id,"./* Predio */
            "oficina.nome as oficina";/* Oficina */
        $whereClause =
            "turma.id_turma = horario_turma_sala.turma_id ".
            " and horario_turma_sala.sala_id = sala.id_sala ".
            " and turma.oficina_id = oficina.id_oficina".
            "  and id_turma = ?";
        return $this->db->select($columns,"turma,horario_turma_sala,sala,oficina",$whereClause,array($turmaId));
    }

    /**
     * @param $turmaId
     * @throws Exception
     */
    public function updateTurma($turmaId){
        //recebendo dados
        $this->getCommonData();
        $ativo = SIM;

        //atualiza turma
        $turmaColumns = array("num_vagas","professor","is_ativo");
        $turmaParams = array($this->vagas,$this->prof,$ativo);
        if($this->db->update($turmaColumns,"turma",$turmaParams,"id_turma = ?",array($turmaId))){
            //Somente vai tentar atualizar os horarios se a tuma for atualizada
            $horarioColumns = array("sala_id","segunda","terca","quarta","quinta","sexta","inicio","fim");
            $horarioParams = array($this->sala,$this->seg,$this->ter,$this->qua,$this->qui,$this->sex,$this->hinic,$this->hfim);

            if($this->db->update($horarioColumns,"horario_turma_sala",$horarioParams,"turma_id = ?",array($turmaId))){
                new mensagem(SUCESSO,"Turma e Horarios Atualizados");
            }else{
                new mensagem(INSERT_ERRO,"Erro ao atualizar horario da turma");
            }

        }else{
            new mensagem(INSERT_ERRO,"Não foi possivel atualizar");
        }
        $this->redireciona();

        /*/atualiza
        echo "<table>";
            foreach ($_POST as $key => $value) {
                echo "<tr>";
                echo "<td>";
                echo $key;
                echo "</td>";
                echo "<td>";
                echo $value;
                echo "</td>";
                echo "</tr>";
            }
        echo "</table>";
        */

    }

    private function redireciona(){
        //depois de inserir redirecionamos para a pagina de infra
        header("Location: ../index.php?pag=DashBoard");
    }
}