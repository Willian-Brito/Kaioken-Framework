<?php

#region Imports
namespace KaiokenFramework\Traits;

use KaiokenFramework\Page\Action;
use KaiokenFramework\Database\Transaction;
use KaiokenFramework\Components\Dialog\Message;
use KaiokenFramework\Components\Dialog\Question;

use Exception;
use KaiokenFramework\Components\Base\JScript;
use KaiokenFramework\Feature\FeatureFlag;
use KaiokenFramework\Log\LoggerTXT;
use KaiokenFramework\Security\Auth;
use KaiokenFramework\Session\Session;
use Usuario;
#endregion

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
            #region Objetos

            Transaction::open();
            // Transaction::setLogger(new LoggerTXT("Backend/Tmp/logSQL.txt"));
            
            $class = $this->activeRecord;
            $object = new $class; 

            $IdTabela = $object->getId();

            $id = $param[$IdTabela];
            $object = $class::find($id);
            #endregion

            #region Validação

            Auth::Authorization($object, $id);

            if($id == Usuario::getIdUsuarioSuporte() && $IdTabela == "IdUsuario")
                throw new Exception("Não é possível excluir o usuário 'suporte'");
            #endregion

            #region Delete

            if($object instanceof Usuario)
            {
                FeatureFlag::para($object->IdUsuario)->removeFeatures();

                if(!empty($object->FotoPerfil))
                    unlink($object->FotoPerfil);
            }
            
            $object->delete();

            Transaction::close();

            // recarrega a datagrid
            // $this->onReload(); 
            
            new Message('success', "Registro excluído com sucesso");
            $this->redirect($param['class']);
            #endregion
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
        $script = "$('.question').hide();";
        JScript::run($script);
    }
    #endregion

    #region redirect
    function redirect($class) 
    {
        $link = "index.php?class=$class";
        JScript::redirect($link, 1000);
    }
    #endregion

    #endregion
}
