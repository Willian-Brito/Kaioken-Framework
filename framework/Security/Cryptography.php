<?php

namespace KaiokenFramework\Security;

use KaiokenFramework\Database\Configuration;

/**
 * Criptografa e descriptografa arquivo de configurações para a conexão com banco de dados
 * @author Willian Brito (h1s0k4)
*/
final class Cryptography
{
    #region Construtor

    /**
     * Private para impedir que se crie instâncias de Cryptography
    */
    private function __construct() {}
    #endregion

    #region Metodos

    #region decryptFile

    /**
     * Descriptografa arquivo de configurações.
     * @param $fileConfig = array dos dados criptografados do arquivo de condigurações
    */
    public static function decryptFile($fileConfig)
    {   
        $chave = self::createKey();
        $amostraDadosCriptografado = self::hexToBin($fileConfig[0]);
        $iv =  self::getIV($amostraDadosCriptografado);

        for($i = 0; $i < count($fileConfig); $i++)
        {
            $dados = self::hexToBin($fileConfig[$i]);
            $cifra = self::getCipher($dados);

            $index = Configuration::getIndex($i);
            $fileConfigDecrypt[$index] = self::decrypt($cifra, $iv, $chave);
        }

        return $fileConfigDecrypt;
    }
    #endregion

    #region decrypt

    /**
     * Descriptografa Informações.
     * @param $cifra = cifra dos dados criptografados
     * @param $iv = vetor de inicialização dos dados criptografados
     * @param $key = chave dos dados criptografados
    */
    public static function decrypt($cifra, $iv, $key)
    {
        $dados = sodium_crypto_secretbox_open($cifra, $iv, $key);
        return $dados;
    }
    #endregion

    #region encrypt
    /**
     * Criptografando Informações.
     * @param $dados = dados que serão criptografados
     * @param $iv = vetor de inicialização para criptografar
     * @param $key = chave dos dados para criptografar
    */
    public static function encrypt($dados, $iv, $key)
    {
        $dadosCriptografado = sodium_bin2hex($iv . sodium_crypto_secretbox($dados, $iv, $key));
        return $dadosCriptografado;
    }
    #endregion

    #region createKey

    /**
     * Cria chave de criptografia
    */
    public static function createKey()
    {
        $Key = __DIR__ . DIRECTORY_SEPARATOR . 'chave.key';

        if(!file_exists($Key))
            file_put_contents($Key, sodium_crypto_secretbox_keygen());
    
        $chave = file_get_contents($Key);

        return $chave;
    }
    #endregion

    #region createIV

    /**
     * Cria vetor de inicialização (IV)
    */
    public static function createIV()
    {
        $iv = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        return $iv;
    }
    #endregion

    #region getIV

    /**
     * Pegar vetor de inicialização (IV)
    */
    public static function getIV($dados)
    {
        $iv =  substr($dados, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        return $iv;
    }
    #endregion

    #region hexToBin
    public static function hexToBin($dados)
    {
        $bin = sodium_hex2bin($dados);
        return $bin;
    }
    #endregion

    #region getCipher
    public static function getCipher($dados)
    {
        $cifra = substr($dados, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        return $cifra;
    }
    #endregion

    #endregion
}