<?php

namespace KaiokenFramework\Util;

use Exception;
use KaiokenFramework\Enum\PageStatusEnum;
use KaiokenFramework\Session\Session;

class Util 
{
    #region Metodos

    #region validaData
    public static function validaData($date) 
    {
        $data = explode("/","$date");
        $d = $data[0];
        $m = $data[1];
        $y = $data[2];

        $res = checkdate($m,$d,$y);
        if ($res == 1)
            return true;
        else
            return false;
    }
    #endregion

    #region limpaFormatacao
    public static function limpaFormatacao($campo)
    {
        try
        {
            $campo = str_replace(".", "", $campo);
            $campo = str_replace(",", "", $campo);
            $campo = str_replace("-", "", $campo);
            $campo = str_replace("/", "", $campo);
            $campo = str_replace("_", "", $campo);
            $campo = str_replace("(", "", $campo);
            $campo = str_replace(")", "", $campo);
            $campo = str_replace(" ", "", $campo);            
        }
        catch (Exception $e)
        {
            $campo = "";
        }

        return $campo;
    }
    #endregion 

    #region validaEmail
    public static function validaEmail($email)
    {
        if(filter_var($email, FILTER_VALIDATE_EMAIL))
            return true;        

        return false;
    }
    // public static function ValidaEmail($email)
    // {
    //     if(!preg_match('/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/', $email))
    //     {
    //         return true;
    //     }
    //     return false;
    // }
    #endregion

    #region validaCelular
    public static function validaCelular($celular)
    {
        $CelularSemFormatacao = self::limpaFormatacao($celular);

        if(strlen($CelularSemFormatacao) == 11)
            return true;
        
        return false;
    }
    #endregion

    #region validaCPF
    public static function validaCPF($cpf) 
    {
 
        // Extrai somente os números
        $cpf = preg_replace( '/[^0-9]/is', '', $cpf );
         
        // Verifica se foi informado todos os digitos corretamente
        if (strlen($cpf) != 11) {
            return false;
        }
    
        // Verifica se foi informada uma sequência de digitos repetidos. Ex: 111.111.111-11
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }
    
