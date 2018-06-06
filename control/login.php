<?php
/**
 * Created Filipe
 * Date: 27/04/18
 * Time: 18:22
 */
include_once '../model/DatabaseOpenHelper.php';
include_once 'constantes.php';
include_once 'mensagem.php';

class login{
    private $db;

    public function __construct(){
        $this->db = new Database();
    }

    /**
     * @throws Exception
     */
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
            $_SESSION['ID'] = $user[0]->id_pessoa;
            $_SESSION['NIVEL'] = $user[0]->nv_acesso;
            $user[0]->nv_acesso = $this->getNVacesso($user[0]->nv_acesso);

            $_SESSION['USER'] = json_encode($user[0],JSON_UNESCAPED_UNICODE);
            $this->getMenu();
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
            new mensagem(ERRO,"Login ou senha Incorretos");
            header("Location: " . $_SERVER['HTTP_REFERER'] . "");//MANDA DE VOLTA PARA O login
        }
    }

    public static function logout(){
        session_destroy();
        header("Location: ../index.php?pag=Login");
    }

    public static function getUser(){
        if(isset($_SESSION['LOGADO']) and $_SESSION['LOGADO'] == true){
            echo $_SESSION['USER'];
        }else{
            header("Location: ../index.php?pag=Login");
        }
    }

    private function getMenu(){
        switch ($_SESSION['NIVEL']){
            case ADMINISTRADOR:
                $_SESSION['MENU'] ='[{"nome":"Infra","link":"#InfraSubmenu","icone":"glyphicon-dashboard","submenu":[{"nome":"Gerenciar","link":"?pag=Infraestrutura"},{"nome":"Novo Predio","link":"?pag=Cad.Predio"},{"nome":"Nova Sala","link":"?pag=Cad.Sala"}]},{"nome":"Oficinas","link":"#OficinasSubmenu","icone":"glyphicon-knight","submenu":[{"nome":"Gerenciar","link":"?pag=Oficinas"},{"nome":"Nova Oficina","link":"?pag=Cad.Oficina"}]},{"nome":"Turmas","link":"#TurmasSubmenu","icone":"glyphicon-bell","submenu":[{"nome":"Gerenciar","link":"?pag=Turmas"},{"nome":"Nova Turma","link":"?pag=Cad.Turma"}]},{"nome":"Usuarios","link":"#UsuariosSubmenu","icone":"glyphicon-user","submenu":[{"nome":"Gerenciar","link":"?pag=Usuarios"},{"nome":"Novo Usuário","link":"?pag=Cad.Pessoa"}]},{"nome":"Alunos","link":"#AlunosSubmenu","icone":"glyphicon-education","submenu":[{"nome":"Gerenciar","link":"?pag=Alunos"},{"nome":"Cad. Aluno em Turma","link":"?pag=Cad.Aluno"}]}]';
                break;
            case PROFESSOR:
                $_SESSION['MENU'] ='[{"nome":"Infra","link":"#InfraSubmenu","icone":"glyphicon-dashboard","submenu":[{"nome":"Gerenciar","link":"?pag=Infraestrutura"},{"nome":"Novo Predio","link":"?pag=Cad.Predio"},{"nome":"Nova Sala","link":"?pag=Cad.Sala"}]},{"nome":"Oficinas","link":"#OficinasSubmenu","icone":"glyphicon-knight","submenu":[{"nome":"Gerenciar","link":"?pag=Oficinas"},{"nome":"Nova Oficina","link":"?pag=Cad.Oficina"}]},{"nome":"Turmas","link":"#TurmasSubmenu","icone":"glyphicon-bell","submenu":[{"nome":"Gerenciar","link":"?pag=Turmas"},{"nome":"Nova Turma","link":"?pag=Cad.Turma"}]},{"nome":"Usuarios","link":"#UsuariosSubmenu","icone":"glyphicon-user","submenu":[{"nome":"Gerenciar","link":"?pag=Usuarios"},{"nome":"Novo Usuário","link":"?pag=Cad.Pessoa"}]},{"nome":"Alunos","link":"#AlunosSubmenu","icone":"glyphicon-education","submenu":[{"nome":"Gerenciar","link":"?pag=Alunos"},{"nome":"Cad. Aluno em Turma","link":"?pag=Cad.Aluno"}]}]';
                break;
            case ALUNO:
                $_SESSION['MENU'] ='[{"nome":"Infra","link":"#InfraSubmenu","icone":"glyphicon-dashboard","submenu":[{"nome":"Gerenciar","link":"?pag=Infraestrutura"},{"nome":"Novo Predio","link":"?pag=Cad.Predio"},{"nome":"Nova Sala","link":"?pag=Cad.Sala"}]},{"nome":"Oficinas","link":"#OficinasSubmenu","icone":"glyphicon-knight","submenu":[{"nome":"Gerenciar","link":"?pag=Oficinas"},{"nome":"Nova Oficina","link":"?pag=Cad.Oficina"}]},{"nome":"Turmas","link":"#TurmasSubmenu","icone":"glyphicon-bell","submenu":[{"nome":"Gerenciar","link":"?pag=Turmas"},{"nome":"Nova Turma","link":"?pag=Cad.Turma"}]},{"nome":"Usuarios","link":"#UsuariosSubmenu","icone":"glyphicon-user","submenu":[{"nome":"Gerenciar","link":"?pag=Usuarios"},{"nome":"Novo Usuário","link":"?pag=Cad.Pessoa"}]},{"nome":"Alunos","link":"#AlunosSubmenu","icone":"glyphicon-education","submenu":[{"nome":"Gerenciar","link":"?pag=Alunos"},{"nome":"Cad. Aluno em Turma","link":"?pag=Cad.Aluno"}]}]';
                break;
            case VISITANTE:
                $_SESSION['MENU'] ='[]';
                break;
            default:
                $_SESSION['MENU'] ='[]';
                break;
        }
    }
}