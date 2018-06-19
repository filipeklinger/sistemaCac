//Recuperando as informações

var notSupported = ['samsungBrowser','MSIE','Trident'];

$(document).ready(function () {
    var b = navigator.userAgent;
    for(var i in notSupported){
        let aux = new RegExp(notSupported[i]);
        if(b.match(aux) != null){
            alert("Navegador Não Supportado, Você Será Redirecionado . . . .");
            window.location.replace("view/unsupported.html");
        }
    }
});

function ajaxLoadGET(destino,funcaoParse,corpo,funcaoEncadeada){
    var body = $(corpo);
    //colocando uma mensagem de load para o usuario
    body.append('<div class="loader"></div>');
    //var xhttp = new XMLHttpRequest();//Objeto Ajax
    var xhttp;
    try{
        // Firefox, Opera 8.0+, Safari
        xhttp=new XMLHttpRequest();
    } catch (e) {
        // Internet Explorer
        try {
            xhttp = new ActiveXObject("Msxml2.XMLHTTP");
        } catch (e) {
            try {
                xhttp = new ActiveXObject("Microsoft.XMLHTTP");
            } catch (e) {
                alert("Esse site não funciona no seu navegador, Use o chrome.");
                return false;
            }
        }
    }
    xhttp.onreadystatechange = function(){//toda vez que mudar estado chama a funcao
        if(xhttp.readyState === 4){//estado 4 é quando recebe a resposta
            $(".loader").remove();
            body.empty();
            funcaoParse(this.responseText,body,funcaoEncadeada);
        }
    };
    if (xhttp.overrideMimeType) {
        xhttp.overrideMimeType('application/json');
    }
    xhttp.open("GET", destino);
    xhttp.send();

}

function jsonParsePredios(json,corpo) {
    var objJson = JSON.parse(json);
    for(var i in objJson){
        corpo.append(
            '<tr>' +
            '<td class=\'col-md-5\'> '+ objJson[i].nome + '</td>' +
            '<td class=\'col-md-5\'> '+ objJson[i].localizacao + '</td>' +
            '<td class=\'col-md-1\'> '+ isAtivo(objJson[i].is_ativo) + '</td>' +
            '<td class=\'col-md-1\'> <a href="?pag=Edit.Predio&id='+objJson[i].id_predio+'" class="btn btn-primary"><span class=\'glyphicon glyphicon-pencil\'></span></a> </td>' +
            '</tr>');
    }
}

/*TODO: reduzir tamanho de colunas da função jsonParseSalas e jsonParsePredios*/

function jsonParseSalas(json,corpo) {
    var objJson = JSON.parse(json);
    for(var i in objJson){
        corpo.append(
            '<tr>' +
            '<td class=\'col-md-4\'> '+ objJson[i].sala + '</td>' +
            '<td class=\'col-md-4\'> '+ objJson[i].predio + '</td>' +
            '<td class=\'col-md-2\'> '+ isAtivo(objJson[i].is_ativo) + '</td>' +
            '<td class=\'col-md-2\'> <a href="?pag=Edit.Sala&id='+objJson[i].id_sala+'" class="btn btn-primary"><span class=\'glyphicon glyphicon-pencil\'></span></a> </td>' +
            '</tr>');
    }
}

function isAtivo(num) {
    if(num == '1') return "sim";
    else return "não";
}

function trancado(num) {
    if(num === '1') return "Sim";
    else return "Não";
}


function jsonParseNomePredios(resposta,corpo) {
    var objJson = JSON.parse(resposta);
    corpo.append('<option value="" disabled selected>Selecione o prédio ao qual a sala pertence</option>');
    for(var i in objJson){
        corpo.append(
            '<option value="' + objJson[i].id_predio+'">' + objJson[i].nome + '</option>' );
    }
}

/*TODO: SUBSTITUR A FUNÇÃO GETdIAsEMANA*/

