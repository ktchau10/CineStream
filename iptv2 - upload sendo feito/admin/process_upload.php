<?php
session_start();

// Configura o diretório temporário
$temp_dirs = [
    __DIR__ . '/../temp/php',                              // Nossa primeira escolha
    '/Applications/XAMPP/xamppfiles/temp',                 // Diretório temp do XAMPP
    '/Applications/XAMPP/xamppfiles/temp/php',             // Subdiretório PHP do XAMPP
    sys_get_temp_dir()                                     // Diretório temporário do sistema
];

$temp_dir_set = false;
foreach ($temp_dirs as $dir) {
    if (is_dir($dir) && is_writable($dir)) {
        ini_set('upload_tmp_dir', $dir);
        error_log("Usando diretório temporário: " . $dir);
        $temp_dir_set = true;
        break;
    }
}

if (!$temp_dir_set) {
    error_log("AVISO: Nenhum diretório temporário gravável encontrado!");
}

// Função para retornar resposta JSON
function sendJsonResponse($success, $message) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message
    ]);
    exit;
}

// Função para sanitizar nome do arquivo
function sanitizeFileName($fileName) {
    // Remove caracteres especiais e espaços
    $fileName = preg_replace('/[^a-zA-Z0-9_-]/', '', $fileName);
    return $fileName;
}

// Verifica se o usuário está logado e é administrador
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    sendJsonResponse(false, 'Acesso negado. Você precisa ser um administrador para realizar esta ação.');
}

// Verifica se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(false, 'Método não permitido.');
}

// Validação do ID do TMDB
$tmdb_id = filter_input(INPUT_POST, 'tmdb_id', FILTER_VALIDATE_INT);
if (!$tmdb_id) {
    sendJsonResponse(false, 'ID do TMDB inválido.');
}

// Verifica se um arquivo foi enviado
if (!isset($_FILES['video_file'])) {
    error_log('Nenhum arquivo enviado');
    sendJsonResponse(false, 'Nenhum arquivo foi enviado.');
}

if ($_FILES['video_file']['error'] !== UPLOAD_ERR_OK) {
    $upload_errors = array(
        UPLOAD_ERR_INI_SIZE => 'O arquivo excede o tamanho máximo permitido pelo PHP (upload_max_filesize)',
        UPLOAD_ERR_FORM_SIZE => 'O arquivo excede o tamanho máximo permitido pelo formulário',
        UPLOAD_ERR_PARTIAL => 'O upload foi interrompido',
        UPLOAD_ERR_NO_FILE => 'Nenhum arquivo foi enviado',
        UPLOAD_ERR_NO_TMP_DIR => 'Diretório temporário não encontrado',
        UPLOAD_ERR_CANT_WRITE => 'Falha ao escrever o arquivo no disco',
        UPLOAD_ERR_EXTENSION => 'Upload bloqueado por uma extensão do PHP'
    );
    
    $error_code = $_FILES['video_file']['error'];
    $error_message = $upload_errors[$error_code] ?? 'Erro desconhecido no upload';
    error_log("Erro no upload: {$error_message} (código: {$error_code})");
    sendJsonResponse(false, 'Erro no upload do arquivo: ' . $error_message);
}

// Log do upload
error_log("Informações do arquivo:");
error_log("Nome original: " . $_FILES['video_file']['name']);
error_log("Tamanho: " . ($_FILES['video_file']['size'] / 1024 / 1024) . " MB");
error_log("Arquivo temporário: " . $_FILES['video_file']['tmp_name']);
error_log("Tipo MIME reportado: " . $_FILES['video_file']['type']);

// Validação do tipo de arquivo
if ($_FILES['video_file']['tmp_name']) {
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $_FILES['video_file']['tmp_name']);
    finfo_close($finfo);
    error_log("Tipo MIME detectado: " . $mime_type);

    // Aceita tanto video/mp4 quanto application/mp4 (alguns sistemas reportam diferente)
    if ($mime_type !== 'video/mp4' && $mime_type !== 'application/mp4') {
        sendJsonResponse(false, 'Tipo de arquivo inválido. Apenas arquivos MP4 são permitidos. Tipo detectado: ' . $mime_type);
    }
} else {
    error_log("ERRO: Arquivo temporário não encontrado!");
    sendJsonResponse(false, 'Erro no upload: arquivo temporário não encontrado');
}

// Prepara o diretório de destino
$upload_dir = __DIR__ . '/../videos/';
if (!is_dir($upload_dir)) {
    if (!mkdir($upload_dir, 0755, true)) {
        error_log("Erro ao criar diretório: " . $upload_dir);
        sendJsonResponse(false, 'Erro ao criar diretório de upload.');
    }
}

// Define o nome do arquivo
$file_extension = '.mp4';
if (!empty($_POST['custom_filename'])) {
    // Usa o nome personalizado se fornecido
    $filename = sanitizeFileName($_POST['custom_filename']);
    if (empty($filename)) {
        sendJsonResponse(false, 'Nome de arquivo inválido. Use apenas letras, números, hífen e underscore.');
    }
} else {
    // Gera um nome único se nenhum nome personalizado for fornecido
    $original_filename = pathinfo($_FILES['video_file']['name'], PATHINFO_FILENAME);
    $filename = hash('sha256', time() . $original_filename);
}

// Adiciona a extensão e verifica se o arquivo já existe
$filename = $filename . $file_extension;
$upload_path = $upload_dir . $filename;
$relative_path = '/videos/' . $filename;

// Verifica se o arquivo já existe
if (file_exists($upload_path)) {
    sendJsonResponse(false, 'Um arquivo com este nome já existe. Escolha outro nome ou deixe em branco para gerar automaticamente.');
}

// Move o arquivo para o diretório final
error_log("Tentando mover arquivo de {$_FILES['video_file']['tmp_name']} para {$upload_path}");
error_log("Tamanho do arquivo: " . ($_FILES['video_file']['size'] / 1024 / 1024) . " MB");

if (!move_uploaded_file($_FILES['video_file']['tmp_name'], $upload_path)) {
    $error = error_get_last();
    error_log("Erro ao mover arquivo: " . ($error['message'] ?? 'Erro desconhecido'));
    error_log("Permissões do destino: " . substr(sprintf('%o', fileperms(dirname($upload_path))), -4));
    sendJsonResponse(false, 'Erro ao mover o arquivo. Verifique os logs do PHP para mais detalhes.');
}

// Conexão com o banco de dados
try {
    require_once __DIR__ . '/../database/config.php';
    
    // Insere o registro na tabela filmes_locais
    $pdo = getDbConnection();
    $stmt = $pdo->prepare('INSERT INTO filmes_locais (tmdb_movie_id, video_path) VALUES (?, ?)');
    if (!$stmt->execute([$tmdb_id, $relative_path])) {
        // Em caso de erro no banco, remove o arquivo
        unlink($upload_path);
        sendJsonResponse(false, 'Erro ao registrar o filme no banco de dados.');
    }
    
    sendJsonResponse(true, "Filme uploadado com sucesso como '$filename'!");
    
} catch (PDOException $e) {
    // Em caso de erro no banco, remove o arquivo
    unlink($upload_path);
    sendJsonResponse(false, 'Erro ao conectar com o banco de dados: ' . $e->getMessage());
}