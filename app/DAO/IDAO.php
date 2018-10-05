<?php
namespace App\DAO;

/** <b>IDAO</b>
 *Classe Interface DAO
 * @autor Luiz Rodrigues <atende@webvirtua.com.br>
 * @copyrigth © 2018, Luiz Rodrigues Web Virtua
 * @version 1.0
 */
interface IDAO
{
    public function insert($object);
    public function update($object);
    public function delete($object);
    public static function findAll();
    public static function findOne($id);
}