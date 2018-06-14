<?php
// Include the main TCPDF library (search for installation path).
//require_once('tcpdf_include.php');
require_once('config/tcpdf_config_alt.php');
require_once('tcpdf.php');

// Extend the TCPDF class to create custom Footer
class MYPDF extends TCPDF {
    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Página  '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

class PDF{
	private $impressao;
	public function __construct($impressaoOBJ){
	    $this->impressao = $impressaoOBJ;
	    $this->inicializa();
    }

    private function inicializa(){
        $pdfTitle = "Lista de Presenca - CAC";
        $orientation = 'L';//P = portrait L = landscape

        // create new PDF document
        $pdf = new MYPDF($orientation, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('CAC System');
        $pdf->SetTitle($pdfTitle);
        $pdf->SetSubject($pdfTitle);
        $pdf->SetKeywords('CAC, PDF, presença, oficina');

        $dataHoje = date('d/m/Y');
        //Cabeçalho
        $header_title = "Universidade Federal Rural do Rio de Janeiro";
        $header_logo = "logo.jpg";
        $txt_header = "Lista de presença \nSistema do Centro de Arte e Cultura\n"."Emitido em: {$dataHoje}";

        $pdf->SetHeaderData($header_logo, PDF_HEADER_LOGO_WIDTH, $header_title, $txt_header, array(0,0,0), array(0,0,0));
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));

        //margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        // ---------------------------------------------------------

        // set font
        $pdf->SetFont('helvetica', '', 12);

        // add a page
        $pdf->AddPage();

        // create HTML content
        $html = "";
            //cabecalho tabela
            $html .= '<table cellspacing="0" cellpadding="1" border="1" align="center">
			<thead>
			    <tr bgcolor="#00b33c">
					<th><font color="#ffffff">#</font></th>
					<th colspan="4"><font color="#ffffff">Nome:</font></th>
					<th><font color="#ffffff">&nbsp;</font></th>
					<th><font color="#ffffff">&nbsp;</font></th>
					<th><font color="#ffffff">&nbsp;</font></th>
					<th><font color="#ffffff">&nbsp;</font></th>
					<th><font color="#ffffff">&nbsp;</font></th>
					<th><font color="#ffffff">&nbsp;</font></th>
					<th><font color="#ffffff">&nbsp;</font></th>
					<th><font color="#ffffff">&nbsp;</font></th>
					<th><font color="#ffffff">&nbsp;</font></th>
					<th><font color="#ffffff">&nbsp;</font></th>
			    </tr>
			    </thead>';
            for($i=0;$i<sizeof($this->impressao);$i++){
                if($i%2 == 0) $html .= "<tr bgcolor=\"#ebebe0\" align=\"center\" nobr=\"true\">";
                else $html .= "<tr align=\"center\" nobr=\"true\">";
                $html .="
					<td >".$this->impressao[$i]->pos."</td>
					<td colspan=\"4\">".$this->impressao[$i]->nome."</td>
					<td >&nbsp;</td>
					<td >&nbsp;</td>
					<td >&nbsp;</td> 
					<td >&nbsp;</td>
					<td >&nbsp;</td>
					<td >&nbsp;</td>
					<td >&nbsp;</td>
					<td >&nbsp;</td>
					<td >&nbsp;</td>
					<td >&nbsp;</td>
				</tr>";

            }
            $html .="</table>";
            // output the HTML content
                    $pdf->writeHTML($html, true, false, false, false, '');
            // ---------------------------------------------------------
            //echo $html;//iprimindo teste
            // close and output PDF document colocar D para Download I para inline
                    $pdf->Output($pdfTitle.'.pdf', 'D');
        }


}


//============================================================+
// END OF FILE
//============================================================+

?>
