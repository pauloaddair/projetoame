<?php
setlocale(LC_ALL, 'pt_BR');
include_once('./include/conexao.php');
include_once('./include/funcoes.php');
include_once('./include/funcoes-fpdf.php');

$pdf = new PDF();
$title = iconv("UTF-8", "ISO-8859-1", 'TERMO DE AUTORIZAÇÃO DE USO DE IMAGEM, 
<br>VOZ E RESPECTIVA CESSÃO DE DIREITOS
<br>(LEI N. 9.610/98)');
$pdf->SetTitle($title);
$pdf->SetAuthor('A.B.I.A.T. - Atendentes Muito Especiais');
$txt = "<br><br>Pelo presente instrumento, eu,<b>@resp</b>, portador do RG/RNE/Passaporte nº <b>@rg</b> e do CPF nº <b>@cpfresp</b>, domiciliado na cidade <b>@cidade</b>, no estado de <b>@estado</b>, responsável pelo atendente especial <b>@atendente</b>, portador do CPF nº <b>@cpf</b>, <b>AUTORIZO</b>, de forma gratuita e sem qualquer ônus, a <i>Associação Brasileira de Inclusão Através do Trabalho (Atendentes Muito Especiais)</i>, a utilização da(s) imagem(ns) e/ou voz nos eventos em o atendente especial participar, e em sua divulgação, se houver, em todos os meios de divulgação possíveis, quer sejam na mídia impressa (livros, catálogos, revistas, jornais, entre outros), televisiva (propagandas para televisão aberta e/ou fechada, vídeos, filmes, entre outros), radiofônica (programas de rádio/podcasts), internet, banco de dados informatizados, multimídia, entre outros, e nos meios de comunicação interna, como jornais e periódicos em geral, na forma de impresso, voz e imagem.
<br><br><br><br>A presente autorização e cessão são outorgadas livre e espontaneamente, em caráter gratuito, não incorrendo à autorizada qualquer custo ou ônus, seja a que título for, sendo que estas são firmadas em caráter irrevogável, irretratável, e por prazo indeterminado, obrigando, inclusive, eventuais herdeiros e sucessores outorgantes.";
$pdf->PrintChapter(1,'AUTORIZAÇÃO',$txt);
$pdf->Ln(30);
$txt = iconv("UTF-8", "ISO-8859-1//IGNORE","São Paulo, ");
$pdf->Cell(0,5,$txt.strftime("%A, %e de %B de %G"),0,1,"R");
$pdf->Ln(30);
$pdf->Line($pdf->GetPageWidth()/2,$pdf->GetY(),$pdf->GetPageWidth()-10,$pdf->GetY());
$pdf->AliasNbPages();
//$pdf->PrintChapter(2,'THE PROS AND CONS','./fpdf/tutorial/20k_c2.txt');
$pdf->Output("F","./docs/autorização.pdf",1);
?>
<a href="./docs/autorização.pdf" target="_blank">Autorização</a>