        // Faz o calculo para validar o CPF
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }

        return true;    
    }
    #endregion

    #region validaCNPJ
    public static function validaCNPJ($cnpj)
    {
        $cnpj = preg_replace('/[^0-9]/', '', (string) $cnpj);
        
        // Valida tamanho
        if (strlen($cnpj) != 14)
            return false;

        // Verifica se todos os digitos são iguais
        if (preg_match('/(\d)\1{13}/', $cnpj))
            return false;	

        // Valida primeiro dígito verificador
        for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++)
        {
            $soma += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }

        $resto = $soma % 11;

        if ($cnpj[12] != ($resto < 2 ? 0 : 11 - $resto))
            return false;

        // Valida segundo dígito verificador
        for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++)
        {
            $soma += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }

        $resto = $soma % 11;

        return $cnpj[13] == ($resto < 2 ? 0 : 11 - $resto);
    }
    #endregion

    #region temCaracterEspecial
    public static function temCaracterEspecial($campo)
    {   
        $campoSemAcentos = self::removerAcentos($campo);

        if(preg_match('/[\'£$%&*}{#?><>|=+¬]/', $campoSemAcentos))
            return true;

        return false;
    }
    #endregion 

    #region removerAcentos
    public static function removerAcentos($string)
    {
        $stringSemAcentos = preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/",
        "/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/",
        "/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/"),
        explode(" ","a A e E i I o O u U n N"),$string);

        return $stringSemAcentos;
    } 
    #endregion

    #region contemNumeros
    public static function contemNumeros($string) : bool
    {
        $naoTemNumero = filter_var($string, FILTER_SANITIZE_NUMBER_INT) === '';

        if($naoTemNumero)
            return false;
         
        return true;
    }
    #endregion

    #region contemLetras
    public static function contemLetras($string) : bool
    {
        $campoSemAcentos = self::removerAcentos($string);

        if(preg_match('/[a-zA-Z]/', $campoSemAcentos))
            return true;

        return false;
    }
    #endregion

    #region getBoolean
    public static function getBoolean($value)
    {
        if(!empty($value))
        {
            $isTrue = ['sim', '1', 'true'];

            for($i = 0; $i < count($isTrue); $i++)
            {
                $temPalavra = str_contains(strtolower($value), $isTrue[$i]);

                if($temPalavra)
                    return true;
            }
        }

        return false;
    }
    #endregion

    #region formataTelefone
    public static function formataTelefone($telefone)
    {

        if(!empty($telefone))
        {
            $tam = strlen(preg_replace("/[^0-9]/", "", self::limpaFormatacao($telefone)));
            
            if ($tam == 13) {
                // COM CÓDIGO DE ÁREA NACIONAL E DO PAIS e 9 dígitos
                return "+".substr($telefone, 0, $tam-11)." (".substr($telefone, $tam-11, 2).") ".substr($telefone, $tam-9, 5)."-".substr($telefone, -4);
            }
            if ($tam == 12) {
                // COM CÓDIGO DE ÁREA NACIONAL E DO PAIS
                return "+".substr($telefone, 0, $tam-10)." (".substr($telefone, $tam-10, 2).") ".substr($telefone, $tam-8, 4)."-".substr($telefone, -4);
            }
            if ($tam == 11) {
                // COM CÓDIGO DE ÁREA NACIONAL e 9 dígitos
                return " (".substr($telefone, 0, 2).") ".substr($telefone, 2, 5)."-".substr($telefone, 7, 11);
            }
            if ($tam == 10) {
                // COM CÓDIGO DE ÁREA NACIONAL
                return " (".substr($telefone, 0, 2).") ".substr($telefone, 2, 4)."-".substr($telefone, 6, 10);
            }
            if ($tam <= 9) {
                // SEM CÓDIGO DE ÁREA
                return substr($telefone, 0, $tam-4)."-".substr($telefone, -4);
            }
        }
    }
    #endregion

    #region formataCPF
    public static function formataCPF($cpf)
    {
        $cpf = self::limpaFormatacao($cpf);

        $cpf = substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);

        return $cpf;
    }
    #endregion

    #region formataCNPJ
    public static function formataCNPJ($cnpj)
    {
        $cnpj = self::limpaFormatacao($cnpj);

        $cnpj = substr($cnpj, 0, 2) . '.' . substr($cnpj, 2, 3) . '.' . substr($cnpj, 5, 3) . '/' . substr($cnpj, 8, 4) . '-' . substr($cnpj, 12, 2);

        return $cnpj;
    }
    #endregion

    #region formataRG
    public static function formataRG($rg)
    {
        $rg = self::limpaFormatacao($rg);

        $rg = substr($rg, 0, 2) . '.' . substr($rg, 2, 3) . '.' . substr($rg, 5, 3);

        return $rg;
    }
    #endregion

    #region dataUSA
    public static function dataUSA($data)
    {
        if( trim($data) == '' )  
            return '';

        // 25/02/1992
        return 	substr($data,6,4) . '/' . 
                substr($data,3,2) . '/' . 
                substr($data,0,2) ;
    }
    #endregion

    #region dataBR
    public static function dataBR($data)
    {
        if( trim($data) == '' ) 
            return '';

        // 1992/02/25
        return 	substr($data,8,2) . '/' . 
                substr($data,5,2) . '/' . 
                substr($data,0,4) ;
    }
    #endregion

    #region isDate
    public static function isDate($date, $format = "dd-mm-yyyy")
    {
        if($format === "dd-mm-yyyy")
        {
            $day = (int) substr($date,0,2);
            $month = (int) substr($date, 3,2);
            $year = (int) substr($date, 6,4);
        }
        else if($format === "yyyy-mm-dd")
        {
            $day = (int) substr($date,8,2);
            $month = (int) substr($date, 5,2);
            $year = (int) substr($date, 0,4);
        }

        return checkdate($month, $day, $year);
    }
    #endregion
    
    #region ehStatusNovo
    public static function ehStatusNovo()
    {
        $ehNovo = Session::getValue('PageStatus') == PageStatusEnum::Novo->value;
        return $ehNovo;
    }
    #endregion

    #endregion
}
