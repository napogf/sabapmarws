# ALTER TABLE `sys_fields_validations` CHANGE `VALUE` `VALUE` VARCHAR(80) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '';

insert into sys_fields_validations (LANGUAGE_ID,FIELD_NAME,VALUE,CODE) VALUES (1, 'tipo_anagrafica' , 'PEC', '10');

set @posizione = 10;
insert into sys_fields_validations (LANGUAGE_ID,FIELD_NAME,VALUE,CODE)
    select  1, 'tipo_anagrafica' , titoli.TITOLO , @posizione:=@posizione+10 as code from (select DISTINCT  TITOLO FROM pratiche  where titolo is not null and titolo > '') as titoli
