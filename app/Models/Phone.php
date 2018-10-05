<?php

namespace App\Models;
use App\DAO\PhoneDAO;
use App\util\UtilMessage;

/** <b>Categories</b>
 *Classe CRUD Categories
 * @autor Luiz Rodrigues <atende@webvirtua.com.br>
 * @copyrigth © 2018, Luiz Rodrigues Web Virtua
 * @version 1.0
 */
class Phone
{
    /** @var  int $idPhone - ID do telefone */
    private $idPhone;
    /** @var  enum $phoneType - Define telefone residencial, romercial, móvel com 1 caractere */
    private $phoneType;
    /** @var  int $areaCode - Código de área do telefone com no mínimo 2 e no máximo 3 */
    private $areaCode;
    /** @var  int $phone - Número do telefone com no mínino 8 e no máximo 9 */
    private $phone;
    /** @var  boolean $whatsApp - Defini se tem WhatsApp ativo no telefone */
    private $whatsApp;
    /** @var  int $id_person - FK da entidade Persons */
    private $id_person;

    /** @return int */
    public function getIdPhone()
    {
        return $this->idPhone;
    }

    /** @param int $idPhone */
    public function setIdPhone($idPhone)
    {
        $this->idPhone = $idPhone;
    }

    /** @return enum */
    public function getPhoneType()
    {
        return $this->phoneType;
    }

    /** @param enum $phoneType */
    public function setPhoneType($phoneType)
    {
        $this->phoneType = $phoneType;
    }

    /** @return int */
    public function getAreaCode()
    {
        return $this->areaCode;
    }

    /** @param int $areaCode */
    public function setAreaCode($areaCode)
    {
        $this->areaCode = $areaCode;
    }

    /** @return int */
    public function getPhone()
    {
        return $this->phone;
    }

    /** @param int $phone */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /** @return bool */
    public function isWhatsApp()
    {
        return $this->whatsApp;
    }

    /** @param bool $whatsApp */
    public function setWhatsApp($whatsApp)
    {
        $this->whatsApp = $whatsApp;
    }

    /** @return int */
    public function getIdPerson()
    {
        return $this->id_person;
    }

    /** @param int $id_person */
    public function setIdPerson($id_person)
    {
        $this->id_person = $id_person;
    }

    /** Cadastra a tabela Phones */
    public function insert($phoneObj)
    {
        try {
            $phone = new PhoneDAO();
            $return = $phone->insert($phoneObj);

            UtilMessage::setSuccessMessage("Telefone Cadastrado com Sucesso!");
            return $return;
        } catch (\Exception $e) {
            UtilMessage::setErrorMessage("Erro tentar Cadastrar Telefone: " . $e->getMessage());
            UtilMessage::setState('store');
            return 0;
        }
    }

    /** Deleta campo na tabela Phones */
    public function delete($phoneObj)
    {
        try {
            $phone = new PhoneDAO();
            $phone->delete($phoneObj);

            UtilMessage::setSuccessMessage("Categoria excluída com sucesso!");
            return 1;
        } catch (\Exception $e) {
            UtilMessage::setErrorMessage("Erro ao tentar excluir Categoria: " . $e->getMessage());
            UtilMessage::setState('category');
            return 0;
        }
    }
}