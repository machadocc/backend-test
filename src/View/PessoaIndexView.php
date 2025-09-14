<?php
namespace App\View;

use App\Model\Pessoa;

/**
 * View responsável por renderizar a listagem de Pessoas.
 *
 * Inclui:
 * - Barra de ações com pesquisa por nome e botão de nova pessoa
 * - Tabela listando as pessoas com ID, Nome, CPF e ações
 * - Modal para cadastro/edição/visualização
 */
class PessoaIndexView extends View {

    /**
     * Renderiza o corpo da listagem de Pessoas.
     *
     * Exibe a tabela de resultados, mensagens de alerta,
     * e fornece botões de ação (Visualizar, Editar, Excluir).
     *
     * @return void
     */
    protected function renderBody(): void {
        $response = $this->getParam('response', []);
        $pessoas = $response['data'] ?? [];
        $filtro  = htmlspecialchars($_GET['q'] ?? '');

        if (!empty($response['alertType']) && !empty($response['message'])) {
            $tipo = htmlspecialchars($response['alertType']);
            $msg  = htmlspecialchars($response['message']);
            echo "<div class='alert {$tipo}'>{$msg}</div>";
        }
        ?>

        <h1><?= htmlspecialchars($this->getParam('titulo', 'Pessoas')) ?></h1>

        <!-- Barra de ações -->
        <div class="acoes-barra">
            <!-- Formulário de pesquisa -->
            <form method="get" action="/pessoa" class="form-pesquisa">
                <input type="text" name="q" value="<?= $filtro ?>" placeholder="Pesquisar por nome..." />
                <button type="submit" class="btn btn-secundario">Buscar</button>
            </form>

            <button class="btn-novo btn-primario" data-url="/pessoa/add">Nova Pessoa</button>
        </div>

        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>CPF</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php if (!empty($pessoas)): ?>
                <?php foreach ($pessoas as $pessoa): ?>
                    <tr>
                        <td><?= $pessoa->getId() ?></td>
                        <td><?= htmlspecialchars($pessoa->getNome()) ?></td>
                        <td><?= htmlspecialchars($pessoa->getCpf()) ?></td>
                        <td class="acoes">
                            <div class="acoes-dropdown">
                                <button class="btn-acoes">⋮</button>
                                <div class="acoes-conteudo">
                                    <button class="btn-visualizar" data-url="/pessoa/view/<?= $pessoa->getId() ?>">Visualizar</button>
                                    <button class="btn-editar" data-url="/pessoa/edit/<?= $pessoa->getId() ?>">Editar</button>
                                    <button class="btn-excluir" data-url="/pessoa/delete/<?= $pessoa->getId() ?>">Excluir</button>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="4">Nenhuma pessoa encontrada.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>

        <div id="modalForm" class="modal">
            <div class="modal-conteudo">
                <span id="fecharModal" class="fechar">&times;</span>
                <div id="modalBody"></div>
            </div>
        </div>

        <?php
    }
}