function getDiaSemana(objdia) {
    let diasSemana = "";
    if (objdia.segunda === "1") diasSemana = "Segunda";
    if (objdia.terca === "1"){
        if(diasSemana.length > 1) diasSemana += " e ";
        diasSemana += "Terça";
    }
    if (objdia.quarta === "1"){
        if(diasSemana.length > 1) diasSemana += " e ";
        diasSemana += "Quarta";
    }
    if (objdia.quinta === "1"){
        if(diasSemana.length > 1) diasSemana += " e ";
        diasSemana += "Quinta";
    }
    if (objdia.sexta === "1"){
        if(diasSemana.length > 1) diasSemana += " e ";
        diasSemana += "Sexta";
    }
    return diasSemana;
}
function getNVacesso(nv){
    switch (nv){
        case '1':
            return "Administrador";
        case '2':
            return "Oficineiro";
        case '3':
            return "Aluno";
        default:
            return "Visitante";
    }
}

function getMsgs() {
    var aviso = $('#avisos');
    var msg = JSON.parse(mensagem);

    switch (msg.tipo){
        case "erro":
            aviso.append(msg.desc);
            aviso.addClass("alert alert-danger");
            break;
        case "sucesso":
            aviso.append(msg.desc);
            aviso.addClass("alert alert-success");
            break;
        default:
            aviso.removeAttr("class");

    }
}

//recuperando o tag por GET com Javascript
function getParameterByName(name) {
    var url = window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
}

/* -----------------------------------------TURMAS--------------------------------------------- */

function carregadorCadTurmas() {
    //obtendo oficinas
    ajaxLoadGET('control/main.php?req=selectOficina', jsonParseOficinasTurma, selectOficinaTurma);
    //obtendo predios
    ajaxLoadGET('control/main.php?req=selectPredio', jsonParseNomePredios, selectPredioTurma);
    //obtendo professores
    ajaxLoadGET('control/main.php?req=selectProfessor', jsonParseProfessorTurma, selectProfTurma);
    //carregando previamente os itens cadastrados no primeiro predio
    SalaFromPredioId();
    disponibilidade();

}

function jsonParseOficinasTurma(resposta, corpo) {
    corpo.empty();
    var objJson = JSON.parse(resposta);
    for (var i in objJson) {
        corpo.append('<option value="' + objJson[i].id_oficina + '">' + objJson[i].nome + '</option>');
    }
    if(objJson.length < 1){
        corpo.append('<option value="-1">Nenhuma Oficina cadastrada</option>');
    }
}

function SalaFromPredioId() {
    let identificador = parseInt(selectPredioTurma.val());
    ajaxLoadGET('control/main.php?req=selectSalaByPredioId&id=' + identificador, jsonParseSalasTurma, '#carregandoSalas');
}

function jsonParseSalasTurma(resposta) {
    cp = $('#selectSala');
    cp.empty();
    var objJson = JSON.parse(resposta);
    for (var i in objJson) {
        cp.append('<option value="' + objJson[i].id_sala + '">' + objJson[i].nome + '</option>');
    }
}

function disponibilidade() {
    var identificador = parseInt(selectSalaTurma.val());

    ajaxLoadGET('control/main.php?req=selectHorario&id=' + identificador, jsonParteHorariosDisponiveis, horariosTurma);
}

function jsonParteHorariosDisponiveis(resposta, corpo) {
    let objJson = JSON.parse(resposta);
    for (let i in objJson) {
        corpo.append(
            '<tr>\n' +
            '                    <th>' + objJson[i].inicio + ' ás ' + objJson[i].fim + '</th>\n' +
            '\n' +
            '                    <td>' + isAtivoX(objJson[i].segunda, objJson[i]) + '</td>\n' +
            '                    <td>' + isAtivoX(objJson[i].terca, objJson[i]) + '</td>\n' +
            '                    <td>' + isAtivoX(objJson[i].quarta, objJson[i]) + '</td>\n' +
            '                    <td>' + isAtivoX(objJson[i].quinta, objJson[i]) + '</td>\n' +
            '                    <td>' + isAtivoX(objJson[i].sexta, objJson[i]) + '</td>\n' +
            '                </tr>');
    }
    if (objJson.length < 1) {
        corpo.append(
            '<tr>\n' +
            '                    <th>--:-- - --:--</th>\n' +
            '\n' +
            '                    <td>Nenhuma turma</td>\n' +
            '                    <td>cadastrada</td>\n' +
            '                    <td>nesta</td>\n' +
            '                    <td>sala</td>\n' +
            '                    <td>--</td>\n' +
            '                    <td>---</td>\n' +
            '                </tr>');
    }
}

