<?php

namespace App\DAO;
use App\core\Sql;

/** <b>User</b>
 *Classe CRUD User - nível de acesso dos Usuários
 * @autor Luiz Rodrigues <atende@webvirtua.com.br>
 * @copyrigth © 2018, Luiz Rodrigues Web Virtua
 * @version 1.0
 */
class UserDAO extends Sql implements IDAO
{
    /** @return \ArrayObject<string> Lista Usuários Cadastrados */
    public static function findAll()
    {
        $sql = new Sql();

        $sql = $sql->select("SELECT us.iduser, pe.name, pe.last_name, pe.cpf FROM tb_persons AS pe
            INNER JOIN tb_users AS us ON pe.idperson = us.id_person");

        return $sql;
    }

    /** @return \ArrayObject<string> Lista uma Pessoa Cadastrada */
    public static function findOne($idPerson)
    {

    }

    /** @return \ArrayObject<string> Retorna uma pessoa existente */
    public static function findRepeat($email)
    {
        $sql = new Sql();

        $sql = $sql->select("SELECT iduser, type, email, password, id_person FROM tb_users WHERE email = :email", array(
            ":email" => $email
        ));

        return $sql;
    }

    /** Cadastra a tabela Users */
    public function insert($user)
    {
        $sql = new Sql();

        try {
            $sql->beginTransaction();

            $sql->query("INSERT INTO tb_users (type, email, password, id_person) VALUES(:type, :email, :password, :id_person)", array(
                ':type' => $user->getUserType(),
                ':email' => $user->getEmail(),
                ':password' => $user->getPassword(),
                ':id_person' => $user->getIdPerson()
            ));
            $lastIdUser = $sql->lastInsertId();

            $sql->commit();
            return $lastIdUser;
        } catch (\Exception $e) {
            $sql->rollBack();
            return  "Erro ao tentar Cadastrar Usuário: " . $e->getMessage();
        }
    }

    /** Altera campo na tabela Phones */
    public function update($user)
    {
        $sql = new Sql();

        try {
            $sql->beginTransaction();

            $sql->query("UPDATE tb_users SET password = :password WHERE iduser = :iduser", array(
                ':iduser' => $user->getIdUser(),
                ':password' => $user->getPassword()
            ));

            return $sql->commit();
        } catch (PDOException $e) {
            $sql->rollBack();
            UtilMessage::setErrorMessage("Erro ao tentar alterar Senha: " . $e->getMessage());
        }
    }

    /** Deleta campo na tabela Phones */
    public function delete($user)
    {

    }
}