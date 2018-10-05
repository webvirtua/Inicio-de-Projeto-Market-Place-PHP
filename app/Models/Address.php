<?php

namespace App\Models;
use App\DAO\AddressDAO;
use App\DAO\CorporationDAO;
use App\util\UtilMessage;
use App\DAO\GenericQueries;

/** <b>Address</b>
 *Classe CRUD Address
 * @autor Luiz Rodrigues <atende@webvirtua.com.br>
 * @copyrigth © 2018, Luiz Rodrigues Web Virtua
 * @version 1.0
 */
class Address /*implements IDAO*/
{
    /** @var  int $idAddress - ID do Endereço */
    private $idAddress;
    /** @var  enum $AddressType - Endereço Residêncial com 1 caractere  */
    private $AddressType;
    /** @var  string $street - Rua, Avenida, etc... com no máximo 50 caracteres  */
    private $street;
    /** @var  smallint $number - Número da casa, apto., etc... com no máximo 8 */
    private $number;
    /** @var  string $complement - Casa, Apto., etc... com no máximo 100 caracteres  */
    private $complement;
    /** @var  string $district - Bairro, Setor, etc... com no máximo 50 caracteres  */
    private $district;
    /** @var  int $zipcode - CEP com no máximo 8 */
    private $zipcode;
    /** @var  string $address_alias - Apelido do endereço com no máximo 20 caracteres  */
    private $address_alias;
    /** @var  string $city - Nome da cidade com no máximo 40 caracteres */
    private $city;
    /** @var  int $id_person - FK da entidade Persons */
    private $id_person;
    /** @var  int $id_corporation - FK da entidade Addresses */
    private $id_corporation;
    /** @var  boolean $verifyCompany - Defini se Endereço pertence a Empresa */
    private $verifyCompany;

    /** @return int */
    public function getIdAddress()
    {
        return $this->idAddress;
    }

    /** @param int $idAddress */
    public function setIdAddress($idAddress)
    {
        $this->idAddress = $idAddress;
    }

    /** @return enum */
    public function getAddressType()
    {
        return $this->AddressType;
    }

    /** @param enum $AddressType */
    public function setAddressType($AddressType)
    {
        $this->AddressType = $AddressType;
    }

    /** @return string */
    public function getStreet()
    {
        return $this->street;
    }

    /** @param string $street */
    public function setStreet($street)
    {
        $this->street = $street;
    }

    /** @return smallint */
    public function getNumber()
    {
        return $this->number;
    }

    /** @param smallint $number */
    public function setNumber($number)
    {
        $this->number = $number;
    }

    /** @return string */
    public function getComplement()
    {
        return $this->complement;
    }

    /** @param string $complement */
    public function setComplement($complement)
    {
        $this->complement = $complement;
    }

    /** @return string */
    public function getDistrict()
    {
        return $this->district;
    }

    /** @param string $district */
    public function setDistrict($district)
    {
        $this->district = $district;
    }

    /** @return int */
    public function getZipcode()
    {
        return $this->zipcode;
    }

    /** @param int $zipcode */
    public function setZipcode($zipcode)
    {
        $this->zipcode = $zipcode;
    }

    /** @return string */
    public function getAddressAlias()
    {
        return $this->address_alias;
    }

    /** @param string $address_alias */
    public function setAddressAlias($address_alias)
    {
        $this->address_alias = $address_alias;
    }

    /** @return string */
    public function getCity()
    {
        return $this->city;
    }

    /** @param string $city */
    public function setCity($city)
    {
        $this->city = $city;
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

    /** @return int */
    public function getIdCorporation()
    {
        return $this->id_corporation;
    }

    /** @param int $id_corporation */
    public function setIdCorporation($id_corporation)
    {
        $this->id_corporation = $id_corporation;
    }

    /** @return bool */
    public function isVerifyCompany()
    {
        return $this->verifyCompany;
    }

    /** @param bool $verifyCompany */
    public function setVerifyCompany($verifyCompany)
    {
        $this->verifyCompany = $verifyCompany;
    }

    /** Cadastra a tabela Addresses */
    public function insert($addressObj)
    {
        if (AddressDAO::findRepeat($addressObj)) {
            UtilMessage::setErrorMessage("Você já tem este endereço cadastrado!");
            return 0;
        }

        $result = GenericQueries::findCityName($this->getCity());

        foreach ($result as $row) {
            $idCity = $row["idcity"];
        }

        if (count($result) == 0)
            $idCity = 100000;

        try {
            $this->setCity($idCity);
            $address = new AddressDAO();
            $return = $address->insert($addressObj);

            if ($this->isVerifyCompany() && is_numeric($return)) {
                $corporation = new Corporation();
                $search = CorporationDAO::findOne($this->getIdCorporation());

                foreach ($search as $row) {
                    $cnpj = $row["cnpj"];
                    $social = $row["social_reason"];
                    $state = $row["state_registration"];
                    $responsible = $row["responsible"];
                }

                $corporation->setIdCorporation($this->getIdCorporation());
                $corporation->setCnpj($cnpj);
                $corporation->setSocialReason($social);
                $corporation->setStateRegistration($state);
                $corporation->setResponsible($responsible);
                $corporation->setIdAddress($return);

                $corporationDAO = new CorporationDAO();
                $corporationDAO->update($corporation);
            }

            UtilMessage::setSuccessMessage("Endereço Cadastrado com Sucesso!");
            return 1;
        } catch (\Exception $e) {
            $result = AddressDAO::findRepeat($addressObj);

            foreach ($result as $row) {
                $idAddress = $row["idaddress"];
            }

            $addressObj->setIdAddress($idAddress);
            $address->delete($addressObj);

            UtilMessage::setErrorMessage("Erro ao tentar cadastrar Endereço: " . $e->getMessage());
            return 0;
        }
    }
}