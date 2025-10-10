<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../database/config.php';

try {
    echo "Iniciando criação do administrador...\n";
    
    // Teste de conexão
    $pdo = getDbConnection();
    echo "Conexão com o banco estabelecida com sucesso!\n";

    // Dados do administrador
    $admin = [
        'nome' => 'Administrador',
        'email' => 'admin@cinestream.com',
        'senha' => 'admin123',
        'role' => 'admin'
    ];

    echo "Verificando se o administrador já existe...\n";
    $stmt = $pdo->prepare('SELECT id, role FROM usuarios WHERE email = ?');
    $stmt->execute([$admin['email']]);
    $usuario_existente = $stmt->fetch();

    if ($usuario_existente) {
        if ($usuario_existente['role'] !== 'admin') {
            echo "Usuário existe mas não é admin. Atualizando role...\n";
            $stmt = $pdo->prepare('UPDATE usuarios SET role = ? WHERE email = ?');
            $stmt->execute(['admin', $admin['email']]);
            echo "Role atualizado para admin!\n";
        } else {
            echo "Administrador já existe!\n";
        }
        
        // Atualiza a senha
        echo "Atualizando senha do administrador...\n";
        $senha_hash = password_hash($admin['senha'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('UPDATE usuarios SET senha_hash = ? WHERE email = ?');
        $stmt->execute([$senha_hash, $admin['email']]);
        echo "Senha atualizada com sucesso!\n";
    } else {
        echo "Criando novo administrador...\n";
        $senha_hash = password_hash($admin['senha'], PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare('INSERT INTO usuarios (nome, email, senha_hash, role) VALUES (?, ?, ?, ?)');
        $stmt->execute([
            $admin['nome'],
            $admin['email'],
            $senha_hash,
            $admin['role']
        ]);
        
        echo "Administrador criado com sucesso!\n";
    }

    echo "\nInformações do administrador:\n";
    echo "Email: " . $admin['email'] . "\n";
    echo "Senha: " . $admin['senha'] . "\n";
    echo "\nIMPORTANTE: Altere a senha após o primeiro login!\n";

} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
