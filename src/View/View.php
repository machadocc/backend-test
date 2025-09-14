<?php
namespace App\View;

/**
 * Classe base para todas as Views.
 *
 * Responsável por gerenciar parâmetros e estruturar
 * o ciclo de renderização (header, body e footer).
 */
abstract class View {

    protected array $params;

    /**
     * Construtor da View.
     *
     * @param array $params Parâmetros iniciais para a renderização
     */
    public function __construct(array $params = []) {
        $this->params = $params;
    }

    /**
     * Define ou substitui parâmetros após a construção.
     *
     * @param array $params
     * @return void
     */
    public function setParams(array $params): void {
        $this->params = $params;
    }

    /**
     * Retorna um parâmetro ou valor padrão.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected function getParam(string $key, $default = null) {
        return $this->params[$key] ?? $default;
    }

    /**
     * Executa o fluxo de renderização completo da View.
     *
     * @return void
     */
    public function render(): void {
        $this->renderHeader();
        $this->renderBody();
        $this->renderFooter();
    }

    /**
     * Cada View concreta deve implementar seu corpo.
     *
     * @return void
     */
    abstract protected function renderBody(): void;

    /**
     * Renderiza o cabeçalho da página.
     *
     * @return void
     */
    protected function renderHeader(): void {
        ?>
        <!DOCTYPE html>
        <html lang="pt-br">
        <head>
            <meta charset="UTF-8">
            <title><?= htmlspecialchars($this->getParam('titulo', 'Minha Aplicação')) ?></title>
            <link rel="stylesheet" href="/css/style.css">
        </head>
        <body>
        <header>
            <nav>
                <a href="/pessoa">Pessoa</a>
                <a href="/contato">Contato</a>
            </nav>
        </header>
        <main class="container-center">
        <?php
    }

    /**
     * Renderiza o rodapé da página.
     *
     * @return void
     */
    protected function renderFooter(): void {
        ?>
        </main>
        <footer>
            <p>© <?= date('Y') ?> Teste Magazord - Cristhian</p>
        </footer>

        <div id="notificacoes"></div>

        <script src="/js/script.js?v=<?= time() ?>"></script>
        </body>
        </html>
        <?php
    }
}
