<?php
namespace App\Models;
use App\DAO\StoreDAO;
use App\util\Util;
use App\util\UtilMessage;

/** <b>Store</b>
 *Classe CRUD Store
 * @autor Luiz Rodrigues <atende@webvirtua.com.br>
 * @copyrigth © 2018, Luiz Rodrigues Web Virtua
 * @version 1.0
*/
class Store
{
    /** @var  int $idStore - ID de Store */
    private $idStore;
    /** @var  int $activated - Define se a loja esta ativa ou não com 1 */
    private $activated;
    /** @var  int $type - Define o tipo de venda, 0 atacado, 1 varejo e 2 atacado/varejo com 1 */
    private $type;
    /** @var  string $name - Nome da loja com no máximo 55 caracteres */
    private $name;
    /** @var  string $image - Nome da imagem com no máximo 255 caracteres */
    private $image;
    /** @var  string $url - Nome da url da loja com no máximo 100 caracteres */
    private $url;
    /** @var  string $store_code - Código da loja com no máximo 20 caracteres */
    private $store_code;
    /** @var  int $wholesale_quantity - Define a quantidade de produtos que caracteriza atacado com 2 */
    private $wholesale_quantity;
    /** @var  int $id_microdata - FK da entidade Microdata */
    private $id_microdata;
    /** @var  int $id_user - FK da entidade Users */
    private $id_user;

    /** @var  array de String $categories - Nomes das categorias com no máximo 20 caracteres cada */
    private $categories;
    /** @var  array de String $tags - Nomes das tags com no máximo 20 caracteres cada */
    private $tags;

    /** @return int */
    public function getIdStore()
    {
        return $this->idStore;
    }

    /** @param int $idStore */
    public function setIdStore($idStore)
    {
        $this->idStore = $idStore;
    }

    /** @return int */
    public function getActivated()
    {
        return $this->activated;
    }

    /** @param int $activated */
    public function setActivated($activated)
    {
        $this->activated = $activated;
    }

    /** @return int */
    public function getType()
    {
        return $this->type;
    }

    /** @param int $type */
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
    public function getImage()
    {
        return $this->image;
    }

    /** @param string $image */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /** @return string */
    public function getUrl()
    {
        return $this->url;
    }

    /** @param string $url */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /** @return string */
    public function getStoreCode()
    {
        return $this->store_code;
    }

    /** @param string $store_code */
    public function setStoreCode($store_code)
    {
        $this->store_code = $store_code;
    }

    /** @return int */
    public function getWholesaleQuantity()
    {
        return $this->wholesale_quantity;
    }

    /** @param int $wholesale_quantity */
    public function setWholesaleQuantity($wholesale_quantity)
    {
        $this->wholesale_quantity = $wholesale_quantity;
    }

    /** @return int */
    public function getIdMicrodata()
    {
        return $this->id_microdata;
    }

