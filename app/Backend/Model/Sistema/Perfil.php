<?php

use KaiokenFramework\Database\Criteria;
use KaiokenFramework\Database\Record;
use KaiokenFramework\Database\Repository;

class Perfil extends Record
{
    const TABLENAME = 'Perfil';

    #region Metodos

    #region get_perfil
    public function get_perfil() 
    {
        if (empty($this->perfil))
        {

            $this->perfil = new Perfil($this->Idperfil);
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

    #region get_nome_usuarios
    public function get_nome_usuarios()
    {
        $nomeUsuarios = "";
        $criteria = new Criteria;    
        $criteria->add("IdPerfil", "=", $this->IdPerfil);

        $repository = new Repository('Usuario');
        $usuarios = $repository->load($criteria);

        foreach($usuarios as $usuario)
        {
            $nomeUsuarios .= "[{$usuario->Usuario}] ";
        }

        return $nomeUsuarios;
    }
    #endregion

    #region deletarTodosFormularios
    /**
     * Exclui todos os formularios do perfil
    */
    public function deletarTodosFormularios()
    {
	    $criteria = new Criteria;
	    $criteria->add('IdPerfil', '=', $this->IdPerfil);
	    
	    $repo = new Repository('PerfilFormulario');
	    return $repo->delete($criteria);
    }
    #endregion

    #region addFormulario
    /**
     * Adiciona formulario no perfil
    */
    public function addFormulario(Formulario $formulario)
    {
        $perfilFormulario = new PerfilFormulario;
        $perfilFormulario->IdPerfil = $this->IdPerfil;
        $perfilFormulario->IdFormulario = $formulario->IdFormulario;
        $perfilFormulario->save();
    }
    #endregion

    #region TemPerfil
    public static function TemPerfil($descricao)
    {
        $criteria = new Criteria;
	    $criteria->add('Descricao', '=', $descricao);

        $repo = new Repository('Perfil');
        $result = $repo->load($criteria);

        return count($result) > 0;
    }
    #endregion

    #region getIdPerfilSuporte
    public static function getIdPerfilSuporte()
    {
        $IdPerfilSuporte = 1;
        return $IdPerfilSuporte;
    }
    #endregion

    #endregion
}
