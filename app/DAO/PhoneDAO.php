<?php

namespace App\DAO;
use App\core\Sql;

/** <b>Categories</b>
 *Classe CRUD Categories
 * @autor Luiz Rodrigues <atende@webvirtua.com.br>
 * @copyrigth © 2018, Luiz Rodrigues Web Virtua
 * @version 1.0
 */
class PhoneDAO extends Sql implements IDAO
{
    /** @return \ArrayObject<string> Lista empresas */
    public static function findAll()
    {

    }

    /** @return \ArrayObject<string> Retorna um telefone */
    public static function findOne($idPhone)
    {
        $sql = new Sql();

        $sql = $sql->select("SELECT idphone, area_code, phone, whatsapp, responsible, id_person
            FROM tb_phones WHERE idphone = :idphone", array(
                ":idphone" => $idPhone
        ));

        return $sql;
    }

    /** @return \ArrayObject<string> Retorna um telefone existente */
    public static function findRepeat($phone)
    {
        $sql = new Sql();

        $sql = $sql->select("SELECT idphone, phone FROM tb_phones WHERE idphone = :idphone AND phone = :phone", array(
            ":idphone" => $phone->getIdPhone(),
            ":phone" => $phone->getPhone()
        ));

        return $sql;
    }

    /** Cadastra a tabela Phones */
    public function insert($phone)
    {
        $sql = new Sql();

        try {
            $sql->beginTransaction();

            $sql->query("INSERT INTO tb_phones (area_code, phone, whatsapp, id_person) VALUES(:area_code, :phone, :whatsapp, :id_person)", array(
                ':area_code' => $phone->getAreaCode(),
                ':phone' => $phone->getPhone(),
                ':whatsapp' => $phone->isWhatsApp(),
                ':id_person' => $phone->getIdPerson()
            ));
            $lastIdPhone = $sql->lastInsertId();

            $sql->commit();
            return $lastIdPhone;
        } catch (\Exception $e) {
            $sql->rollBack();
            return "Erro tentar Cadastrar Telefone: ".$e->getMessage();
        }
    }

    /** Altera campo na tabela Phones */
    public function update($phone)
    {

    }

    /** Deleta campo na tabela Phones */
    public function delete($phone)
    {
        $sql = new Sql();

        try {
            $sql->beginTransaction();

            $sql->query("DELETE FROM tb_phones WHERE idphone = :idphone", array(
                ':idphone' => $phone->getIdPhone()
            ));

            return $sql->commit();
        } catch (PDOException $e) {
            $sql->rollBack();
            return "Erro ao tentar excluir Empresa: " . $e->getMessage();
        }
    }
}