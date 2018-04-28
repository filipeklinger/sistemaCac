<?php
/**
 * Created Filipe
 * Date: 27/04/18
 * Time: 18:22
 */
include_once '../model/DatabaseOpenHelper.php';
class login{
    private $db;

    public function __construct(){
        $this->db = new Database();
    }

    public function verifyUser($usuario,$senha){
        //usuario retornado caso de erro no login
        $user = "[{\"id_pessoa\":\"-1\",\"nome\":\"err\",\"nv_acesso\":\"-1\"}]";
        //primeiro buscamos os usuarios possiveis
        $usr = json_decode($this->db->select("senha,pessoa_id","login","usuario = ?",array($usuario)));
        //depois vericamos se o usuario encontrado e a senha informada conferem
        if(password_verify($senha,$usr[0]->senha)){
            $user = $this->db->select("id_pessoa,nome,nv_acesso","pessoa","id_pessoa = ?",array($usr[0]->pessoa_id));
        }
        return $user;
    }
}

$login = new login();
echo $login->verifyUser("filipe",123);
//no banco de dados essa senha esta gravada como
//PASSWORD_BCRYPT: $2y$10$rPPCz9EYNmxCPKSB3vUERe/zQLX0ZdldkyrCppn6cSRsRn.n/9TQq