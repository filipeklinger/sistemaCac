<?php
/**
 * Created by Filipe
 * Date: 07/06/18
 * Time: 11:28
 */
include_once '../model/DatabaseOpenHelper.php';
class relatorio{
 private $db;

 public function __construct(){
     $this->db = new Database();
 }

    //o total de alunos é obtdo atraves da soma dos alunos por oficina no front-end diminuindo processamento
    /**
     * @param $tempoId
     * @return string
     * @throws Exception
     */
    public function getAlunosPorOficina($tempoId){
        $projection = "oficina.nome as oficina,count(aluno_turma.id_aluno) as alunos,".
        //lista de espera
            "(SELECT count(aluno_turma.id_aluno) from aluno_turma WHERE aluno_turma.turma_id = turma.id_turma and aluno_turma.lista_espera = 1) as espera";
        $table = "turma,aluno_turma,oficina";
        $whereClause = "turma.tempo_id = ? AND turma.id_turma = aluno_turma.turma_id and aluno_turma.lista_espera = 0 and aluno_turma.trancado = 0 and turma.oficina_id = oficina.id_oficina";
        $groupBy = " GROUP BY id_turma";
        return $this->db->select($projection,$table,$whereClause.$groupBy,array($tempoId));
 }

    /**
     * @return string
     * @throws Exception
     */
    public function getAlunosHistorico(){
        $columns = "pessoa.nome,pessoa.sobrenome,COUNT(oficina.nome) as oficinas";
        $whereClause = "pessoa.id_pessoa = aluno_turma.pessoa_id and aluno_turma.turma_id = turma.id_turma AND turma.oficina_id = oficina.id_oficina";
        $group = " GROUP BY pessoa.nome, pessoa.sobrenome";
        return $this->db->select($columns,"pessoa,aluno_turma,turma,oficina",$whereClause.$group,null,"pessoa.nome",ASC);
    }

    /**
     * @throws Exception
     */
    public function getAlunosCadastrados(){
            //alunos da rural
            $ruralino = json_decode($this->db->select("count(*) as n","ruralino"));
            //crianças
            $crianca = json_decode($this->db->select("count(*) as n","menor_idade"));
            //total de cadastros
            $total = json_decode($this->db->select("count(*) as n","pessoa"));
            $json = new stdClass();
            $json->ruralino = $ruralino[0]->n;
            $json->crianca = $crianca[0]->n;
            $json->total = $total[0]->n;

            return json_encode($json,JSON_UNESCAPED_UNICODE);
    }
}