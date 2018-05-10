<?php
/**
 * Created Filipe
 * Date: 27/04/18
 * Time: 18:22
 */
include_once '../model/DatabaseOpenHelper.php';
include_once 'constantes.php';

class login{
    private $db;

    public function __construct(){
        $this->db = new Database();
    }

    public function verifyUser(){
        //Obtendo dados atraves de POST
        $login = isset($_POST['login']) ? $_POST['login'] : INVALIDO;
        $senha = isset($_POST['senha']) ? $_POST['senha'] : INVALIDO;

        //primeiro buscamos os usuario possiveis
        try {
            $usr = json_decode($this->db->select("senha,pessoa_id", "login", "usuario = ?", array($login)));
        } catch (Exception $e) {
        }

        //depois vericamos se o usuario encontrado e a senha informada conferem
        if($usr != null and password_verify($senha,$usr[0]->senha)){
            $user = json_decode($this->db->select("id_pessoa,nome,nv_acesso","pessoa","id_pessoa = ?",array($usr[0]->pessoa_id)));
            $_SESSION['LOGADO'] = true;
            $user[0]->nv_acesso = $this->getNVacesso($user[0]->nv_acesso);

            $_SESSION['USER'] = json_encode($user[0],JSON_UNESCAPED_UNICODE);
            $_SESSION['NIVEL'] = $user[0]->nv_acesso;
            $_SESSION['ID'] = $user[0]->id_pessoa;
            $this->redireciona(true);
        }else{
            $_SESSION['LOGADO'] = false;
            $this->redireciona(false);
        }
    }

    private function getNVacesso($nv){
        switch ($nv){
            case ADMINISTRADOR:
                return "Administrador";
            case PROFESSOR:
                return "Oficineiro";
            case ALUNO:
                return "Aluno";
            default:
                return "Visitante";
        }
    }

    private function redireciona($is_logado){
        if($is_logado){
            header("Location: ../index.php?pag=DashBoard");
        }else{
            header("Location: " . $_SERVER['HTTP_REFERER'] . "");//MANDA DE VOLTA PARA O login
        }
    }
}