<?php
/**
 * Created by Filipe
 * Date: 28/04/18
 * Time: 11:33
 */
include_once '../model/DatabaseOpenHelper.php';
class getPessoas{
    private $db;

    public function __construct(){
        $this->db = new Database();
    }

    /**
     * @return string JSON
     */
    public function getAdministradores(){
        //Obtemos todos os administradores com left Join em Maior idade pois e obrigatorio ser maior de idade
        //entretando selecionamos tambem os que nao completaram as informacoes
        $joinClause = " LEFT JOIN documento ON id_pessoa = pessoa_id";
        $adm = $this->db->select("id_pessoa,nome,nv_acesso,menor_idade,ruralino,data_nascimento,numero_documento,tipo_documento","pessoa".$joinClause,"nv_acesso = ?",array(1));
        //transformamos o JSON em objeto php
        $objAdm = json_decode($adm);
        //verificmos se esse administrador esuda na rural e adicionamos as informacoes necessarias
        for($i=0;$i< sizeof($objAdm);$i++){
            if($objAdm[$i]->ruralino == 1){
                $ruralino = json_decode($this->db->select("curso,bolsista","ruralino","pessoa_id = ?",array($objAdm[$i]->id_pessoa)));
                $objAdm[$i]->curso = $ruralino[$i]->curso;
                $objAdm[$i]->bolsista = $ruralino[$i]->bolsista;

            }
            $adm = json_encode($objAdm,JSON_UNESCAPED_UNICODE);
        }

        return $adm;
    }
    public function getProfesores(){
        //Obtemos todos os professores com left Join em Maior idade
        $joinClause = " LEFT JOIN documento ON id_pessoa = pessoa_id";
        $prof = $this->db->select("id_pessoa,nome,nv_acesso,menor_idade,ruralino,data_nascimento,numero_documento,tipo_documento","pessoa".$joinClause,"nv_acesso = ?",array(2));
        //transformamos o JSON em objeto php
        $objProf = json_decode($prof);
        //verificmos se esse administrador esuda na rural e adicionamos as informacoes necessarias
        for($i=0;$i< sizeof($objProf);$i++){
            if($objProf[$i]->ruralino == 1){
                $ruralino = json_decode($this->db->select("curso,bolsista","ruralino","pessoa_id = ?",array($objProf[$i]->id_pessoa)));
                $objProf[$i]->curso = $ruralino[$i]->curso;
                $objProf[$i]->bolsista = $ruralino[$i]->bolsista;

            }
            $prof = json_encode($objProf,JSON_UNESCAPED_UNICODE);
        }

        return $prof;
    }

    public function getCandidatos(){
        //Obtemos todos os Candidatos com left Join em Maior idade
        $joinClause = " LEFT JOIN documento ON id_pessoa = pessoa_id";
        $cand = $this->db->select("id_pessoa,nome,nv_acesso,menor_idade,ruralino,data_nascimento,numero_documento,tipo_documento","pessoa".$joinClause,"nv_acesso >= ?",array(3));
        //transformamos o JSON em objeto php
        $objCand = json_decode($cand);
        //verificmos se esse administrador esuda na rural e adicionamos as informacoes necessarias
        for($i=0;$i< sizeof($objCand);$i++){
            //aqui verificamos se o candidato e da rural
            if($objCand[$i]->ruralino == 1){
                $ruralino = json_decode($this->db->select("curso,bolsista","ruralino","pessoa_id = ?",array($objCand[$i]->id_pessoa)));
                $objCand[$i]->curso = $ruralino[0]->curso;
                $objCand[$i]->bolsista = $ruralino[0]->bolsista;

            }
            //aqui verificamos se o candidato e menor de idade e buscamos seu responsavel
            if($objCand[$i]->menor_idade == 1){
                $respsavel = json_decode($this->db->select("nome,responsavel_parentesco","menor_idade,pessoa","responsavel_id = id_pessoa and pessoa_id = ?",array($objCand[$i]->id_pessoa)));
                $objCand[$i]->responsavel = $respsavel[0]->nome;
                $objCand[$i]->parentesco = $respsavel[0]->responsavel_parentesco;
            }
            $cand = json_encode($objCand,JSON_UNESCAPED_UNICODE);
        }

        return $cand;
    }
}
//Dependendo de como será o design podemos enviar as informaçoes de todos os usuario do sistema
//ou enviamos cada tipo de usuario numa requisicao diferente
$pess = new getPessoas();
echo $pess->getAdministradores();
echo "<br>Professores: <br>";
echo $pess->getProfesores();
echo "<br>Candidatos e visitantes: <br>";
echo $pess->getCandidatos();