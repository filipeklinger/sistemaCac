<?php
/**
 * Constantes para a aplicação
 * Created by Filipe
 * Date: 28/04/18
 * Time: 17:02
 */
const SIM = 1;
const NAO = 0;
const INVALIDO = -1;

//niveis de acesso
const ADMINISTRADOR = 1;
const PROFESSOR = 2;
const ALUNO = 3;
const VISITANTE = 4;

//mensagens
const SUCESSO = 200;
const ERRO = 500;
const INSERT_ERRO = 501;

//paginador
const REGISTROS = 25;

/* Variaveis de Ambiente */
class Ambiente{
    //Nome do sistema
    public static function getSystemName(){return "Sistema CAC";}
    public static function getInstituicaoName(){return "Universidade Federal Rural do Rio de Janeiro";}
    public static function getSystemNameExtenso(){return "Sistema do Centro de Arte e Cultura - CAC";}
    //Menus
    public static function getAdmMenu(){return '[{"nome":"Infra","link":"InfraSubmenu","icone":"glyphicon-dashboard","submenu":[{"nome":"Gerenciar","link":"?pag=Infraestrutura"},{"nome":"Novo Predio","link":"?pag=Cad.Predio"},{"nome":"Nova Sala","link":"?pag=Cad.Sala"}]},{"nome":"Oficinas","link":"OficinaSubmenu","icone":"glyphicon-knight","submenu":[{"nome":"Gerenciar","link":"?pag=Oficinas"},{"nome":"Nova Oficina","link":"?pag=Cad.Oficina"}]},{"nome":"Usuarios","link":"UsuarioSubmenu","icone":"glyphicon-user","submenu":[{"nome":"Gerenciar","link":"?pag=Usuarios"},{"nome":"Novo Usuário","link":"?pag=Cad.Pessoa"}]},{"nome":"Turmas","link":"TurmaSubmenu","icone":"glyphicon-bell","submenu":[{"nome":"Mudar Período","link":"?pag=Trocar.Periodo"},{"nome":"Gerenciar Turmas","link":"?pag=Turmas"},{"nome":"Nova Turma","link":"?pag=Cad.Turma"}]},{"nome":"Alunos","link":"AlunoSubmenu","icone":"glyphicon-education","submenu":[{"nome":"Gerenciar","link":"?pag=Alunos"},{"nome":"Cad. Aluno em Turma","link":"?pag=Cad.Aluno"}]},{"nome":"Relatórios","link":"RelatorioSubmenu","icone":"glyphicon-print","submenu":[{"nome":"Gerar","link":"?pag=Relatorios"}]}]';}
    public static function getProfMenu(){return '[{"nome":"Minha conta","link":"ContaSubmenu","icone":"glyphicon-user","submenu":[{"nome":"Meus Dados","link":"?pag=Meus-Dados&id='.$_SESSION['ID'].'"}]},{"nome":"Minhas Turmas","link":"TurmasSubmenu","icone":"glyphicon-bell","submenu":[{"nome":"ver","link":"?pag=Turmas"},{"nome":"Nova Turma","link":"?pag=Cad.Turma"}]},{"nome":"Alunos","link":"AlunosSubmenu","icone":"glyphicon-education","submenu":[{"nome":"Gerenciar","link":"?pag=Presença"}]}]';}
    public static function getAlunoMenu(){return '[{"nome":"Minha conta","link":"ContaSubmenu","icone":"glyphicon-user","submenu":[{"nome":"Meus Dados","link":"?pag=Meus-Dados&id='.$_SESSION['ID'].'"}]}]';}
    //Cargos do sistema
    public static function getCargoAdm(){return 'Administrador';}
    public static function getCargoProf(){return 'Oficineiro';}
    public static function getCargoAluno(){return 'Aluno';}
    //Nome da atividade
    public static function getAtividadeName(){return 'Oficina';}
    //Quantidade maxima de Atividades que um aluno pode se cadastrar num mesmo periodo
    public static function getMaxOficinas(){ return 2;}
}