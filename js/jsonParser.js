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

function jsonParseSalas(json,corpo) {
    var objJson = JSON.parse(json);
    for(var i in objJson){
        corpo.append(
            '<tr>' +
            '<td class=\'col-md-5\'> '+ objJson[i].sala + '</td>' +
            '<td class=\'col-md-5\'> '+ objJson[i].predio + '</td>' +
            '<td class=\'col-md-1\'> '+ isAtivo(objJson[i].is_ativo) + '</td>' +
            '<td class=\'col-md-1\'> <a href="?pag=Edit.Sala&id='+objJson[i].id_sala+'" class="btn btn-primary"><span class=\'glyphicon glyphicon-pencil\'></span></a> </td>' +
            '</tr>');
    }
}

function isAtivo(num) {
    if(num === '1') return "Ativo";
    else return "Inativo";
}


function jsonParseNomePredios(resposta,corpo) {
    var objJson = JSON.parse(resposta);
    for(var i in objJson){
        corpo.append(
            '<li value="' + objJson[i].id_predio+'">' + objJson[i].nome + '</li>'
        );
    }
}

/*TODO: SUBSTITUR A FUNÇÃO getDiaSemana*/

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