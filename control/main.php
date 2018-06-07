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
include_once 'aluno.php';
include_once 'relatorio.php';
include_once 'mensagem.php';

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
                //Infraestrutura
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
                    new mensagem(ERRO,$e);
                }
                break;
            case "selectSala":
                $infra = new infraestrutura();
                echo $infra->getSalas();
                break;
            case "selectSalaByPredioId":
                $infra = new infraestrutura();
                try {
                    echo $infra->getSalaByPredioId($_GET['id']);
                } catch (Exception $e) {
                    new mensagem(ERRO,$e);
                }
                break;
            case "selectSalaById":
                $infra = new infraestrutura();
                try {
                    echo $infra->getSalaById($_GET['id']);
                } catch (Exception $e) {
                    new mensagem(ERRO,$e);
                }
                break;
                //Pessoa
            case "insertPessoa":
                $pess = new pessoa();
                try {
                    $pess->setPessoa();
                } catch (Exception $e) {
                    new mensagem(ERRO,"Erro: ".$e);
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
            case "selectPessoaById":
                $pess = new pessoa();
                echo $pess->getPessoaById($_GET['id']);
                break;
            case "selectRuralinoByPessoaId":
                $pess = new pessoa();
                echo $pess->getRuralinoByPessoaId($_GET['id']);
                break;
            case "selectResponsavelByMenorId":
                $pess = new pessoa();
                echo $pess->getResponsavelByMenorId($_GET['id']);
                break;
            case "selectTelefoneByPessoaId":
                $pess = new pessoa();
                echo $pess->getTelefone($_GET['id']);
                break;
            case "selectEndereco":
                $pess = new pessoa();
                echo $pess->getEndereco($_GET['id']);
                break;
            case "selectDocumento":
                $pess = new pessoa();
                echo $pess->getDocumento($_GET['id']);
                break;
            case "selectDependentes":
                $pess = new pessoa();
                echo $pess->getDependentes($_GET['id']);
                break;
            case "updateDadosBasicos":
                $pess = new pessoa();
                $pess->updateDadosBasicos($_GET['id']);
                break;
            case "updateEndereco":
                $pess = new pessoa();
                $pess->updateEndereco($_GET['id']);
                break;
            case "updateDoc":
                $pess = new pessoa();
                $pess->updateDocument($_GET['id']);
                break;
            case "updateContato":
                $pess = new pessoa();
                $pess->updateContato($_GET['id']);
                break;
            //Oficina
            case "insertOficina":
                $ofic = new oficina();
                $ofic->setOficina();
                break;
            case "updateOficina":
                $ofic = new oficina();
                $ofic->updateOficina($_GET['id']);
                break;
            case "selectOficina":
                $ofic = new oficina();
                echo $ofic->getOficina();
                break;
            case "selectOficinaById":
                $ofic = new oficina();
                echo $ofic->getOficinaById($_GET['id']);
                break;
                //TURMA
            case "insertTurma":
                $turma = new turma();
                $turma->setTurma();
                break;
            case "selectTurma":
                $turma = new turma();
                echo $turma->getTurmas($_GET['id']);
                break;
            case "updateTurma":
                $turma = new turma();
                $turma->updateTurma($_GET['id']);
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
            case "selectTurmaById":
                $turma = new turma();
                echo $turma->getTurmaById($_GET['id']);
                break;
            case "selectPeriodoAtual":
                $turma = new turma();
                echo $turma->getTempo();
                break;
            case "selectTempoHistorico":
                $turma = new turma();
                echo $turma->getTempoHistorico();
                break;
            case "setPeriodo":
                $turma = new turma();
                $turma->setPeriodo();
                break;
                //Aluno
            case "insertAluno":
                $aluno = new aluno();
                try {
                    $aluno->setAluno();
                } catch (Exception $e) {
                    new mensagem(ERRO,$e);
                }
                break;
            case "selectAlunosByTurmaId":
                $aluno = new aluno();
                try {
                    $aluno->getAlunos($_GET['id']);
                } catch (Exception $e) {
                    new mensagem(ERRO,$e);
                }
                break;
            case "selectAlunosListaEspera":
                $aluno = new aluno();
                try {
                    echo $aluno->getAlunoListaEspera($_GET['id']);
                } catch (Exception $e) {
                    new mensagem(ERRO,$e);
                }
                break;
            case "trancarMatricula":
                $aluno = new aluno();
                try {
                    $aluno->trancarMatricula($_GET['id']);
                } catch (Exception $e) {
                    echo($e);
                }
                break;
            //Relatorio
            case "relAlunosAtivos":
                $relatorio = new relatorio();
                echo $relatorio->getTotalAlunosAtivos();
                break;
            case "relAlunosPorTurmaPeriodo":
                $relatorio = new relatorio();
                echo $relatorio->getAlunosPorOficina($_GET['id']);
                break;
            //USUARIO
            case "selecUsuarioLogado":
                login::getUser();
                break;
            case "logout":
                login::logout();
                break;
            default:
                    header("Location: ../index.php?pag=Login");
                break;
        }
    }

}

$controlador = new main();
$controlador->setAction();