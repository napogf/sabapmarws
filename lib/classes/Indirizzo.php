<?php

/**
 * Created by PhpStorm.
 * User: giacomo
 * Date: 17/01/17
 * Time: 12.36
 */
class Indirizzo
{
    protected $fields;

    protected $fieldsRequired = [
            'titolo',
            'cognome'
        ];

    protected $errors;

    public function __construct(array $fields)
    {

        foreach ($fields as $field => $value){
            if(array_search($field, $this->fieldsRequired)){
                if(!isSet($fields[$field]) or empty($fields[$field]) ){
                    $this->errors[] = 'Campo ' . $field . ' non valido! ';
                }
            }
        }
        $this->fields = $fields;

        return $this;
    }

    public function save()
    {
        if(!$this->getError()){
            foreach ($this->fields as $field => $value) {
                $queryFields[] = $field . ' = :' . $field;
                $queryParams[':'.$field] = $value;
            }
            if(empty($this->fields['nome'])){
                $mittente = Db_Pdo::getInstance()->query('select id from arc_mittenti 
                where titolo = :titolo  
                and cognome = :cognome',[
                    ':titolo' => $this->fields['titolo'],
                    ':cognome' => $this->fields['cognome'],
                ])->fetchColumn();
            } else {
                $mittente = Db_Pdo::getInstance()->query('select id from arc_mittenti 
                where titolo = :titolo and 
                nome = :nome 
                and cognome = :cognome',[
                    ':titolo' => $this->fields['titolo'],
                    ':nome' => $this->fields['nome'],
                    ':cognome' => $this->fields['cognome'],
                ])->fetchColumn();
            }

            if($mittente){
                $queryParams[':id'] = $mittente;
                Db_Pdo::getInstance()->query('update arc_mittenti SET '.
                    implode(',', $queryFields) . ' WHERE id = :id',$queryParams);
            } else {
                Db_Pdo::getInstance()->query('INSERT INTO arc_mittenti SET ' .
                    implode(',',$queryFields) ,
                    $queryParams
                );
            }
        }

        return $this;
    }

    public function getError()
    {

        return (empty($this->errors) ? false : $this->errors);
    }


}