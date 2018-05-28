<?php
/**
 * Created by Filipe
 * Date: 28/04/18
 * Time: 15:39
 */
include_once '../model/DatabaseOpenHelper.php';
include_once 'constantes.php';
include_once 'mensagem.php';

class pessoa{
    private $db;

    //Dados basicos
    private $nv,$docNumber,$docType;
    //contato
    private $respTel,$respTelType;
    //Endereco
    private $rua,$numero,$complemento,$bairro,$cidade,$estado;
    //ruralino
    private $matricula,$curso;
    //Caso menor
    private $responsavelID,$parentesco = INVALIDO;//inicializamos como invalido
    //login
    private $user,$senha;

    public function __construct(){
        $this->db = new Database();
    }


    /**
     * um usuário pode cadastrar varios dependentes (menores de idade)
     * @throws Exception
     */
    public function setPessoa(){

        if (isset($_SESSION['NIVEL']) and $_SESSION['NIVEL'] == "Administrador") {//se não for adm o nivel é automaticamente aluno
            $this->nv = isset($_POST['nv_acesso']) ? $_POST['nv_acesso'] : VISITANTE;//se der erro fica como visitante
        } else {
            $this->nv = ALUNO;
        }
        //-----------------DADOS BASICOS------------------------------
        $nome = isset($_POST['nome']) ? $_POST['nome'] : INVALIDO;
        $sobrenome = isset($_POST['sobrenome']) ? $_POST['sobrenome'] : INVALIDO;
        $nascimento = isset($_POST['nascimento']) ? $_POST['nascimento'] : INVALIDO;
        $ruralino = isset($_POST['ruralino']) ? SIM : NAO;

        $this->responsavelID = $this->insertDadosBasicos($nome,$sobrenome,$this->nv,NAO,$ruralino,$nascimento);//recuperamos o ID do adulto cadastrado
        if($this->responsavelID != null and $this->responsavelID != INVALIDO){
            new mensagem(SUCESSO,$nome." cadastrado com sucesso");
        }else{
            new mensagem(INSERT_ERRO,"Erro ao cadastrar");
        }
        //---------------Contato------------------------------------------------------------------
        $this->respTel = isset($_POST['resp_tel']) ? $_POST['resp_tel'] : INVALIDO;
        $this->respTelType = isset($_POST['resp_tel_type']) ? $_POST['resp_tel_type'] : INVALIDO;
        
        $this->insertContato();
        //---------------Documentos-----------------------------------------------------------------
        $this->docType = isset($_POST['doc_type']) ? $_POST['doc_type'] : INVALIDO;
        $this->docNumber = isset($_POST['doc_number']) ? $_POST['doc_number'] : INVALIDO;

        $this->insertDocumento();

        //----------------------Endereço---------------------------------------------

        $this->rua = isset($_POST['rua']) ? $_POST['rua'] : INVALIDO;
        $this->numero = isset($_POST['numero']) ? $_POST['numero'] : INVALIDO;
        $this->complemento = isset($_POST['complemento']) ? $_POST['complemento'] : INVALIDO;
        $this->bairro = isset($_POST['bairro']) ? $_POST['bairro'] : INVALIDO;
        $this->cidade = isset($_POST['cidade']) ? $_POST['cidade'] : INVALIDO;
        $this->estado = isset($_POST['estado']) ? $_POST['estado'] : INVALIDO;

        $this->insertEndereco();
        //---------------------------RURALINO--------------------------
        if (isset($_POST['ruralino']) and $_POST['ruralino'] == "on") {
            $this->matricula = isset($_POST['matricula']) ? $_POST['matricula'] : INVALIDO;
            $this->curso = isset($_POST['curso']) ? $_POST['curso'] : INVALIDO;

            $this->insertRuralino();
        }

        //-------------------LOGIN-----------------------------

        $this->user = isset($_POST['usuario']) ? $_POST['usuario'] : INVALIDO;
        $this->senha = isset($_POST['senha']) ? $_POST['senha'] : INVALIDO;

        $this->insertLogin();

        //-----------------Menor-Idade------------------------------
        if (isset($_POST['qtd_menor']) and $_POST['qtd_menor'] > 0) {
            $this->parentesco = isset($_POST['parentesco']) ? $_POST['parentesco'] : INVALIDO;
            for ($i = 0; $i < $_POST['qtd_menor']; $i++) {
                $nomeAtual = 'nome_menor' . ($i + 1);
                $sobrenomeAtual = 'sobrenome_menor' . ($i + 1);
                $nascimentoAtual = 'nascimento_menor' . ($i + 1);
                //-----------recuperando dados
                $nomeMenor = isset($_POST[$nomeAtual]) ? $_POST[$nomeAtual] : INVALIDO;
                $sobrenomeMenor = isset($_POST[$sobrenomeAtual]) ? $_POST[$sobrenomeAtual] : INVALIDO;
                $nascimentoMenor = isset($_POST[$nascimentoAtual]) ? $_POST[$nascimentoAtual] : INVALIDO;

                $menorID = $this->insertDadosBasicos($nomeMenor,$sobrenomeMenor,ALUNO,SIM,NAO,$nascimentoMenor);

                $this->insertRelacaoDependente($menorID);
            }
        }
        $this->redireciona();

    }

    /**
     * @param $nome
     * @param $sobrenome
     * @param $nv
     * @param $isMenor
     * @param $ruralino
     * @param $nascimento
     * @return bool| integer - Retorna o ID do dado inserido ou falso se der erro
     * @throws Exception
     */
    private function insertDadosBasicos($nome,$sobrenome,$nv,$isMenor,$ruralino,$nascimento){
        $params = array($nome,$sobrenome,$nv,$isMenor,$ruralino,$nascimento);
        $this->db->insert("nome,sobrenome,nv_acesso,menor_idade,ruralino,data_nascimento","pessoa",$params);
        return $this->db->getLastId();
    }

