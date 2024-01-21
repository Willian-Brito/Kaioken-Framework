<?php

#region Imports

use KaiokenFramework\Components\Base\JScript;
use KaiokenFramework\Components\Dialog\Message;
use KaiokenFramework\Components\Form\CheckButton;
use KaiokenFramework\Components\Form\Combo;
use KaiokenFramework\Components\Form\Form;
use KaiokenFramework\Components\Form\Password;
use KaiokenFramework\Components\Form\Text;
use KaiokenFramework\Components\Wrapper\KaiokenFormWrapper;

use KaiokenFramework\Database\Criteria;
use KaiokenFramework\Database\Repository;
use KaiokenFramework\Database\Transaction;

use KaiokenFramework\Enum\PageStatusEnum;
use KaiokenFramework\Feature\FeatureFlag;
use KaiokenFramework\Log\LoggerTXT;
use KaiokenFramework\Page\Action;
use KaiokenFramework\Page\Page;
use KaiokenFramework\Router\Router;
use KaiokenFramework\Security\Token;
use KaiokenFramework\Session\Session;

use KaiokenFramework\Traits\EditTrait;
use KaiokenFramework\Traits\ReloadTrait;

use KaiokenFramework\Util\Util;

#endregion

class UsuarioForm extends Page  
{
    #region Objetos

    private $form;
    private $datagrid;
    private $loaded;
    private $activeRecord;
    private $filters;
    private $idInput;
    private $html;
    private $ehNovo;
    private $fotoAntiga;

    private $emailEhObrigatório;
    #endregion

    #region Traits

    use ReloadTrait;
    use EditTrait;
    #endregion

    #region Construtor

    /**
     * Construtor da página
    */
    public function __construct()
    {
        parent::__construct();

        $this->getParametros();
        $this->createPage();
    }
    #endregion

    #region Metodos

    #region Principais

    #region createPage
    private function createPage()
    {
        // Define o Active Record
        $this->activeRecord = 'Usuario';

        #region Html

        $template = __DIR__ . DIRECTORY_SEPARATOR . "UsuarioForm.html";
        $this->html = file_get_contents($template);

        #region Criar Formulario
        $this->form = new KaiokenFormWrapper(new Form('form_usuario'));

        #region criando campos

        $txtNome = new Text('Nome');
        $txtNome->id = "txtNome";
        $txtNome->maxlength = "100";

        $txtUsuario = new Text('Usuario');
        $txtUsuario->id = "txtUsuario";
        $txtUsuario->maxlength = "20";

        $txtSenha = new Password('Senha');
        $txtSenha->id = "txtSenha";
        $txtSenha->maxlength = "200";

        $txtEmail = new Text('Email');
        $txtEmail->id = "txtEmail";
        $txtEmail->maxlength = "200";

        $cbPerfil = new Combo('IdPerfil');
        $cbPerfil->id = "cbPerfil";
        $cbPerfil->class = "form-control";

        $chkEhAtivo = new CheckButton('EhAtivo');
        $chkEhAtivo->id = "chkEhAtivo";
        $chkEhAtivo->class = "field";

        $this->setComboBox($cbPerfil);
        #endregion

        #region adicionando campos
        $this->form->addField('Nome', $txtNome, '80%');
        $this->form->addField('Usuario', $txtUsuario, '80%');
        $this->form->addField('Senha', $txtSenha, '80%');
        $this->form->addField('Email', $txtEmail, '80%');
        $this->form->addField('Perfil', $cbPerfil, '100%');
        $this->form->addField('EhAtivo', $chkEhAtivo, '10px');
        
        $this->form->addAction('Salvar', new Action(array($this, 'onSave')));
        $this->form->addAction('Cancelar', new Action(array($this, 'onEdit')));
        #endregion
        
        #endregion

        #endregion

        if( !isset($_GET['method']) || ($_GET['method'] == 'onEdit') )
            Token::generateTokenCsrf();

        if(!isset($_GET["IdUsuario"]))
            parent::add($this->html);
    }
    #endregion
    
