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
        //Obtemos todos os professores com left Join em Maior idade pois e obrigatorio ser maior de idade
        //entretando selecionamos tambem os que nao completaram as informacoes
        $joinClause = " LEFT JOIN maior_idade ON id_pessoa = pessoa_id";
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
}

$pess = new getPessoas();
echo $pess->getAdministradores();