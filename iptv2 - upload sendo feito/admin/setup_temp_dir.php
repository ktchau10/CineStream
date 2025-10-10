<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Configurando diretório temporário para uploads...\n\n";

// Define o caminho do novo diretório temporário
$temp_dir = __DIR__ . '/../temp/php';
$videos_dir = __DIR__ . '/../videos';

echo "Criando diretórios necessários...\n";

// Criar diretório temp se não existir
if (!file_exists($temp_dir)) {
    echo "Criando diretório temporário em: $temp_dir\n";
    if (!mkdir($temp_dir, 0777, true)) {
        echo "ERRO: Não foi possível criar o diretório temporário.\n";
        $error = error_get_last();
        echo "Erro: " . $error['message'] . "\n\n";
    }
}

// Ajustar permissões
echo "\nAjustando permissões...\n";
echo "Comando para executar no terminal:\n\n";
echo "sudo chmod -R 777 " . dirname($temp_dir) . "\n";
echo "sudo chown -R _www:_www " . dirname($temp_dir) . "\n\n";

echo "Estado atual dos diretórios:\n";
echo "--------------------------------\n";
echo "Diretório temporário ($temp_dir):\n";
echo "Existe: " . (file_exists($temp_dir) ? 'Sim' : 'Não') . "\n";
echo "Gravável: " . (is_writable($temp_dir) ? 'Sim' : 'Não') . "\n";
if (file_exists($temp_dir)) {
    echo "Permissões: " . substr(sprintf('%o', fileperms($temp_dir)), -4) . "\n";
    echo "Proprietário: " . posix_getpwuid(fileowner($temp_dir))['name'] . "\n";
    echo "Grupo: " . posix_getgrgid(filegroup($temp_dir))['name'] . "\n";
}

echo "\nDiretório de vídeos ($videos_dir):\n";
echo "Existe: " . (file_exists($videos_dir) ? 'Sim' : 'Não') . "\n";
echo "Gravável: " . (is_writable($videos_dir) ? 'Sim' : 'Não') . "\n";
if (file_exists($videos_dir)) {
    echo "Permissões: " . substr(sprintf('%o', fileperms($videos_dir)), -4) . "\n";
    echo "Proprietário: " . posix_getpwuid(fileowner($videos_dir))['name'] . "\n";
    echo "Grupo: " . posix_getgrgid(filegroup($videos_dir))['name'] . "\n";
}

// Criar arquivo de teste no diretório temp
echo "\nTestando escrita no diretório temporário...\n";
$test_file = $temp_dir . '/test.txt';
if (file_put_contents($test_file, "Teste")) {
    echo "✅ Sucesso: Arquivo de teste criado com sucesso\n";
    unlink($test_file);
    echo "✅ Sucesso: Arquivo de teste removido com sucesso\n";
} else {
    echo "❌ ERRO: Não foi possível criar arquivo de teste\n";
    $error = error_get_last();
    echo "Erro: " . $error['message'] . "\n";
}