    /**
     * @throws Exception
     */
    private function insertDocumento(){
        $params = array($this->responsavelID,$this->docNumber,$this->docType);
        $this->db->insert("pessoa_id,numero_documento,tipo_documento","documento",$params);
    }
	/**
     * @throws Exception
     */
    private function insertContato(){
    	$params = array($this->responsavelID,$this->respTel,$this->respTelType);
    	$this->db->insert("pessoa_id,numero,tipo_telefone","telefone",$params);
    }

    /**
     * @throws Exception
     */
    private function insertEndereco(){
        $params = array($this->responsavelID,$this->rua,$this->numero,$this->complemento,$this->bairro,$this->cidade,$this->estado);
        $this->db->insert("pessoa_id,rua,numero,complemento,bairro,cidade,estado","endereco",$params);
    }

    /**
     * @throws Exception
     */
    private function insertRuralino(){
        $params = array($this->responsavelID,$this->matricula,$this->curso,NAO);
        $this->db->insert("pessoa_id,matricula,curso,bolsista","ruralino",$params);
    }

    /**
     * @return void - Retorna o ID do dado inserido ou falso se der erro
     * @throws Exception
     */
    private function insertLogin(){
        $params = array($this->responsavelID,$this->user,$this->make_hash($this->senha));
        $this->db->insert("pessoa_id,usuario,senha","login",$params);
    }

    /**
     * @param $depAtualID - O dependente Atual
     * @return bool
     * @throws Exception
     */
    private function insertRelacaoDependente($depAtualID){
        $paramns = array($depAtualID,$this->responsavelID,$this->parentesco);
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
     * @throws Exception
     */
    public function getAdministradores(){
        //Obtemos todos os administradores com left Join em Maior idade pois e obrigatorio ser maior de idade
        //entretando selecionamos tambem os que nao completaram as informacoes
        $joinClause = " LEFT JOIN documento ON id_pessoa = pessoa_id";
        $adm = $this->db->select("id_pessoa,nome,sobrenome,nv_acesso,menor_idade,ruralino,data_nascimento,numero_documento,tipo_documento", "pessoa" . $joinClause, "nv_acesso = ?", array(1));

        //transformamos o JSON em objeto php
        $objAdm = json_decode($adm);
        //verificmos se esse usuario esuda na rural e adicionamos as informacoes necessarias
        for($i=0;$i< sizeof($objAdm);$i++){
            if(isset($objAdm[$i]->ruralino) and $objAdm[$i]->ruralino == 1){
                $ruralino = json_decode($this->db->select("curso,bolsista","ruralino","pessoa_id = ?",array($objAdm[$i]->id_pessoa)));
                if(sizeof($ruralino) > 0){
                    $objAdm[$i]->curso = $ruralino[$i]->curso;
                    $objAdm[$i]->bolsista = $ruralino[$i]->bolsista;
                }
            }
        }
        $adm = json_encode($objAdm,JSON_UNESCAPED_UNICODE);
        return $adm;
    }

    /**
     * @return string JSON
     * @throws Exception
     */
    public function getProfesores(){
        //Obtemos todos os professores com left Join em Maior idade
        $joinClause = " LEFT JOIN documento ON id_pessoa = pessoa_id";
        $prof = $this->db->select("id_pessoa,nome,sobrenome,nv_acesso,menor_idade,ruralino,data_nascimento,numero_documento,tipo_documento","pessoa".$joinClause,"nv_acesso <= ?",array(2));
        //transformamos o JSON em objeto php
        $objProf = json_decode($prof);
        //verificmos se esse usuario esuda na rural e adicionamos as informacoes necessarias
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
        }
        $prof = json_encode($objProf,JSON_UNESCAPED_UNICODE);
        return $prof;
    }

    /**
     * @return string JSON
     * @throws Exception
     */
    public function getCandidatos(){
        //Obtemos todos os Candidatos com left Join em Maior idade
        $joinClause = " LEFT JOIN documento ON id_pessoa = pessoa_id";
        $cand = $this->db->select("id_pessoa,nome,sobrenome,nv_acesso,menor_idade,ruralino,data_nascimento,numero_documento,tipo_documento","pessoa".$joinClause,"nv_acesso >= ?",array(3));
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
        }
        $cand = json_encode($objCand,JSON_UNESCAPED_UNICODE);
        return $cand;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getPessoaById($identificador){
        return $this->db->select("nome,sobrenome,menor_idade,ruralino,data_nascimento","pessoa","id_pessoa = ?",array($identificador));
    }

    /**
     * @param $identificador
     * @return string
     * @throws Exception
     */
    public function getRuralinoByPessoaId($identificador){
        return $this->db->select("matricula,curso,bolsista","ruralino","pessoa_id = ?",array($identificador));
    }

    /**
     * @param $identificador
     * @return string
     * @throws Exception
     */
    public function getResponsavelByMenorId($identificador){
        $projection = "responsavel_parentesco as parentesco,nome,sobrenome,menor_idade.responsavel_id";
        $whereClause = "menor_idade.pessoa_id = ? and responsavel_id = pessoa.id_pessoa";
        return $this->db->select($projection,"menor_idade,pessoa",$whereClause,array($identificador));
    }

    /**
     * @param $identificador
     * @return string
     * @throws Exception
     */
    public function getTelefoneByPessoaId($identificador){
        return $this->db->select("numero,tipo_telefone as tipo","telefone","pessoa_id = ?",array($identificador));
    }
    private function redireciona(){header("Location: ../index.php?pag=Login");}

}