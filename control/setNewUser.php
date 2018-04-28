<?php
/**
 * Created by PhpStorm.
 * User: filipe
 * Date: 28/04/18
 * Time: 15:39
 */
include_once '../model/DatabaseOpenHelper.php';
include 'constantes.php';
class newUser{
    private $db;
    private $responsavelID,$parentesco,$user,$senha;

    public function __construct(){
        $this->db = new Database();
    }


    /**
     * Esse Metodo recebe primeiro o numero de cadastros pois como foi definida na documentação
     * um usuário pode cadastrar varios dependentes (menores de idade)
     * @return bool - Cadastrado ou nao
     */
    public function recebeDados(){
        $num_cadastros = isset($_POST['num_cadastros']) ? $_POST['num_cadastros'] : 0;

        //cada cadastro deve ter somente um login
        $this->user = isset($_POST['usuario']) ? $_POST['usuario'] : '';
        $this->senha = isset($_POST['senha']) ? $_POST['senha'] : INVALIDO;

        if($this->senha == INVALIDO){return "Erro";}

        $this->parentesco = isset($_POST['parentesco']) ? $_POST['parentesco'] : INVALIDO;

        //aqui iteramos sobre os dados de usuarios enviados
        for($i=0;$i<sizeof($num_cadastros);$i++){

            //iterando sobre a matriz POST
            $nomeAtual = "nome".$i;
            $sobrenomeAtual = "sobrenome".$i;
            $nvAcessoAtual = "nv_acesso".$i;
            $menorAtual = "menor_idade".$i;
            $ruralinoAtual = "ruralino".$i;
            $nascimentoAtual = "data_nascimento".$i;

            //Obtendo variaveis
            $nome = $_POST[$nomeAtual];
            $sobrenome = $_POST[$sobrenomeAtual];
            $nvAcesso = isset($_POST[$nvAcessoAtual]) ? $_POST[$nvAcessoAtual] : VISITANTE;
            $menor = $_POST[$menorAtual];
            $ruralino = $_POST[$ruralinoAtual];
            $nascimento = $_POST[$nascimentoAtual];

            //aqui finalmente inserimos os dados na tabela Pessoa e recuperamos o identificador
            $pessoaId = $this->insertDadosBasicos($nome,$sobrenome,$nvAcesso,$menor,$ruralino,$nascimento);

            if($menor == NAO){
                //caso menor sera acessado pelo login do responsavel
                $this->responsavelID = $this->insertLogin($pessoaId);
                $this->insertDocumento($pessoaId);
            }else{
                $this->insertDependente($pessoaId);
            }

            if($ruralino == SIM){
                $this->insertRuralino($pessoaId);
            }

        }
        return true;
    }

    /**
     * @return bool| integer - Retorna o ID do dado inserido ou falso se der erro
     */
    private function insertDadosBasicos($nome, $sobre, $nv, $menor, $rural, $nasc){
        $params = array($nome,$sobre,$nv,$menor,$rural,$nasc);
        $this->db->insert("nome,sobrenome,nv_acesso,menor_idade,ruralino,data_nascimento","pessoa",$params);
        return $this->db->getLastId();
    }

    private function insertDocumento($pessoaID){
        $numero = isset($_POST['numero_documento']) ? $_POST['numero_documento'] : INVALIDO;
        $tipo = isset($_POST['tipo_documento']) ? $_POST['tipo_documento'] : INVALIDO;

        $params = array($pessoaID,$numero,$tipo);
        $this->db->insert("pessoa_id,numero_documento,tipo_documento","documento",$params);

    }

    private function insertRuralino($pessoaID){
        $matricula = isset($_POST['matricula']) ? $_POST['matricula'] : INVALIDO;
        $curso = isset($_POST['curso']) ? $_POST['curso'] : INVALIDO;
        $bolsista = isset($_POST['bolsista']) ? $_POST['bolsista'] : INVALIDO;

        $params = array($pessoaID,$matricula,$curso,$bolsista);
        $this->db->insert("pessoa_id,matricula,curso,bolsista","ruralino",$params);
    }

    /**
     * @param $pessoa_id int - identificador da pessoa na tabela pessoa
     * @return bool|integer - Retorna o ID do dado inserido ou falso se der erro
     */
    private function insertLogin($pessoa_id){
        $params = array($pessoa_id,$this->user,$this->make_hash($this->senha));
        $this->db->insert("pessoa_id,usuario,senha","login",$params);

        return $this->db->getLastId();
    }

    private function insertDependente($pessoaAtualID){
        $paramns = array($pessoaAtualID,$this->responsavelID,$this->parentesco);
        return $this->db->insert("pessoa_id,responsavel_id,responsavel_parentesco","menor_idade",$paramns);
    }

    /**
     * @param $str string - senha a ser Encriptada
     * @return bool|string - senha encriptada
     */
    private function make_hash($str){
        return password_hash($str, PASSWORD_BCRYPT);

    }
}

//Criando instancia
$cadastro = new newUser();
$cadastro->recebeDados();