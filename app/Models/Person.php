<?php
namespace App\Models;
use App\DAO\PersonDAO;
use App\util\UtilMessage;

/** <b>Person</b>
 *Classe CRUD Person
 * @version 1.0
 * @autor Luiz Rodrigues <atende@webvirtua.com.br>
 * @copyrigth © 2018, Luiz Rodrigues Web Virtua
 * @since 1.0
 * @package App\Models
*/
class Person
{
    /** @var  int $idPerson - ID de Person */
    private $idPerson;
    /** @var  enum $type - Define Pessoa Física ou Jurídica com 1 caractere  */
    private $type;
    /** @var  string $name - Nome da Pessoa com no máximo 20 caracteres */
    private $name;
    /** @var  string $last_name - Sobrenome da pessoa com no máximo 50 caracteres */
    private $last_name;
    /** @var  string $cpf - Número do CPF da pessoa com 11 caracteres */
    private $cpf;
    /** @var  date $birt - Data de nascimento da pessoa YYYY/MM/DD */
    private $birth;
    /** @var  enum $sex - Sexo da pessoa com 1 caractere */
    private $sex;
    /** @var  boolean $newsletter - Ativado ou desativado */
    private $newsletter;

    /** @return int */
    public function getIdPerson()
    {
        return $this->idPerson;
    }

    /** @param int $idPerson */
    public function setIdPerson($idPerson)
    {
        $this->idPerson = $idPerson;
    }

    /** @return enum */
    public function getType()
    {
        return $this->type;
    }

    /** @param enum $type */
    public function setType($type)
    {
        $this->type = $type;
    }

    /** @return string */
    public function getName()
    {
        return $this->name;
    }

    /** @param string $name */
    public function setName($name)
    {
        $this->name = $name;
    }

    /** @return string */
    public function getLastName()
    {
        return $this->last_name;
    }

    /** @param string $last_name */
    public function setLastName($last_name)
    {
        $this->last_name = $last_name;
    }

    /** @return string */
    public function getCpf()
    {
        return $this->cpf;
    }

    /** @param string $cpf */
    public function setCpf($cpf)
    {
        $this->cpf = $cpf;
    }

    /** @return date */
    public function getBirth()
    {
        return $this->birth;
    }

    /** @param date $birt */
    public function setBirth($birth)
    {
        $this->birth = $birth;
    }

    /** @return enum */
    public function getSex()
    {
        return $this->sex;
    }

    /** @param enum $sex */
    public function setSex($sex)
    {
        $this->sex = $sex;
    }

    /** @return bool */
    public function isNewsletter()
    {
        return $this->newsletter;
    }

    /** @param bool $newsletter */
    public function setNewsletter($newsletter)
    {
        $this->newsletter = $newsletter;
    }

    /** Cadastra as tabelas Person */
    public function insert($personObj)
    {
        $result = PersonDAO::findRepeat($this->getCpf());

        if(count($result) > 0) {
            UtilMessage::setErrorMessage("Este CPF já esta sendo usado em nossa base de dados! Se este CPF é seu você pode alterar a senha!");
            return 0;
        }

        try {
            $person = new PersonDAO();
            $return = $person->insert($personObj);

            UtilMessage::setSuccessMessage("Seu Cadastro foi realizado com Sucesso!");
            return $return;
        } catch (\Exception $e) {
            UtilMessage::setErrorMessage("Erro ao tentar Cadastrar: ".$e->getMessage());
            UtilMessage::setState('store');
            return 0;
        }
    }

    /** Deleta campo na tabela Person */
    public function delete($personObj)
    {
        try {
            $person = new PersonDAO();
            $person->delete($personObj);

            UtilMessage::setSuccessMessage("Categoria excluída com sucesso!");
            return 1;
        } catch (\Exception $e) {
            UtilMessage::setErrorMessage("Erro ao tentar excluir Categoria: " . $e->getMessage());
            UtilMessage::setState('category');
            return 0;
        }
    }
}
