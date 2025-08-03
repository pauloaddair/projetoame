<?php
include_once('include/conexao.php');
include_once('include/nav.php');
include_once('include/head.php');
// Caminho do diretório que você quer ler
$diretorio = 'docs/';

// Extensões de arquivos compactados
$extensoes_compactados = ['zip', 'rar', 'tar', 'gz', '7z','pdf','doc','docx'];

// Obter todos os arquivos do diretório
$arquivos = scandir($diretorio);

// Filtrar os arquivos compactados
$arquivos_compactados = array_filter($arquivos, function ($arquivo) use ($diretorio, $extensoes_compactados) {
    // Caminho completo do arquivo
    $caminhoArquivo = $diretorio . DIRECTORY_SEPARATOR . $arquivo;

    // Verifica se é um arquivo e tem uma das extensões especificadas
    return is_file($caminhoArquivo) && in_array(pathinfo($arquivo, PATHINFO_EXTENSION), $extensoes_compactados);
});

// Exibir os arquivos compactados encontrados
if (!empty($arquivos_compactados)) {
    echo "<body><div class='container mt-5'><header class='mt-5'><h1 class='text-center'>Arquivos compactados encontrados:</h1></header><div class='row'><ol>";
    foreach ($arquivos_compactados as $arquivo) {
        echo "<li><a href='$diretorio$arquivo'>".$arquivo . "</a></li>";
    }
} else {
    echo "Nenhum arquivo compactado encontrado no diretório.";
}
echo "</ol></div></div>";
include_once('include/footer.php');
echo "</body>";
include_once('include/scripts.php');
include_once('include/end.php');
?>