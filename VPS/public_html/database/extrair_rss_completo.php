<?php
$content = file_get_contents('F:/01_Projetos/Ativos/PROJETO_AME/apoio/projetoame.org_home_feed_.xml');
$xml = simplexml_load_string($content);
$file = fopen('F:/01_Projetos/Ativos/PROJETO_AME/apoio/eventos_rss_completo.csv', 'w');
fputcsv($file, ['titulo', 'link', 'data']);

if ($xml && isset($xml->channel->item)) {
    foreach ($xml->channel->item as $item) {
        fputcsv($file, [(string)$item->title, (string)$item->link, (string)$item->pubDate]);
    }
}
fclose($file);
echo "Extração concluída com sucesso.";
?>