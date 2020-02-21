# Sistema do CAC

Sistema para automatização dos processos administrativos do **Centro de Arte e cultura**

## Proposta

Informatizar o processo:

- cadastro de alunos e professores
- Gerenciamento de Professores
- Organização de Oficinas
- alocação de salas

## Sobre o Sistema
- Projeto no padrão MVC
- Front-end em Bootstrap 3.3.7
- Back-end em PHP (compativel com 5.6)
- Comunicação em JSON
  
## Pré requisitos para desenvolvimento

- Apache, PHP (5.6+), MySql (5.5+)
- VsCode (com o Plugin `PHP intelephense`)
- Recomendado instalar uma Stack (LAMP / WAMP / XAMP / entre outros)

### Instalando 

1. Clone o repositório dentro do seu APACHE
2. Execute o DDL mais recente no MySql (pode ser carregado no PhpMyAdmin)
3. Insira as credenciais do banco no `database.ini` dentro de `model`

## Documentação

O acesso ao banco de dados utiliza a biblioteca [PhpdatabaseOpenHelper](https://github.com/filipeklinger/PHPdatabaseOpenHelper) modificada para retornar JSON.

As tabelas de horários foram criadas com a biblioteca [TimeTable](http://timetablejs.org)

Os arquivos PDF são criados utilizando a biblioteca [TCPDF](https://tcpdf.org/)

Documentação específica do projeto [aqui](./documentacao/Documentacao.md)

### Versionamento

O software obedece as regras do Versionamento Semântico, disponíveis em [SemVer](http://semver.org/)

## Licença

Esse projeto e toda sua produção pertence ao Centro de Arte e Cultura / UFRRJ e
não deve ser copiado ou utilizado sem uma autorização expressa do mesmo.