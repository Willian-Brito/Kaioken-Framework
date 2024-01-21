<?php

use KaiokenFramework\Database\Criteria;
use KaiokenFramework\Database\Record;
use KaiokenFramework\Database\Repository;
use KaiokenFramework\Session\Session;
use KaiokenFramework\Util\Util;

class Usuario extends Record
{
    #region Propriedades da Classe
    const TABLENAME = 'Usuario';
    private $perfil;
    #endregion

    #region Metodos
    
    #region get_perfil 
    public function get_perfil()
    {
        if (empty($this->perfil))
        {
            $this->perfil = new Perfil($this->IdPerfil);
        }
        
        return $this->perfil;
    }
    #endregion

    #region get_nome_perfil
    
    public function get_nome_perfil()
    {
        if (empty($this->perfil))
        {
            $this->perfil = new Perfil($this->IdPerfil);
        }
        
        return $this->perfil->Descricao;
    }
    #endregion

    #region get_eh_ativo
    
    public function get_eh_ativo()
    {
        $ehAtivo = $this->EhAtivo ? "Sim" : "Não";
        
        return $ehAtivo;
    }
    #endregion

    #region findByLogin
    public static function findByLogin($login)
    {
        $criteria = new Criteria;
        $criteria->add('Usuario', '=', $login);
        
        $repository = new Repository('Usuario');
        $users = $repository->load($criteria);

        if (count($users) > 0)
            return $users[0];
        
    }
    #endregion

    #region findByEmail
    public static function findByEmail($email)
    {
        $criteria = new Criteria;
        $criteria->add('Email', '=', $email);
        
        $repository = new Repository('Usuario');
        $users = $repository->load($criteria);

        if (count($users) > 0)
            return $users[0];
        
    }
    #endregion

    #region estaAtivo
    public static function estaAtivo($login)
    {
        $criteria = new Criteria;
        $criteria->add('Usuario', '=', $login);
        $criteria->add('EhAtivo', '=', 1);
        
        $repository = new Repository('Usuario');
        $users = $repository->load($criteria);

        if (count($users) > 0)
            return true;

        return false;        
    }
    #endregion
    
    #region validatePassword
    public function validatePassword($password)
    {
        return password_verify($password, $this->Senha);
    }
    #endregion

    #region emailJaExiste
    public static function emailJaExiste($email)
    {
        $usuarios = Usuario::all();

        foreach($usuarios as $usuario)
        {
            if ($usuario->Email == $email)
                return true;
        }

        return false;
    }
    #endregion

    #region usuarioJaExiste
    public static function usuarioJaExiste($usuarioForm)
    {
        $usuarios = Usuario::all();

        foreach($usuarios as $usuario)
        {
            if ($usuario->Usuario == $usuarioForm)
                return true;
        }

        return false;
    }
    #endregion

    #region checkPassword
    /**
     * Verifica força da senha.
    */
    public static function checkPassword($senha)
    {
        if(strlen($senha) < 8)
            throw new Exception('Senha deve conter 8 ou mais dígitos!');

        if( !(Util::contemNumeros($senha) && Util::contemLetras($senha)) )
            throw new Exception('Senha deve conter letras e números!');       
            
        if(!Util::temCaracterEspecial($senha))
            throw new Exception('Senha deve conter pelo menos 1 caracter especial!'); 
    }
    #endregion

    #region getFormulariosComPermissao
    private static function getFormulariosComPermissao()
    {
        if( Session::getValue('formulariosComPermissao') == null )
        {
            $IdUsuarioLogado = Session::getValue("IdUsuarioLogado");
            $usuario = new Usuario($IdUsuarioLogado);

            $criteria = new Criteria;
            $criteria->add('IdPerfil', '=', $usuario->IdPerfil);

            $repository = new Repository("PerfilFormulario");
            Session::setValue('formulariosComPermissao', $repository->load($criteria));

            return Session::getValue('formulariosComPermissao');
        }
        
        return Session::getValue('formulariosComPermissao');
    }
    #endregion

    #region temPermissao
    public static function temPermissao($IdFormulario)
    {
        Session::setValue('formulariosComPermissao', self::getFormulariosComPermissao());

        $form = new Formulario($IdFormulario);

        if($form->ArquivoFormulario == 'LoginForm')
            return true;

        foreach (Session::getValue('formulariosComPermissao') as $formulario)
        {
            if($formulario->IdFormulario == $IdFormulario)
                return true;
        }

        return false;
    }
    #endregion

    #region getIdUsuarioSuporte
    public static function getIdUsuarioSuporte()
    {
        $IdUsuarioSuporte = 1;
        return $IdUsuarioSuporte;
    }
    #endregion

    #endregion
}
