<?php
/*
 * Created on 24/lug/07
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 require('fpdf.php');


	$wk_logo=getcwd().'/immagini/logo_new.jpg';

    define('FPDF_LOGO',$wk_logo);
    define('FPDF_VER_OFFSET',50);



 class PDF extends FPDF
{

	private $widths;
	private $aligns;
	private $_reportTitle;

	function SetWidths($w)
	{
	    //Set the array of column widths
	    $this->widths=$w;
	}

	function SetAligns($a)
	{
	    //Set the array of column alignments
	    $this->aligns=$a;
	}
	function SetReportTitle($text)
	{
	    //Set the array of column widths
	    $this->_reportTitle=$text;
	}

	function Row($data)
	{
	    //Calculate the height of the row
	    $nb=0;
	    for($i=0;$i<count($data);$i++)
	        $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
	    $h=4*$nb;
	    //Issue a page break first if needed
	    $this->CheckPageBreak($h);
	    //Draw the cells of the row
	    for($i=0;$i<count($data);$i++)
	    {
	        $w=$this->widths[$i];
	        $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
	        //Save the current position
	        $x=$this->GetX();
	        $y=$this->GetY();
	        //Draw the border
	        $this->Rect($x,$y,$w,$h);
	        //Print the text
	        $this->MultiCell($w,4,$data[$i],0,$a);
	        //Put the position to the right of the cell
	        $this->SetXY($x+$w,$y);
	    }
	    //Go to the next line
	    $this->Ln($h);
	}

	function CheckPageBreak($h)
	{
	    //If the height h would cause an overflow, add a new page immediately
	    if($this->GetY()+$h>$this->PageBreakTrigger)
	        $this->AddPage($this->CurOrientation);
	}

	function NbLines($w,$txt)
	{
	    //Computes the number of lines a MultiCell of width w will take
	    $cw=&$this->CurrentFont['cw'];
	    if($w==0)
	        $w=$this->w-$this->rMargin-$this->x;
	    $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
	    $s=str_replace("\r",'',$txt);
	    $nb=strlen($s);
	    if($nb>0 and $s[$nb-1]=="\n")
	        $nb--;
	    $sep=-1;
	    $i=0;
	    $j=0;
	    $l=0;
	    $nl=1;
	    while($i<$nb)
	    {
	        $c=$s[$i];
	        if($c=="\n")
	        {
	            $i++;
	            $sep=-1;
	            $j=$i;
	            $l=0;
	            $nl++;
	            continue;
	        }
	        if($c==' ')
	            $sep=$i;
	        $l+=$cw[$c];
	        if($l>$wmax)
	        {
	            if($sep==-1)
	            {
	                if($i==$j)
	                    $i++;
	            }
	            else
	                $i=$sep+1;
	            $sep=-1;
	            $j=$i;
	            $l=0;
	            $nl++;
	        }
	        else
	            $i++;
	    }
	    return $nl;
	}

	private $_para;
	function setPara($value){
		$this->_para=$value;
	}
	function Cover(){
		$this->AddPage();
	}

	function Header()
	{
			$this->Image(FPDF_LOGO,16,6,36);
		     $this->SetY(FPDF_VER_OFFSET);
		    $this->SetFont('Arial','B',14);
			$this->SetY(5);

			$this->Cell(50,12,'','',0,'C',0);
		    $this->Cell(140,12,$this->_reportTitle[0],'',1,'C',0);
		    $this->Cell(50,12,'','',0,'C',0);
		    $this->Cell(140,12,$this->_reportTitle[1],'',1,'C',0);
		    $this->Ln();

	}
	function Footer()
	{
	    //Page footer
	    $disclaimer="Soprintendenza per i Beni Architettonici e Paesaggistici per le " .
	    		"province di Verona, Rovigo e Vicenza";

	    $this->SetY(-15);
	    $this->SetFont('Arial','B',8);
	    $this->MultiCell(190,4,$disclaimer,1,'C');

	}

	function printBody($data)
	{
	    //Colors, line width and bold font
	    $header=array('Vs.Rif.','Arrivata il','Data Reg.','Nr. Prot.','Oggetto','Esito');
	    $this->SetFillColor(211,211,211);
	    $this->SetTextColor(0);
	    $this->SetDrawColor(0,0,0);
	    $this->SetLineWidth(.2);
//	    $this->SetFont('','B');
		$this->SetFont('Arial','',6);
	    //Header
	    $w=array(20,20,20,20,80,30);
	    $this->SetWidths($w);

	    for($i=0;$i<count($header);$i++)
	        $this->Cell($w[$i],5,$header[$i],1,0,'C',1);
	    $this->Ln();

	    //Color and font restoration
	    $this->SetFillColor(224,235,255);
	    $this->SetTextColor(0);
	    $this->SetFont('');
	    //Data
	    $fill=0;
	    foreach($data as $row)
	    {
			$this->Row(array($row['NUMERORIFERIMENTO'] . ' / ' .$row['DATADOCUMENTO'] ,$row['DATAARRIVO'],$row['DATAREGISTRAZIONE'],$row['NUMEROREGISTRAZIONE'],$row['OGGETTO_ESPI'],$row['ESITO']));
	    }
	    $this->Cell(array_sum($w),0,'','T');
	}



	function ChapterTitle($chapData)
	{
	    //Title
	    $this->SetFont('Arial','',12);
	    $this->SetFillColor(200,220,255);
		$this->SetWidths(array(40,150));
	    $this->Cell(40,6,"Comune",1,0,'L',1);
	    $this->Cell(150,6,$chapData['COMUNE'],1,0,'L',1);
	    $this->Ln();

	    $this->Cell(40,6,"Identificativo",1,0,'L',1);
		$this->Cell(150,6,$chapData['IDENTIFICATIVO'],1,0,'L',1);
	    $this->Ln();

	    $this->Cell(40,6,"Ubicazione",1,0,'L',1);
		$this->Cell(150,6,$chapData['BLOCCO'].' - '.$chapData['UBICAZIONE'],1,0,'L',1);
	    $this->Ln();

	    $this->Ln(3);

	    //Colors, line width and bold font
	    $this->SetFillColor(211,211,211);
	    $this->SetTextColor(0);
	    $this->SetDrawColor(0,0,0);


	    //Save ordinate
	    $this->y0=$this->GetY();
	}

	function PrintChapter($data)
	{
	    //Add chapter
	    $this->AddPage();
	    $this->ChapterTitle($data[0]);
		$this->printBody($data);

	}
}
?>