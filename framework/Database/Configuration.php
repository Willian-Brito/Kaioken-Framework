<?php

namespace KaiokenFramework\Database;

use Exception;
use KaiokenFramework\Security\Cryptography;
use KaiokenFramework\Session\Session;

/**
 * Lê arquivo de configurações para conexão com banco de dados
 * @author Willian Brito (h1s0k4)
*/
class Configuration
{
    #region Propriedades da Classe

    const ARQUIVO_CONFIG = "Backend/Config/Msystem.ini";
    #endregion

    #region Construtor

    /**
     * Private para impedir que se crie instâncias de Configuration
    */
    private function __construct() {}
    #endregion

    #region Metodos

    #region [+] Public

    #region getInstance
    public static function getInstance()
    { 
        $EhPrimeiraVez = empty(Session::getValue("Database"));
        
        if($EhPrimeiraVez)
        {
            Session::setValue("Database", self::open());
            return Session::getValue("Database");
        }

        return Session::getValue("Database");
    }
    #endregion

    #region readFile
    public static function readFile()
    {
        return parse_ini_file(self::ARQUIVO_CONFIG);
    }
    #endregion

    #endregion

    #region [-] Private

    #region open
    private static function open()
    {
        $ExisteArquivoConfiguracao = file_exists(self::ARQUIVO_CONFIG);

        if ($ExisteArquivoConfiguracao)
        {
            // lê o INI e retorna um array
            $fileConfig = file_get_contents(self::ARQUIVO_CONFIG);
            $stringParaArray = explode(', ', $fileConfig);
        }
        else
        {
            throw new Exception("Arquivo '". self::ARQUIVO_CONFIG ."' não encontrado");
        }

        $configDecryt = Cryptography::decryptFile($stringParaArray);

        return $configDecryt;
    }
    #endregion

    #region getIndex
    public static function getIndex($index)
    {
        switch($index)
        {
            #region host
            case 0:
                return 'host';
            #endregion
            
            #region name
            case 1:
                return 'name';
            #endregion

            #region user
            case 2:
                return 'user';
            #endregion

            #region pass
            case 3:
                return 'pass';
            #endregion

            #region port
            case 4:
                return 'port';
            #endregion

            #region type
            case 5:
                return 'type';
            #endregion        
        }
    }
    #endregion

    #endregion

    #endregion
}