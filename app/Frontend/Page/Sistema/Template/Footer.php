<?php

#region Imports

use KaiokenFramework\Components\Base\Element;

use KaiokenFramework\Database\Criteria;
use KaiokenFramework\Database\Record;
use KaiokenFramework\Database\Repository;
use KaiokenFramework\Database\Transaction;
#endregion

class Footer extends Record
{    
    #region Metodos

    #region loadFooter
    public static function loadFooter()
    {
        $footer = new Element('div');
        $footer->class = "footer";

        $span = new Element('span');

        $h3 = new Element('h3');
        $h3->add("Desenvolvido com");

        $coracao = new Element('span');
        $coracao->class = "material-icons-sharp red";
        $coracao->add("favorite");

        $h3->add($coracao);
        $h3->add("por ");

        #region Criando nome da empresa [M sys tem]

        $m = new Element('span');
        $m->class = "msystem-rodape-color-primary";
        $m->add("M");

        $sys = new Element('span');
        $sys->class = "msystem-rodape-color-secondary";
        $sys->add("sys");

        $tem = new Element('span');
        $tem->class = "msystem-rodape-color-primary";
        $tem->add("tem");
        #endregion

        $msystem = trim("$m $sys $tem");

        $h3->add($msystem);
        $span->add($h3);
        $footer->add($span);

        return $footer;
    }
    #endregion

    #endregion
}
