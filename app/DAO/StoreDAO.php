<?php

namespace App\DAO;
use App\core\Sql;

/** <b>Address</b>
 *Classe CRUD Address
 * @autor Luiz Rodrigues <atende@webvirtua.com.br>
 * @copyrigth © 2018, Luiz Rodrigues Web Virtua
 * @version 1.0
 */
class StoreDAO extends Sql implements IDAO
{
    /** @return \ArrayObject<string> que Lista Lojas */
    public static function findAll()
    {
        $sql = new Sql();

        $sql = $sql->select("SELECT idstore, activated, type, name, image, url, store_code, wholesale_quantity FROM tb_stores ORDER BY idstore DESC");

        return $sql;
    }

    /** @return \ArrayObject<string> Retorna uma Loja */
    public static function findOne($idLoja)
    {
        $sql = new Sql();

        $sql = $sql->select("
            SELECT st.idstore, st.activated, st.type, st.name, st.image, st.url, st.store_code, st.wholesale_quantity, st.id_microdata, st.id_user, 
            mi.name AS microdata, mi.translation_microdata, 
            pe.cpf, pe.name AS name_person, pe.last_name
            FROM tb_stores AS st
            INNER JOIN tb_microdata AS mi ON mi.idmicrodata = st.id_microdata
            INNER JOIN tb_users AS us ON us.iduser = st.id_user
            INNER JOIN tb_persons AS pe ON pe.idperson = us.id_person
            WHERE st.idstore = :idstore", array(
            ":idstore" => $idLoja
        ));

