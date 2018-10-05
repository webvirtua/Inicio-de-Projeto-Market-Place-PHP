<?php

namespace App\DAO;
use App\core\Sql;

/** <b>Address</b>
 *Classe CRUD Address
 * @autor Luiz Rodrigues <atende@webvirtua.com.br>
 * @copyrigth © 2018, Luiz Rodrigues Web Virtua
 * @version 1.0
 */
class AddressDAO extends Sql implements IDAO
{
    /** @return \ArrayObject<string> Lista endereços */
    public static function findAll()
    {
        $sql = new Sql();

        $sql = $sql->select("SELECT type, street, number, complement, district, zipcode, address_alias FROM tb_addresses");

        return $sql;
    }

    /** @return \ArrayObject<string> Retorna um endereço */
    public static function findOne($idAddress)
    {
        $sql = new Sql();

        $sql = $sql->select("SELECT type, street, number, complement, district, zipcode, address_alias FROM tb_addresses WHERE idaddress = :idaddress", array(
            ":idaddress" => $idAddress
        ));

        return $sql;
    }

    /** @return \ArrayObject<string> Retorna um endereço existente */
    public static function findRepeat($address)
    {
        $sql = new Sql();

        $sql = $sql->select("SELECT idaddress FROM tb_addresses WHERE zipcode = :zipcode AND street = :street AND number = :number AND complement = :complement", array(
            ":zipcode" => $address->getZipcode(),
            ":street" => $address->getStreet(),
            ":number" => $address->getNumber(),
            ":complement" => $address->getComplement()
        ));

        return $sql;
    }

    /** Cadastra a tabela Addresses */
    public function insert($address)
    {
        $sql = new Sql();

        try {
            $sql->beginTransaction();

            $sql->query("INSERT INTO tb_addresses (type, street, number, complement, district, zipcode, address_alias, id_person, id_city) 
                VALUES(:type, :street, :number, :complement, :district, :zipcode, :address_alias, :id_person, :id_city)", array(
                    ':type' => $address->getAddressType(),
                    ':street' => $address->getStreet(),
                    ':number' => $address->getNumber(),
                    ':complement' => $address->getComplement(),
                    ':district' => $address->getDistrict(),
                    ':zipcode' => $address->getZipcode(),
                    ':address_alias' => $address->getAddressAlias(),
                    ':id_person' => $address->getIdPerson(),
                    ':id_city' => $address->getCity()
            ));
            $lastIdAddress = $sql->lastInsertId();

            $sql->commit();
            return $lastIdAddress;
        } catch (\Exception $e) {
            $sql->rollBack();
            return "Erro tentar Cadastrar Endereço: " . $e->getMessage();

        }
    }

    /** Altera campo na tabela Address */
    public function update($address)
    {

    }

    /** Deleta campo na tabela Address */
    public function delete($address)
    {
        $sql = new Sql();

        try {
            $sql->beginTransaction();

            $sql->query("DELETE FROM tb_addresses WHERE idaddress = :idaddress", array(
                ':idaddress' => $address->getIdAddress()
            ));

            return $sql->commit();
        } catch (PDOException $e) {
            $sql->rollBack();
            return "Erro ao tentar excluir Endereço: " . $e->getMessage();
        }
    }
}