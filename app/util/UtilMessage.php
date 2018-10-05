<?php
namespace App\util;

/** <b>UtilMessage</b>
 *Classe com Funcionalidades get e set de mensagens
 * @version 1.0
 * @autor Luiz Rodrigues <atende@webvirtua.com.br>
 * @copyrigth © 2018, Luiz Rodrigues Web Virtua
 * @since 1.0
 * @package App\util
 */
class UtilMessage
{
    const ERROR_REGISTER = "ErrorRegister";
    const SUCCESS_REGISTER = "SuccessRegister";
    const SET_STATE = "State";
    const SET_ERRORS = "Errors";

    /** Set uma mensagem de erro */
    public static function setErrorMessage($msg){
        $_SESSION[UtilMessage::ERROR_REGISTER] = $msg;
    }

    /** @return string com da mensagem de erro */
    public static function getErrorMessage(){
        $msg = $_SESSION[UtilMessage::ERROR_REGISTER];

        UtilMessage::clearSession(UtilMessage::ERROR_REGISTER);

        return $msg;
    }

    /** Set uma mensagem de sucesso */
    public static function setSuccessMessage($msg){
        $_SESSION[UtilMessage::SUCCESS_REGISTER] = $msg;
    }

    /** @return string com da mensagem de sucesso */
    public static function getSuccessMessage(){
        $msg = $_SESSION[UtilMessage::SUCCESS_REGISTER];

        UtilMessage::clearSession(UtilMessage::SUCCESS_REGISTER);

        return $msg;
    }

    /** Set uma mensagem de estado */
    public static function setState($msg){
        $_SESSION[UtilMessage::SET_STATE] = $msg;
    }

    /** @return string com da mensagem de estado */
    public static function getState(){
        $msg = $_SESSION[UtilMessage::SET_STATE];

        UtilMessage::clearSession(UtilMessage::SET_STATE);

        return $msg;
    }

    /** Set uma mensagem de estado */
    public static function setErrors($msg){
        $_SESSION[UtilMessage::SET_ERRORS] = $msg;
    }

    /** @return string com da mensagem de estado */
    public static function getErrors(){
        $msg = $_SESSION[UtilMessage::SET_ERRORS];

        UtilMessage::clearSession(UtilMessage::SET_ERRORS);

        return $msg;
    }

    /** Limpa a sessão da mensagem */
    public static function clearSession($sessionName){
        unset($_SESSION[$sessionName]);
    }
}