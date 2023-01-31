<?php

namespace KaiokenFramework\Traits;

use KaiokenFramework\Database\Transaction;
use KaiokenFramework\Components\Dialog\Message;
use Exception;
use KaiokenFramework\Log\LoggerTXT;

trait EditTrait
{
    #region Metodos
    
    #region onEdit

    /**
     * Carrega registro para edição
    */
    function onEdit($param)
    {
        try
        {
            $class = $this->activeRecord;

            $object = new $class; 
            $IdTabela = $object->getId();

            if (isset($param[$IdTabela]))
            {
                $id = $param[$IdTabela];
                
                Transaction::open();
                // Transaction::setLogger( new LoggerTXT('Backend/Tmp/LogSQL.txt') );
                
                // instancia o Active Record
                $object = $class::find($id); 
                
                 // lança os dados no formulário
                $this->form->setData($object);

                #region UsuarioAltecao | DataAlteracao

                try
                {
                    $conn = Transaction::get();

                    #region UsuarioAlteracao
                    $sql = "Select UsuarioAlteracao From {$object->getEntity()} Limit 1";

                    $result = $conn->query($sql);

                    if($result)
                    {
                        $object->UsuarioAlteracao = 'suporte';
                    }
                    #endregion

                    #region DataAlteracao

                    $sql = "Select DataAlteracao From {$object->getEntity()} Limit 1";
        
                    $result = $conn->query($sql);
        
                    if($result)
                    {
                        $object->DataAlteracao =  date('Y-m-d H:i') ;
                    }
                    #endregion
                }
                catch(Exception $ex) { }
                
                #endregion

                Transaction::close();
            }
        }
        catch (Exception $e)
        {
            Transaction::rollback();
            new Message('error', $e->getMessage());
        }
    }
    #endregion

    #endregion
}
