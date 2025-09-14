<?php
namespace App\Controller;

use App\View\HomeView;

/**
 * Controlador responsável pela tela inicial (rota "/").
 */
class HomeController {

    /**
     * Exibe a tela inicial com a logo e botões de navegação.
     */
    public function index() {
        $view = new HomeView([
            'titulo' => 'Início'
        ]);
        $view->render();
    }
}
