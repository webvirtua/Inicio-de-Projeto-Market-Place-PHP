<?php

namespace App\DAO;
use App\core\Sql;

/** <b>Address</b>
 *Classe CRUD Address
 * @autor Luiz Rodrigues <atende@webvirtua.com.br>
 * @copyrigth © 2018, Luiz Rodrigues Web Virtua
 * @version 1.0
 */
class PasswordRecoveryDAO extends Sql implements IDAO
{
    /** @return \ArrayObject<string> Lista endereços */
    public static function findAll()
    {

    }

    /** @return \ArrayObject<string> Retorna um endereço */
    public static function findOne($idrecovery)
    {

    }

    /** @return \ArrayObject<string> Retorna um endereço existente */
    public static function verifyValidity($idUser)
    {
        $sql = new Sql();

        $sql = $sql->select("SELECT idpassword_recovery FROM tb_password_recovereries
            WHERE id_user = :iduser 
            AND recovey_date IS NULL
            AND DATE_ADD(register_date, INTERVAL 24 HOUR) >= NOW()
            ORDER BY idpassword_recovery DESC LIMIT 1
        ", array(
            ":iduser" => $idUser
        ));

        return $sql;
    }

    /** Cadastra a tabela Password Recovery */
    public function insert($recovery)
    {
        $sql = new Sql();

        try {
            $sql->beginTransaction();

            $sql->query("INSERT INTO tb_password_recovereries (ip, id_user) VALUES(:ip, :id_user)", array(
                ':ip' => $recovery->getIp(),
                ':id_user' => $recovery->getIdUser()
            ));

            return $sql->commit();
        } catch (\Exception $e) {
            $sql->rollBack();
            return  "Erro ao tentar recuperar a senha: " . $e->getMessage();
        }
    }

    /** Altera campo na tabela Password Recovery */
    public function update($recovery)
    {
        $sql = new Sql();

        try {
            $sql->beginTransaction();

            $sql->query("UPDATE tb_password_recovereries SET recovey_date = NOW() WHERE id_user = :iduser AND idpassword_recovery = :idpassword_recovery", array(
                ':iduser' => $recovery->getIdUser(),
                ':idpassword_recovery' => $recovery->getIdPasswordRecovery()
            ));

            return $sql->commit();
        } catch (\Exception $e) {
            $sql->rollBack();
            return  "Erro ao tentar alterar a senha: " . $e->getMessage();
        }
    }

    /** Deleta campo na tabela Address */
    public function delete($recovery)
    {

    }
}