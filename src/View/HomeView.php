<?php
namespace App\View;

/**
 * View responsável por renderizar a página inicial.
 */
class HomeView extends View {

    /**
     * Renderiza o corpo da tela inicial.
     */
    protected function renderBody(): void {
        ?>
        <section class="container-form" style="max-width: 400px; text-align: center;">
            <h1 style="margin-bottom: 1.5rem;">Bem-vindo</h1>

            <div style="margin-bottom: 2rem;">
                <img src="/img/logo.png" alt="Logo" style="max-width: 180px;">
            </div>

            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <a href="/pessoa" class="btn btn-primario">Pessoas</a>
                <a href="/contato" class="btn btn-secundario">Contatos</a>
            </div>
        </section>
        <?php
    }
}