function jsonParseProfessorTurma(resposta, corpo) {
    corpo.empty();
    let objJson = JSON.parse(resposta);
    for (let i in objJson) {
        corpo.append('<option value="' + objJson[i].id_pessoa + '">' + objJson[i].nome + '</option>');
    }
}

function isAtivoX(num, obj) {
    if (num === '1') return obj.oficina;
    else return " ";
}

function parsePeriodoText(resposta,corpo) {
    let json = JSON.parse(resposta);
    $('#anoAtual').append(json.ano);
    corpo.append(json.periodo);
}

function parsePeriodosSelect(resposta, corpo,funcaoEncadeada) {
    let objJson = JSON.parse(resposta);
    let opcoes = '';
    for (i in objJson) {
        if (i == objJson.length - 1) opcoes += '<option value="' + objJson[i].id_tempo + '" selected="selected">';
        else opcoes += '<option value="' + objJson[i].id_tempo + '">';
        opcoes += objJson[i].ano + ' - ' + objJson[i].periodo + '</option>';
    }
    corpo.append(opcoes);
    //aqui executamos uma funcao 5ms apos o parse de periodo
    setTimeout(funcaoEncadeada,5);
}

/* ------------------------------------------------USUARIOS-----------------------------------------------------------*/
function jsonParseInfoPessoa(json, corpo) {
    let objJson = JSON.parse(json);
    nome = objJson[0].nome
    $('.nome').append(nome);
    if(objJson[0].excluido == 1) $('#nomeLabel').append(nome+"- Usuário Excluído");
    else $('#nomeLabel').append(nome);
    $('#sobrenome').append(objJson[0].sobrenome);
    $('#nasc').append(objJson[0].data_nascimento);
    if (objJson[0].menor_idade === "1") {
        loadMenor();
    } else {
        loadTel(identificador);
        loadEnd(identificador);
        loadDocument();
        loadDepententes();
    }
    if (objJson[0].ruralino === "1"){ loadRuralino(); }else{ btnInsertRuralino(); }
    if(objJson[0].excluido == '0') addBtnEdicaoPessoa();
}

function loadMenor() {
    $('#menorIdade').removeAttr("hidden");
    ajaxLoadGET('control/main.php?req=selectResponsavelByMenorId&id='+identificador, parseMenor, '.carr');
    function parseMenor(json,corpo) {
        var objJson = JSON.parse(json);
        $('#menorIdade').removeAttr("hidden");
        $('#respnome').append(objJson[0].nome);
        $('#respsobrenome').append(objJson[0].sobrenome);
        $('#parentesco').append(objJson[0].parentesco);

        loadTel(objJson[0].responsavel_id);
        loadEnd(objJson[0].responsavel_id);
    }
}

function loadRuralino() {
    //adicionamos o botão editar
    $('#ruraLabel').append('&nbsp; <button id="btnDeps" onclick="editaRuralino()" class="btn btn-primary"><span class=\'glyphicon glyphicon-pencil\'></span></button>');
    $('#ruralinoConteudo').removeAttr("hidden");
    ajaxLoadGET('control/main.php?req=selectRuralinoByPessoaId&id='+identificador, parseRuralino, '.carr');
    function parseRuralino(json,corpo) {
        var objJson = JSON.parse(json);
        $('#matricula').append(objJson[0].matricula);
        $('#curso').append(objJson[0].curso);
        $('#bolsista').append(isAtivo(objJson[0].bolsista));
    }
}

