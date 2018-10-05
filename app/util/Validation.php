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
            return ["CPF inválido, digite um CPF válido!", 0];
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
            return ["CPF inválido, digite um CPF válido!", 0];
        } else {
            for ($t = 9; $t < 11; $t++) {
                for ($d = 0, $c = 0; $c < $t; $c++) {
                    $d += $cpf{$c} * (($t + 1) - $c);
                }
                $d = ((10 * $d) % 11) % 10;

                if ($cpf{$c} != $d)
                    return ["CPF inválido, digite um CPF válido!", 0];
                else
                    return [filter_var($cpf, FILTER_SANITIZE_NUMBER_INT), 1];
            }
        }
    }

    /** Validação de CNPJ */
    public static function cnpjValidate($cnpj)
    {
        if (empty(trim($cnpj)) || strlen($cnpj) != 14 || !is_numeric($cnpj)) {
            return ["CNPJ inválido, digite um CNPJ válido!", 0];
        } else {
            //Valida primeiro dígito verificador
            for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++) {
                $soma += $cnpj{$i} * $j;
                $j = ($j == 2) ? 9 : $j - 1;
            }
            $resto = $soma % 11;

            if ($cnpj{12} != ($resto < 2 ? 0 : 11 - $resto))
                return ["CNPJ inválido, digite um CNPJ válido!", 0];

            //Valida segundo dígito verificador
            for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++) {
                $soma += $cnpj{$i} * $j;
                $j = ($j == 2) ? 9 : $j - 1;
            }
            $resto = $soma % 11;

            if (!($cnpj{13} == ($resto < 2 ? 0 : 11 - $resto)))
                return ["CNPJ inválido, digite um CNPJ válido!", 0];
            else
                return [filter_var($cnpj, FILTER_SANITIZE_NUMBER_INT), 1];
        }
    }

    public static function verifyInt($number)
    {
        if (empty(trim($number)))
            return ["Este campo é obrigatório!", 0];
        else if (!filter_var($number, FILTER_VALIDATE_INT))
            return ["Este campo só pode conter números!", 0];
        else
            return [$number, 1];
    }

    public static function verifyWords($words)
    {
        $number = filter_var($words, FILTER_SANITIZE_NUMBER_INT);

        if (empty(trim($words))) //se vazio ou digitar espaços
            return ["Este campo é obrigatório!", 0];
        else if (is_numeric($words) || $number == "0" || is_numeric($number))
            return ["Este campo não pode conter números!", 0];
        else
            return [filter_var($words, FILTER_SANITIZE_STRING), 1];
    }

    public static function verifyEmail($email)
    {
        if (empty(trim($email)) || !filter_var($email, FILTER_VALIDATE_EMAIL))
            return ["Digite um E-mail válido!", 0];
        else
            return [filter_var($email, FILTER_SANITIZE_EMAIL), 1];
    }

    public static function verifyPassword($password)
    {
        if (empty(trim($password)))
            return ["É obrigatório digitar uma Senha!", 0];
        else if (strlen($password) < 6)
            return ["Sua Senha precisa ter no mínimo 6 caracteres!", 0];
        else
            return [$password, 1];
    }

    public static function passwordConfirm($password, $confirm)
    {
        if ($password !== $confirm)
            return ["Sua Senha e a confirmação da Senha devem ser idênticas!", 0];
        else
            return [$password, 1];
    }

    public static function verifyDate($date)
    {
        $date = str_replace(array("/", "-"), '', $date);

        $year = substr($date, 0,4);
        $month = substr($date, 4,2);
        $day = substr($date, 6,2);

        if (empty(trim($date)))
            return ["É obrigatório digitar a Data!", 0];
        else if (($year < 1900 || $year > 3000) || ($month < 1 || $month > 12) || ($day < 1 || $day > 31) || strlen($date) < 8)
            return ["Data inválida!", 0];
        return [$date, 1];
    }

    public static function verifyDdd($ddd)
    {
        if (empty(trim($ddd)))
            return ["É obrigatório digitar o DDD do Telefone!", 0];
        else if (!filter_var($ddd, FILTER_VALIDATE_INT))
            return ["O DDD só pode conter números!", 0];
        else if (strlen($ddd) != 2)
            return ["O DDD deve ter dois números!", 0];
        else
            return [$ddd, 1];
    }

    public static function verifyPhone($phone)
    {
        if (empty(trim($phone)))
            return ["É obrigatório digitar o Telefone!", 0];
        else if (!filter_var($phone, FILTER_VALIDATE_INT))
            return ["O Telefone só pode conter números!", 0];
        else if (strlen($phone) < 8 || strlen($phone) > 9)
            return ["O Telefone deve ter oito ou nove números!", 0];
        else
            return [$phone, 1];
    }

    public static function verifyCep($cep)
    {
        if (empty(trim($cep)))
            return ["É obrigatório digitar o CEP!", 0];
        else if (!filter_var($cep, FILTER_VALIDATE_INT))
            return ["O CEP só pode conter números!", 0];
        else if (strlen($cep) != 8)
            return ["O CEP deve ter oito números!", 0];
        else
            return [$cep, 1];
    }

    public static function verifyImage($arquivo)
    {
        $ext = pathinfo($arquivo, PATHINFO_EXTENSION);

        if ($ext == 'png' || $ext == 'gif' || $ext == 'jpg' || $ext == 'jpeg')
            return [filter_var($arquivo, FILTER_SANITIZE_STRING), 1];
        else
            return ["Este campo é obrigatório, escolha uma Imagem Jpeg, Png ou Gif!", 0];
    }

    public static function verifyEmpty($var)
    {
        if (empty(trim($var)))
            return ["Este campo é obrigatório!", 0];
        else
            return [filter_var($var, FILTER_SANITIZE_STRING), 1];
    }

    public static function verifyIfInt($number)
    {
        if (empty($number))
            return [$number, 1];
        else if (!filter_var($number, FILTER_VALIDATE_INT))
            return ["Este campo só pode conter números!", 0];
        else
            return [filter_var($number, FILTER_SANITIZE_NUMBER_INT), 1];
    }

    public static function verifyIfFloat($number)
    {
        $number = Util::subChars($number, '.', array(',', '..', '_', '-'));

        if (empty($number))
            return [$number, 1];
        else if (!filter_var($number, FILTER_VALIDATE_FLOAT))
            return ["Este campo só pode conter números decimais, Ex: 2,99 ou 2.99!", 0];
        else
            return [filter_var($number, FILTER_VALIDATE_FLOAT), 1];
    }

    public static function verifyIfArray($array)
    {
        $i = 0;

        foreach ($array AS $row) {
            $array[$i] = filter_var($array[$i], FILTER_SANITIZE_STRING);

            if ($array[$i] == '')
                unset($array[$i]);

            $i++;
        }

        if (!is_array($array))
            return ["Este campo está inválido!", 0];
        else
            return [$array, 1];
    }

    public static function verifyTextArea($text)
    {
        $text = Util::subChars($text, ' ', array(
            '<?', '<?php', '?php', '?>', 'OR 1 = 1', 'or 1 = 1', 'or 1=', "or 1=\\'1\'", '\\', 'drop', 'DROP', 'table', 'TABLE', 'alter', 'ALTER'
        ));

        if (empty($text))
            return ["Este campo não pode ser vazio!", 0];
        else
            return [$text, 1];
    }

    public static function checkUrlRepeated($urlName)
    {
        $urlName = filter_var($urlName, FILTER_SANITIZE_STRING);
        $urlName = Util::charsSpecial($urlName, '-', array(' ', '.', '..', '...', '_', '__', '___', '(', ')', '--', '---'));
        $urlName = strtolower(substr($urlName, 0, 100));

        $result = ProductDAO::findUrl($urlName);

        if (count($result) > 0) {
            $strFinalNumber = end(explode('-', $urlName));

            if (filter_var($strFinalNumber, FILTER_VALIDATE_INT)) {
                $arrayLessNumber = explode('-', $urlName, -1);
                $strFinal = implode("-", $arrayLessNumber);

                $urlName = $strFinal."-";
                $strFinalNumber++;
            } else {
                $strFinalNumber = 1;
                $urlName = $urlName."-";
            }

            do {
                $result = ProductDAO::findUrl($urlName.$strFinalNumber);

                if (count($result) > 0)
                    $strFinalNumber++;
            } while (count($result) > 0);

            $urlName = $urlName.$strFinalNumber;
        }

        return $urlName;
    }
}