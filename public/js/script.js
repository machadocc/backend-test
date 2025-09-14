document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById('modalForm');
    const modalBody = document.getElementById('modalBody');
    const fecharModal = document.getElementById('fecharModal');
    const notificacoes = document.getElementById('notificacoes');

    /**
     * Exibe um toast de notificação.
     * @param {string} mensagem
     * @param {string} [tipo='success']
     * @param {number} [duracao=4000]
     */
    function mostrarToast(mensagem, tipo = 'success', duracao = 4000) {
        const toast = document.createElement('div');
        toast.className = `toast ${tipo}`;
        toast.textContent = mensagem;
        notificacoes.appendChild(toast);

        setTimeout(() => toast.classList.add('show'), 100);
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => notificacoes.removeChild(toast), 300);
        }, duracao);
    }

    /**
     * Aplica máscara de CPF em inputs.
     */
    function aplicarMascaraCPF() {
        const cpfInput = modalBody.querySelector('#cpf');
        if (!cpfInput) return;

        cpfInput.addEventListener('input', () => {
            let value = cpfInput.value.replace(/\D/g, '').slice(0, 11);
            if (value.length > 3) value = value.replace(/^(\d{3})(\d)/, '$1.$2');
            if (value.length > 6) value = value.replace(/^(\d{3})\.(\d{3})(\d)/, '$1.$2.$3');
            if (value.length > 9) value = value.replace(/^(\d{3})\.(\d{3})\.(\d{3})(\d)/, '$1.$2.$3-$4');
            cpfInput.value = value;
        });
    }

    /**
     * Abre modal carregando conteúdo via fetch.
     * @param {string} url
     */
    async function abrirModal(url) {
        try {
            const response = await fetch(url);
            const html = await response.text();
            modalBody.innerHTML = html;
            modal.style.display = 'block';
            aplicarMascaraCPF();
            adicionarSubmitAjax();
        } catch (err) {
            mostrarToast("Erro ao carregar o formulário", "error");
            console.error(err);
        }
    }

    /**
     * Configura envio de formulário via fetch (AJAX).
     */
    function adicionarSubmitAjax() {
        const form = modalBody.querySelector('form');
        if (!form) return;

        form.addEventListener('submit', async e => {
            e.preventDefault();
            const formData = new FormData(form);
            const action = form.action;
            const method = form.method.toUpperCase();

            try {
                const res = await fetch(action, { method, body: formData });
                const text = await res.text();
                let result;
                try { result = JSON.parse(text); }
                catch { mostrarToast("Resposta inválida do servidor", "error"); return; }

                mostrarToast(result.message, result.status);

                if (result.status === 'success') {
                    modal.style.display = 'none';
                    atualizarTabela();
                }
            } catch (err) {
                mostrarToast("Erro na requisição", "error");
                console.error(err);
            }
        });
    }

    /**
     * Atualiza a tabela de pessoas/contatos via fetch.
     */
    async function atualizarTabela() {
        const url = window.location.pathname;
        try {
            const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const text = await res.text();
            let result;
            try { result = JSON.parse(text); }
            catch { return; }

            if (result.status === 'success') {
                const tbody = document.querySelector('table tbody');
                if (!tbody) return;
                tbody.innerHTML = '';

                if (result.data.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="100%">Nenhum registro encontrado.</td></tr>`;
                    return;
                }

                result.data.forEach(item => {
                    const tr = document.createElement('tr');

                    if ("cpf" in item) {
                        tr.innerHTML = `
                            <td>${item.id}</td>
                            <td>${item.nome}</td>
                            <td>${item.cpf}</td>
                            <td>
                                <div class="acoes-dropdown">
                                    <button class="btn-acoes">⋮</button>
                                    <div class="acoes-conteudo">
                                        <button class="btn-visualizar" data-url="/pessoa/view/${item.id}">Visualizar</button>
                                        <button class="btn-editar" data-url="/pessoa/edit/${item.id}">Editar</button>
                                        <button class="btn-excluir" data-url="/pessoa/delete/${item.id}">Excluir</button>
                                    </div>
                                </div>
                            </td>`;
                    } else {
                        const tipoLabel = item.tipo === "Telefone" || item.tipo === "E-mail"
                            ? item.tipo
                            : (item.tipo == 0 ? "Telefone" : "E-mail");

                        tr.innerHTML = `
                            <td>${item.id}</td>
                            <td>${item.pessoa ?? ''}</td>
                            <td>${item.descricao}</td>
                            <td>${tipoLabel}</td>
                            <td>
                                <div class="acoes-dropdown">
                                    <button class="btn-acoes">⋮</button>
                                    <div class="acoes-conteudo">
                                        <button class="btn-visualizar" data-url="/contato/view/${item.id}">Visualizar</button>
                                        <button class="btn-editar" data-url="/contato/edit/${item.id}">Editar</button>
                                        <button class="btn-excluir" data-url="/contato/delete/${item.id}">Excluir</button>
                                    </div>
                                </div>
                            </td>`;
                    }

                    tbody.appendChild(tr);
                });

                ativarEventosAcoes();
            }
        } catch (err) {
            console.error("Erro ao atualizar tabela:", err);
        }
    }

    /**
     * Ativa eventos dos botões de ação (dropdown, editar, excluir, etc.).
     */
    function ativarEventosAcoes() {
        document.querySelectorAll('.btn-acoes').forEach(btn => {
            btn.onclick = () => {
                const conteudo = btn.nextElementSibling;

                document.querySelectorAll('.acoes-conteudo').forEach(c => {
                    if (c !== conteudo) c.style.display = 'none';
                });

                conteudo.style.top = "100%";
                conteudo.style.bottom = "auto";

                const linha = btn.closest("tr");
                const tbody = linha?.parentElement;

                if (tbody) {
                    const linhas = Array.from(tbody.children);
                    const index = linhas.indexOf(linha);

                    if (linhas.length <= 2) {
                        conteudo.style.top = "100%";
                        conteudo.style.bottom = "auto";
                    } else if (index >= linhas.length - 2) {
                        conteudo.style.top = "auto";
                        conteudo.style.bottom = "100%";
                    }
                }

                conteudo.style.display = conteudo.style.display === 'block' ? 'none' : 'block';
            };
        });

        document.querySelectorAll('.btn-editar').forEach(btn => {
            btn.onclick = () => abrirModal(btn.dataset.url);
        });

        document.querySelectorAll('.btn-visualizar').forEach(btn => {
            btn.onclick = () => abrirModal(btn.dataset.url);
        });

        document.querySelectorAll('.btn-excluir').forEach(btn => {
            btn.onclick = async () => {
                if (!confirm('Deseja realmente excluir?')) return;
                try {
                    const res = await fetch(btn.dataset.url, { method: 'POST' });
                    const text = await res.text();
                    let result;
                    try { result = JSON.parse(text); }
                    catch { mostrarToast("Erro na resposta do servidor", "error"); return; }

                    mostrarToast(result.message, result.status);
                    if (result.status === 'success') atualizarTabela();
                } catch (err) {
                    mostrarToast("Erro ao excluir", "error");
                    console.error(err);
                }
            };
        });
    }

    document.querySelectorAll('.btn-novo').forEach(btn => {
        btn.addEventListener('click', () => abrirModal(btn.dataset.url));
    });

    fecharModal.onclick = () => modal.style.display = 'none';
    window.onclick = e => { if (e.target === modal) modal.style.display = 'none'; }

    ativarEventosAcoes();
});
