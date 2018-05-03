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
            '<td class=\'col-md-1\'> <a href="#" class="btn btn-primary"><span class=\'glyphicon glyphicon-pencil\'></span></a> </td>' +
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
            '<td class=\'col-md-1\'> <a href="#" class="btn btn-primary"><span class=\'glyphicon glyphicon-pencil\'></span></a> </td>' +
            '</tr>');
    }
}

function isAtivo(num) {
    if(num === '1') return "Sim";
    else return "nao";
}


function jsonParseNomePredios(resposta) {
    var corpo = $('#tipoPredio');
    var objJson = JSON.parse(resposta);
    for(var i in objJson){
        corpo.append(
            '<option value="' + objJson[i].id_predio+'">' + objJson[i].nome + '</option>' );
    }
}