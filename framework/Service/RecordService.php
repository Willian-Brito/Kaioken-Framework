<?php

namespace KaiokenFramework\Service;

use KaiokenFramework\Database\Transaction;
use KaiokenFramework\Database\Repository;
use KaiokenFramework\Database\Criteria;

/**
 *
 * Classe base de serviços REST
 * @author Willian Brito (h1s0k4)
*/
abstract class RecordService
{

    #region Propriedades da Classe
    private $activeRecord;
    private $data;
    #endregion

    #region Metodos

    #region load
    /**
     * Find a Active Record and returns it
     * @return The Active Record itself as array
     * @param $param HTTP parameter
     */
    public function load($param)
    {
        $activeRecord = $this->activeRecord;
        
        Transaction::open();
        
        $object = new $activeRecord($param['id'], FALSE);
        
        Transaction::close();
        $attributes = $this->data;
        return $object->toArray( $attributes );
    }
    #endregion
    
    #region delete
    /**
     * Delete an Active Record object from the database
     * @param [$id]     HTTP parameter
     */
    public function delete($param)
    {
        
        $activeRecord = $this->activeRecord;
        
        Transaction::open();
        
        $object = new $activeRecord($param['id']);
        $object->delete();
        
        Transaction::close();
        return;
    }
    #endregion
    
    #region save
    /**
     * Store the objects into the database
     * @param $param HTTP parameter
     */
    public function save($param)
    {
        
        $activeRecord = $this->activeRecord;
        
        Transaction::open();
        
        $object = new $activeRecord;
        $pk = $object->getId();
        $param['data'][$pk] = $param['data']['id'] ?? NULL;
        $object->fromArray( (array) $param['data']);
        $object->save();
        
        Transaction::close();
        return $object->toArray();
    }
    #endregion
    
    #region loadAll
    /**
     * List the Active Records by the filter
     * @return The Active Record list as array
     * @param $param HTTP parameter
     */
    public function loadAll($param)
    {
        
        $activeRecord = $this->activeRecord;
        
        Transaction::open();
        
        $criteria = new Criteria;
        if (isset($param['offset']))
        {
            $criteria->setProperty('offset', $param['offset']);
        }
        if (isset($param['limit']))
        {
            $criteria->setProperty('limit', $param['limit']);
        }
        if (isset($param['order']))
        {
            $criteria->setProperty('order', $param['order']);
        }
        if (isset($param['filters']))
        {
            foreach ($param['filters'] as $filter)
            {
                $criteria->add($filter[0], $filter[1], $filter[2]);
            }
        }
        
        $repository = new Repository($activeRecord);
        $objects = $repository->load($criteria, FALSE);
        $attributes = $this->data;
        
        $return = [];
        if ($objects)
        {
            foreach ($objects as $object)
            {
                $return[] = $object->toArray( $attributes );
            }
        }
        Transaction::close();
        return $return;
    }
    #endregion
    
    #region deleteAll
    /**
     * Delete the Active Records by the filter
     * @return The result of operation
     * @param $param HTTP parameter
     */
    public function deleteAll($param)
    {
        
        $activeRecord = $this->activeRecord;
        
        Transaction::open();
        
        $criteria = new Criteria;
        if (isset($param['filters']))
        {
            foreach ($param['filters'] as $filter)
            {
                $criteria->add($filter[0], $filter[1], $filter[2]);
            }
        }
        
        $repository = new Repository($activeRecord);
        $return = $repository->delete($criteria);
        Transaction::close();
        return $return;
    }
    #endregion

    #region countAll
    /**
     * Find the count Records by the filter
     * @return The Active Record list as array
     * @param $param HTTP parameter
     */
    public function countAll($param)
    {
        
        $activeRecord = $this->activeRecord;

        Transaction::open();

        $criteria = new Criteria;
        if (isset($param['offset']))
        {
            $criteria->setProperty('offset', $param['offset']);
        }
        if (isset($param['limit']))
        {
            $criteria->setProperty('limit', $param['limit']);
        }
        if (isset($param['order']))
        {
            $criteria->setProperty('order', $param['order']);
        }
        if (isset($param['filters']))
        {
            foreach ($param['filters'] as $filter)
            {
                $criteria->add($filter[0], $filter[1], $filter[2]);
            }
        }

        $repository = new Repository($activeRecord);
        $count = $repository->count($criteria, FALSE);

        Transaction::close();
        return $count;
    }
    #endregion
    
    #region handle
    /**
     * Handle HTTP Request and dispatch
     * @param $param HTTP POST and php input vars
     */
    public function handle($param)
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD']);
        
        unset($param['class']);
        unset($param['method']);
        $param['data'] = $param;
        
        switch( $method )
        {
            #region GET
            case 'GET':
                if (!empty($param['id']))
                {
                    return self::load($param);
                }
                else
                {
                    return self::loadAll($param);
                }
                break;
            #endregion

            #region POST
            case 'POST':
                return self::save($param);
                break;
            #endregion

            #region PUT
            case 'PUT':
                return self::save($param);
                break;  
            #endregion
            
            #region DELETE
            case 'DELETE':
                if (!empty($param['id']))
                {
                    return self::delete($param);
                }
                else
                {
                    return self::deleteAll($param);
                }
                break;
            #endregion
        }
    }
    #endregion

    #endregion
}
