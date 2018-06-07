<?php
/**
 * Created by Filipe
 * Date: 28/04/18
 * Time: 18:50
 */

include_once '../model/DatabaseOpenHelper.php';
include_once 'constantes.php';
include_once 'mensagem.php';

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
                if($this->db->insert("nome,localizacao", "predio", $params)){
                    new mensagem(SUCESSO,"Predio: ".$nome." cadastrado com sucesso!!");
                }else{
                    new mensagem(INSERT_ERRO,"Erro ao inserir");
                }

            } catch (Exception $e) {
                new mensagem(ERRO,"Erro: ".$e);
            }
        }

        $this->redireciona();
    }

    public function updatePredio($identificador){
        $nome = isset($_POST['nome']) ? $_POST['nome'] : INVALIDO;
        $localizacao = isset($_POST['localizacao']) ? $_POST['localizacao'] : INVALIDO;
        $ativo = isset($_POST['ativo']) ? $_POST['ativo'] : NAO;
        $params = array($nome,$localizacao,$ativo);

        if($nome != INVALIDO){
            try {
                $columns = array("nome","localizacao","is_ativo");

                if($this->db->update($columns,"predio",$params,"id_predio=?",array($identificador))){
                    new mensagem(SUCESSO," Predio: ".$nome." atualizado com sucesso!!");
                }else{
                    new mensagem(INSERT_ERRO,"Erro ao atualizar");
                }

            } catch (Exception $e) {
                new mensagem(ERRO,"Erro: ".$e);
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
                if($this->db->insert("predio_id,nome,is_ativo", "sala", $params)){
                    new mensagem(SUCESSO," ".$nome." cadastrado com sucesso!!");
                }else{
                    new mensagem(INSERT_ERRO,"Erro ao inserir");
                }

            } catch (Exception $e) {
                new mensagem(ERRO,"Erro: ".$e);
            }
        }
        $this->redireciona();
    }

    public function updateSala($identificador){
        $nome = isset($_POST['nome']) ? $_POST['nome'] : INVALIDO;
        $is_ativo = isset($_POST['ativo']) ? $_POST['ativo'] : SIM;

        $params = array($nome,$is_ativo);
        if($nome != INVALIDO){
            try {
                $columns = array("nome","is_ativo");
                if($this->db->update($columns,"sala",$params,"id_sala = ?",array($identificador))){
                    new mensagem(SUCESSO," ".$nome." atualizado com sucesso!!");
                }else{
                    new mensagem(INSERT_ERRO,"Erro ao atualizar");
                }

            } catch (Exception $e) {
                new mensagem(ERRO,"Erro: ".$e);
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
     * @param $identificador Integer
     * @return string
     * @throws Exception
     */
    public function getPredioById($identificador){
        return $this->db->select("nome,localizacao,is_ativo","predio","id_predio = ?",array($identificador));
    }
    /**
     * @param $identificador
     * @return string
     * @throws Exception
     */
    public function getSalaById($identificador){
        //retornamos uma sala especifica
        return $this->db->select("id_sala,nome,is_ativo","sala","id_sala = ?",array($identificador));
    }
    /**
     * @param $identificador
     * @return string
     * @throws Exception
     */
    public function getSalaByPredioId($identificador){

        //retornamos as salas de um predio especifico
        return $this->db->select("id_sala,nome","sala","predio_id = ?",array($identificador));
    }

    private function redireciona(){
        //depois de inserir redirecionamos para a pagina de infra
        header("Location: ../index.php?pag=Infraestrutura");
    }
}