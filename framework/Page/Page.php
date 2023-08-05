<?php

#region Imports

namespace KaiokenFramework\Page;

use Exception;
use KaiokenFramework\Components\Base\Element;
use KaiokenFramework\Components\Form\Field;
use KaiokenFramework\Enum\PageStatusEnum;
use KaiokenFramework\Session\Session;
use KaiokenFramework\Security\Token;

use Formulario;
use KaiokenFramework\Components\Base\JScript;
use KaiokenFramework\Database\Criteria;
use KaiokenFramework\Database\Repository;
use KaiokenFramework\Security\Auth;
use Usuario;
#endregion

/**
 * Page controller
 * @author Willian Brito (h1s0k4)
*/
class Page extends Element
{
    #region Construtor

    /**
     * Método construtor
    */
    public function __construct()
    {
        parent::__construct('div');
    }
    #endregion
    
    #region Metodos

    #region Principal

    #region show

    /**
     * Executa determinado método de acordo com os parâmetros recebidos
    */
    public function show()
    {
        if ($_GET)
        {
            $class  = isset($_GET['class'])  ? htmlspecialchars($_GET['class'], double_encode:false) : '';
            $method = isset($_GET['method']) ? htmlspecialchars($_GET['method'], double_encode:false) : '';

            #region Validação

            #region CSRF

            if($method == 'onSave')
                Token::validateTokenCSRF();            
                
            #endregion

            #region Authorization

            if( isset($_GET['key']) )
            {
                $id = $_GET['key'];

                if(!empty($id))
                {
                    if($class)
                    {
                        $criteria = new Criteria;
                        $criteria->add('ArquivoFormulario', '=', $class);

                        $repo = new Repository('Formulario');
                        $formulario = $repo->load($criteria);

                        if($formulario)
                        {
                            $tabela = $formulario[0]->Classe;

                            if(!empty($tabela))
                            {
                                $IdTabela = "Id" . $tabela;
    
                                $criteria = new Criteria;
                                $criteria->add($IdTabela, '=', $id);
    
                                $repo = new Repository($tabela);
                                $object = $repo->load($criteria)[0];  
    
                                Auth::Authorization($object, $id);                        
                            }
                        }
                    }
                }
            }
            #endregion

            #endregion

            #region Perfil Usuário Logado

            if($this->estaEditandoUsuarioLogado($class, $method) || $this->FormSemAutorizacao($class))
            {
                $this->redirectPage($class, $method);            
                parent::show();

                return;
            }
            #endregion

            #region Pages

            if ($class)
            {       
                $formulario = Formulario::getFormLista($class);
                $IdFormulario = $formulario->IdFormulario;

                if(!empty($IdFormulario))
                {
                    if(Usuario::temPermissao($IdFormulario)) 
                        $this->redirectPage($class, $method);                    
                    else
                        throw new Exception("Usuário não tem permissão para acessar este recurso!");
                }
            }
            #endregion
        }
        
        parent::show();
    }
    #endregion

    #endregion

    #region Auxiliar

    #region setPageStatus
    private function setPageStatus($formulario)
    {
        if($formulario)
        {
            $temKey = isset($_GET['key']);
            $metodo = isset($_GET['method']) ? $_GET['method'] : null;
            $status = isset($_POST['PageStatus']) ? $_POST['PageStatus'] : 0;

            $clickouEmNovo = !$temKey && $metodo == 'onEdit' && $status != PageStatusEnum::Alterar->value;
            
            if($clickouEmNovo)
            {
                Session::setValue('PageStatus', PageStatusEnum::Novo->value);
            }
            else
            {
                $clickouEmAlterar = $temKey || $status  == PageStatusEnum::Alterar->value;

                if($clickouEmAlterar)
                {
                    Session::setValue('PageStatus', PageStatusEnum::Alterar->value);
                }
                else
                {
                    Session::setValue('PageStatus', PageStatusEnum::Novo->value);         
                }
            }    
            
            $value = Session::getValue('PageStatus');
            $script= "setPageStatus('$value')";

            JScript::run($script, 500);
        }        
    }
    #endregion

    #region toHTML
    public function toHTML(Field $field)
    {
        $div = new Element('div');
        $div->add($field);

        $html = $this->retirarDiv($div);
        return "$html";
    }
    #endregion

    #region retirarDiv
    private function retirarDiv($field)
    {
        $html = str_replace('</div>', '', "$field");
        $tagHtmlSemDiv = str_replace('<div>', '', $html);

        return $tagHtmlSemDiv;
    }
    #endregion

    #region estaEditandoUsuarioLogado
    private function estaEditandoUsuarioLogado($class, $method)
    {
        $object = $class == get_class($this) ? $this : new $class;
        
        if (method_exists($object, $method))
        {
            if($class == "UsuarioForm" && ($method == "onEdit" OR $method == "onSave") )
                return true;
        }

        return false;
    }
    #endregion

    #region redirectPage
    private function redirectPage($class, $method)
    {
        $object = $class == get_class($this) ? $this : new $class;
        
        if (method_exists($object, $method))
        {
            call_user_func(array($object, $method), $_GET);
        }

        $this->setPageStatus(Formulario::getForm($class));
    }
    #endregion

    #region resetForm
    public function resetForm()
    {
        $statusNovo = PageStatusEnum::Novo->value;
        $_POST['PageStatus'] = $statusNovo;
        $script = "setPageStatus('$statusNovo')";

        JScript::run($script, 3000);
    }
    #endregion

    #region FormSemAutorizacao
    private function FormSemAutorizacao($class)
    {
        $formSemAutorizacao = ['DashboardList', 'RecuperarSenhaForm', 'ErrorForm'];

        if(in_array($class, $formSemAutorizacao))
            return true;

        return false;
    }
    #endregion

    #endregion

    #endregion
}