function btnInsertRuralino(){
    //adicionamos o botão Adicionar
    $('#ruraLabel').append('&nbsp; <button id="btnDeps" onclick="insertRuralino()" class="btn btn-primary"><span class=\'glyphicon glyphicon-pencil\'></span></button>');
}

function insertRuralino() {
    let conteudo = $('#ruralinoConteudo');
    conteudo.empty();
    conteudo.removeAttr('hidden');
    conteudo.append(
        '<form action="control/main.php?req=insertRuralino&id=' + identificador + '" method="POST">' +
        '<p>Curso: <input type="text" name="curso" placeholder="Educação Física" required="required"></p>' +
        '<p>Matricula: <input type="number" name="matricula" placeholder="2018180188" ></p>'+
        '<div class="form-group">\n' +
        '                <label class="control-label" >Bolsista do CAC?</label>\n' +
        '                <div class="">\n' +
        '                    <label class="radio-inline">\n' +
        '                        <input type="radio" name="bolsista" value="1">SIM\n' +
        '                    </label>\n' +
        '                    <label class="radio-inline">\n' +
        '                        <input type="radio" name="bolsista" value="0">NÃO\n' +
        '                    </label>\n' +
        '                </div>\n' +
        '            </div>'+
        '<br/><input type="submit" class="btn btn-primary" value="Gravar"/>' +
        '</form>'
    );
}

function editaRuralino() {
    let curso = $('#curso').text();
    let matricula = $('#matricula').text();
    let conteudo = $('#ruralinoConteudo');
    conteudo.empty();
    conteudo.append(
        '<form action="control/main.php?req=updateRuralino&id=' + identificador + '" method="POST">' +
        '<p>Curso: <input type="text" name="curso" value="' + curso + '" required="required"></p>' +
        '<p>Matricula: <input type="number" name="matricula" value="' + matricula + '" ></p>'+
        '<div class="form-group">\n' +
        '                <label class="control-label" >Bolsista do CAC?</label>\n' +
        '                <div class="">\n' +
        '                    <label class="radio-inline">\n' +
        '                        <input type="radio" name="bolsista" value="1">SIM\n' +
        '                    </label>\n' +
        '                    <label class="radio-inline">\n' +
        '                        <input type="radio" name="bolsista" value="0">NÃO\n' +
        '                    </label>\n' +
        '                </div>\n' +
        '            </div>'+
        '<br/><input type="submit" class="btn btn-primary" value="Gravar"/>' +
        '</form>'
    );
}

function loadTel(id) {
    ajaxLoadGET('control/main.php?req=selectTelefoneByPessoaId&id='+id, parseTel, '#tels');

    function parseTel(json,corpo) {
        jsonContato = json;
        var objJson = JSON.parse(json);
        for(i in objJson){
            corpo.append('<p>Telefone ('+getTelType(objJson[i].tipo)+'): '+objJson[i].numero+'</p>');
        }
    }

    function getTelType(num) {
        num = parseInt(num);
        switch (num){
            case 1:
                return "celular";
            case 2:
                return "Whatsapp";
            case 3:
                return "Fixo";
            case 4:
                return "Recados";
            default:
                return "...";
        }

    }
}

function loadEnd(id) {
    ajaxLoadGET('control/main.php?req=selectEndereco&id='+id, parseEnd, '.carr');

    function parseEnd(json,corpo) {
        var objJson = JSON.parse(json);
        $('#rua').append(objJson[0].rua);
        $('#numero').append(objJson[0].numero);
        $('#complemento').append(objJson[0].complemento);
        $('#bairro').append(objJson[0].bairro);
        $('#cidade').append(objJson[0].cidade);
        $('#estado').append(objJson[0].estado);

    }
}

function loadDocument() {
    ajaxLoadGET('control/main.php?req=selectDocumento&id='+identificador, parseDocumento, '.carr');
    $('#documentos').removeAttr("hidden");
    function parseDocumento(resposta,corpo){
        let json = JSON.parse(resposta);
        $('#tipoDoc').append(documenTipo(json[0].tipo_documento));
        $('#numeroDoc').append(json[0].numero_documento);
    }

    function documenTipo(num) {
        if(num === "1") return "Registro Geral (RG)";
        return "Passaporte";
    }
}

