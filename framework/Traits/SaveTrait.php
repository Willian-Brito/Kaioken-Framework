<?php

namespace KaiokenFramework\Traits;

use KaiokenFramework\Database\Transaction;
use KaiokenFramework\Components\Dialog\Message;
use Exception;
use KaiokenFramework\Log\LoggerTXT;
use KaiokenFramework\Session\Session;
use KaiokenFramework\Security\Token;
use Usuario;

trait SaveTrait
{

    #region Metodos

    #region onSave

    /**
     * Salva os dados do formulário
    */
    function onSave()
    {
        try
        {
            Transaction::open();
            // Transaction::setLogger( new LoggerTXT('Backend/Tmp/LogSQL.txt') );
            
            $class = $this->activeRecord;
            $dados = $this->form->getData();

            $object = new $class; // instancia objeto
            $object->fromArray( (array) $dados); // carrega os dados

            #region UsuarioAltecao | DataAlteracao

            $conn = Transaction::get();

            #region UsuarioAlteracao
            $sql = "Select UsuarioAlteracao From {$object->getEntity()} Limit 1";

            // $sql = "SELECT Column_name
            //           FROM INFORMATION_SCHEMA.COLUMNS 
            //          WHERE Table_Schema  = 'Msystem'
            //            AND Table_name = '{$object->getEntity()}'
            //            AND Column_name = 'UsuarioAlteracao' 
            //          LIMIT 1 ";

            $result = $conn->query($sql);

            if($result)
            {
                $usuario = Usuario::find(Session::getValue("IdUsuarioLogado"));
                $object->UsuarioAlteracao = $usuario->Usuario;
            }
            #endregion

            #region DataAlteracao

            $sql = "Select DataAlteracao From {$object->getEntity()} Limit 1";

            // $sql = "SELECT Column_name
            //           FROM INFORMATION_SCHEMA.COLUMNS 
            //          WHERE Table_Schema  = 'Msystem'
            //            AND Table_name = '{$object->getEntity()}'
            //            AND Column_name = 'DataAlteracao' 
            //          LIMIT 1 ";

            $result = $conn->query($sql);

            if($result)
            {
                $object->DataAlteracao =  date('Y-m-d H:i') ;
            }
            #endregion
            
            #endregion

            $object->save(); // armazena o objeto
            
            Transaction::close(); // finaliza a transação
            new Message('success', 'Dados armazenados com sucesso!');
                      
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
