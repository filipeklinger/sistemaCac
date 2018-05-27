<?php
/**
 * Created by Filipe
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
            //buscando se o aluno já esta cadasrado nessa turma
            $jaNaTurma = json_decode($this->db->select("count(*) as n","aluno_turma","turma_id = ? and pessoa_id = ?",array($idTurma,$idPessoa)));
            $jaNaTurma = $jaNaTurma[0]->n;
            if($jaNaTurma == null or $jaNaTurma == NAO){
                //obtendo num de vagas cadastradas na turma
                $numVagas = json_decode($this->db->select("num_vagas","turma","id_turma = ?",array($idTurma)));
                $numVagas = $numVagas[0]->num_vagas;
                //inserindo aluno em turma
                $mens = "";
                if($numVagas > 0){
                    //pessoas dentro do numero de vagas
                    $params = array($idTurma,$idPessoa,NAO,SIM);
                    //atualizamos o valor de vagas
                    $paramsUpdate = array(($numVagas-1));
                    $this->db->update(array("num_vagas"),"turma",$paramsUpdate,"id_turma = ?",array($idTurma));
                }else{
                    //pessoas na lista de espera
                    $params = array($idTurma,$idPessoa,SIM,SIM);

                    $this->db->select("count(*) as n","aluno_turma","id_turma = ? and lista_espera = ?",array($idTurma,));
                    $mens = "na lista de espera ";
                }

                //tentando inserir o aluno na turma
                if($this->db->insert("turma_id,pessoa_id,lista_espera,is_ativo","aluno_turma",$params)){
                    new mensagem(SUCESSO,"Aluno inserido com sucesso ".$mens);
                }else{
                    new mensagem(INSERT_ERRO,"Não foi possivel inserir o aluno");
                }
            }else{
                new mensagem(ERRO,"Aluno Já está nessa turma");
            }
        }else{
            new mensagem(ERRO,"Aluno já esta participando de ".$estaParticipando." Oficinas");
        }
        $this->redireciona();
    }

    /**
     * Buscando os alunos de turma especifica
     * @param $identificador Integer - Id da turma
     */
    public function getAlunoByTurmaId($identificador){
        //$this->db->select("")
    }

    private function redireciona(){header("Location: ../index.php?pag=Cad.Aluno");}
}