    #region onSave
    public function onSave()
    {
        try
        {
            $this->validarCampos();
            $this->salvar();

            FeatureFlag::runAllFeatures();

            new Message('success', 'Dados armazenados com sucesso');
            parent::resetForm();
            
            $link = 'index.php?class=UsuarioList';
            JScript::redirect($link,1000);
        }
        catch(Exception $ex)
        {
            Transaction::rollback();
            new Message('error', $ex->getMessage(), time: 5000, IdFoco: "#$this->idInput");

            $this->loadDataForm();
        }
    }
    #endregion
    
    #region onNovo
    public function onNovo()
    {
        Token::generateTokenCsrf();
        $this->ehNovo = true;
    }
    #endregion

    #region onEdit

    /**
    * Carrega registro para edição
    */
    public function onEdit($param)
    {
        try
        {
            if (isset($param["key"]))
            {
                $IdUsuario = $param["key"];

                Transaction::open(); 

                $usuario = Usuario::find($IdUsuario);
                Session::setValue('KAIOKEN', clone $usuario);
                Session::getValue('KAIOKEN')->IdUsuario = $usuario->IdUsuario;
                    
                if ($usuario) 
                    $this->setDataByForm($usuario);               

                Transaction::close(); 
            }
            else
            {
                if(!$this->ehNovo)
                {
                    $link = 'index.php?class=UsuarioList';
                    JScript::redirect($link, 50);
                }
            }
        }
        catch (Exception $e)
        {
            Transaction::rollback(); 
            new Message('error', $e->getMessage());
        }
    }
    #endregion

    #region onRemove
    public function onRemove()
    {
        $pageStatus = Session::getValue("PageStatus");
        $statusAlterar = PageStatusEnum::Alterar->value;

        if($pageStatus == $statusAlterar)
        {
            $user = Session::getValue('KAIOKEN');
            $fotoPadrao = "app/Frontend/assets/img/icon/avatar.svg";
            $user->FotoPerfil = "";

            $this->setFotoPerfil($fotoPadrao);
        }
    }
    #endregion

    #region show

    /**
     * exibe a página
     */
    public function show()
    {
        parent::show();
    }
    #endregion

    #endregion

    #region Auxiliares

    #region salvar
    private function salvar()
    {
        Transaction::open();

        $img = $_FILES["FotoPerfil"];
        $dados = $this->form->getData();
        $user = new Usuario(); 

        $IdUsuarioLogado = Session::getValue("IdUsuarioLogado");
        $usuarioLogado = Usuario::find($IdUsuarioLogado);
        
        $user->fromArray( (array) $dados);

        $user->EhAtivo = empty($user->EhAtivo) ? 0 : 1;
        $user->DataAlteracao =  date('Y-m-d H:i');
        $user->UsuarioAlteracao = $usuarioLogado->Usuario;

        if ($this->ehStatusNovo())
        {
            Session::setValue('KAIOKEN', NULL);
            $user->Senha = password_hash($user->Senha, PASSWORD_ARGON2ID);
        }
        else
        {
            $user->IdUsuario = Session::getValue('KAIOKEN')->IdUsuario;
            $user->Senha = Session::getValue('KAIOKEN')->Senha;
        }

        $user->FotoPerfil = $this->uploadIMG($user, $img);
        $user->save();

        $this->updateHeader($user);
        
        Transaction::close();
        Session::setValue('KAIOKEN', NULL);
    }
    #endregion

    #region setDataByForm
    private function setDataByForm($usuario)
    {
        $template = __DIR__ . DIRECTORY_SEPARATOR . "UsuarioForm.html";
        $template = file_get_contents($template);

        if(!$this->ehUsuarioSuporte())
            $this->ocultarPerfil();

        $this->ocultarSenha();
        $usuario->Senha = "";

        $this->popularComboBox();
        $this->html = $this->form->getHTML($template, $usuario);

        $template = $this->getHtmlComboBox($this->html);

        parent::add($template);

        $this->setCheckBox($usuario);
        $this->setImg($usuario->FotoPerfil);
    }
    #endregion

    #region ehUsuarioSuporte
    private function ehUsuarioSuporte()
    {
        $IdUsuarioLogado = Session::getValue("IdUsuarioLogado");
        return $IdUsuarioLogado == Usuario::getIdUsuarioSuporte();
    }
    #endregion

