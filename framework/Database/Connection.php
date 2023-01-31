<?php

namespace KaiokenFramework\Database;

use PDO;
use Exception;

/**
 * Cria conexões com bancos de dados
 * @author Willian Brito (h1s0k4)
*/
final class Connection
{
    #region Construtor

    /**
     * Não podem existir instâncias de Connection
    */
    private function __construct() {}
    #endregion

    #region Metodos

    #region Publicos

    #region Open

    /**
     * Recebe o nome do conector de BD e instancia o objeto PDO
     */
    public static function open()
    {        
        $fileConfig = Configuration::getInstance();
        $conn = self::getConnection($fileConfig);
        
        // define para que o PDO lance exceções na ocorrência de erros
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $conn;
    }
    #endregion

    #endregion

    #region Privados

    #region getConnection
    private static function getConnection($fileConfig)
    {
        $type = isset($fileConfig['type']) ? $fileConfig['type'] : NULL;
        $conn = self::getDriver($type, $fileConfig);

        return $conn;
    }
    #endregion

    #region getDriver
    private static function getDriver($type, $fileConfig)
    {
        #region Variaveis Auxiliares

        // lê as informações contidas no arquivo
        $user = isset($fileConfig['user']) ? $fileConfig['user'] : NULL;
        $pass = isset($fileConfig['pass']) ? $fileConfig['pass'] : NULL;
        $name = isset($fileConfig['name']) ? $fileConfig['name'] : NULL;
        $host = isset($fileConfig['host']) ? $fileConfig['host'] : NULL;
        $port = isset($fileConfig['port']) ? $fileConfig['port'] : NULL;        
        #endregion

        #region Drivers

        // descobre qual o tipo (driver) de banco de dados a ser utilizado
        switch ($type)
        {
            #region PostgreSQL
            case 'pgsql':
                $port = $port ? $port : '5432';
                $conn = new PDO("pgsql:dbname={$name}; user={$user}; password={$pass};
                        host=$host;port={$port}");
                break;
            #endregion

            #region MySQL
            case 'mysql':
                $port = $port ? $port : '3306';
                $conn = new PDO("mysql:host={$host};port={$port};dbname={$name}", $user, $pass);
                break;
            #endregion

            #region SQLite
            case 'sqlite':
                $conn = new PDO("sqlite:{$name}");
                $conn->query('PRAGMA foreign_keys = ON');
                break;
            #endregion

            #region PostgreSQL
            case 'ibase':
                $conn = new PDO("firebird:dbname={$name}", $user, $pass);
                break;
            #endregion

            #region Oracle
            case 'oci8':
                $conn = new PDO("oci:dbname={$name}", $user, $pass);
                break;
            #endregion

            #region Microsoft SQL Server
            case 'mssql':
                $conn = new PDO("dblib:host={$host}:{$port};dbname={$name}", $user, $pass);
                break;
            #endregion
        }

        return $conn;
        
        #endregion
    }
    #endregion

    #endregion

    #endregion
}
