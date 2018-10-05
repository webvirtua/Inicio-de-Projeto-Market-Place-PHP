<?php
namespace App\DAO;
use App\core\Sql;

/** <b>TagDAO</b>
 *Classe CRUD TagDAO
 * @autor Luiz Rodrigues <atende@webvirtua.com.br>
 * @copyrigth © 2018, Luiz Rodrigues Web Virtua
 * @version 1.0
 */
class GenericQueries extends Sql
{
    /** @return \ArrayObject<string> Retorna uma Cidade */
    public static function findCityName($cityName)
    {
        $sql = new Sql();

        $sql = $sql->select("SELECT idcity FROM tb_cities WHERE name = :name", array(
            ":name" => $cityName
        ));

        return $sql;
    }

    /** @return \ArrayObject<string> Retorna uma Cidade */
    public static function findPessoa($idPerson)
    {
        $sql = new Sql();

        $sql = $sql->select("SELECT pe.name, peco.id_corporation FROM tb_persons AS pe
                INNER JOIN tb_persons_has_tb_corporations AS peco ON peco.id_person = pe.idperson
                WHERE pe.idperson = :idperson ORDER BY peco.id_corporation ASC LIMIT 1",
            array(
                ":idperson" => $idPerson
            ));

        return $sql;
    }

    /** @return \ArrayObject<string> Lista uma Pessoa Cadastrada com Empresa */
    public static function findPessoaAndCorporation($idPerson)
    {
        $sql = new Sql();

        $sql = $sql->select("SELECT us.iduser, us.type, pe.idperson, pe.name, co.idcorporation FROM tb_persons AS pe
            INNER JOIN tb_users AS us ON pe.idperson = us.id_person
            INNER JOIN tb_persons_has_tb_corporations AS pc ON pc.id_person = pe.idperson
            INNER JOIN tb_corporations AS co ON  co.idcorporation = pc.id_corporation
            WHERE pe.idperson = :idperson", array(
            ":idperson" => $idPerson
        ));

        return $sql;
    }

    /** @return \ArrayObject<string> Retorna o nome de um Pessoa */
    public static function getNome($email)
    {
        $sql = new Sql();

        $result = $sql->select("SELECT pe.name, pe.last_name, us.iduser FROM tb_persons AS pe
            INNER JOIN tb_users AS us ON us.id_person = pe.idperson
            WHERE us.email = :email",
            array(
                ":email" => $email
            ));

        return $result;
    }
}