    #region validarCampos
    private function validarCampos()
    {      
        $campos =  $this->form->getData();
        $txtConfirmarSenha = $_POST['ConfirmarSenha'];
        $txtSenha = $_POST['Senha'];

        #region Permissão
        $criteria = new Criteria;
        $criteria->add("ArquivoFormulario", '=', 'UsuarioForm');

        $repo = new Repository("Formulario");
        $formulario = $repo->load($criteria)[0];

        if(Util::ehStatusNovo() && !Usuario::temPermissao($formulario->IdFormulario))
            throw new Exception("Usuario sem permissão para este recurso!");
        
        #endregion

        foreach ($campos as $indice => $value) 
        {
            #region Campos Obrigatórios
            if($this->forObrigatorio($indice))
            {
                #region Nome

                if($indice == "Nome")
                {
                    if(empty($value))
                    {
                        $this->idInput = $this->getIdInput($indice);
                        throw new Exception("Preencha o campo $indice!");
                    }
    
                    if(strlen($value) > 100)
                    {
                        $this->idInput = $this->getIdInput($indice);
                        throw new Exception("Nome deve conter menos de 100 caracteres");
                    }
                }
                #endregion

                #region Usuario

                if($indice == "Usuario")
                {
                    if(empty($value))
                    {
                        $this->idInput = $this->getIdInput($indice);
                        throw new Exception("Preencha o campo $indice!");
                    }
    
                    if(strlen($value) > 20)
                    {
                        $this->idInput = $this->getIdInput($indice);
                        throw new Exception("Usuario deve conter menos de 20 caracteres");
                    }

                    if(!Util::ehStatusNovo() && strtolower($value) != "kaioken")
                    {
                        if(Session::getValue("KAIOKEN")->IdUsuario == Usuario::getIdUsuarioSuporte())
                        {
                            $this->idInput = $this->getIdInput($indice);
                            throw new Exception("Não é possível alterar o login do usuário 'kaioken'");
                        }
                    }
    
                    if($this->ehStatusNovo() && Usuario::usuarioJaExiste($value))
                    {
                        $this->idInput = $this->getIdInput($indice);
                        throw new Exception("Usuário já existente!");
                    }
                }
                #endregion

                #region Senha

                if($indice == "Senha")
                {
                    if($this->ehStatusNovo() && empty($value))
                    {
                        $this->idInput = $this->getIdInput($indice);
                        throw new Exception("Preencha o campo $indice!");
                    }
    
                    if($this->ehStatusNovo()) 
                    {
                        if(strlen($value) > 200)
                        {
                            $this->idInput = $this->getIdInput($indice);
                            throw new Exception("Senha deve conter menos de 200 caracteres");
                        }
    
                        Usuario::checkPassword($value);
                    }
                }
                #endregion

                #region Perfil
                if($indice == "IdPerfil") 
                {              
                    if(empty($value))
                    {
                        $this->idInput = $this->getIdInput($indice);
                        throw new Exception("Preencha o campo Perfil!");
                    }

                    if(!Util::ehStatusNovo())
                    {
                        $IdUsuarioLogado = Session::getValue("IdUsuarioLogado");
                        $usuario = Usuario::find($IdUsuarioLogado);

                        if($usuario->IdPerfil != $value && $IdUsuarioLogado != Usuario::getIdUsuarioSuporte())
                        {
                            $perfil = Perfil::find($usuario->IdPerfil);
                            $this->idInput = $this->getIdInput($indice);

                            throw new Exception("O Perfil deve ser '{$perfil->Descricao}'");
                        }
                    }
                }
                #endregion
            }  
            #endregion            

            #region Email

            if($this->emailEhObrigatório)
            {
                if($indice == "Email" && empty($value))
                {
                    $this->idInput = $this->getIdInput($indice);
                    throw new Exception("Preencha o campo E-mail!");
                }
            }

            if($indice == "Email" && !empty($value))
            {
                if($indice == "Email" && strlen($value) > 200)
                {
                    $this->idInput = $this->getIdInput($indice);
                    throw new Exception("E-mail deve conter menos de 200 caracteres");
                }

                if( ($this->ehStatusNovo()) && Usuario::emailJaExiste($value))
                {
                    $this->idInput = $this->getIdInput($indice);
                    throw new Exception("E-mail já existente!");
                }

                if(!Util::validaEmail($value))
                {
                    $this->idInput = $this->getIdInput($indice);
                    throw new Exception("E-mail inválido!");
                }
            }
            #endregion
        }

        #region Confirmar Senha

        if($this->ehStatusNovo() && empty($txtConfirmarSenha)) 
        {
            $this->idInput = "txtConfirmarSenha";
            throw new Exception("Preencha o campo Confirmar Senha!");        
        }          

        if($this->ehStatusNovo() && $txtSenha != $txtConfirmarSenha)
        {
            $this->idInput = "txtConfirmarSenha";
            throw new Exception("Senhas Divergentes!");
        }
        #endregion
    }
    #endregion

