<?php
/**
 * Created by Filipe
 * Date: 02/05/18
 * Time: 11:46
 * Sistema de Rotas para o controler
 */
include_once 'constantes.php';
include_once 'login.php';
include_once 'infraestrutura.php';
include_once 'oficina.php';
include_once 'pessoa.php';
include_once 'turma.php';

session_start();
class main{
    private $act;

    public function __construct(){

    }

    public function setAction(){
        $this->act = isset($_GET['req']) ? $_GET['req'] : INVALIDO;
        $this->doAction();
    }

    private function doAction(){
        switch ($this->act){
            case "login":
                $login = new login();
                $login->verifyUser();
                break;
            case "insertPredio":
                $infra = new infraestrutura();
                $infra->setPredio();
                break;
            case "insertSala":
                $infra = new infraestrutura();
                $infra->setSala();
                break;
            case "selectPredio":
                $infra = new infraestrutura();
                echo $infra->getPredios();
                break;
            case "selectSala":
                $infra = new infraestrutura();
                echo $infra->getSalas();
                break;
            case "insertPessoa":
                $pess = new pessoa();
                $pess->setPessoa();
                break;
            case "selectAdministrador":
                $pess = new pessoa();
                echo $pess->getAdministradores();
                break;
            case "selectProfessor":
                $pess = new pessoa();
                echo $pess->getProfesores();
                break;
            case "selectCandidato":
                $pess = new pessoa();
                echo $pess->getCandidatos();
                break;
            case "insertOficina":
                $ofic = new oficina();
                $ofic->setOficina();
                break;
            case "selectOficina":
                $ofic = new oficina();
                echo $ofic->getOficina();
                break;
            case "insertTurma":
                $turma = new turma();
                $turma->setTurma();
                break;
            case "selectTurma":
                $turma = new turma();
                echo $turma->getTurmas();
                break;
            case "selectTurmaAtiva":
                $turma = new turma();
                echo $turma->getTurmasAtivas();
                break;
            case "selectHorario":
                $turma = new turma();
                echo $turma->getHorariosBySalaId($_GET['id']);
                break;
            case "selectSalaById":
                $infra = new infraestrutura();
                echo $infra->getSalaById($_GET['id']);
                break;
            case "selecUsuarioLogado":
                if(isset($_SESSION['LOGADO']) and $_SESSION['LOGADO'] == true){
                    echo $_SESSION['USER'];
                }else{
                    header("Location: ../index.php?pag=Login");
                }
                break;
            case "logout":
                session_destroy();
                header("Location: ../index.php?pag=Login");
                break;
            default:
                    header("Location: ../index.php?pag=Login");
                break;
        }
    }

}

$controlador = new main();
$controlador->setAction();