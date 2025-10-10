<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Configurações do PHP para Upload:\n\n";

$config_values = array(
    'upload_max_filesize',
    'post_max_size',
    'memory_limit',
    'max_execution_time',
    'max_input_time'
);

foreach ($config_values as $value) {
    echo "{$value}: " . ini_get($value) . "\n";
}

echo "\nDiretórios:\n";
echo "upload_tmp_dir: " . ini_get('upload_tmp_dir') . "\n";
echo "Diretório temporário atual: " . sys_get_temp_dir() . "\n";

echo "\nPermissões do diretório temporário:\n";
$tmp_dir = sys_get_temp_dir();
echo "Existe: " . (file_exists($tmp_dir) ? 'Sim' : 'Não') . "\n";
echo "Gravável: " . (is_writable($tmp_dir) ? 'Sim' : 'Não') . "\n";
echo "Permissões: " . substr(sprintf('%o', fileperms($tmp_dir)), -4) . "\n";

echo "\nDiretório de vídeos:\n";
$videos_dir = __DIR__ . '/../videos';
echo "Caminho: {$videos_dir}\n";
echo "Existe: " . (file_exists($videos_dir) ? 'Sim' : 'Não') . "\n";
echo "Gravável: " . (is_writable($videos_dir) ? 'Sim' : 'Não') . "\n";
echo "Permissões: " . substr(sprintf('%o', fileperms($videos_dir)), -4) . "\n";
echo "Proprietário: " . posix_getpwuid(fileowner($videos_dir))['name'] . "\n";
echo "Grupo: " . posix_getgrgid(filegroup($videos_dir))['name'] . "\n";

echo "\nInformações do PHP:\n";
echo "Usuário do PHP: " . exec('whoami') . "\n";
echo "Limite de memória: " . ini_get('memory_limit') . "\n";