    #region ocultarSenha
    private function ocultarSenha()
    {
        JScript::run('ocultarSenha()', 150);
    }
    #endregion

    #region ocultarPerfil
    private function ocultarPerfil()
    {
        JScript::run('ocultarPerfil()', 100);
    }
    #endregion

    #region getIdInput
    private function getIdInput($indice) 
    {

        $field = $this->form->getFields()[$indice];
        return $field->getProperty("id");
    }
    #endregion

    #region popularComboBox
    private function popularComboBox()
    {
        foreach($this->form->getFields() as $name => $field)
        {
            if($name == "IdPerfil")
            {
                Transaction::open();
        
                $perfis = Perfil::all();
                $items = array();

                foreach ($perfis as $obj_perfil)
                {
                    $items[$obj_perfil->IdPerfil] = $obj_perfil->Descricao;
                }
                
                Transaction::close();
                
                $field->addItems($items);
            }
        }
    }
    #endregion

    #region getHtmlComboBox
    private function getHtmlComboBox($template)
    {
        $cbPerfil = $this->form->getFields()["IdPerfil"];        
        $html = str_replace('{cbPerfil}', $this->toHTML($cbPerfil), $template);

        return $html;
    }
    #endregion

    #region setComboBox
    private function setComboBox($comboBox)
    {
        Transaction::open();
        
        $perfis = Perfil::all();
        $items = array();

        foreach ($perfis as $obj_perfil)
        {
            $items[$obj_perfil->IdPerfil] = $obj_perfil->Descricao;
        }
        
        Transaction::close();
        
        $comboBox->addItems($items);

        $this->html = str_replace('{cbPerfil}', $this->toHTML($comboBox), $this->html);
    }
    #endregion

    #region setCheckBox
    private function setCheckBox($usuario)
    {
        if($usuario->EhAtivo)            
            JScript::checked('chkEhAtivo');
    }
    #endregion

    #region loadDataForm
    private function loadDataForm()
    {
        if(!$this->ehStatusNovo())
        {
            if(!$this->ehUsuarioSuporte())
                $this->ocultarPerfil();

            $this->ocultarSenha();            
        }

        foreach ($this->form->getData() as $indice => $value)
        {
            if($indice != "FotoPerfil")
            {
                if($indice == "EhAtivo" && $value = "1")
                    JScript::checked('chkEhAtivo');
    
                if(!empty($value))
                {
                    $id = $this->getIdInput($indice);
                    JScript::setDataForm($id, $value);
                }
            }
        }

        $value = isset($_POST["ConfirmarSenha"]) ? $_POST["ConfirmarSenha"] : "";
        JScript::setDataForm('txtConfirmarSenha', $value);
    }
    #endregion

    #region forObrigatorio
    private function forObrigatorio($indice)
    {
        return $indice != "Email" && $indice != "EhAtivo";
    }
    #endregion

    #region ehStatusNovo
    private function ehStatusNovo()
    {
        $ehNovo = Session::getValue('PageStatus') == PageStatusEnum::Novo->value;
        return $ehNovo;
    }
    #endregion

    #region getParametros
    private function getParametros()
    {
        $IdUsuarioLogado = Session::getValue("IdUsuarioLogado");

        #region Utiliza Email Obrigatório no cadastro de usuarios
        $feature = FeatureFlag::Utiliza_Email_Obrigatorio_No_Cadastro_De_Usuarios;

        $this->emailEhObrigatório = FeatureFlag::para($IdUsuarioLogado)->estaAtivo($feature);
        #endregion
    }
    #endregion

