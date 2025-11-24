<?php
// NOVO: Desativa a exibição de erros para garantir que apenas JSON seja retornado
error_reporting(E_ALL);
ini_set('display_errors', 0); // <--- Adicionar esta linha

require_once __DIR__ . '/../../../database/config.php';
require_once __DIR__ . '/../../../utils/functions.php';

// Não requer autenticação
// session_start(); // Não precisa de sessão

try {
    $pdo = getDbConnection();
    
    // Busca todo o conteúdo ativo
    $stmt = $pdo->prepare('
        SELECT tmdb_id, media_type, titulo, poster_path, vote_average, release_year 
        FROM catalogo_curado 
        ORDER BY data_adicao DESC
    ');
    $stmt->execute();
    
    $conteudo = $stmt->fetchAll(PDO::FETCH_ASSOC);

    jsonResponse([
        'success' => true,
        'conteudo' => $conteudo
    ]);

} catch (Exception $e) {
    error_log($e->getMessage());
    jsonResponse(['error' => 'Erro ao listar conteúdo ativo'], 500);
}