function loadDepententes() {
    ajaxLoadGET('control/main.php?req=selectDependentes&id='+identificador, parseDependentes,'#dep');
    $('#dependentes').removeAttr("hidden");

    function parseDependentes(resposta,corpo) {
        let json = JSON.parse(resposta);
        for(i in json){
            corpo.append('<p>Nome: <span class="depNome"> '+json[i].nome + '</span>&nbsp;<span class="depsobrenome">' + json[i].sobrenome +
                '&nbsp; <a href="?pag=Meus-Dados&id='+json[i].id_pessoa+'" class="btn btn-primary"><span class=\'glyphicon glyphicon-pencil\'></span></a>' +
                '&nbsp; <a href="control/main.php?req=removeDependente&id='+json[i].id_pessoa+'" class="btn btn-primary"><span class=\'glyphicon glyphicon-remove\'></span></a>' +
                '</span></p>');
        }
    }
}
//----------------------------EDITA USUARIO--------------------------------
function addBtnEdicaoPessoa() {
    $('#dadosBasicos').append('&nbsp; <button id="btNome" onclick="editUsuarioNome()" class="btn btn-primary"><span class=\'glyphicon glyphicon-pencil\'></span></button>');
    $('#contato').append('&nbsp; <button id="btnCont" onclick="editUsuarioContato()" class="btn btn-primary"><span class=\'glyphicon glyphicon-pencil\'></span></button>');
    $('#endereco').append('&nbsp; <button id="btnEnd" onclick="editUsuarioEndereco()" class="btn btn-primary"><span class=\'glyphicon glyphicon-pencil\'></span></button>');
    $('#docLabel').append('&nbsp; <button id="btnDoc" onclick="editUsuarioDocumento()" class="btn btn-primary"><span class=\'glyphicon glyphicon-pencil\'></span></button>');
    $('#dependentes h4').append('&nbsp; <button id="btnDeps" onclick="adicionaDependete()" class="btn btn-primary"><span class=\'glyphicon glyphicon-plus\'></span></button>');
}

function addMenor() {
    var quantidade = $('#qtd_menor').val();
    var divContent = $('#menor_de_idade');

    quantidade++;

    $(function () {
        $('<div class="aluno">' +
            '<h4> Dependente #'+quantidade+' </h4><hr>\n' +
            '            <div class="form-group col-md-8 col-lg-push-2">\n' +
            '                <label class=" control-label" for="nome_menor' + quantidade + '">Nome</label>\n' +
            '                <div class="">\n' +
            '                    <input id="nome_menor0" name="nome_menor' + quantidade + '" type="text" placeholder="nome menor ' + quantidade + '"  class="form-control input-md" required="">\n' +
            '                </div>\n' +
            '            </div><br/>\n' +
            '\n' +
            '            <!-- Sobrenome do menor 0 -->\n' +
            '            <div class="form-group col-md-8 col-lg-push-2">\n' +
            '                <label class="control-label" for="sobrenome_menor' + quantidade + '">Sobrenome</label>\n' +
            '                <div class="">\n' +
            '                    <input id="sobrenome_menor0" name="sobrenome_menor' + quantidade + '" type="text" placeholder="sobrenome menor ' + quantidade + '"  class="form-control input-md" required="">\n' +
            '                </div>\n' +
            '            </div><br/>\n' +
            '\n' +
            '            <!-- Nascimento do menor ' + quantidade + ' -->\n' +
            '            <div class="form-group col-md-8 col-lg-push-2">\n' +
            '                <label class="control-label" for="nascimento_menor' + quantidade + '">Nascimento</label>\n' +
            '                <div class="">\n' +
            '                    <input id="nascimento_menor0" name="nascimento_menor' + quantidade + '" type="date" placeholder="dd/mm/aaaa"  class="form-control input-md" required="">\n' +
            '                </div>\n' +
            '            </div><br/>' +
            '</div>').appendTo(divContent);
        $('#qtd_menor').remove();

        $('<input type="hidden" name="qtd_menor" value=" ' + quantidade + '" id="qtd_menor">').appendTo(divContent);
    });
}

