<?php

#region Imports

use KaiokenFramework\Components\Base\Element;

use KaiokenFramework\Database\Criteria;
use KaiokenFramework\Database\Record;
use KaiokenFramework\Database\Repository;
use KaiokenFramework\Database\Transaction;
#endregion

class Menu extends Record
{
    #region Propriedades da Classe
    const TABLENAME = 'Menu';
    #endregion
    
    #region Metodos

    #region getFormularioCaminhoMenu
    public function getFormularioCaminhoMenu($IdMenu, $caminho)
    {
        try
        {
            if($IdMenu == NULL)
                return $caminho;

            Transaction::open();
            
            $criteria = new Criteria;
    
            $criteria->add("IdMenu", "=", $IdMenu);
    
            $repository = new Repository('Menu');
            $menu = $repository->load($criteria);

            $caminho .= "{$menu[0]->Descricao} -> ";
    
            Transaction::close();

            return $this->getFormularioCaminhoMenu($menu[0]->IdMenuPai, $caminho);
        }
        catch(Exception $ex)
        {
            Transaction::rollback();
            throw new Exception($ex->getMessage());
        }
    }
    #endregion

    #endregion
}
