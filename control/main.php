<?php
/**
 * Created by PhpStorm.
 * User: filipe
 * Date: 02/05/18
 * Time: 11:46
 */

class main{
    private $pagina;

    public function __construct(){
        $this->getPagina();
        $this->setControl();
    }

    private function getPagina(){
        $this->pagina = $_GET['requisicao'];
    }

    private function setControl(){
        switch ($this->pagina){
            case 'getInfra':
                //todo
                break;
            case 'setInfra':
                //todo
                break;
            case 'getPessoas':
                //todo
                break;
            case 'setPessoas';
                //todo
                break;
            default:
                //todo
                break;
        }
    }

}