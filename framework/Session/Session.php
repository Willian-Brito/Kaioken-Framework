<?php

namespace KaiokenFramework\Session;

/**
 * Gerencia o registro da seção
 * @author Willian Brito (h1s0k4)
*/
class Session
{
    #region Construtor

    /**
     * Não podem existir instâncias de Connection
    */
    private function __construct() {}
    #endregion

    #region Metodos

    #region start
    
    /**
     * inicializa uma seção
    */
    public static function start()
    {
        if (!session_id())
        {
            session_start();
        }
    }
    #endregion

    #region regenerate
    /**
     * Apaga a sessão antiga e gera uma nova.
     * OBS: É uma boa pratica de segurança regerar o ID da sessão no login e no logout, 
     * para evitar o ataque de sequestro de sessão.
    */
    public static function regenerate()
    {
        session_regenerate_id(true);
    }
    #endregion

    #region setValue

    /**
     * Armazena uma variável na seção
     * @param $var   = Nome da variável
     * @param $value = Valor
    */
    public static function setValue($var, $value)
    {
        $_SESSION[$var] = $value;
    }
    #endregion

    #region getValue

    /**
     * Retorna uma variável da seção
     * @param $var = Nome da variável
    */
    public static function getValue($var)
    {
        if (isset($_SESSION[$var]))
        {
            return $_SESSION[$var];
        }
    }
    #endregion

    #region delValue
    /**
     * Deleta uma variável da seção
     * @param $var = Nome da variável
    */
    public static function delValue($var)
    {
        if (isset($_SESSION[$var]))
        {
            unset($_SESSION[$var]);
        }
    }
    #endregion

    #region freeSession

    /**
     * Destrói os dados de uma seção
    */
    public static function freeSession()
    {
        $_SESSION = array();
        session_destroy();
    }
    #endregion

    #endregion
}
