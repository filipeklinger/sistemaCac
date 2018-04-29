<?php
/**
 * Created Filipe
 * Date: 27/04/18
 * Time: 18:22
 */
include_once '../model/DatabaseOpenHelper.php';
class validadorLogin{
    private $db;

    public function __construct(){
        $this->db = new Database();
    }

    public function verifyUser($login, $senha){
        //primeiro buscamos os usuarios possiveis
        $usr = json_decode($this->db->select("senha,pessoa_id","login","usuario = ?",array($login)));
        //depois vericamos se o usuario encontrado e a senha informada conferem
        if($usr != null and password_verify($senha,$usr[0]->senha)){
            $user = json_decode($this->db->select("id_pessoa,nome,nv_acesso","pessoa","id_pessoa = ?",array($usr[0]->pessoa_id)));
            $_SESSION['LOGADO'] = true;
            $_SESSION['USER'] = $user[0]->nome;
            $_SESSION['NIVEL'] = $user[0]->nv_acesso;
            $_SESSION['ID'] = $user[0]->id_pessoa;
            $this->redireciona(true);
        }else{
            $_SESSION['LOGADO'] = false;
            $this->redireciona(false);
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
//Obtendo dados atraves de GET (para um debug mais facil)
$login = isset($_POST['login']) ? $_POST['login'] : 'erro';
$senha = isset($_POST['senha']) ? $_POST['senha'] : 0;

$objLogin = new validadorLogin();

$objLogin->verifyUser($login,$senha);
//no banco de dados essa senha esta gravada como
//PASSWORD_BCRYPT: $2y$10$rPPCz9EYNmxCPKSB3vUERe/zQLX0ZdldkyrCppn6cSRsRn.n/9TQq