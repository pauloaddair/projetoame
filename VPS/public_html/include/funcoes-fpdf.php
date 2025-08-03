<?php
require('./fpdf/fpdf.php');

class PDF extends FPDF
{
protected $B = 0;
protected $I = 0;
protected $U = 0;
protected $HREF = '';
protected $X = 0;
protected $Y = 5;

function WriteHTML($html)
{
    // HTML parser
    $html = str_replace("\n",' ',$html);
    $a = preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
    foreach($a as $i=>$e)
    {
        if($i%2==0)
        {
            // Text
            if($this->HREF)
                $this->PutLink($this->HREF,$e);
            else
                $this->Write(5,$e);
        }
        else
        {
            // Tag
            if($e[0]=='/')
                $this->CloseTag(strtoupper(substr($e,1)));
            else
            {
                // Extract attributes
                $a2 = explode(' ',$e);
                $tag = strtoupper(array_shift($a2));
                $attr = array();
                foreach($a2 as $v)
                {
                    if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))
                        $attr[strtoupper($a3[1])] = $a3[2];
                }
                $this->OpenTag($tag,$attr);
            }
        }
    }
}
function OpenTag($tag, $attr)
{
    // Opening tag
    if($tag=='B' || $tag=='I' || $tag=='U')
        $this->SetStyle($tag,true);
    if($tag=='A')
        $this->HREF = $attr['HREF'];
    if($tag=='BR')
        $this->Ln(5);
}

function CloseTag($tag)
{
    // Closing tag
    if($tag=='B' || $tag=='I' || $tag=='U')
        $this->SetStyle($tag,false);
    if($tag=='A')
        $this->HREF = '';
}

function SetStyle($tag, $enable)
{
    // Modify style and select corresponding font
    $this->$tag += ($enable ? 1 : -1);
    $style = '';
    foreach(array('B', 'I', 'U') as $s)
    {
        if($this->$s>0)
            $style .= $s;
    }
    $this->SetFont('',$style);
}

function PutLink($URL, $txt)
{
    // Put a hyperlink
    $this->SetTextColor(0,0,255);
    $this->SetStyle('U',true);
    $this->Write(5,$txt,$URL);
    $this->SetStyle('U',false);
    $this->SetTextColor(0);
}
	
function Header()
{
    global $title;

    // Arial bold 15
    $this->SetFont('Arial','B',15);
    // Calculate width of title and position
    $w = $this->GetStringWidth($title)+6;
    // $this->SetX((210-$w)/2);
    // Colors of frame, background and text
    $this->SetDrawColor(0,80,180);
    $this->SetFillColor(255,255,255);
    $this->SetTextColor(220,50,50);
    $this->SetTextColor(0,0,0);
    // Thickness of frame (1 mm)
    $this->SetLineWidth(1);
    // Title
	$titlelines = explode("<br>",$title);
	foreach ($titlelines as $linha){
   		$this->Cell(0,7,$linha,0,1,'C',true);	
	}
    //    $this->MultiCell(0,5,$title,0,1,"C",false);
    // Line break
    $this->Ln(10);
}

function Footer()
{
    // Position at 1.5 cm from bottom
    $this->SetY(-15);
    // Arial italic 8
    $this->SetFont('Arial','I',8);
    // Text color in gray
    $this->SetTextColor(128);
	$rodape = iconv("UTF-8", "ISO-8859-1", 'Página '.$this->PageNo().'/{nb}');
    // Page number
    $this->Cell(0,10,$rodape,0,0,'C');
}

function ChapterTitle($num, $label)
{
	$label1 = iconv("UTF-8", "ISO-8859-1", "Capítulo". $num .":". $label);
    // Arial 12
    $this->SetFont('Arial','B',14);
    // Background color
    // $this->SetFillColor(200,220,255);
    $this->SetFillColor(255,255,255);
    $this->SetTextColor(128);
  	// Title
//  $this->Cell(0,6,$label1,0,1,'L',true);
    $this->Cell(0,6,$label,0,1,'C',true);
    // Line break
    $this->Ln(4);
}

function ChapterBody($texto)
{
    // Read text file
    // $txt = file_get_contents($file);
	// $txt = iconv("UTF-8", "ISO-8859-1", $texto);
    // Times 12
    $this->SetFont('Times','',14);
    // Output justified text
    // $this->MultiCell(0,5,$texto);
	$this->WriteHTML($texto);
    // Line break
//    $this->Ln();
    // Mention in italics
//    $this->SetFont('','I');
//    $this->Cell(0,5,'(end of excerpt)');
}

	function PrintChapter($num, $title, $file)
	{
		$title = iconv("UTF-8", "ISO-8859-1", $title);
		$file = iconv("UTF-8", "ISO-8859-1", $file);
		$this->AddPage();
		$this->ChapterTitle($num,$title);
		$this->ChapterBody($file);
	}
}