    /** @param int $id_microdata */
    public function setIdMicrodata($id_microdata)
    {
        $this->id_microdata = $id_microdata;
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

    /** @return array */
    public function getCategories()
    {
        return $this->categories;
    }

    /** @param array $categories */
    public function setCategories($categories)
    {
        $this->categories = $categories;
    }

    /** @return array */
    public function getTags()
    {
        return $this->tags;
    }

    /** @param array $tags */
    public function setTags($tags)
    {
        $this->tags = $tags;
    }

    /** @return \ArrayObject<string> que Lista Lojas */
    public static function findAll()
    {
        $return = StoreDAO::findAll();

        $return = Util::encodeIdOnArray($return);

        return $return;
    }

    /** @return \ArrayObject<string> Retorna uma Loja */
    public static function findOne($idLoja)
    {
        $return = StoreDAO::findOne($idLoja);

        $return = Util::encodeIdOnArray($return);

        return $return;
    }

    /** @return <int> Altera loja a ser administrada */
    public static function storeSession($idStore)
    {
        $pageAtual = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $pageAtual = end(explode('/', $pageAtual));

        if (filter_var($_POST['sessionStore'], FILTER_VALIDATE_INT)) {
            UtilMessage::setErrorMessage("Houve um erro ao tentar Adminitrar outra Loja!");
        } else if ($idStore) {
            $store = StoreSeller::findOneStore($idStore);

            foreach ($store as $row) {
                $idStore = $row["idstore"];
                $activated = $row["activated"];
                $nameStore = $row["name"];
            }

            User::setSessionStore($idStore, $activated, $nameStore);
            header("Location: ".Util::urlLoja()."/".$pageAtual);
            exit();
        }

        return 1;
    }

    /** Cadastra a tabela Stores */
    public function insert($storeObj)
    {
        $result = StoreDAO::findStoreCodeRepeat($this->getStoreCode());

        if(count($result) > 0) {
            UtilMessage::setErrorMessage("Este Código já esta sendo usado por outra loja! Tente um Código diferente.");
            UtilMessage::setState('store');
            return 0;
        }

        $result = StoreDAO::findStoreUrlRepeat($this->getUrl());

        if(count($result) > 0) {
            UtilMessage::setErrorMessage("Esta Url já está sendo usada por outra loja! Tente uma Url diferente, ex: nome-loja-3");
            UtilMessage::setState('store');
            return 0;
        }

        try {
            $store = new StoreDAO();
            $store->insert($storeObj);

            UtilMessage::setSuccessMessage("Loja Cadastrada com Sucesso!");
            return 1;
        } catch (\Exception $e) {
            UtilMessage::setErrorMessage("Erro tentar Cadastrar Loja: ".$e->getMessage());
            UtilMessage::setState('store');
            return 0;
        }
    }

    /** Altera campo na tabela Stores */
    public function update($storeObj)
    {
        try {
            $store = new StoreDAO();
            $store->update($storeObj);

            UtilMessage::setSuccessMessage("Loja alterado com sucesso!");
            return 1;
        } catch (PDOException $e) {
            UtilMessage::setErrorMessage("Erro ao tentar alterar Loja: " . $e->getMessage());
            UtilMessage::setState('store');
            return 0;
        }
    }

    /** Deleta campo na tabela Stores */
    public function delete($storeObj)
    {
        $store = new StoreDAO();

        $idStore = $storeObj->getIdStore();

        if (filter_var($storeObj->getCategories(), FILTER_VALIDATE_INT)) {
            if (!isset($idStore)) {
                UtilMessage::setErrorMessage("Erro ao tentar excluir Categoria!");
                return 0;
            }

            try {
                $store->deleteCategory($storeObj);

                UtilMessage::setSuccessMessage("Categoria excluída com sucesso!");
                return 1;
            } catch (PDOException $e) {
                UtilMessage::setErrorMessage("Erro ao tentar excluir Categoria: " . $e->getMessage());
                UtilMessage::setState('category');
                return 0;
            }
        } else if (filter_var($this->getTags(), FILTER_VALIDATE_INT)) {
            if (!isset($idStore)) {
                UtilMessage::setErrorMessage("Erro ao tentar excluir Tag!");
                return 0;
            }

            try {
                $store->deleteTag($storeObj);

                UtilMessage::setSuccessMessage("Tag excluída com sucesso!");
                return 1;
            } catch (PDOException $e) {
                UtilMessage::setErrorMessage("Erro ao tentar excluir Tag: " . $e->getMessage());
                UtilMessage::setState('tag');
                return 0;
            }
        } else {
            if (!isset($idStore)) {
                UtilMessage::setErrorMessage("Erro ao tentar excluir Loja!");
                return 0;
            }

            try {
                $store->delete($storeObj);

                UtilMessage::setSuccessMessage("Loja excluída com sucesso!");
                return 1;
            } catch (PDOException $e) {
                UtilMessage::setErrorMessage("Erro ao tentar excluir Loja: " . $e->getMessage());
                UtilMessage::setState('store');
                return 0;
            }
        }
    }
}