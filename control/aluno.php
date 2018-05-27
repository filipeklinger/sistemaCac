<?php
/**
 * Created by PhpStorm.
 * User: filipe
 * Date: 26/05/18
 * Time: 19:05
 */
include_once '../model/DatabaseOpenHelper.php';
include_once 'mensagem.php';
include_once 'constantes.php';
class aluno{
    private $db;

    public function __construct(){
        $this->db = new Database();
    }

    /**
     * @throws Exception
     */
    public function setAluno(){
        //recebendo dados do user
        $idPessoa = isset($_POST['candidato']) ? $_POST['candidato'] : INVALIDO;
        $idTurma = isset($_POST['turma']) ? $_POST['turma'] : INVALIDO;

        //verificando se o aluno já esta cadastrado em 2 oficinas
        $estaParticipando = json_decode($this->db->select("count(*) as n","aluno_turma","pessoa_id = ? and is_ativo = ?",array($idPessoa,SIM)));
        $estaParticipando = $estaParticipando[0]->n;

        if($estaParticipando < 2){

            //obtendo num de vagas cadastradas na turma
            $numVagas = json_decode($this->db->select("num_vagas","turma","id_turma = ?",array($idTurma)));
            $numVagas = $numVagas[0]->num_vagas;
            echo "Vagas".$numVagas;
            //inserindo aluno em turma
            if($numVagas > 0){
                $params = array($idTurma,$idPessoa,NAO,SIM);
                //atualizamos o valor de vagas
                $paramsUpdate = array(($numVagas-1));
                $this->db->update(array("num_vagas"),"turma",$paramsUpdate,"id_turma = ?",array($idTurma));
            }else{
                $params = array($idTurma,$idPessoa,SIM,SIM);
            }
            if($this->db->insert("turma_id,pessoa_id,lista_espera,is_ativo","aluno_turma",$params)){
                new mensagem(SUCESSO,"Aluno inserido com sucesso");
            }else{
                new mensagem(INSERT_ERRO,"Não foi possivel inserir o aluno");
            }

        }else{
            new mensagem(ERRO,"Aluno já esta participando de ".$estaParticipando." Oficinas");
        }
        $this->redireciona();
    }

    private function redireciona(){header("Location: ../index.php?pag=Cad.Aluno");}
}