    #region uploadIMG
    private function uploadIMG($user, $img)
    {
        $temImagem = isset($img['name']) && !empty($img['name']);

        if($temImagem)
        {
            $this->validateImg($img);

            $diretorio = "app/Frontend/Page/Sistema/Usuario/img/";

            $this->criarDiretorio($diretorio);

            $nomeImg = !empty($user->IdUsuario) ? $user->IdUsuario : $user->getLastId();
            $caminhoCompleto = "{$diretorio}{$nomeImg}.jpeg"; //$this->getExtensao($img)
            $extensao = $this->getExtensao($img);

            if($this->extesaoForDeImagem($extensao))
            {
                move_uploaded_file($img['tmp_name'], $caminhoCompleto);    
                return $caminhoCompleto;
            }

            throw new Exception("Imagem Inválida!");
        }

        $FotoPerfil = isset(Session::getValue('KAIOKEN')->FotoPerfil) ? Session::getValue('KAIOKEN')->FotoPerfil : "";

        if(!empty($FotoPerfil))
            return $FotoPerfil;        

        if(!empty($this->fotoAntiga))
            unlink($this->fotoAntiga);

        return "";
    }
    #endregion

    #region validateImg
    private function validateImg($img)
    {
        $tamanho = 200000; // 200 Kilobytes
        $largura = 800;
        $altura = 800;

        if(!$this->ehImagem($img))
        {
            throw new Exception("Arquivo em formato inválido! A imagem deve ser jpg, jpeg ou png.");
        }

        // Verifica tamanho do arquivo
        if($img["size"] > $tamanho)
        {
            throw new Exception("Arquivo em tamanho muito grande! A imagem deve ser de no máximo 200 Kilobytes");
        }
        
        // Para verificar as dimensões da imagem
        $tamanhos = getimagesize($img["tmp_name"]);
        
        // Verifica largura
        if($tamanhos[0] > $largura)
        {
            throw new Exception("Largura da imagem não deve ultrapassar " . $largura . " pixels");
        }

        // Verifica altura
        if($tamanhos[1] > $altura)
        {
            throw new Exception("Altura da imagem não deve ultrapassar " . $altura . " pixels");
        }
    }
    #endregion

    #region ehImagem
    private function ehImagem($img)
    {
        $mimetype = mime_content_type($img['tmp_name']);

        if(in_array($mimetype, array('image/jpeg', 'image/jpg', 'image/png')))
            return true;

        return false;
    }
    #endregion

    #region extesaoForDeImagem
    private function extesaoForDeImagem($extensao)
    {
        if(in_array($extensao, array('jpeg', 'jpg', 'png')))
            return true;

        return false;
    }
    #endregion
    
    #region getExtensao
    private function getExtensao($file)
    {
        $img = $file['name'];
        $array = explode('.', $img);
        $extensao = strtolower(array_pop($array));
        return $extensao;
    }
    #endregion

    #region criarDiretorio
    private function criarDiretorio($diretorio)
    {
        if(!is_dir($diretorio))
        {
            mkdir($diretorio, 0755);
            chmod($diretorio, 0755);
        }
    }
    #endregion

    #region setImg
    private function setImg($path)
    {
        if(!empty($path))
            JScript::run("setImg('$path')", 100);
    }
    #endregion

    #region updateHeader
    private function updateHeader($user)
    {
        if($user->IdUsuario == Session::getValue("IdUsuarioLogado"))
        {
            $this->setFotoPerfil($user->FotoPerfil);
            $this->setUsuario($user->Usuario);
            $this->setPerfil($user->IdPerfil);
        }
    }
    #endregion

    #region setFotoPerfil
    private function setFotoPerfil($fotoPerfil)
    {
        if(!empty($fotoPerfil))
            JScript::run("setFotoPerfil('$fotoPerfil')", 100);
    }
    #endregion

    #region setUsuario
    private function setUsuario($nome)
    {
        if(!empty($nome))
            JScript::run("setUsuario('$nome')", 100);
    }
    #endregion

    #region setPerfil
    private function setPerfil($IdPerfil)
    {
        $perfil = new Perfil($IdPerfil);

        if(!empty($perfil->descricao))
            JScript::run("setPerfil('$perfil->descricao')", 100);
    }
    #endregion

    #endregion

    #endregion
}