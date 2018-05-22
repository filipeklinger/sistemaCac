<?php
/**
 * Created by Filipe
 * Date: 28/04/18
 * Time: 18:50
 */

include_once '../model/DatabaseOpenHelper.php';
include_once 'constantes.php';

class infraestrutura{
    private $db;

    public function __construct(){
        $this->db = new Database();
    }

    public function setPredio(){
        $nome = isset($_POST['nome']) ? $_POST['nome'] : INVALIDO;
        $localizacao = isset($_POST['localizacao']) ? $_POST['localizacao'] : INVALIDO;
        $params = array($nome,$localizacao);

        if($nome != INVALIDO){
            try {
                $this->db->insert("nome,localizacao", "predio", $params);
                $_SESSION['MSG'] = "{\"tipo\":\"sucesso\",\"desc\":\"".$nome." cadastrado com sucesso!!\"}";
            } catch (Exception $e) {
                $_SESSION['MSG'] = "{\"tipo\":\"erro\",\"desc\":\"Erro: ".$e."\"}";
            }
        }

        $this->redireciona();
    }

    public function setSala(){
        $predioId = isset($_POST['predio_id']) ? $_POST['predio_id'] : INVALIDO;;
        $nome = isset($_POST['nome']) ? $_POST['nome'] : INVALIDO;
        $is_ativo = 1;

        $params = array($predioId,$nome,$is_ativo);
        if($nome != INVALIDO){
            try {
                $this->db->insert("predio_id,nome,is_ativo", "sala", $params);
                $_SESSION['MSG'] = "{\"tipo\":\"sucesso\",\"desc\":\" Sala de ".$nome." cadastrado com sucesso!!\"}";
            } catch (Exception $e) {
                $_SESSION['MSG'] = "{\"tipo\":\"erro\",\"desc\":\"Erro: ".$e."\"}";
            }
        }
        $this->redireciona();
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getPredios(){
        return $this->db->select("id_predio,nome,localizacao,is_ativo", "predio");

    }

    /**
     * @return string
     * @throws Exception
     */
    public function getSalas(){
        //retornamos as salas agrupadas por predio
        return $this->db->select("id_sala,predio.nome as predio,sala.nome as sala,sala.is_ativo", "predio,sala", "id_predio = predio_id", null, "predio_id");
    }

    /**
     * @param $identificador
     * @return string
     * @throws Exception
     */
    public function getSalaById($identificador){

        //retornamos as salas de um predio especifico
        return $this->db->select("id_sala,nome","sala","predio_id = ?",array($identificador));
    }

    private function redireciona(){
        //depois de inserir redirecionamos para a pagina de infra
        header("Location: ../index.php?pag=Infraestrutura");
    }
}