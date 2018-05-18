<?php
/**
 * Created by PhpStorm.
 * User: filipe
 * Date: 28/04/18
 * Time: 15:39
 */
include_once '../model/DatabaseOpenHelper.php';
include_once 'constantes.php';

class pessoa{
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
    public function setPessoa(){
        /*
Campos enviados pelo form         *
menor	1
nv_acesso	2
nome	Filipe
sobrenome	klinger
ruralino	1
matricula	2016390288
curso	Sistemas de informação
nascimento	1994-08-23
resp_tel	972935253
resp_tel_type	2
doc_type	1
doc_number	273319186
rua	Rua das acacias
numero	12
complemento	ap 205
bairro	campo lindo
cidade	Seropédica
estado	RJ
nome_menor0	pedrinho
sobrenome_menor0	junior
nascimento_menor0	2008-05-03

        $num_cadastros = isset($_POST['num_cadastros']) ? $_POST['num_cadastros'] : INVALIDO;
        if($num_cadastros == INVALIDO) return false;
        //cada cadastro deve ter somente um login
        $this->user = isset($_POST['usuario']) ? $_POST['usuario'] : '';
        $this->senha = isset($_POST['senha']) ? $_POST['senha'] : INVALIDO;

        if($this->senha == INVALIDO){return "Erro";}

        $this->parentesco = isset($_POST['parentesco']) ? $_POST['parentesco'] : INVALIDO;

        //aqui iteramos sobre os dados de usuario enviados
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
        */
       echo "<table>";
            foreach ($_POST as $key => $value) {
                echo "<tr>";
                echo "<td>";
                echo $key;
                echo "</td>";
                echo "<td>";
                echo $value;
                echo "</td>";
                echo "</tr>";
            }
        echo "</table>";
    }

    /**
     * @return bool| integer - Retorna o ID do dado inserido ou falso se der erro
     * @throws Exception
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
     * @throws Exception
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

    /**
     * @return string JSON
     */
    public function getAdministradores(){
        //Obtemos todos os administradores com left Join em Maior idade pois e obrigatorio ser maior de idade
        //entretando selecionamos tambem os que nao completaram as informacoes
        $joinClause = " LEFT JOIN documento ON id_pessoa = pessoa_id";
        try {
            $adm = $this->db->select("id_pessoa,nome,nv_acesso,menor_idade,ruralino,data_nascimento,numero_documento,tipo_documento", "pessoa" . $joinClause, "nv_acesso = ?", array(1));
        } catch (Exception $e) {
        }
        //transformamos o JSON em objeto php
        $objAdm = json_decode($adm);
        //verificmos se esse administrador esuda na rural e adicionamos as informacoes necessarias
        for($i=0;$i< sizeof($objAdm);$i++){
            if(isset($objAdm[$i]->ruralino) and $objAdm[$i]->ruralino == 1){
                $ruralino = json_decode($this->db->select("curso,bolsista","ruralino","pessoa_id = ?",array($objAdm[$i]->id_pessoa)));
                if(sizeof($ruralino) > 0){
                    $objAdm[$i]->curso = $ruralino[$i]->curso;
                    $objAdm[$i]->bolsista = $ruralino[$i]->bolsista;
                }


            }
            $adm = json_encode($objAdm,JSON_UNESCAPED_UNICODE);
        }

        return $adm;
    }
    public function getProfesores(){
        //Obtemos todos os professores com left Join em Maior idade
        $joinClause = " LEFT JOIN documento ON id_pessoa = pessoa_id";
        $prof = $this->db->select("id_pessoa,nome,nv_acesso,menor_idade,ruralino,data_nascimento,numero_documento,tipo_documento","pessoa".$joinClause,"nv_acesso <= ?",array(2));
        //transformamos o JSON em objeto php
        $objProf = json_decode($prof);
        //verificmos se esse administrador esuda na rural e adicionamos as informacoes necessarias
        for($i=0;$i< sizeof($objProf);$i++){
            if($objProf[$i]->ruralino == 1){
                $ruralino = json_decode($this->db->select("curso,bolsista","ruralino","pessoa_id = ?",array($objProf[$i]->id_pessoa)));

                if(sizeof($ruralino)> 0){//verificacao adicional para verificar se existe a tupla indicada
                    $objProf[$i]->curso = $ruralino[0]->curso;
                    $objProf[$i]->bolsista = $ruralino[0]->bolsista;
                }else{
                    $objProf[$i]->curso = null;
                    $objProf[$i]->bolsista = null;
                }


            }
            $prof = json_encode($objProf,JSON_UNESCAPED_UNICODE);
        }

        return $prof;
    }

    public function getCandidatos(){
        //Obtemos todos os Candidatos com left Join em Maior idade
        $joinClause = " LEFT JOIN documento ON id_pessoa = pessoa_id";
        $cand = $this->db->select("id_pessoa,nome,nv_acesso,menor_idade,ruralino,data_nascimento,numero_documento,tipo_documento","pessoa".$joinClause,"nv_acesso >= ?",array(3));
        //transformamos o JSON em objeto php
        $objCand = json_decode($cand);
        //verificmos se esse administrador esuda na rural e adicionamos as informacoes necessarias
        for($i=0;$i< sizeof($objCand);$i++){
            //aqui verificamos se o candidato e da rural
            if($objCand[$i]->ruralino == 1){
                $ruralino = json_decode($this->db->select("curso,bolsista","ruralino","pessoa_id = ?",array($objCand[$i]->id_pessoa)));
                $objCand[$i]->curso = $ruralino[0]->curso;
                $objCand[$i]->bolsista = $ruralino[0]->bolsista;

            }
            //aqui verificamos se o candidato e menor de idade e buscamos seu responsavel
            if($objCand[$i]->menor_idade == 1){
                $respsavel = json_decode($this->db->select("nome,responsavel_parentesco","menor_idade,pessoa","responsavel_id = id_pessoa and pessoa_id = ?",array($objCand[$i]->id_pessoa)));
                $objCand[$i]->responsavel = $respsavel[0]->nome;
                $objCand[$i]->parentesco = $respsavel[0]->responsavel_parentesco;
            }
            $cand = json_encode($objCand,JSON_UNESCAPED_UNICODE);
        }

        return $cand;
    }
}