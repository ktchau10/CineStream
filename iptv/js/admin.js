// iptv/js/admin.js

document.addEventListener('DOMContentLoaded', () => {
    // 1. Verificar se o usuário é admin (poderia ser feito no backend, mas aqui é para segurança extra no frontend)
    // Uma requisição inicial segura ao endpoint de listagem já faz isso, graças ao requireAdmin()

    const listContainer = document.getElementById('movie-list-container');
    const addMovieBtn = document.getElementById('add-movie-btn');

    // Funções de UI
    const showLoading = () => listContainer.innerHTML = '<p id="loading-message">Carregando filmes...</p>';
    const showError = (msg) => listContainer.innerHTML = `<p class="error">${msg}</p>`;

    // 2. Função READ (Listar)
    async function fetchLocalMovies() {
        showLoading();
        try {
            const response = await fetch('../api/admin/list_local.php');
            
            if (response.status === 403) { // Acesso negado
                showError('Acesso não autorizado. Você não é um administrador.');
                return;
            }
            if (!response.ok) throw new Error('Falha ao buscar filmes locais.');

            const data = await response.json();
            if (data.success) {
                renderMovieTable(data.filmes);
            } else {
                showError(data.error || 'Erro ao carregar lista de filmes.');
            }

        } catch (error) {
            console.error('Erro de rede:', error);
            showError('Erro de conexão com o servidor.');
        }
    }

    // 3. Renderizar Tabela
    function renderMovieTable(filmes) {
        if (filmes.length === 0) {
            listContainer.innerHTML = '<p>Nenhum filme local cadastrado.</p>';
            return;
        }

        let tableHtml = `
            <table class="movie-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>TMDB ID</th>
                        <th>Path do Vídeo</th>
                        <th>Data Upload</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
        `;

        filmes.forEach(f => {
            tableHtml += `
                <tr data-id="${f.id}">
                    <td>${f.id}</td>
                    <td>${f.tmdb_movie_id}</td>
                    <td>${f.video_path}</td>
                    <td>${f.data_upload.split(' ')[0]}</td>
                    <td class="action-btns">
                        <button class="btn-edit" data-id="${f.id}">Editar</button>
                        <button class="btn-delete" data-id="${f.id}">Excluir</button>
                    </td>
                </tr>
            `;
        });

        tableHtml += `</tbody></table>`;
        listContainer.innerHTML = tableHtml;

        // Anexar listeners para Editar e Excluir
        document.querySelectorAll('.btn-delete').forEach(btn => btn.addEventListener('click', handleDelete));
        document.querySelectorAll('.btn-edit').forEach(btn => btn.addEventListener('click', handleEdit));
    }

    // 4. Funções CRUD (Stubs - a serem implementadas nos endpoints PHP)
    function handleDelete(e) {
        const id = e.target.dataset.id;
        if (confirm(`Tem certeza que deseja excluir o filme ID ${id}?`)) {
            // Lógica para chamar api/admin/delete_local.php (requer implementação)
            alert(`Implementar DELETE para ID: ${id}`);
            // Após sucesso, chamar fetchLocalMovies();
        }
    }

    function handleEdit(e) {
        const id = e.target.dataset.id;
        // Lógica para abrir modal/form para edição, chamar api/admin/update_local.php (requer implementação)
        alert(`Implementar EDIT para ID: ${id}`);
    }

    addMovieBtn.addEventListener('click', () => {
         // Lógica para abrir modal/form para adição, chamar api/admin/create_local.php (requer implementação)
         alert('Implementar formulário de Adição de Filme.');
    });

    // Início
    fetchLocalMovies();
});