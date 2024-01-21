<?php

use KaiokenFramework\Database\Criteria;
use KaiokenFramework\Database\Record;
use KaiokenFramework\Database\Repository;

class Parametro extends Record
{
    #region Propriedades da Classe
    const TABLENAME = 'Parametro';

    private $tipoParametro;
    private $user;
    #endregion

    #region Metodos

    #region get_tipo_parametro
    
    public function get_tipo_parametro()
    {
        if (empty($this->tipoParametro))
        {
            $this->tipoParametro = new TipoParametro($this->IdTipoParametro);
        }
        
        return $this->tipoParametro->Descricao;
    }
    #endregion

    #region get_usuario
    
    public function get_usuario()
    {
        if (empty($this->user))
        {
            $this->user = new Usuario($this->IdUsuario);
        }
        
        return $this->user->Usuario;
    }
    #endregion

    #endregion
}