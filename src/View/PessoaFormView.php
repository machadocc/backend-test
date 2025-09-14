<?php
namespace App\View;

use App\Model\Pessoa;

/**
 * View responsável por renderizar o formulário de Pessoa.
 *
 * Suporta os modos:
 * - Cadastro
 * - Edição
 * - Visualização somente leitura (readonly)
 */
class PessoaFormView extends Form {

    /**
     * Renderiza o corpo do formulário de Pessoa.
     *
     * Exibe os campos de Nome e CPF, além de mensagens de erro/sucesso.
     * O formulário adapta sua apresentação conforme o contexto:
     * - Cadastro de nova Pessoa
     * - Edição de Pessoa existente
     * - Visualização em modo somente leitura
     *
     * @return void
     */
    protected function renderBody(): void {
        /** @var Pessoa|null $pessoa */
        $pessoa = $this->params['pessoa'] ?? null;
        $editando = $pessoa && $pessoa->getId();
        $readonly = $this->params['readonly'] ?? false;

        $mensagem  = $this->params['mensagem'] ?? null;
        $alertType = $this->params['alertType'] ?? null;
        ?>

        <section class="container-form">
            <h1 class="titulo-form">
                <?= htmlspecialchars($this->params['titulo'] ?? ($editando ? "Editar Pessoa" : "Nova Pessoa")) ?>
            </h1>

            <?php if ($mensagem): ?>
                <div class="alert <?= $alertType === 'error' ? 'alert-erro' : 'alert-sucesso' ?>">
                    <?= htmlspecialchars($mensagem) ?>
                </div>
            <?php endif; ?>

            <form method="POST"
                  action="<?= $editando ? '/pessoa/edit/' . $pessoa->getId() : '/pessoa/add' ?>"
                  class="formulario modal-form">

                <input type="hidden" name="id" value="<?= htmlspecialchars($pessoa?->getId() ?? '') ?>">

                <div class="form-grupo">
                    <label for="nome">Nome:</label>
                    <input type="text" id="nome" name="nome"
                           value="<?= htmlspecialchars($pessoa?->getNome() ?? '') ?>"
                            <?= $readonly ? 'readonly' : '' ?> required>
                </div>

                <div class="form-grupo">
                    <label for="cpf">CPF:</label>
                    <input type="text" id="cpf" name="cpf"
                           maxlength="14"
                           value="<?= htmlspecialchars($pessoa?->getCpf() ?? '') ?>"
                            <?= $readonly ? 'readonly' : '' ?> required>
                </div>

                <div class="form-botoes">
                    <?php if (!$readonly): ?>
                        <button type="submit" class="btn-primario">
                            <?= $editando ? "Atualizar" : "Cadastrar" ?>
                        </button>
                        <a href="/pessoa" class="btn-secundario">Voltar</a>
                    <?php else: ?>
                        <a href="/pessoa" class="btn-secundario">Fechar</a>
                    <?php endif; ?>
                </div>
            </form>
        </section>

        <?php
    }
}
