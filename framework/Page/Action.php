<?php

namespace KaiokenFramework\Page;

/**
 * Encapsula uma ação
 * @author Willian Brito (h1s0k4)
 */
class Action implements IAction
{
    #region Propriedades da Classe

    private $action;
    private $param;
    #endregion
    
    #region Construtor

    /**
     * Instancia uma nova ação
     * @param $action = método a ser executado ou [$object, $method]
    */
    public function __construct(Callable $action)
    {
        $this->action = $action;
    }
    #endregion

    #region Metodos

    #region setParameter
    /**
     * Acrescenta um parâmetro ao método a ser executado
     * @param $param = nome do parâmetro
     * @param $value = valor do parâmetro
    */
    public function setParameter($param, $value)
    {
        $this->param[$param] = $value;
    }
    #endregion

    #region serialize

    /**
     * Transforma a ação em uma string do tipo URL
    */
    public function serialize()
    {
        // verifica se a ação é um método
        if (is_array($this->action))
        {
            // obtém o nome da classe
            $url['class'] = is_object($this->action[0]) ? get_class($this->action[0]) : $this->action[0];
            // obtém o nome do método
            $url['method'] = $this->action[1];
            
            // verifica se há parâmetros
            if ($this->param)
            {
                $url = array_merge($url, $this->param);
            }

            // monta a URL
            return '?' . http_build_query($url);
        }
    }
    #endregion

    #endregion
}
