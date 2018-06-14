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

        //verificando se o aluno já esta cadastrado em 2 oficinas do periodo atual
         $estaParticipando = $this->getParticipacaoPeriodoAtual($idPessoa);

        if($estaParticipando < 2){
            //buscando se o aluno já esta cadasrado nessa turma
            $jaNaTurma = json_decode($this->db->select("count(*) as n","aluno_turma","turma_id = ? and pessoa_id = ?",array($idTurma,$idPessoa)));
            $jaNaTurma = $jaNaTurma[0]->n;
            if($jaNaTurma == null or $jaNaTurma == NAO){
                //obtendo num de vagas cadastradas na turma
                $turma = json_decode($this->getVagasDisponiveis($idTurma));
                $turma = $turma[0];
                //inserindo aluno em turma
                $mens = "";
                if(($turma->ocupadas) < ($turma->vagas)){
                    //pessoa dentro do numero de vagas
                    $params = array($idTurma,$idPessoa,NAO);
                }else{
                    //pessoas na lista de espera
                    $params = array($idTurma,$idPessoa,SIM);
                    //$posicao = json_decode($this->db->select("count(*) as n","aluno_turma","id_turma = ? and lista_espera = ?",array($idTurma,SIM)));
                    $mens = "na lista de espera";
                }

                //tentando inserir o aluno na turma
                if($this->db->insert("turma_id,pessoa_id,lista_espera","aluno_turma",$params)){
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
     * aqui conseguimos obter a parcicipacao do aluno em turmas do periodo atual
     * @param $idPessoa
     * @return object
     * @throws Exception
     */
    private function getParticipacaoPeriodoAtual($idPessoa){
        $tempoAtual = turma::getTempoStatic($this->db);
        $whereCLause = "id_turma = turma_id and turma.tempo_id = ? and pessoa_id = ?";
        $estaParticipando = json_decode($this->db->select("count(*) as n","aluno_turma,turma",$whereCLause,array($tempoAtual->id_tempo,$idPessoa)));
        $estaParticipando = $estaParticipando[0]->n;
        return $estaParticipando;
    }

    /**
     * @param $turmaId
     * @return string
     * @throws Exception
     */
    private function getVagasDisponiveis($turmaId){
        $projection = "turma.id_turma,num_vagas as vagas,".
            "(SELECT count(*) as n from aluno_turma where aluno_turma.turma_id = id_turma and lista_espera = 0 and trancado = 0) as ocupadas";
        return $this->db->select($projection,"turma","id_turma = ?",array($turmaId));
    }

    /**
     * Buscando os alunos de turma especifica
     * @param $turmaId Integer - Id da turma
     * @return string JSON
     * @throws Exception
     */
    public function getAlunos($turmaId){
        $tempo = turma::getTempoStatic($this->db);
        $columns = "aluno_turma.id_aluno,pessoa.nome,pessoa.sobrenome,turma.nome_turma as turma,lista_espera,aluno_turma.trancado";
        $whereClause = "aluno_turma.turma_id = turma.id_turma and aluno_turma.pessoa_id = pessoa.id_pessoa and turma.tempo_id = ? and turma.id_turma = ?";
        return $this->db->select($columns,"pessoa,turma,aluno_turma",$whereClause,array($tempo->id_tempo,$turmaId),"pessoa.nome",ASC);
    }

    /**
     * @param $turmaId
     * @return string
     * @throws Exception
     */
    public function getAlunoListaEspera($turmaId){
        $columns = "id_aluno,pessoa_id,nome,sobrenome";
        $whereClause = "pessoa_id = id_pessoa and lista_espera = ? and turma_id = ?";
        return $this->db->select($columns,"aluno_turma,pessoa",$whereClause,array(SIM,$turmaId),"id_aluno");
    }

    /**
     * @param $alunoId
     * @throws Exception
     */
    public function trancarMatricula($alunoId){
        $mensagenAcumulada = "";
        //trancamos a matricula do aluno informado
        if($this->db->update(array("trancado"),"aluno_turma",array(SIM),"id_aluno = ?",array($alunoId))){
            $mensagenAcumulada .= "Matricula trancada";
            //se trancamento deu certo vamos verificar os alunos na lista de espera

            //buscando a turma do aluno
            $turma = json_decode($this->db->select("turma_id","aluno_turma","id_aluno = ?",array($alunoId)));

            $turma = $turma[0]->turma_id;
            $vagas = json_decode($this->getVagasDisponiveis($turma));
            if(($vagas[0]->vagas - $vagas->ocupadas) > 0)
            $alunoSelecionado = json_decode($this->getAlunoListaEspera($turma));
            else $alunoSelecionado = 0 ;
            //Verificamos se existe lista de Espera
            if(sizeof($alunoSelecionado) > 0){
                $alunoSelecionado = $alunoSelecionado[0];

                //colocamos o primeiro aluno da lista de espera na turma
                if($this->db->update(array("lista_espera"),"aluno_turma",array(NAO),"id_aluno = ?",array($alunoSelecionado->id_aluno))){
                    $mensagenAcumulada.= ", Aluno ".$alunoSelecionado->nome." ".$alunoSelecionado->sobrenome." que estava na lista de espera foi Inserido na Turma";
                    new mensagem(SUCESSO,$mensagenAcumulada);

                }else{
                    new mensagem(ERRO,"Matricula trancada mas lista de espera não pôde andar");
                }
            }else{
                new mensagem(SUCESSO,$mensagenAcumulada);
            }

        }else{
            new mensagem(INSERT_ERRO,"Não foi possivel trancar a matricula");
        }

       $this->redirecionaPagAnterior();
    }

    /**
     * aqui geramos uma lista de presença com os alunos ativos na turma
     * @param $turmaId
     * @return string HTML
     * @throws Exception
     */
    public function getListaPresenca($turmaId){
        $alunos = json_decode($this->getAlunos($turmaId));

        $posAluno = 0;
        $listaAlunos = "<table>".
        "<thead>
                <tr>
                    <th>Num</th>
                    <th>Nome</th>
                    <th>Presenças</th>
                    <th></th>
                </tr>
                </thead>
                <tbody id=\"alunos\">
                ";
        for($i=0;$i< sizeof($alunos);$i++){
            $posAluno++;
            $listaAlunos .= "<tr>";
            $listaAlunos .= "<td>".$posAluno."</td>";
            $listaAlunos .= "<td>".$alunos[$i]->nome." ";
            $listaAlunos .= $alunos[$i]->sobrenome."</td>";
            $listaAlunos .= "<td></td>";
            $listaAlunos .= "</tr>";
        }
        $listaAlunos .="</tbody>
            </table>";
        return $listaAlunos;

    }

    private function redireciona(){header("Location: ../index.php?pag=Cad.Aluno");}
    private function redirecionaPagAnterior(){header("Location: " . $_SERVER['HTTP_REFERER'] . "");}
}