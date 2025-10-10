<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$videos_dir = __DIR__ . '/../videos';
$test_file = $videos_dir . '/test.txt';

echo "Testando permissões do diretório de vídeos:\n\n";
echo "Diretório: $videos_dir\n";
echo "Proprietário atual: " . posix_getpwuid(fileowner($videos_dir))['name'] . "\n";
echo "Grupo atual: " . posix_getgrgid(filegroup($videos_dir))['name'] . "\n";
echo "Permissões: " . substr(sprintf('%o', fileperms($videos_dir)), -4) . "\n";
echo "PHP está rodando como usuário: " . exec('whoami') . "\n";
echo "Diretório é gravável? " . (is_writable($videos_dir) ? "Sim" : "Não") . "\n\n";

echo "Tentando criar arquivo de teste...\n";
if (file_put_contents($test_file, "Teste de escrita")) {
    echo "Sucesso! Arquivo de teste criado.\n";
    unlink($test_file);
    echo "Arquivo de teste removido.\n";
} else {
    echo "ERRO: Não foi possível criar o arquivo de teste.\n";
    $error = error_get_last();
    echo "Mensagem de erro: " . $error['message'] . "\n";
}