        return $sql;
    }

    /** @return \ArrayObject<string> Retorna quantidade de imagens com mesmo nome */
    public static function findImageRepeat($image)
    {
        $sql = new Sql();

        $sql = $sql->select("SELECT image FROM tb_stores WHERE image = :image", array(
            ":image" => $image
        ));

        $count = count($sql);

        return $count;
    }

    /** @return \ArrayObject<string> Retorna se código de loja já existe */
    public static function findStoreCodeRepeat($storeCode)
    {
        $sql = new Sql();

        $sql = $sql->select("SELECT store_code FROM tb_stores WHERE store_code = :store_code", array(
            ":store_code" => $storeCode
        ));

        return $sql;
    }

    /** @return \ArrayObject<string> Retorna se url de loja já existe */
    public static function findStoreUrlRepeat($url)
    {
        $sql = new Sql();

        $sql = $sql->select("SELECT url FROM tb_stores WHERE url = :url", array(
            ":url" => $url
        ));

        return $sql;
    }

    /** @return \ArrayObject<string> que Lista Lojas */
    public static function findUserStore($idUser)
    {
        $sql = new Sql();

        $sql = $sql->select("SELECT idstore, activated, name FROM tb_stores WHERE id_user = :id_user LIMIT 1", array(
            ":id_user" => $idUser
        ));

        return $sql;
    }

    /** Cadastra a tabela Stores */
    public function insert($store)
    {
        $sql = new Sql();

        try {
            $sql->beginTransaction();

            $sql->query("INSERT INTO tb_stores (activated, type, name, image, url, store_code, wholesale_quantity, id_microdata, id_user) 
                VALUES(:activated, :type, :name, :image, :url, :store_code, :wholesale_quantity, :id_microdata, :id_user)", array(
                ':activated' => $store->getActivated(),
                ':type' => $store->getType(),
                ':name' => $store->getName(),
                ':image' => $store->getImage(),
                ':url' => $store->getUrl(),
                ':store_code' => $store->getStoreCode(),
                ':wholesale_quantity' => $store->getWholesaleQuantity(),
                ':id_microdata' => $store->getIdMicrodata(),
                ':id_user' => $store->getIdUser()
            ));
            $lastIdCategory = $sql->lastInsertId();

            foreach ($store->getCategories() AS $row) {
                $sql->query("INSERT INTO tb_stores_has_tb_categories (id_store, id_category) VALUES(:id_store, :id_category)", array(
                    ':id_store' => $lastIdCategory,
                    ':id_category' => $row
                ));
            }

            foreach ($store->getTags() AS $row) {
                $sql->query("INSERT INTO tb_stores_has_tb_tags (id_store, id_tag) VALUES(:id_store, :id_tag)", array(
                    ':id_store' => $lastIdCategory,
                    ':id_tag' => $row
                ));
            }

            return $sql->commit();
        } catch (\Exception $e) {
            $sql->rollBack();
            return "Erro tentar Cadastrar Loja: ".$e->getMessage();
        }
    }

    /** Altera campo na tabela Stores */
    public function update($store)
    {
        $sql = new Sql();

        try {
            $sql->beginTransaction();

            $sql->query("UPDATE tb_stores SET activated = :activated, type = :type, name = :name, image = :image, url = :url, 
                store_code = :store_code, wholesale_quantity = :wholesale_quantity, id_microdata = :id_microdata , id_user = :id_user 
                WHERE idstore = :idstore", array(
                ':idstore' => $store->getIdStore(),
                ':activated' => $store->getActivated(),
                ':type' => $store->getType(),
                ':name' => $store->getName(),
                ':image' => $store->getImage(),
                ':url' => $store->getUrl(),
                ':store_code' => $store->getStoreCode(),
                ':wholesale_quantity' => $store->getWholesaleQuantity(),
                ':id_microdata' => $store->getIdMicrodata(),
                ':id_user' => $store->getIdUser()
            ));

            $sql->query("DELETE FROM tb_stores_has_tb_categories WHERE id_store = :id_store", array(
                ':id_store' => $store->getIdStore()
            ));

            foreach ($store->getCategories() AS $row) {
                $result = $sql->select("SELECT id_store FROM tb_stores_has_tb_categories WHERE id_store = :id_store AND id_category = :id_category", array(
                    ":id_store" => $store->getIdStore(),
                    ":id_category" => $row
                ));

                if ($row != '' && count($result) == 0) {
                    $sql->query("INSERT INTO tb_stores_has_tb_categories (id_store, id_category) VALUES(:id_store, :id_category)", array(
                        ':id_store' => $store->getIdStore(),
                        ':id_category' => $row
                    ));
                }
            }

            $sql->query("DELETE FROM tb_stores_has_tb_tags WHERE id_store = :id_store", array(
                ':id_store' => $store->getIdStore()
            ));

            foreach ($store->getTags() AS $row) {
                $result = $sql->select("SELECT id_store FROM tb_stores_has_tb_tags WHERE id_store = :id_store AND id_tag = :id_tag", array(
                    ":id_store" => $store->getIdStore(),
                    ":id_tag" => $row
                ));

                if ($row != '' && count($result) == 0) {
                    $sql->query("INSERT INTO tb_stores_has_tb_tags (id_store, id_tag) VALUES(:id_store, :id_tag)", array(
                        ':id_store' => $store->getIdStore(),
                        ':id_tag' => $row
                    ));
                }
            }

            return $sql->commit();
        } catch (PDOException $e) {
            $sql->rollBack();
            return "Erro ao tentar alterar Loja: " . $e->getMessage();
        }
    }

    /** Deleta campo na tabela Address */
    public function delete($store)
    {
        $sql = new Sql();

        try {
            $sql->beginTransaction();

            $sql->query("DELETE FROM tb_stores_has_tb_categories WHERE id_store = :id_store", array(
                ':id_store' => $store->getIdStore()
            ));

            $sql->query("DELETE FROM tb_stores_has_tb_tags WHERE id_store = :id_store", array(
                ':id_store' => $store->getIdStore()
            ));

            $sql->query("DELETE FROM tb_stores WHERE idstore = :idstore", array(
                ':idstore' => $store->getIdStore()
            ));

            return $sql->commit();
        } catch (PDOException $e) {
            $sql->rollBack();
            return "Erro ao tentar excluir Loja: " . $e->getMessage();
        }
    }

    /** Deleta campo na tabela Category */
    public function deleteCategory($store)
    {
        $sql = new Sql();

        try {
            $sql->beginTransaction();

            $sql->query("DELETE FROM tb_stores_has_tb_categories WHERE id_store = :id_store AND id_category = :id_category", array(
                ':id_store' => $store->getIdStore(),
                ':id_category' => $store->getCategories()
            ));

            return $sql->commit();
        } catch (PDOException $e) {
            $sql->rollBack();
            return "Erro ao tentar excluir Categoria: " . $e->getMessage();
        }
    }

    /** Deleta campo na tabela Tags */
    public function deleteTag($store)
    {
        $sql = new Sql();

        try {
            $sql->beginTransaction();

            $sql->query("DELETE FROM tb_stores_has_tb_tags WHERE id_store = :id_store AND id_tag = :id_tag", array(
                ':id_store' => $store->getIdStore(),
                ":id_tag" => $store->getTags()
            ));

            return $sql->commit();
        } catch (PDOException $e) {
            $sql->rollBack();
            return "Erro ao tentar excluir Tag: " . $e->getMessage();
        }
    }
}