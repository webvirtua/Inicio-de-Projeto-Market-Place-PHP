<?php
namespace App\util;

use App\Models\User;

/** <b>Util</b>
 *Classe com Funcionalidades extras
 * @version 1.0
 * @autor Luiz Rodrigues <atende@webvirtua.com.br>
 * @copyrigth © 2018, Luiz Rodrigues Web Virtua
 * @since 1.0
 * @package App\util
 */
class Util
{
    public static function passwordHash($password)
    {
        $passwordHash = password_hash($password, PASSWORD_BCRYPT, [
            "cost" => 10
        ]);

        return $passwordHash;
    }

    public static function encodeIdOnArray($array)
    {
        $newArray = array();

        $count = 0;
        $key = array_keys($array);
        foreach($key as $value) {
            $val = $array[$value];

            if(is_array($val)) {
                foreach($val as $keyIn => $valueIn) {
                    $count++;
                    $keyName = strtolower($keyIn);

                    $arrayKey[$count] = $keyName;
                    $arrayKeys[] = $arrayKey[$count];
                }
            }
            break;
        }
        $arrayKeys[] = 'code';

        foreach ($array as $row) {
            $idEncode = Util::idEncode($row[$arrayKey[1]]);

            for ($i = 0; $i < $count; $i++) {
                $arrayValues[] = $row[$arrayKey[$i+1]];
            }

            $arrayValues[] = $idEncode;
            $arrayTemp = array_combine($arrayKeys, $arrayValues);
            $arrayValues = null;

            array_push($newArray, $arrayTemp);
        }

        return $newArray;
    }

    public static function verifyGetPostCode($code)
    {
        $code = filter_var($code, FILTER_SANITIZE_STRING);

        if (!filter_var($code, FILTER_VALIDATE_INT)) {
            $decode = Util::idDecode($code);

            if (filter_var($decode, FILTER_VALIDATE_INT))
                return $decode;
        }

        return null;
    }

    public static function charsSpecial($string, $subStr, $array)
    {
        return str_replace($array, $subStr, preg_replace("/&([a-z])[a-z]+;/i", "$1", htmlentities(trim($string))));
    }

    public static function subChars($string, $subStr, $array)
    {
        return str_replace($array, $subStr, $string);
    }
}
