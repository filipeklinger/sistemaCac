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
                $projection = "turma.id_turma,num_vagas as vagas,".
                    "(SELECT count(*) as n from aluno_turma where aluno_turma.turma_id = id_turma and lista_espera = 0) as ocupadas";
                $turma = json_decode($this->db->select($projection,"turma","id_turma = ?",array($idTurma)));
                $turma = $turma[0];
                //inserindo aluno em turma
                $mens = "";
                if(($turma->ocupadas) < ($turma->vagas)){
                    //pessoa dentro do numero de vagas
                    $params = array($idTurma,$idPessoa,NAO,SIM);
                }else{
                    //pessoas na lista de espera
                    $params = array($idTurma,$idPessoa,SIM,SIM);
                    //$posicao = json_decode($this->db->select("count(*) as n","aluno_turma","id_turma = ? and lista_espera = ?",array($idTurma,SIM)));
                    $mens = "na lista de espera";
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
//SELECT turma.id_turma,num_vagas, (SELECT count(*) as n from aluno_turma where aluno_turma.turma_id = id_turma) as ocupadas
//from turma
    /**
     * Buscando os alunos de turma especifica
     * @param $identificador Integer - Id da turma
     * @throws Exception
     */
    public function getAlunoByTurmaId($identificador){
        $columns = "pessoa.nome,pessoa.sobrenome,turma.nome_turma as turma,lista_espera,aluno_turma.is_ativo";
        $whereClause = "aluno_turma.turma_id = turma.id_turma and aluno_turma.pessoa_id = pessoa.id_pessoa and turma.id_turma = ?";
        echo $this->db->select($columns,"pessoa,turma,aluno_turma",$whereClause,array($identificador));
    }

    private function redireciona(){header("Location: ../index.php?pag=Cad.Aluno");}
}