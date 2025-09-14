<?php
namespace App\View;

use App\Model\Contato;
use App\Model\Enum\ContatoEnum;

/**
 * View responsável por renderizar a listagem de Contatos.
 */
class ContatoIndexView extends View {

    /**
     * Renderiza o corpo da página de listagem de contatos.
     *
     * Exibe filtro de pesquisa, botão de novo contato, tabela de contatos
     * e modal para operações de CRUD via AJAX.
     *
     * @return void
     */
    protected function renderBody(): void {
        $response = $this->getParam('response', []);
        $contatos = $response['data'] ?? [];

        if (!empty($response['alertType']) && !empty($response['message'])) {
            $tipo = htmlspecialchars($response['alertType']);
            $msg  = htmlspecialchars($response['message']);
            echo "<div class='alert {$tipo}'>{$msg}</div>";
        }
        ?>

        <h1><?= htmlspecialchars($this->getParam('titulo', 'Contatos')) ?></h1>

        <!-- Barra de ações: pesquisa e botão novo -->
        <div class="acoes-barra">
            <form method="get" class="form-pesquisa">
                <input type="text" name="q" placeholder="Pesquisar por descrição ou pessoa"
                       value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                <button type="submit" class="btn-secundario">Buscar</button>
            </form>
            <button class="btn-novo btn-primario" data-url="/contato/add">Novo Contato</button>
        </div>

        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>Pessoa</th>
                <th>Descrição</th>
                <th>Tipo</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php if (!empty($contatos)): ?>
                <?php foreach ($contatos as $contato): ?>
                    <tr>
                        <td><?= $contato->getId() ?></td>
                        <td><?= htmlspecialchars($contato->getPessoa()->getNome() ?? '') ?></td>
                        <td><?= htmlspecialchars($contato->getDescricao()) ?></td>
                        <td><?= htmlspecialchars(ContatoEnum::getLabel($contato->getTipo())) ?></td>
                        <td class="acoes">
                            <div class="acoes-dropdown">
                                <button class="btn-acoes">⋮</button>
                                <div class="acoes-conteudo">
                                    <button class="btn-visualizar" data-url="/contato/view/<?= $contato->getId() ?>">Visualizar</button>
                                    <button class="btn-editar" data-url="/contato/edit/<?= $contato->getId() ?>">Editar</button>
                                    <button class="btn-excluir" data-url="/contato/delete/<?= $contato->getId() ?>">Excluir</button>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5">Nenhum contato encontrado.</td></tr>
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
