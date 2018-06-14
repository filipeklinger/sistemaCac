//Recuperando as informações

function ajaxLoadGET(destino,funcaoParse,corpo,funcaoEncadeada){
    var body = $(corpo);
    //colocando uma mensagem de load para o usuario
    body.append('<div class="loader"></div>');
    var xhttp = new XMLHttpRequest();//Objeto Ajax
    xhttp.onreadystatechange = function(){//toda vez que mudar estado chama a funcao
        if(xhttp.readyState === 4){//estado 4 é quando recebe a resposta
            $(".loader").remove();
            body.empty();
            funcaoParse(this.responseText,body,funcaoEncadeada);
        }
    };
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
    var diasSemana = "";
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
    ajaxLoadGET('control/main.php?req=selectSalaByPredioId&id=' + identificador, jsonParseSalasTurma, selectSala);
}

function jsonParseSalasTurma(resposta, corpo) {
    corpo.empty();
    var objJson = JSON.parse(resposta);
    for (var i in objJson) {
        corpo.append('<option value="' + objJson[i].id_sala + '">' + objJson[i].nome + '</option>');
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
    $('#ruralino').removeAttr("hidden");
    ajaxLoadGET('control/main.php?req=selectRuralinoByPessoaId&id='+identificador, parseRuralino, '.carr');
    function parseRuralino(json,corpo) {
        var objJson = JSON.parse(json);
        $('#matricula').append(objJson[0].matricula);
        $('#curso').append(objJson[0].curso);
        $('#bolsista').append(isAtivo(objJson[0].bolsista));
    }
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
                '&nbsp; <a href="?pag=Info.Pessoa&id='+json[i].id_pessoa+'" class="btn btn-primary"><span class=\'glyphicon glyphicon-pencil\'></span></a>' +
                '&nbsp; <a href="control/main.php?req=removeDependente&id='+json[i].id_pessoa+'" class="btn btn-primary"><span class=\'glyphicon glyphicon-remove\'></span></a>' +
                '</span></p>');
        }
    }
}

function addMenor() {
    let quantidade = $('#qtd_menor').val();
    let divContent = $('#menor_de_idade');

    quantidade++;

    $(function () {
        $('<div class="aluno">' +
            '<!-- Primeiro nome do menor ' + quantidade + ' -->\n' +
            '            <div class="form-group">\n' +
            '                <label class="col-md-4 control-label" for="nome_menor' + quantidade + '">Nome</label>\n' +
            '                <div class="col-md-4">\n' +
            '                    <input id="nome_menor0" name="nome_menor' + quantidade + '" type="text" placeholder="nome menor ' + quantidade + '"  class="form-control input-md" required="">\n' +
            '                </div>\n' +
            '            </div>\n' +
            '\n' +
            '            <!-- Sobrenome do menor 0 -->\n' +
            '            <div class="form-group">\n' +
            '                <label class="col-md-4 control-label" for="sobrenome_menor' + quantidade + '">Sobrenome</label>\n' +
            '                <div class="col-md-4">\n' +
            '                    <input id="sobrenome_menor0" name="sobrenome_menor' + quantidade + '" type="text" placeholder="sobrenome menor ' + quantidade + '"  class="form-control input-md" required="">\n' +
            '                </div>\n' +
            '            </div>\n' +
            '\n' +
            '            <!-- Nascimento do menor ' + quantidade + ' -->\n' +
            '            <div class="form-group">\n' +
            '                <label class="col-md-4 control-label" for="nascimento_menor' + quantidade + '">Nascimento</label>\n' +
            '                <div class="col-md-4">\n' +
            '                    <input id="nascimento_menor0" name="nascimento_menor' + quantidade + '" type="date" placeholder="dd/mm/aaaa"  class="form-control input-md" required="">\n' +
            '                </div>\n' +
            '            </div>' +
            '</div>').appendTo(divContent);
        $('#qtd_menor').remove();

        $('<input type="hidden" name="qtd_menor" value=" ' + quantidade + '" id="qtd_menor">').appendTo(divContent);
    });
}