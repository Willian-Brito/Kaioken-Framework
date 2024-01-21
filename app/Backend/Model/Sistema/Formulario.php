<?php

use KaiokenFramework\Database\Criteria;
use KaiokenFramework\Database\Record;
use KaiokenFramework\Database\Repository;

class Formulario extends Record
{
    #region Propriedades da Classe
    const TABLENAME = 'Formulario';
    private $formulario;
    #endregion

    #region Metodos

    #region get_formulario
    public function get_formulario() 
    {
        if (empty($this->formulario))
        {

            $this->formulario = new Formulario($this->Idformulario);
        }
        
        return $this->formulario;
    }
    #endregion

    #region get_nome_formulario
    
    public function get_nome_formulario()
    {
        $this->formulario = new Formulario($this->IdFormulario);
        $descricao = $this->formulario->DescricaoFormulario == "Dashboard" 
                   ? "" : $this->formulario->DescricaoFormulario;

        return $descricao;        
    }
    #endregion

    #region get_caminho_formulario
    public function get_caminho_formulario()
    {
        #region Objetos

        $Menu = new Menu();
        $criteria = new Criteria;
        $repository = new Repository('Menu');
        $caminhoFormulario = "";
        #endregion

        if($this->temLista())
        {
            $criteria->add('IdFormulario', '=', $this->IdFormulario);
            $menu = $repository->load($criteria)[0];

            $caminho = $Menu->getFormularioCaminhoMenu($menu->IdMenu, '');
            $arrayCaminho = explode(' -> ', $caminho);

            for ($i = count($arrayCaminho); $i >= 0; $i--)
            {
                if(!empty($arrayCaminho[$i]))
                {
                    if($i == 0)
                        $caminhoFormulario .= "{$arrayCaminho[$i]}";
                    else
                        $caminhoFormulario .= "{$arrayCaminho[$i]} -> ";
                }
            }

            $caminhoFormulario = $caminhoFormulario == "Dashboard" ? "" : $caminhoFormulario;

            return $caminhoFormulario;
        }        
    }
    #endregion

    #region temLista
    private function temLista()
    {
        $formulario = new Formulario($this->IdFormulario);

        if(!empty($formulario->ArquivoLista))
            return true;

        return false;
    }
    #endregion

    #region getFormLista
    public static function getFormLista($class)
    {
        $repo = new Repository('Formulario');
        $criteria = new Criteria();

        $criteria->add('ArquivoLista', '=', $class);
        $criteria->add('ArquivoFormulario', '=', $class, 'OR');
        $formulario = $repo->load($criteria);

        if (count($formulario) > 0)
            return $formulario[0];
    }
    #endregion

    #region getForm
    public static function getForm($class)
    {
        $repo = new Repository('Formulario');
        $criteria = new Criteria();

        $criteria->add('ArquivoFormulario', '=', $class);
        $formulario = $repo->load($criteria);

        if (count($formulario) > 0)
            return $formulario[0];
    }
    #endregion

    #endregion
}
