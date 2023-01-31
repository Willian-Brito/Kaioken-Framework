<?php

namespace KaiokenFramework\Database;

use KaiokenFramework\Log\Logger;

/**
 * Fornece os métodos necessários manipular transações
 * @author Willian Brito (h1s0k4)
 */

final class Transaction
{
    #region Propiedades da Classe

    private static $conn;   // conexão ativa
    private static $logger; // objeto de LOG
    #endregion
    
    #region Construtor

    /**
     * Private para impedir que se crie instâncias de Transaction
    */
    private function __construct() {}
    #endregion

    #region Metodos

    #region Open

    /**
     * Abre uma transação e uma conexão ao BD
    */
    public static function open()
    {
        //abre uma conexão e armazena na propriedade estática $conn
        $NaoTemConexao = empty(self::$conn);

        if ($NaoTemConexao)
        {            
            self::$conn = Connection::open();
            
            // inicia a transação
            self::$conn->beginTransaction();

            // desliga o log de SQL
            self::$logger = NULL;
        }
    }
    #endregion

    #region Commit

    /**
     * Aplica todas operações realizadas e fecha a transação
    */
    public static function close()
    {
        if (self::$conn)
        {
            self::$conn->commit();
            self::$conn = null;
        }
    }
    #endregion

    #region Rollback

    /**
     * Desfaz todas operações realizadas na transação
    */
    public static function rollback()
    {
        if (self::$conn)
        {
            self::$conn->rollback();
            self::$conn = null;
        }
    }
    #endregion

    #region GetTransaction

    /**
     * Retorna a conexão ativa da transação
    */
    public static function get()
    {
        $conn = self::$conn ? self::$conn : Connection::open();
        return $conn;
    }
    #endregion

    #region SetLogger

    /**
     * Define qual estratégia (algoritmo de LOG será usado)
    */
    public static function setLogger(Logger $logger)
    {
        self::$logger = $logger;
    }
    #endregion

    #region Log

    /**
     * Armazena uma mensagem no arquivo de LOG conforme o logger atual
    */
    public static function log($message)
    {
        if (self::$logger)
        {
            self::$logger->write($message);
        }
    }
    #endregion

    #endregion
}
