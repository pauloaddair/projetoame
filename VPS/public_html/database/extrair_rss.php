<?php
$xml_content = file_get_contents('F:/01_Projetos/Ativos/PROJETO_AME/apoio/feed_rss_temp.xml');
// Remove namespaces para facilitar o SimpleXML
$xml_content = preg_replace('/<(name|title|link|pubDate|item)>/i', '<$1>', $xml_content);
$xml = simplexml_load_string($xml_content);

$file = fopen('F:/01_Projetos/Ativos/PROJETO_AME/apoio/eventos_rss_robusto.csv', 'w');
fputcsv($file, ['titulo', 'link', 'data']);

if ($xml && isset($xml->channel->item)) {
    foreach ($xml->channel->item as $item) {
        $titulo = (string) $item->title;
        $link = (string) $item->link;
        $data = (string) $item->pubDate;
        fputcsv($file, [$titulo, $link, $data]);
        echo "Extraído: $titulo\n";
    }
}
fclose($file);
?>