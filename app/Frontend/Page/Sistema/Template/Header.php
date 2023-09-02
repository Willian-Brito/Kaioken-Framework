<?php

#region Imports

use KaiokenFramework\Components\Base\Element;

use KaiokenFramework\Database\Record;
use KaiokenFramework\Session\Session;

#endregion

class Header extends Record
{    
    #region Metodos

    #region loadHeader
    public static function loadHeader($usuario)
    {
        $header = new Element('div');
        $header->class = "cabecalho";

        $element = self::getElementsHeader($usuario);
        $header->add($element);

        return $header;
    }
    #endregion

    #region getElementsHeader
    private static function getElementsHeader($usuario) 
    {
        $elements = new Element('div');
        $elements->class = "top";

        $button = self::getButton();
        $theme = self::getToggleTheme();
        $foto = self::getFotoPerfil($usuario);

        $elements->add($button);
        $elements->add($theme);
        $elements->add($foto);

        return $elements;
    }
    #endregion

    #region getButton
    private static function getButton()
    {
        $btn = new Element('button');
        $btn->id = "btn-menu";

        $span = new Element('span');
        $span->class = "material-icons-sharp";
        $span->add("menu");

        $btn->add($span);

        return $btn;
    }
    #endregion

    #region getToggleTheme
    private static function getToggleTheme()
    {
        $theme = new Element('div');
        $theme->class = "trocar-tema";

        $light = new Element('span');
        $light->class = "material-icons-sharp ativo";
        $light->add("light_mode");

        $dark = new Element('span');
        $dark->class = "material-icons-sharp";
        $dark->add("dark_mode");

        $theme->add($light);
        $theme->add($dark);

        return $theme;
    }
    #endregion

    #region getFotoPerfil
    private static function getFotoPerfil($usuario)
    {
        $div = new Element('div');
        $div->class = "perfil";

        #region Info
        $info = new Element('div');
        $info->class = "info";

        $paragrafo = new Element('p');
        
        $negrito = new Element('b');
        $negrito->id = "idUser";
        $negrito->add("$usuario->Usuario");

        $paragrafo->add("Olá $negrito");

        $perfil = new Element('small');
        $perfil->id = "idPerfil";
        $perfil->class = "text-muted";

        $descricaoPerfil = new Perfil($usuario->IdPerfil);

        $perfil->add($descricaoPerfil->Descricao);
        
        $info->add($paragrafo);
        $info->add($perfil);
        #endregion

        #region Foto
        $foto = new Element('div');
        $foto->class = "foto-perfil";

        $fotoPerfil = !empty($usuario->FotoPerfil) ? $usuario->FotoPerfil : "Frontend/assets/img/icon/avatar.svg";

        $img = new Element('img');
        $img->src = "$fotoPerfil";
        $img->alt = "foto de perfil";

        $foto->add($img);
        #endregion

        #region Dropdown

        $IdUsuarioLogado = Session::getValue("IdUsuarioLogado");

        $dropdown = new Element('div');
        $dropdown->class = "dropdown-user";
        $dropdown->style = "display: none";

        $ul = new Element('ul');
        
        #region Meu Perfil

        $link = "index.php?class=UsuarioForm&method=onEdit&key=$IdUsuarioLogado&IdUsuario=$IdUsuarioLogado";
        $icon = "fa-solid fa-user";
        $descricao = "Meu Perfil";

        $meuPerfil = self::getLinkUsuario($link, $icon, $descricao);
        #endregion

        #region Alterar Senha
        
        $link = "index.php?class=AlterarSenhaForm&method=onEdit&IdUsuario=$IdUsuarioLogado";
        $icon = "fa-solid fa-pen-to-square";
        $descricao = "Alterar Senha";

        $alterarSenha = self::getLinkUsuario($link, $icon, $descricao);
        #endregion

        #region Sair
        
        $link = "index.php?class=LoginForm&method=onLogout";
        $icon = "fa-solid fa-right-from-bracket";
        $descricao = "Sair";

        $sair = self::getLinkUsuario($link, $icon, $descricao);
        #endregion

        $ul->add($meuPerfil);
        $ul->add($alterarSenha);
        $ul->add($sair);

        $dropdown->add($ul);

        #endregion

        $div->add($info);
        $div->add($foto);
        $div->add($dropdown);

        return $div;
    }
    #endregion

    #region getLinkUsuario
    private static function getLinkUsuario($link, $icon, $descricao)
    {
        $li = new Element('li');

        $a = new Element('a');
        $a->href = $link;

        $i = new Element('i');
        $i->class = $icon;
        $i->add("");

        $span = new Element('span');
        $span->add($descricao);

        $a->add($i);
        $a->add($span);

        $li->add($a);

        return $li;
    }
    #endregion

    #endregion
}
