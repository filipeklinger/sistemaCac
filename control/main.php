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
            case 1:
                $login = new login();
                $login->verifyUser();
                break;
            case 2:
                $infra = new infraestrutura();
                $infra->setPredio();
                break;
            case 3:
                $infra = new infraestrutura();
                $infra->setSala();
                break;
            case 4:
                $infra = new infraestrutura();
                echo $infra->getPredios();
                break;
            case 5:
                $infra = new infraestrutura();
                echo $infra->getSalas();
                break;
            case 6:
                $pess = new pessoa();
                $pess->setPessoa();
                break;
            case 7:
                $pess = new pessoa();
                echo $pess->getAdministradores();
                break;
            case 8:
                $pess = new pessoa();
                echo $pess->getProfesores();
                break;
            case 9:
                $pess = new pessoa();
                echo $pess->getCandidatos();
                break;
            case 10:
                $ofic = new oficina();
                $ofic->setOficina();
                break;
            case 11:
                $ofic = new oficina();
                echo $ofic->getOficina();
                break;
            case 12:
                $turma = new turma();
                $turma->setTurma();
                break;
            case 13:
                $turma = new turma();
                $turma->getTurmas();
                break;
            case 14:
                $infra = new infraestrutura();
                echo $infra->getSalaById($_GET['id']);
                break;
            default:
                echo "Erro nenhuma requisição solicitada";
                break;
        }
    }

}

$controlador = new main();
$controlador->setAction();