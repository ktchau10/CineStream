<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Diretório base do projeto
$base_dir = __DIR__ . '/..';
$videos_dir = $base_dir . '/videos';

echo "Verificando e configurando diretório de uploads...\n\n";

// Verifica se o diretório base é gravável
echo "Diretório base ($base_dir):\n";
echo "- Existe: " . (file_exists($base_dir) ? 'Sim' : 'Não') . "\n";
echo "- Permissões: " . substr(sprintf('%o', fileperms($base_dir)), -4) . "\n";
echo "- Gravável: " . (is_writable($base_dir) ? 'Sim' : 'Não') . "\n\n";

// Cria o diretório de vídeos se não existir
echo "Diretório de vídeos ($videos_dir):\n";
if (!file_exists($videos_dir)) {
    echo "- Criando diretório de vídeos...\n";
    if (!mkdir($videos_dir, 0755, true)) {
        echo "ERRO: Não foi possível criar o diretório de vídeos.\n";
        echo "Erro: " . error_get_last()['message'] . "\n";
    }
}

// Verifica e ajusta as permissões
if (file_exists($videos_dir)) {
    echo "- Existe: Sim\n";
    echo "- Permissões atuais: " . substr(sprintf('%o', fileperms($videos_dir)), -4) . "\n";
    
    // Tenta ajustar as permissões
    if (!chmod($videos_dir, 0755)) {
        echo "ERRO: Não foi possível ajustar as permissões.\n";
    } else {
        echo "- Permissões ajustadas para: 0755\n";
    }
    
    echo "- Gravável: " . (is_writable($videos_dir) ? 'Sim' : 'Não') . "\n";
} else {
    echo "ERRO: Diretório de vídeos não existe e não pode ser criado.\n";
}

echo "\nInstruções para correção manual:\n";
echo "1. Execute no terminal:\n";
echo "   sudo mkdir -p " . $videos_dir . "\n";
echo "   sudo chmod -R 755 " . $videos_dir . "\n";
echo "   sudo chown -R daemon:daemon " . $videos_dir . "\n\n";
echo "2. Ou execute no terminal (para desenvolvimento apenas):\n";
echo "   sudo chmod -R 777 " . $videos_dir . "\n";
