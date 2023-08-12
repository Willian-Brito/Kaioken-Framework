<?php

namespace KaiokenFramework\Report;
require_once('vendor/autoload.php');

use DateTime;
use FPDF;

/**
 * Classe para geração de relatórios em PDF
 * Manual FPDF: http://previagudo.com.br/omegaweb/tools/fpdf/fpdf/index.htm
 * @author Willian Brito (h1s0k4)
 */
class DocumentGeneratorPDF
{
    #region Propriedades da Classe
    private $pdf;            // objeto PDF
    private $fontSize;
    public $title;
    public $filtro;
    public $empresa;
    #endregion

    #region Construtor
    /**
     * Método construtor
     * Instancia o objeto FPDF
     */
    public function __construct()
    {
        // Cria um novo documento PDF
        $this->pdf = new FPDF('P', 'pt');
        $this->pdf->SetMargins(2,2,2); // define margens

        $this->setMetadata();

        // Adiciona uma página
        $this->pdf->AddPage();
        $this->pdf->AliasNbPages();
        $this->pdf->Ln();
    }
    #endregion

    #region Metodos

    #region [+] Public

    #region createHeader

    public function createHeader()
    {
        #region Parametros Iniciais
        $this->pdf->SetX(20);
        $this->pdf->SetY(15);
        $this->pdf->setFont('Times');
        $this->setFontSize(11);
        $this->pdf->SetTextColor(0,0,0);
        #endregion

        #region Logo
        $logo = PATH_IMG . "/msystem-logo.png";

        //caminho | margin-left | margin-top | tamanho
        $this->pdf->Image($logo, 15, 22, 140);
        #endregion

        #region Borda

        // Margin-Left | Margin-Top | Largura | Altura
        $this->pdf->Rect(20, 15, 550, 80);
        #endregion

        #region Nome Empresa
        $this->pdf->setFont('Times', 'BU');
        $this->pdf->SetXY(150, 22);
        $this->pdf->Cell(350, 12, utf8_decode($this->empresa->nome), 0, 1, 'C');
        $this->pdf->setFont('Times');
        #endregion

        #region Documento | Contato
        $this->setFontSize(8);
        $documentoContato = 'CNPJ: ' . $this->empresa->documento . ' CEP: ' . $this->empresa->cep . ' FONE: ' .  $this->empresa->telefone . ' / ' . $this->empresa->celular;
        $this->pdf->SetXY(150, 35);
        $this->pdf->Cell(350, 12, utf8_decode($documentoContato), 0, 1, 'C');
        #endregion

        #region Data | Hora
        $this->setFontSize(9);
        $now = date('Y-m-d');
        $data = DateTime::createFromFormat('Y-m-d', $now)->format('d/m/Y');
        $hora = date('H:i:s');
        $paginaAtual = $this->pdf->PageNo();
        $pagina = $paginaAtual . '/{nb}';


        $this->pdf->Text(537,29, $pagina);
        $this->pdf->Text(522,40, $data);
        $this->pdf->Text(527,50, $hora);
        $this->pdf->setFontSize(8);
        #endregion

        #region Endereço
        $endereco = utf8_decode($this->empresa->endereco . ' - ' . $this->empresa->bairro . ' - ' . $this->empresa->cidade . '/' . $this->empresa->estado);
        $this->pdf->SetXY(150, 47);
        $this->pdf->Cell(350, 12, $endereco, 0, 1, 'C');
        #endregion

        #region Titulo
        $this->setFontSize(10);
        $this->pdf->SetXY(150, 62);
        $this->pdf->setFont('Times', 'B');
        $this->pdf->Cell(350, 12, utf8_decode($this->title), 1, 1, 'C');

        $this->pdf->setFont('Times');
        #endregion

        #region Filtro
        $this->setFontSize(8);
        $this->pdf->SetXY(150, 77);
        $this->pdf->Cell(350, 12, utf8_decode($this->filtro), 0, 1, 'C');
        #endregion

        $this->pdf->SetY(120);

    }
    #endregion

    #region setFontSize
    /**
	 * Define tamanho da fonte
	 */
	public function setFontSize($size)
	{
		$this->pdf->SetFontSize($size);
		$this->fontSize = $size;
	}
    #endregion

