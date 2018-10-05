<?php

namespace App\DAO;
use App\core\Sql;

/** <b>Person</b>
 *Classe CRUD Person
 * @version 1.0
 * @autor Luiz Rodrigues <atende@webvirtua.com.br>
 * @copyrigth © 2018, Luiz Rodrigues Web Virtua
 * @since 1.0
 * @package App\Models
 */
class PersonDAO extends Sql implements IDAO
{
    /** @return \ArrayObject<string> Lista Pessoas Cadastradas */
    public static function findAll()
    {
        $sql = new Sql();

        $sql = $sql->select("SELECT name, last_name, cpf FROM tb_persons");

        return $sql;
    }

    /** @return \ArrayObject<string> Lista uma Pessoa Cadastrada */
    public static function findOne($idPerson)
    {
        $sql = new Sql();

        $sql = $sql->select("SELECT idperson, type, name, last_name, cpf, birth, sex, newsletter FROM tb_persons WHERE idperson = :idperson", array(
            ":idperson" => $idPerson
        ));

        return $sql;
    }

    /** @return \ArrayObject<string> Retorna uma pessoa existente */
    public static function findRepeat($cpf)
    {
        $sql = new Sql();

        $sql = $sql->select("SELECT idperson FROM tb_persons WHERE cpf = :cpf", array(
            ":cpf" => $cpf
        ));

        return $sql;
    }

    /** Cadastra a tabela Phones */
    public function insert($person)
    {
        $sql = new Sql();

        try {
            $sql->beginTransaction();

            $sql->query("INSERT INTO tb_persons (type, name, last_name, cpf, birth, sex, newsletter) 
                VALUES(:type, :name, :last_name, :cpf, :birth, :sex, :newsletter)", array(
                    ':type' => $person->getType(),
                    ':name' => $person->getName(),
                    ':last_name' => $person->getLastName(),
                    ':cpf' => $person->getCpf(),
                    ':birth' => $person->getBirth(),
                    ':sex' => $person->getSex(),
                    ':newsletter' => $person->isNewsletter()
            ));
            $lastIdPerson = $sql->lastInsertId();

            $sql->commit();
            return $lastIdPerson;
        } catch (\Exception $e) {
            $sql->rollBack();
            return  "Erro ao tentar Cadastrar: ".$e->getMessage();
        }
    }

    /** Altera campo na tabela Phones */
    public function update($person)
    {

    }

    /** Deleta campo na tabela Phones */
    public function delete($person)
    {
        $sql = new Sql();

        try {
            $sql->beginTransaction();

            $sql->query("DELETE FROM tb_persons WHERE idperson = :idperson", array(
                ':idperson' => $person->getIdPerson()
            ));

            return $sql->commit();
        } catch (PDOException $e) {
            $sql->rollBack();
            return "Erro ao tentar excluir Pessoa: " . $e->getMessage();
        }
    }
}