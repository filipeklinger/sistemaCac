<?php
if ( session_status() !== PHP_SESSION_ACTIVE ) {
    session_start();
}

/** @noinspection PhpIncludeInspection */
include_once '../model/DatabaseOpenHelper.php';

class search{
    private $db;

    function __construct(){
        $this->db = new Database();
    }

    /**
     * @return string
     * @throws Exception
     */
    private function getDuplicados(){
        $clausulaWhere = "numero_documento IN (SELECT B.numero_documento FROM documento B GROUP BY B.numero_documento HAVING COUNT(*) > 1)";
        return $this->db->select('pessoa_id,numero_documento',"documento",$clausulaWhere,null,"pessoa_id");
    }

    /**
     * @param $pessoaId
     * @return string
     * @throws Exception
     */
    private function estaEmTurma($pessoaId){
        $numTurmas = json_decode($this->db->select("count(id_aluno) as n","aluno_turma","pessoa_id = ?",array($pessoaId)));
        $numTurmas = $numTurmas[0]->n;
        return $numTurmas;
    }

    /**
     * @param $pessoaId
     * @return string
     * @throws Exception
     */
    private function getNome($pessoaId){
        $pessoa = json_decode($this->db->select("nome,sobrenome","pessoa","id_pessoa = ?",array($pessoaId)));
        $pessoa = $pessoa[0]->nome." ".$pessoa[0]->sobrenome;
        return $pessoa;
    }

    /**
     * @throws Exception
     */
    public function getDataFromDuplicados(){
        $duplic = json_decode($this->getDuplicados());
        $stFmt = "";
        $pessoaId = 0;
        for($i=0;$i<sizeof($duplic);$i++){
            $pessoaId = $duplic[$i]->pessoa_id;
            $stFmt .= "<tr>
                        <td> {$pessoaId} </td>
                        <td> {$duplic[$i]->numero_documento} </td>
                        <td> {$this->getNome($pessoaId)} </td>
                        <td> {$this->estaEmTurma($pessoaId)} </td>";
            if($this->estaEmTurma($pessoaId) == 0)
                $stFmt .= "<td><input type=\"checkbox\" name=\"pessoa_id[]\" value=\"{$pessoaId}\" > </td>";
            else
                $stFmt .= "<td><input type=\"checkbox\" name=\"pessoa_id[]\" value=\"{$pessoaId}\"> </td>";
            $stFmt .= "</tr>";
        }
        return $stFmt;
    }
}
?>