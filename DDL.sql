-- MySQL Script
-- Sáb 28 Abr 2018 10:45:45 -03
-- Model: Sistema CAC    Version: 10.0

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema sistema_cac
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema sistema_cac
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `sistema_cac` DEFAULT CHARACTER SET utf8 ;
USE `sistema_cac` ;

-- -----------------------------------------------------
-- Table `sistema_cac`.`predio`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sistema_cac`.`predio` (
  `id_predio` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(45) NOT NULL,
  `localizacao` VARCHAR(60) NOT NULL,
  `is_ativo` TINYINT NOT NULL DEFAULT 1 COMMENT '1 sim\n0 nao',
  PRIMARY KEY (`id_predio`))
  ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sistema_cac`.`sala`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sistema_cac`.`sala` (
  `id_sala` INT NOT NULL AUTO_INCREMENT,
  `predio_id` INT NOT NULL,
  `nome` VARCHAR(45) NOT NULL,
  `is_ativo` TINYINT NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_sala`),
  INDEX `predio_sala_idx` (`predio_id` ASC),
  CONSTRAINT `predio_sala`
  FOREIGN KEY (`predio_id`)
  REFERENCES `sistema_cac`.`predio` (`id_predio`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
  ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sistema_cac`.`oficina`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sistema_cac`.`oficina` (
  `id_oficina` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(60) NOT NULL,
  `pre_requisito` TEXT(255) NOT NULL,
  PRIMARY KEY (`id_oficina`))
  ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sistema_cac`.`pessoa`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sistema_cac`.`pessoa` (
  `id_pessoa` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(25) NOT NULL,
  `sobrenome` VARCHAR(45) NULL,
  `nv_acesso` INT NOT NULL COMMENT '1 administrador\n2 professor\n3 aluno\n4 visitante',
  `menor_idade` TINYINT NOT NULL DEFAULT 0 COMMENT '1 sim\n0 nao',
  `ruralino` TINYINT NOT NULL DEFAULT 0 COMMENT '1 sim\n0 nao',
  `data_nascimento` DATE NOT NULL,
  PRIMARY KEY (`id_pessoa`))
  ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sistema_cac`.`turma`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sistema_cac`.`turma` (
  `id_turma` INT NOT NULL AUTO_INCREMENT,
  `criacao_turma` DATE NOT NULL,
  `oficina_id` INT NOT NULL,
  `num_vagas` INT NOT NULL,
  `nome_turma` VARCHAR(45) NOT NULL,
  `professor` INT NOT NULL,
  `is_ativo` TINYINT NOT NULL DEFAULT 1 COMMENT '1 sim\n0 nao',
  PRIMARY KEY (`id_turma`),
  INDEX `turma_oficina_idx` (`oficina_id` ASC),
  INDEX `professor_idx` (`professor` ASC),
  CONSTRAINT `turma_oficina`
  FOREIGN KEY (`oficina_id`)
  REFERENCES `sistema_cac`.`oficina` (`id_oficina`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `professor`
  FOREIGN KEY (`professor`)
  REFERENCES `sistema_cac`.`pessoa` (`id_pessoa`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
  ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sistema_cac`.`horario_turma_sala`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sistema_cac`.`horario_turma_sala` (
  `ano` YEAR NOT NULL,
  `sala_id` INT NOT NULL,
  `dia_semana` INT NOT NULL COMMENT '1 dom\n2 seg\n3 ter\n4 qua\n5 qui\n6 sex\n7 sab',
  `inicio` TIME(2) NOT NULL,
  `fim` TIME(2) NOT NULL,
  `turma_id` INT NOT NULL,
  PRIMARY KEY (`ano`, `sala_id`, `dia_semana`, `inicio`),
  INDEX `horario_turma_idx` (`turma_id` ASC),
  CONSTRAINT `horario_turma`
  FOREIGN KEY (`turma_id`)
  REFERENCES `sistema_cac`.`turma` (`id_turma`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `horario_sala`
  FOREIGN KEY (`sala_id`)
  REFERENCES `sistema_cac`.`sala` (`id_sala`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
  ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sistema_cac`.`menor_idade`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sistema_cac`.`menor_idade` (
  `pessoa_id` INT NOT NULL,
  `responsavel_id` INT NOT NULL,
  `responsavel_parentesco` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`pessoa_id`, `responsavel_id`),
  INDEX `fk_responsavel_idx` (`responsavel_id` ASC),
  CONSTRAINT `fk_menor_idade`
  FOREIGN KEY (`pessoa_id`)
  REFERENCES `sistema_cac`.`pessoa` (`id_pessoa`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_responsavel`
  FOREIGN KEY (`responsavel_id`)
  REFERENCES `sistema_cac`.`pessoa` (`id_pessoa`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
  ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sistema_cac`.`maior_idade`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sistema_cac`.`maior_idade` (
  `pessoa_id` INT NOT NULL,
  `numero_documento` VARCHAR(45) NOT NULL COMMENT 'numero sem pontos nem traco',
  `tipo_documento` INT(2) NOT NULL COMMENT '1 Registro geral (RG)\n2 Passaporte',
  PRIMARY KEY (`pessoa_id`, `numero_documento`),
  CONSTRAINT `pessoa_maior`
  FOREIGN KEY (`pessoa_id`)
  REFERENCES `sistema_cac`.`pessoa` (`id_pessoa`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
  ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sistema_cac`.`ruralino`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sistema_cac`.`ruralino` (
  `pessoa_id` INT NOT NULL,
  `matricula` VARCHAR(45) NOT NULL,
  `curso` VARCHAR(45) NOT NULL,
  `bolsista` TINYINT NOT NULL COMMENT '1 sim\n0 nao',
  PRIMARY KEY (`pessoa_id`, `matricula`),
  CONSTRAINT `pessoa_ruralina`
  FOREIGN KEY (`pessoa_id`)
  REFERENCES `sistema_cac`.`pessoa` (`id_pessoa`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
  ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sistema_cac`.`aluno_turma`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sistema_cac`.`aluno_turma` (
  `id_aluno` INT NOT NULL AUTO_INCREMENT,
  `turma_id` INT NOT NULL,
  `pessoa_id` INT NOT NULL,
  `lista_espera` TINYINT NOT NULL,
  `is_ativo` TINYINT NOT NULL DEFAULT 1 COMMENT '1 sim\n0 nao',
  PRIMARY KEY (`id_aluno`, `turma_id`),
  INDEX `aluno_pessoa_idx` (`pessoa_id` ASC),
  INDEX `aluno_turma_idx` (`turma_id` ASC),
  CONSTRAINT `aluno_pessoa`
  FOREIGN KEY (`pessoa_id`)
  REFERENCES `sistema_cac`.`pessoa` (`id_pessoa`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `aluno_turma`
  FOREIGN KEY (`turma_id`)
  REFERENCES `sistema_cac`.`turma` (`id_turma`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
  ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sistema_cac`.`lista_presenca`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sistema_cac`.`lista_presenca` (
  `aluno_id` INT NOT NULL,
  `data` DATE NOT NULL,
  `is_presente` TINYINT NOT NULL DEFAULT 1 COMMENT '1 sim\n0 nao',
  PRIMARY KEY (`aluno_id`, `data`),
  CONSTRAINT `presenca_aluno`
  FOREIGN KEY (`aluno_id`)
  REFERENCES `sistema_cac`.`aluno_turma` (`id_aluno`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
  ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sistema_cac`.`endereco`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sistema_cac`.`endereco` (
  `pessoa_id` INT NOT NULL,
  `rua` VARCHAR(45) NOT NULL,
  `numero` INT(6) NOT NULL,
  `complemento` VARCHAR(45) NOT NULL,
  `bairro` VARCHAR(45) NOT NULL,
  `cidade` VARCHAR(45) NOT NULL,
  `estado` VARCHAR(2) NOT NULL COMMENT 'somente sigla',
  PRIMARY KEY (`pessoa_id`),
  INDEX `contato_pessoa` (`pessoa_id` ASC),
  CONSTRAINT `fk_contato_pessoa`
  FOREIGN KEY (`pessoa_id`)
  REFERENCES `sistema_cac`.`pessoa` (`id_pessoa`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
  ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sistema_cac`.`login`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sistema_cac`.`login` (
  `pessoa_id` INT NOT NULL,
  `usuario` VARCHAR(60) NOT NULL,
  `senha` VARCHAR(60) NOT NULL,
  PRIMARY KEY (`pessoa_id`),
  UNIQUE INDEX `usuario_UNIQUE` USING BTREE (`usuario` ASC),
  UNIQUE INDEX `pessoa_id_UNIQUE` (`pessoa_id` ASC),
  CONSTRAINT `fk_login_pessoa`
  FOREIGN KEY (`pessoa_id`)
  REFERENCES `sistema_cac`.`pessoa` (`id_pessoa`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
  ENGINE = InnoDB
  COMMENT = 'dados de login no sistema\npode ser a combinacao de \nemail e senha ou\ntelefone e senha';


-- -----------------------------------------------------
-- Table `sistema_cac`.`telefone`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sistema_cac`.`telefone` (
  `id_telefone` INT NOT NULL AUTO_INCREMENT,
  `pessoa_id` INT NOT NULL,
  `numero` VARCHAR(45) NOT NULL,
  `tipo_telefone` INT(2) NOT NULL COMMENT '1 celular\n2 whatsapp\n3 fixo (residencial)\n4 recados',
  PRIMARY KEY (`id_telefone`),
  INDEX `numero` (`numero` ASC),
  CONSTRAINT `fk_telefone_pessoa`
  FOREIGN KEY (`pessoa_id`)
  REFERENCES `sistema_cac`.`pessoa` (`id_pessoa`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
  ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