    #region addContents
    /**
	 * Adiciona linha com conteúdos
	 */
	public function addContents($contents)
	{
		$this->pdf->SetY($this->pdf->GetY());
        $this->pdf->SetTextColor(100,100,100);
        $this->pdf->SetX(20);

		if ($contents)
		{
			$i = 0;
			foreach ($contents as $content)
			{
				$label = $content[0];
				$width = $content[2];

				$this->pdf->Cell($width, $this->fontSize + 4, $label, 'LTR', (int) ($i==count($contents)-1), 'L');

				$i ++;
			}
		}

        $this->pdf->SetTextColor(0,0,0);
        $this->pdf->SetX(20);

		if ($contents)
		{
			$i = 0;
			foreach ($contents as $content)
			{
				$value = $content[1];
				$width = $content[2];

				$this->pdf->Cell($width, $this->fontSize + 8, $value, 'LBR', (int) ($i==count($contents)-1), 'L');
				$i++;
			}
		}
    }
    #endregion

    #region addTableRow
    /**
     * Adiciona um registro na tabela
     * @param $columns Objeto com os atributos das colunas
     * @param $borda Utiliza borda o padrão é não
     */
    public function addTableRow( $columns, $borda = 0)
    {
		$this->pdf->SetX(20);
        if ($columns)
        {
			$i = 0;
			foreach ($columns as $column)
			{
				$value = utf8_decode($column[0]);
				$width = $column[1];
				$align = $column[2];

				$this->pdf->Cell($width,  $this->fontSize + 8, $value, $borda, (int) ($i==count($columns)-1), $align, 1);
				$i ++;
			}
		}
    }
    #endregion

    #region setBorder
    public function setBorder($columns)
    {
        $this->pdf->SetX(20);

        if ($columns)
        {
			$i = 0;
            $border = '';
            $TotalColunas = count($columns)-1;

			foreach ($columns as $column)
			{
				$value = utf8_decode($column[0]);
				$width = $column[1];
				$align = $column[2];

                $border = $this->getBorder($i, $TotalColunas);                

				$this->pdf->Cell($width,  $this->fontSize + 6, $value, $border, (int) ($i==$TotalColunas), $align, 0);
				$i ++;
			}
		}
    }
    #endregion

    #region setZebrado
    public function setZebrado($i)
    {
        if($i % 2 == 0)
        {
            $this->pdf->SetFillColor(230,230,230);
        }
        else
        {
            $this->pdf->SetFillColor(255,255,255);
        }
    }
    #endregion

    #region save
    /**
     * Salva a nota fiscal em um arquivo
     * @param $arquivo localização do arquivo de saída
     */
    public function save($arquivo)
    {
        $this->pdf->Output('F', $arquivo);
    }
    #endregion

    #region open
    /**
     * Abrir no Navegador
     * @param $arquivo localização do arquivo de saída
     */
    public function open($arquivo)
    {
        $this->pdf->Output('I', $arquivo);
    }
    #endregion

    #region Download
    /**
     * Download do PDF
     * @param $arquivo localização do arquivo de saída
     */
    public function Download($arquivo)
    {
        $this->pdf->Output('D', $arquivo);
    }
    #endregion

    #endregion

    #region [-] Private

    #region setMetadata
    private function setMetadata()
    {
        $this->pdf->SetAuthor('h1s0k4');
        $this->pdf->SetCreator('MSystem Software');
        $this->pdf->SetKeywords('php, pdf');
    }
    #endregion

    #region getBorder
    private function getBorder($i, $TotalColunas)
    {
        // # LTRB -> Left, Top, Right, Bottom
        $border = 'TB';

        if($i == 0)
        {
            $border = 'LTB';

            if($TotalColunas == 0)
            {
                $border = 'LTRB';
            }
        }
        else if($i == $TotalColunas)
        {
            $border = 'TRB';
        }

        return $border;
    }
    #endregion

    #endregion

    #region [*] Magic
    /**
	 * Decorator - Redireciona chamadas
	 */
	public function __call($method, $parameters)
	{
		call_user_func_array([$this->pdf, $method], $parameters);
	}
    #endregion

    #endregion
}

?>