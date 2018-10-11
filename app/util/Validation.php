<?php
namespace App\util;

use App\DAO\ProductDAO;

/** <b>Validation</b>
 *Classe com validações
 * @version 1.0
 * @autor Luiz Rodrigues <atende@webvirtua.com.br>
 * @copyrigth © 2018, Luiz Rodrigues Web Virtua
 * @since 1.0
 * @package App\util
 */
class Validation
{
    /** Validação de CPF */
    public static function cpfValidate($cpf)
    {
        if (empty(trim($cpf)) || strlen($cpf) != 11 || !is_numeric($cpf)) {
            return "CPF inválido, digite um CPF válido!";
        } else if (
            $cpf == '00000000000' ||
            $cpf == '11111111111' ||
            $cpf == '22222222222' ||
            $cpf == '33333333333' ||
            $cpf == '44444444444' ||
            $cpf == '55555555555' ||
            $cpf == '66666666666' ||
            $cpf == '77777777777' ||
            $cpf == '88888888888' ||
            $cpf == '99999999999'
        ) {
            return "CPF inválido, digite um CPF válido!";
        } else {
            for ($t = 9; $t < 11; $t++) {
                for ($d = 0, $c = 0; $c < $t; $c++) {
                    $d += $cpf{$c} * (($t + 1) - $c);
                }
                $d = ((10 * $d) % 11) % 10;

                if ($cpf{$c} != $d)
                    return "CPF inválido, digite um CPF válido!";
            }
        }
    }

    /** Validação de CNPJ */
    public static function cnpjValidate($cnpj)
    {
        if (empty(trim($cnpj)) || strlen($cnpj) != 14 || !is_numeric($cnpj)) {
            return "CNPJ inválido, digite um CNPJ válido!";
        } else {
            //Valida primeiro dígito verificador
            for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++) {
                $soma += $cnpj{$i} * $j;
                $j = ($j == 2) ? 9 : $j - 1;
            }
            $resto = $soma % 11;

            if ($cnpj{12} != ($resto < 2 ? 0 : 11 - $resto))
                return "CNPJ inválido, digite um CNPJ válido!";

            //Valida segundo dígito verificador
            for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++) {
                $soma += $cnpj{$i} * $j;
                $j = ($j == 2) ? 9 : $j - 1;
            }
            $resto = $soma % 11;

            if (!($cnpj{13} == ($resto < 2 ? 0 : 11 - $resto)))
                return "CNPJ inválido, digite um CNPJ válido!";
        }
    }

    public static function verifyInt($number)
    {
        if (empty(trim($number)))
            return "Este campo é obrigatório!";
        else if (!filter_var($number, FILTER_VALIDATE_INT))
            return "Este campo só pode conter números!";
    }

    public static function verifyWords($words)
    {
        $number = filter_var($words, FILTER_SANITIZE_NUMBER_INT);

        if (empty(trim($words))) //se vazio ou digitar espaços
            return "Este campo é obrigatório!";
        else if (is_numeric($words) || $number == "0" || is_numeric($number))
            return "Este campo não pode conter números!";
    }

    public static function verifyEmail($email)
    {
        if (empty(trim($email)) || !filter_var($email, FILTER_VALIDATE_EMAIL))
            return "Digite um E-mail válido!";
    }

    public static function verifyPassword($password)
    {
        if (empty(trim($password)))
            return "É obrigatório digitar uma Senha!";
        else if (strlen($password) < 6)
            return "Sua Senha precisa ter no mínimo 6 caracteres!";
    }

    public static function passwordConfirm($password, $confirm)
    {
        if ($password !== $confirm)
            return "Sua Senha e a confirmação da Senha devem ser idênticas!";
    }

    public static function verifyDate($date)
    {
        $date = str_replace(array("/", "-"), '', $date);

        $year = substr($date, 0,4);
        $month = substr($date, 4,2);
        $day = substr($date, 6,2);

        if (empty(trim($date)))
            return "É obrigatório digitar a Data!";
        else if (($year < 1900 || $year > 3000) || ($month < 1 || $month > 12) || ($day < 1 || $day > 31) || strlen($date) < 8)
            return "Data inválida!";
    }

    public static function verifyDdd($ddd)
    {
        if (empty(trim($ddd)))
            return "É obrigatório digitar o DDD do Telefone!";
        else if (!filter_var($ddd, FILTER_VALIDATE_INT))
            return "O DDD só pode conter números!";
        else if (strlen($ddd) != 2)
            return "O DDD deve ter dois números!";
    }

    public static function verifyPhone($phone)
    {
        if (empty(trim($phone)))
            return "É obrigatório digitar o Telefone!";
        else if (!filter_var($phone, FILTER_VALIDATE_INT))
            return "O Telefone só pode conter números!";
        else if (strlen($phone) < 8 || strlen($phone) > 9)
            return "O Telefone deve ter oito ou nove números!";
    }

    public static function verifyCep($cep)
    {
        if (empty(trim($cep)))
            return "É obrigatório digitar o CEP!";
        else if (!filter_var($cep, FILTER_VALIDATE_INT))
            return "O CEP só pode conter números!";
        else if (strlen($cep) != 8)
            return "O CEP deve ter oito números!";
    }

    public static function verifyImage($arquivo)
    {
        $ext = pathinfo($arquivo, PATHINFO_EXTENSION);

        if (!isset($arquivo) || $_FILES["arquivo"]["error"] > 0 || !($ext == 'png' || $ext == 'gif' || $ext == 'jpg' || $ext == 'jpeg'))
            return "Este campo é obrigatório, escolha uma Imagem Jpeg, Png ou Gif!";
    }

    public static function verifyEmpty($var)
    {
        if (empty(trim($var)))
            return "Este campo é obrigatório!";
    }

    public static function verifyIfInt($number)
    {
        if (empty($number))
            return null;
        else if (!filter_var($number, FILTER_VALIDATE_INT))
            return "Este campo só pode conter números!";
    }

    public static function verifyIfFloat($number)
    {
        $number = Util::charsSpecial($number, '.', array(','));

        if (!filter_var($number, FILTER_VALIDATE_FLOAT))
            return "Este campo deve conter números com duas casas decimais, Ex: 2,99 ou 2.99!";
    }

    public static function verifyIfArray($array)
    {
        if (!is_array($array))
            return "Este campo está inválido!";
    }
}