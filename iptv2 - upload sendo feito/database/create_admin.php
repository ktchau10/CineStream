<?php
require_once '../config/database.php';

// Dados do administrador
$admin = [
    'nome' => 'Administrador',
    'email' => 'admin@cinestream.com',
    'senha' => 'admin123', // Você deve alterar esta senha em produção
    'role' => 'admin'
];

try {
    // Verifica se o admin já existe
    $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email = ?');
    $stmt->execute([$admin['email']]);
    
    if ($stmt->fetch()) {
        die('Administrador já existe!' . PHP_EOL);
    }

    // Hash da senha
    $senha_hash = password_hash($admin['senha'], PASSWORD_DEFAULT);

    // Insere o administrador
    $stmt = $pdo->prepare('INSERT INTO usuarios (nome, email, senha_hash, role) VALUES (?, ?, ?, ?)');
    $stmt->execute([
        $admin['nome'],
        $admin['email'],
        $senha_hash,
        $admin['role']
    ]);

    echo 'Administrador criado com sucesso!' . PHP_EOL;
    echo 'Email: ' . $admin['email'] . PHP_EOL;
    echo 'Senha: ' . $admin['senha'] . PHP_EOL;
    echo 'IMPORTANTE: Altere a senha após o primeiro login!' . PHP_EOL;

} catch (PDOException $e) {
    die('Erro ao criar administrador: ' . $e->getMessage() . PHP_EOL);
}
