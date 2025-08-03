<?php
include 'conexao.php';
include 'simple_html_dom.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $evento_id = $_POST['evento_id'];
    $url = filter_var($_POST['url'], FILTER_SANITIZE_URL);
    $seletor_itens = $_POST['seletor_itens'];
    $seletor_nome = $_POST['seletor_nome'];

    // Verificar se os campos obrigatórios foram preenchidos
    if (!$evento_id || !$url || !$seletor_itens || !$seletor_nome) {
        echo json_encode(['success' => false, 'message' => 'Preencha todos os campos obrigatórios.']);
        exit;
    }

    // Fazer o scraping
    $html = file_get_html($url);
    if ($html) {
        $expositores_encontrados = 0;
        foreach ($html->find($seletor_itens) as $item) {
            $nome = $item->find($seletor_nome, 0) ? trim($item->find($seletor_nome, 0)->plaintext) : 'Sem nome';
            $telefone = null; // Pode expandir para buscar contatos
            $email = null;
            $whatsapp = null;
            $instagram = null;

            // Inserir no banco
            $sql = "INSERT INTO expositores (evento_id, nome, telefone, email, whatsapp, instagram) 
                    VALUES (?, ?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE nome = VALUES(nome)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isssss", $evento_id, $nome, $telefone, $email, $whatsapp, $instagram);
            if ($stmt->execute()) {
                $expositores_encontrados++;
            }
            $stmt->close();
        }

        if ($expositores_encontrados > 0) {
            echo json_encode([
                'success' => true,
                'message' => "$expositores_encontrados expositores salvos com sucesso!"
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Nenhum expositor encontrado com os seletores fornecidos.']);
        }

        $html->clear();
        unset($html);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao acessar o URL fornecido. Verifique se está correto.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método inválido. Use POST.']);
}

$conn->close();
?>