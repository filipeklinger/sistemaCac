//Recuperando as informações

function ajaxLoadGET(destino,funcaoParse,corpo){
    var body = $(corpo);
    //colocando uma mensagem de load para o usuario
    body.append('<div class="loader"></div>');
    var xhttp = new XMLHttpRequest();//Objeto Ajax
    xhttp.onreadystatechange = function(){//toda vez que mudar estado chama a funcao
        if(xhttp.readyState === 4){//estado 4 é quando recebe a resposta
            $(".loader").remove();
            body.empty();
            funcaoParse(this.responseText,body);
            //body.append(this.responseText);

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
    if(num === '1') return "sim";
    else return "não";
}


function jsonParseNomePredios(resposta,corpo) {
    var objJson = JSON.parse(resposta);
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
            //aviso.append("Err");
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
}

function SalaFromPredioId() {
    var identificador = parseInt(selectPredioTurma.val());
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
            '                    <td>--</td>\n' +
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
