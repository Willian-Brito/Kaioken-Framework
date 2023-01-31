<?php

namespace KaiokenFramework\Components\Dialog;

use KaiokenFramework\Components\Base\Element;

/**
 * Exibe mensagens ao usuário
 * @author Willian Brito (h1s0k4)
 */
class Message
{
    #region Construtor

    /**
     * Instancia a mensagem
     * @param $type      = tipo de mensagem (info, error)
     * @param $message = mensagem ao usuário
    */
    public function __construct($type, $message, $time = 3000, $IdFoco = "")
    {
        $div = new Element('div');

        $button = new Element('button');
        $button->class = "close-alert";

        $i = new Element('i'); 
        $i->class = "material-icons";
        $div->class = "toast material-alert {$type}";

        $span = new Element('span');
        $span->style = "margin-right: 10px;";
        $span->add($message);

        if ($type == 'info')
        {
            $i->add('info_outline');
        }
        else if ($type == 'error')
        {
            $i->add('error_outline');
        }
        else if($type == 'success')
        {
            $i->add('check');
        }
        else if($type == 'warning') 
        {
            $i->add('warning');
        }

        $button->add('x');
        $div->add($button);
        $div->add($i);
        $div->add($span);
        $div->show();

        $this->focus($IdFoco);
        $this->fecharMsg($time);
    }
    #endregion

    #region Metodos

    #region fecharMsg

    function fecharMsg($time)
    {
        echo "<script> setTimeout(function(){ CloseAlert(); $('.close-alert').trigger('click') }, {$time}); </script>";        
    }
    #endregion

    #region focus

    function focus($IdFoco)
    {
        echo "<script> 
                setTimeout(function(){ 
                    $('.close-alert').on('click', function(){ 
                        $('$IdFoco').focus(); 
                    })
                }, 100); 
              </script>";
    }
    #endregion

    #endregion
}