function editUsuarioNome() {
    $('#btNome').removeAttr("onclick");
    let dadosBasicos = $('#nomeNasc');
    let sobrenome = $('#sobrenome').text();
    let nasc = $('#nasc').text();
    dadosBasicos.empty();
    dadosBasicos.append(
        '<form action="control/main.php?req=updateDadosBasicos&id=' + identificador + '" method="POST">' +
        '<p>Nível de Acesso: <select id="nv_acesso" name="nv_acesso">\n' +
        '                        <option value=4>Visitante</option>\n' +
        '                        <option value=3>Aluno</option>\n' +
        '                        <option value=2>Oficineiro</option>\n' +
        '                        <option value=1>Administrador</option>\n' +
        '                    </select>' +
        '</p>' +
        '<p>Nome: <input type=\'text\' name=\'nome\' value="' + nome + '" required="required"></p>' +
        '<p>Sobrenome: <input type="text" name="sobrenome" value="' + sobrenome + '" required="required"></p>' +
        '<p>Data nascimento: <input type="date" name="nascimento" value="' + nasc + '" required="required"> </p>' +
        '<br/><input type="submit" class="btn btn-primary" value="Gravar"/>' +
        '</form>');
}

function editUsuarioContato() {
    $('#btnCont').removeAttr("onclick");
    let tels = $('#tels');
    let acm = '<form action="control/main.php?req=updateContato&id=' + identificador + '" method="POST">';
    let tipo = 0;
    tels.empty();
    if (jsonContato !== null) {
        jsonContato = JSON.parse(jsonContato);
        for (i in jsonContato) {
            tipo = jsonContato[i].tipo;
            acm += '<p>Tel: <input type="number" name="resp_tel" value="' + jsonContato[i].numero + '" required="required">' +
                'Tipo: <select id="resp_tel_type" name="resp_tel_type">\n' +
                '                        <option value="2" ' + verTp(tipo, 2) + '>Whatsapp</option>\n' +
                '                        <option value="1"' + verTp(tipo, 1) + '>Celular</option>\n' +
                '                        <option value="3"' + verTp(tipo, 3) + '>Fixo (residencial)</option>\n' +
                '                        <option value="4"' + verTp(tipo, 4) + '>Recados</option>\n' +
                '                    </select>' +
                '</p>';
        }
    }
    acm += '<br/><input type="submit" class="btn btn-primary" value="Gravar"/></form>';
    tels.append(acm);

    function verTp(tp, val) {
        if (parseInt(tp) === parseInt(val)) return 'selected';
        return '';
    }
}

function editUsuarioEndereco() {
    $('#btnEnd').removeAttr("onclick");
    let end = $('#end');
    //Obtendo dados atuais
    let rua = $('#rua').text();
    let numero = $('#numero').text();
    let complemento = $('#complemento').text();
    let bairro = $('#bairro').text();
    let cidade = $('#cidade').text();
    let estado = $('#estado').text();
    end.empty();
    //Inserindo campos de edicao
    end.append(
        '<form action="control/main.php?req=updateEndereco&id=' + identificador + '" method="POST">' +
        '<p>Rua: <input type=\'text\' name=\'rua\' value="' + rua + '" required="required"></p>' +
        '<p>Numero: <input type="number" name="numero" value="' + numero + '" required="required"></p>' +
        '<p>Complemento: <input type="text" name="complemento" value="' + complemento + '" ></p>' +
        '<p>Bairro: <input type="text" name="bairro" value="' + bairro + '" required="required"></p>' +
        '<p>Cidade: <input type="text" name="cidade" value="' + cidade + '" required="required"></p>' +
        '<p>Estado: <input type="text" name="estado" value="' + estado + '" required="required"></p>' +
        '<br/><input type="submit" class="btn btn-primary" value="Gravar"/>' +
        '</form>');
}

