<?php

#region Imports

use KaiokenFramework\Components\Base\Element;

use KaiokenFramework\Database\Criteria;
use KaiokenFramework\Database\Record;
use KaiokenFramework\Database\Repository;
use KaiokenFramework\Util\Util;
#endregion

class Sidebar extends Record
{
    #region Propriedades da Classe
    const TABLENAME = 'Menu';
    #endregion
    
    #region Metodos

    #region loadMenu
    public static function loadMenu()
    {
        $barraLateral = new Element('ul');
        $barraLateral->class =  "nav-links";

        $todosMenusPrincipais = self::loadMenuPrincipal();

        foreach($todosMenusPrincipais as $menu)
        {
            $li = new Element('li');
            $descricaoMenu = str_replace("ç", "c", Util::removerAcentos($menu->Descricao));
            $li->id = $descricaoMenu;

            $li->add( self::createMenu($menu) );
            $subMenus = self::getAllSubMenu($menu->IdMenu);

            if(count($subMenus) > 0)
                $li->add( self::createSubMenu($menu, $subMenus) );  

            $barraLateral->add($li);
        }

        $logoff = self::getMenuLogoff();
        $barraLateral->add($logoff);

        return $barraLateral;
    }
    #endregion

    #region createMenu
    private static function createMenu($menu)
    {                   
        $div = new Element('div');
        $div->class = "icon-link";

        $link = new Element('a');
        $link->class = "nav_link";

        if($menu->Descricao == "Dashboard")
            $link->href = "index.php?class=CidadeList";

        $span = new Element('span');
        $span->class = "material-icons-sharp";
        $span->add( self::getIconMenu($menu->Descricao) );

        $h3 = new Element('h3');
        $h3->add($menu->Descricao);
        
        $link->add($span);
        $link->add($h3);
        $div->add($link);

        if($menu->Descricao != "Dashboard")
        {
            if(self::temSubMenu($menu))
            {
                $i = new Element('i');
                $i->class = "bx bxs-chevron-down arrow";
    
                if($menu->Descricao == "Configurações")
                    $i->class .= " config";
    
                $i->add("");
                $div->add($i);
            }
            else 
            {
                $emBreve = new Element('div');
                $emBreve->class = "em-breve";
                $emBreve->add("Em Breve");
    
                $link->add($emBreve);
            }
        }

        return $div;
    }
    #endregion

    #region createSubMenu
    private static function createSubMenu($menu, $subMenus)
    {
        $ul = new Element('ul');
        $ul->class = "sub-menu";

        $p = new Element('p');
        $p->class = "link_name";
        $p->href = "#";
        $p->add($menu->Descricao);

        $ul->add($p);

        // IMPLEMENTAR RECURSÃO
        foreach($subMenus as $subMenu)
        {
            $formulario = new Formulario($subMenu->IdFormulario);

            if(Usuario::temPermissao($formulario->IdFormulario))
            {
                $li = new Element('li');
                
                $link = new Element('a');
                $link->href = "index.php?class=$formulario->ArquivoLista";
                $link->add($subMenu->Descricao);
    
                $li->add($link);
                $ul->add($li);
            }
        }

        return $ul;
    }
    #endregion

    #region getMenuLogoff
    private static function getMenuLogoff()
    {
        $logoff = new Element('li');
        $logoff->class = "logoff";

        $link = new Element('a');
        $link->href = "index.php?class=LoginForm&method=onLogout";

        $span = new Element('span');
        $span->class = "material-icons-sharp";
        $span->add("logout");

        $h3 = new Element('h3');
        $h3->add("Sair");

        $link->add($span);
        $link->add($h3);

        $logoff->add($link);

        return $logoff;
    }
    #endregion

    #region getIconMenu
    private static function getIconMenu($menu)
    {
        switch($menu)
        {
            #region Dashboard
            case "Dashboard":
                return "dashboard";
            #endregion

            #region Empresas
            case "Empresas":
                return "account_balance";
            #endregion

            #region Cadastros
            case "Cadastros":
                return "person_add";
            #endregion

            #region Consultas
            case "Consultas":
                return "inventory";
            #endregion

            #region Relatórios
            case "Relatórios":
                return "receipt_long";
            #endregion

            #region Configurações
            case "Configurações":
                return "settings";
            #endregion
        }
    }
    #endregion

    #region temSubMenu
    private static function temSubMenu($menu)
    {
        $criteria = new Criteria;
    
        $criteria->add("IdMenuPai", "=", $menu->IdMenu);
        $repository = new Repository('Menu');
        $subMenu = $repository->load($criteria);

        if(count($subMenu) > 0)
            return true;

        return false;
    }
    #endregion

    #region loadMenuPrincipal
    private static function loadMenuPrincipal()
    {
        $criteria = new Criteria;
    
        $criteria->add("EhPrincipal", "=", 1);
        $repository = new Repository('Menu');
        $menu = $repository->load($criteria);

        return $menu;
    }
    #endregion

    #region getAllSubMenu
    private static function getAllSubMenu($IdMenu)
    {
        $criteria = new Criteria;

        $criteria->add("IdMenuPai", "=", $IdMenu);

        $repository = new Repository('Menu');
        $subMenu = $repository->load($criteria);

        return $subMenu;
    }
    #endregion

    #endregion
}
