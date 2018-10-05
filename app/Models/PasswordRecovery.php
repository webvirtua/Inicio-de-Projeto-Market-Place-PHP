<?php

namespace App\Models;
use App\DAO\PasswordRecoveryDAO;
use App\util\UtilMessage;

/** <b>Categories</b>
 *Classe CRUD Categories
 * @autor Luiz Rodrigues <atende@webvirtua.com.br>
 * @copyrigth © 2018, Luiz Rodrigues Web Virtua
 * @version 1.0
 */
class PasswordRecovery
{
    /** @var  int $idPasswordRecovery - ID de Password Recovery */
    private $idPasswordRecovery;
    /** @var  string $ip - Ip do cliente com no máximo 45 caracteres */
    private $ip;
    /** @var  date $recovery_date - Data de recuperação */
    private $recovery_date;
    /** @var  date $register_date - Data de registro */
    private $register_date;
    /** @var  int $id_user - FK da entidade Users */
    private $id_user;

    /** @return int */
    public function getIdPasswordRecovery()
    {
        return $this->idPasswordRecovery;
    }

    /** @param int $idPasswordRecovery */
    public function setIdPasswordRecovery($idPasswordRecovery)
    {
        $this->idPasswordRecovery = $idPasswordRecovery;
    }

    /** @return string */
    public function getIp()
    {
        return $this->ip;
    }

    /** @param string $ip */
    public function setIp($ip)
    {
        $this->ip = $ip;
    }

    /** @return date */
    public function getRecoveryDate()
    {
        return $this->recovery_date;
    }

    /** @param date $recovery_date */
    public function setRecoveryDate($recovery_date)
    {
        $this->recovery_date = $recovery_date;
    }

    /** @return date */
    public function getRegisterDate()
    {
        return $this->register_date;
    }

    /** @param date $register_date */
    public function setRegisterDate($register_date)
    {
        $this->register_date = $register_date;
    }

    /** @return int */
    public function getIdUser()
    {
        return $this->id_user;
    }

    /** @param int $id_user */
    public function setIdUser($id_user)
    {
        $this->id_user = $id_user;
    }

    /** Cadastra a tabela Password Recovery */
    public function insert($recoveryObj)
    {
        if (filter_input($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP))
            $ip = $_SERVER['REMOTE_ADDR'];
        else
            $ip = 0;

        $recoveryObj->setIp($ip);

        try {
            $recovery = new PasswordRecoveryDAO();
            $recovery->insert($recoveryObj);

            UtilMessage::setSuccessMessage("Email de recuperação de senha enviado com Sucesso! Siga as instruções recebidas no seu E-mail para alterar a Senha.");
            return 1;
        } catch (\Exception $e) {
            UtilMessage::setErrorMessage("Erro tentar recuperar senha: " . $e->getMessage());
            return 0;
        }
    }

    /** Altera campo na tabela Password Recovery */
    public function update($recoveryObj)
    {
        try {
            $recovery = new PasswordRecoveryDAO();
            $recovery->update($recoveryObj);

            UtilMessage::setSuccessMessage("Senha alterada com sucesso.");
            return 1;
        } catch (\Exception $e) {
            UtilMessage::setErrorMessage("Erro tentar alterar senha: " . $e->getMessage());
            return 0;
        }
    }
}