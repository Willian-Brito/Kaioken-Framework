<?php

use KaiokenFramework\Database\Criteria;
use KaiokenFramework\Database\Record;
use KaiokenFramework\Database\Repository;
use KaiokenFramework\Database\Transaction;

class PerfilFormulario extends Record
{
    #region Propriedades da Classe
    const TABLENAME = 'PerfilFormulario';
    #endregion

    #region Metodos

    #region GetFormulariosByPerfil
    public function GetFormulariosByPerfil($IdPerfil)
    {
        try
        {
            Transaction::open();
        
            $criteria = new Criteria;
    
            $criteria->add("IdPerfil", "=", $IdPerfil);
    
            $repository = new Repository('PerfilFormulario');
            $result = $repository->load($criteria);
    
            Transaction::close();
        }
        catch(Exception $ex)
        {
            Transaction::rollback();
            throw new Exception($ex->getMessage());
        }

        return $result;
    }
    #endregion

    #endregion
}
