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
 /*
  *

4. Recuperar histórico de um aluno específico em oficinas já concluídas ou
em curso;
5. Consultar total de oficinas ativas e seus respectivos “Oficineiros”;
6. Evasão de alunos por oficina, sendo calculado através da quantidade de
alunos matriculados no primeiro dia de aula e os que saíram até o
último dia de aula;
7. Quantidade média de aulas de cada oficina, sendo contabilizado através
do número de aulas de cada oficina, pelo número de oficinas ativas no
período consultado;
8. Nível de procura de cada oficina, sendo calculado através do tamanho
da lista de espera em relação a quantidade de vagas disponibilizadas;
  */

    //o total de alunos pode ser obtdo atraves da soma dos alunos por oficina no front-end diminuindo processamento
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
}