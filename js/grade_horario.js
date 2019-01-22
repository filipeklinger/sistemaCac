/**
 * Para funcionar é necessario chamar a funcao criagrade
 *
 */
const cores = ['#C5E1A5', '#FFCDD2', '#9FA8DA', '#FFF59D', '#90CAF9', '#B39DDB', '#B0BEC5', '#BCAAA4', '#80DEEA', '#EEEEFF', '#A5D6A7', '#80CBC4', '#E1BEE7', '#FFE082', '#E6EE9C', '#F8BBD0', '#FFAB91', '#FFCC80', '#81D4FA'];

function criaGrade(resposta, idTabela) {
    let obj = JSON.parse(resposta);

    var timetable = new TimetableMin();
    timetable.setScope(8, 22); // optional, only whole hours between 0 and 23
    timetable.addLocations(['Segunda', 'Terça', 'Quarta', 'Quinta','Sexta']);

    if (obj.length < 1) {
        /*TODO: Colocar uma mensagem ou não?*/
    } else {
        //console.log(obj);
        for(i in obj){
            let hini =  obj[i].inicio.split(':');
            let hfim =  obj[i].fim.split(':');
            //console.log( obj[i] );

            timetable.addEvent(
                obj[i].oficina,
                qualDia(obj[i]),
                new Date(
                    1970,
                    1,
                    1,
                    parseInt(hini[0]),
                    parseInt(hini[1])

                    //parseInt( obj[0].inicio.split(':')[1] )
                ),
                new Date(
                    1970,
                    1,
                    1,
                    parseInt(hfim[0]),
                    parseInt(hfim[1])
                )
            );

        }
    }

    /*Rendereer*/
    var renderer = new TimetableMin.Renderer(timetable);
    renderer.draw('.timetable'); // any css selector

}

//const aproxima = (horario, valor) => horario.split(':')[0] + ":" + (parseInt(horario.split('h')[0].split(':')[1]) + valor) + "h";

/*todo: no futuro, mudar a implementação do json para dia:segunda ou dia:nº*/
function qualDia(obj){
    if(obj.segunda == 1){
        return "Segunda";
    }else if(obj.terca == 1){
        return "Terça";
    }else if(obj.quarta == 1){
        return "Quarta";
    }else if(obj.quinta == 1){
        return "Quinta";
    }else if(obj.sexta == 1){
        return "Sexta";
    }else{
        console.log("grade_horário: erro nos dias da semana");
        return null;
    }
}