function editUsuarioDocumento() {
    $('#btnDoc').removeAttr("onclick");
    let docs = $('#docs');
    //obtendo dados atuais
    let tipo = $('#tipoDoc').text();
    let numero = $('#numeroDoc').text();
    docs.empty();

    docs.append(
        '<form action="control/main.php?req=updateDoc&id=' + identificador + '" method="POST">' +
        '<p>Tipo: ' +
        '   <select id="doc_type" name="doc_type">\n' +
        '       <option value="1">Registro geral (RG)</option>\n' +
        '       <option value="2">Passaporte</option>\n' +
        '   </select>' +
        '</p>' +
        '<p>Numero: <input type="number" name="doc_number" value="' + numero + '" required="required"></p>' +
        '<br/><input type="submit" class="btn btn-primary" value="Gravar"/>' +
        '</form>');
}

function adicionaDependete() {
    $('#gravaMenor').attr('type','submit');
    $('#label_parentesco').removeAttr('hidden');
    //requisitamos adicionar dependente no id do usuario atual
    $('#formDependentes').attr('action','control/main.php?req=addDependente&id=' + identificador);
    addMenor();
}
/*-----------------------------VERIFICAÇÃO DE FORMULARIO DE ENTRADA---------------------------------------------------*/
//verirficadores
var userDisponivel = false;
var senhaOk = false;

function verificadores(){
    let btn = $('#btn-senha');
    if(userDisponivel && senhaOk){
        btn.attr('type','submit');
    }else{
        alert("Dados Para Acesso a Conta precisam ser corrigidos");
    }
}

function verificaSenha() {
    let erro = $('#error-senha').empty();
    if($('#senha').val() === $('#repsenha').val()){

        senhaOk = true;
    }else{
        erro.append('Senhas não conferem');
        senhaOk = false;
    }
}

function verificaUsuarioDuplicado() {
    let usuario = $('#usuario').val();
    ajaxLoadGET('control/main.php?req=verificaUser&nome='+usuario,parseUserDuplicado,'#error-user');

    function parseUserDuplicado(resposta) {
        let json = JSON.parse(resposta);
        let msg = $('#error-user').empty();
        if(json[0].usuario != 0){
            msg.append('Usuário indisponivel');
            userDisponivel = false;
        }else{
            msg.append('Usuário OK');
            userDisponivel = true;
        }
    }
}

/*------------------------------GERENCIAMENTO DE USUARIOS ----------------------------------------------------------- */
function jsonParseUsuarios(resposta, corpo) {
    var objJson = JSON.parse(resposta);
    let string = '';
    for (var i in objJson) {
        string +=
            '<tr>\n';
        if(objJson[i].excluido == '1'){
            string += '     <td class="col-md-4">' + objJson[i].nome + " " + objJson[i].sobrenome + ' - Excluído</td>\n';
        }else{
            string += '     <td class="col-md-4">' + objJson[i].nome + " " + objJson[i].sobrenome + '</td>\n';
        }
        string+='     <td class="col-md-2">' + getNVacesso(objJson[i].nv_acesso) + '</td>\n' +
            '     <td class="col-md-2">' + isAtivo(objJson[i].menor_idade) + '</td>\n' +
            '     <td class="col-md-2">' + isAtivo(objJson[i].ruralino) + '</td>\n'+
            '<td  class="col-md-2"> <a href="?pag=Info.Pessoa&id=' + objJson[i].id_pessoa + '" class="btn btn-primary"><span class=\'glyphicon glyphicon-eye-open\'></span></a> </td>'+
            '</tr>';
        corpo.empty();
        corpo.append(string);
    }
    if (objJson.length < 1) {
        corpo.append(
            '<tr>\n' +
            '\n' +
            '                    <td>:( NÃO</td>\n' +
            '                    <td>ESIXTEM</td>\n' +
            '                    <td>USUÁRIOS </td>\n' +
            '                    <td>NESTA</td>\n' +
            '                    <td>CATEGORIA</td>\n' +
            '                    <td>---</td>\n' +
            '                </tr>');
    }
}