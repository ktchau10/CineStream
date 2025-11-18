document.addEventListener('DOMContentLoaded', () => {
    // Configuração do botão de logout
    const logoutBtn = document.getElementById('logout-btn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', async () => {
            try {
                const response = await fetch('auth/logout.php', {
                    method: 'POST',
                    credentials: 'include'
                });
                
                if (response.ok) {
                    window.location.href = 'login.html';
                } else {
                    console.error('Erro ao fazer logout');
                }
            } catch (error) {
                console.error('Erro:', error);
            }
        });
    }

    const form = document.querySelector('form');
    if (!form) return; // Se não houver formulário, retorna (página index)
    
    const errorMessage = document.getElementById('error-message');
    const successMessage = document.getElementById('success-message');
    const submitButton = form.querySelector('button[type="submit"]');
    
    const isLoginPage = form.id === 'login-form';

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        // Reset messages
        errorMessage.style.display = 'none';
        successMessage.style.display = 'none';

        // Add loading state
        submitButton.classList.add('loading');
        submitButton.disabled = true;
        
        const formData = new FormData(form);
        const data = {};
        formData.forEach((value, key) => {
            data[key] = value;
        });

        try {
            const endpoint = isLoginPage ? 'auth/login.php' : 'auth/cadastro.php';
            const response = await fetch(endpoint, {
                method: 'POST',
                body: formData // Envia como multipart/form-data
            });

            const responseData = await response.json();

            if (responseData.success) {
                if (isLoginPage) {
                    successMessage.textContent = 'Login realizado com sucesso!';
                    successMessage.style.display = 'block';
                    
                    // ------------------ LÓGICA CORRIGIDA ------------------
                    // Verifica se 'user' e 'role' estão definidos, e se 'role' é estritamente 'admin'
                    const userRole = responseData.user && responseData.user.role ? responseData.user.role : 'user';
                    
                    const redirectPage = userRole === 'admin' 
                                        ? 'admin/dashboard.html' // Redireciona admin
                                        : 'index.html'; // Redireciona usuário comum
                    
                    // Redireciona para a página
                    setTimeout(() => {
                        // Usamos replace() para que o usuário não possa voltar à tela de login
                        window.location.replace(redirectPage); 
                    }, 1000);
                    // ------------------------------------------------------
                    
                } else{
                    successMessage.textContent = 'Cadastro realizado com sucesso!';
                    successMessage.style.display = 'block';
                    
                    // Redirect to login page
                    setTimeout(() => {
                        window.location.href = 'index.html';
                    }, 1500);
                }
            } else {
                errorMessage.textContent = responseData.error || 'Ocorreu um erro';
                errorMessage.style.display = 'block';
            }
        } catch (error) {
            console.error('Erro:', error);
            errorMessage.textContent = 'Erro ao conectar com o servidor. Verifique o console para mais detalhes.';
            errorMessage.style.display = 'block';
        } finally {
            // Remove loading state
            submitButton.classList.remove('loading');
            submitButton.disabled = false;
        }
    });
});
