<?php

#region Imports

namespace KaiokenFramework\Page;

use KaiokenFramework\Components\Base\Element;
use KaiokenFramework\Security\Token;
#endregion

/**
 * Page controller
 * @author Willian Brito (h1s0k4)
*/
class Page extends Element
{

    #region Construtor

    /**
     * Método construtor
    */
    public function __construct()
    {
        parent::__construct('div');
    }
    #endregion
    
    #region Metodos

    #region show

    /**
     * Executa determinado método de acordo com os parâmetros recebidos
    */
    public function show()
    {
        if ($_GET)
        {
            $class  = isset($_GET['class'])  ? htmlspecialchars($_GET['class'], double_encode:false) : '';
            $method = isset($_GET['method']) ? htmlspecialchars($_GET['method'], double_encode:false) : '';

            #region Validação
            if($method == 'onSave')
                Token::validateTokenCSRF();            
            #endregion

            #region Pages 

            if ($_GET)
            {   
                if ($class)
                {
                    $object = ($class == get_class($this)) ? $this : new $class;

                    if (is_callable(array($object, $method) ) )
                    {
                        call_user_func(array($object, $method), $_REQUEST);
                    }
                }
                else if (function_exists($method))
                {
                    call_user_func($method, $_REQUEST);
                }
            }
            #endregion
        }
        
        parent::show();
    }
    #endregion

    #endregion

    #endregion
}
