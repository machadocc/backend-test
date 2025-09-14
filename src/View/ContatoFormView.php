<?php
namespace App\View;

use App\Model\Contato;
use App\Model\Enum\ContatoEnum;

/**
 * View responsável por renderizar o formulário de Contato.
 */
class ContatoFormView extends Form {

    /**
     * Renderiza o corpo do formulário de contato.
     *
     * Exibe os campos de descrição, tipo e pessoa,
     * com suporte para modo de leitura (visualização) e edição.
     *
     * @return void
     */
    protected function renderBody(): void {
        /** @var Contato|null $contato */
        $contato = $this->params['contato'] ?? null;
        $editando = $contato && $contato->getId();
        $readonly = $this->params['readonly'] ?? false;

        $mensagem   = $this->params['mensagem'] ?? null;
        $alertType  = $this->params['alertType'] ?? null;

        $pessoas = $this->params['pessoas'] ?? [];
        $pessoaSelecionadaId = $contato?->getPessoa()?->getId();
        ?>

        <section class="container-form">
            <h1 class="titulo-form">
                <?= htmlspecialchars($this->params['titulo'] ?? ($editando ? "Editar Contato" : "Novo Contato")) ?>
            </h1>

            <?php if ($mensagem): ?>
                <div class="alert <?= $alertType === 'error' ? 'alert-erro' : 'alert-sucesso' ?>">
                    <?= htmlspecialchars($mensagem) ?>
                </div>
            <?php endif; ?>

            <form method="POST"
                  action="<?= $editando ? '/contato/edit/' . $contato->getId() : '/contato/add' ?>"
                  class="formulario">

                <div class="form-grupo">
                    <label for="descricao">Descrição:</label>
                    <input type="text" id="descricao" name="descricao"
                           value="<?= htmlspecialchars($contato?->getDescricao() ?? '') ?>"
                            <?= $readonly ? 'readonly' : '' ?> required>
                </div>

                <div class="form-grupo">
                    <label for="tipo">Tipo:</label>
                    <select id="tipo" name="tipo" <?= $readonly ? 'disabled' : '' ?> required>
                        <?php
                        $tipoAtual = $contato?->getTipo();
                        foreach (ContatoEnum::getTipoContatoList() as $valor => $label): ?>
                            <option value="<?= $valor ?>"
                                    <?= ($tipoAtual === $valor ? 'selected' : '') ?>>
                                <?= htmlspecialchars($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-grupo">
                    <label for="pessoa">Pessoa:</label>
                    <select id="pessoa" name="pessoa" <?= $readonly ? 'disabled' : '' ?> required>
                        <option value="">Selecione...</option>
                        <?php foreach ($pessoas as $pessoa): ?>
                            <option value="<?= $pessoa->getId() ?>"
                                    <?= ($pessoaSelecionadaId === $pessoa->getId() ? 'selected' : '') ?>>
                                <?= $pessoa->getId() ?> - <?= htmlspecialchars($pessoa->getNome()) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-botoes">
                    <?php if (!$readonly): ?>
                        <button type="submit" class="btn-primario">
                            <?= $editando ? "Atualizar" : "Cadastrar" ?>
                        </button>
                        <a href="/contato" class="btn-secundario">Voltar</a>
                    <?php else: ?>
                        <a href="/contato" class="btn-secundario">Fechar</a>
                    <?php endif; ?>
                </div>
            </form>
        </section>

        <?php
    }
}
