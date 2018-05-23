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

if ( session_status() !== PHP_SESSION_ACTIVE ) {
    session_start();
}
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
            case "updatePredio":
                $infra = new infraestrutura();
                $infra->updatePredio($_GET['id']);
                break;
            case "insertSala":
                $infra = new infraestrutura();
                $infra->setSala();
                break;
            case "updateSala":
                $infra = new infraestrutura();
                $infra->updateSala($_GET['id']);
                break;
            case "selectPredio":
                $infra = new infraestrutura();
                echo $infra->getPredios();
                break;
            case "selectPredioById":
                $infra = new infraestrutura();
                try {
                    echo $infra->getPredioById($_GET['id']);
                } catch (Exception $e) {
                    echo $e;
                }
                break;
            case "selectSala":
                $infra = new infraestrutura();
                echo $infra->getSalas();
                break;
            case "insertPessoa":
                $pess = new pessoa();
                try {
                    $pess->setPessoa();
                } catch (Exception $e) {
                    $_SESSION['MSG'] = "{\"tipo\":\"erro\",\"desc\":\"Erro: ".$e."\"}";
                }
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
                header("Content-Type: application/json; charset=UTF-8");
                $turma = new turma();
                echo $turma->getTurmasAtivas();
                break;
            case "selectHorario":
                $turma = new turma();
                echo $turma->getHorariosBySalaId($_GET['id']);
                break;
            case "selectSalaByPredioId":
                $infra = new infraestrutura();
                try {
                    echo $infra->getSalaByPredioId($_GET['id']);
                } catch (Exception $e) {
                    $_SESSION['MSG'] = "{\"tipo\":\"erro\",\"desc\":\"Erro: ".$e."\"}";
                }
                break;
            case "selectSalaById":
                $infra = new infraestrutura();
                try {
                    echo $infra->getSalaById($_GET['id']);
                } catch (Exception $e) {
                    $_SESSION['MSG'] = "{\"tipo\":\"erro\",\"desc\":\"Erro: ".$e."\"}";
                }
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