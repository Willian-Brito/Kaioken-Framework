<?php

namespace KaiokenFramework\Traits;

use KaiokenFramework\Database\Transaction;
use KaiokenFramework\Database\Repository;
use KaiokenFramework\Database\Criteria;
use KaiokenFramework\Components\Dialog\Message;

use Exception;

trait ReloadTrait
{
    #region Metodos

    #region onReload

    /**
     * Carrega a DataGrid com os objetos
     */
    function onReload()
    {
        try
        {
            Transaction::open();
            $table = constant($this->activeRecord.'::TABLENAME');
            $Id = "Id{$table}";
            $repository = new Repository( $this->activeRecord );

            // cria um critério de seleção de dados
            $criteria = new Criteria;
            $criteria->setProperty('order', "{$Id} desc");
            $criteria->setProperty('limit', 100);
            
            if (isset($this->filters))
            {
                foreach ($this->filters as $filter)
                {
                    $criteria->add($filter[0], $filter[1], $filter[2], $filter[3]);
                }
            }
            
            // carrega os objetos que satisfazem o critério
            $objects = $repository->load($criteria);
            $this->datagrid->clear();

            if ($objects)
            {
                foreach ($objects as $object)
                {
                    // adiciona o objeto na DataGrid
                    $this->datagrid->addRow($object);
                }
            }

            Transaction::close();
        }
        catch (Exception $e)
        {
            new Message('error', $e->getMessage());
        }
    }
    #endregion

    #endregion
}
