<?php

namespace KaiokenFramework\Traits;

use KaiokenFramework\Page\Action;
use KaiokenFramework\Database\Transaction;
use KaiokenFramework\Components\Dialog\Message;
use KaiokenFramework\Components\Dialog\Question;

use Exception;
use KaiokenFramework\Log\LoggerTXT;

trait DeleteTrait
{
    #region Metodos

    #region onDelete
    /**
     * Pergunta sobre a exclusão de registro
     */
    function onDelete($param)
    {
        $class = $this->activeRecord;

        $object = new $class; 
        $IdTabela = $object->getId();

        $id = $param[$IdTabela]; // obtém o parâmetro $id

        $actionYes = new Action(array($this, 'Delete'));
        $actionYes->setParameter($IdTabela, $id);

        $actionNo = new Action(array($this, 'fecharMsg'));
        
        new Question('Deseja realmente excluir o registro?', $actionYes, $actionNo);
    }
    #endregion

    #region Delete
    /**
     * Exclui um registro
    */
    function Delete($param)
    {
        try
        {            
            Transaction::open();
            // Transaction::setLogger(new LoggerTXT("Backend/Tmp/logSQL.txt"));
            
            $class = $this->activeRecord;
            $object = new $class; 

            $IdTabela = $object->getId();

            $id = $param[$IdTabela];
            $object = $class::find($id);
            $object->delete();

            Transaction::close();

            // recarrega a datagrid
            // $this->onReload(); 
            
            new Message('success', "Registro excluído com sucesso");
            $this->redirect($param['class']);
        }
        catch (Exception $e)
        {
            Transaction::rollback();
            new Message('error', $e->getMessage());
        }
    }
    #endregion

    #region fecharMsg
    function fecharMsg()
    {
        echo "<script> $('.question').hide(); </script>";
    }
    #endregion

    #region redirect
    function redirect($class) 
    {
        echo "<script>setTimeout(function(){ window.location = 'index.php?class=$class'; }, 1000); </script>";
    }
    #endregion

    #endregion
}
