<?php
namespace App\Controller;

use Doctrine\ORM\EntityManager;

/**
 * Classe base abstrata para todos os controllers.
 * Fornece métodos comuns para operações de CRUD, acesso ao EntityManager,
 * e manipulação de requests/responses.
 */
abstract class Controller {

    /** @var object|null Instância do modelo atual */
    private $model;

    /** @var EntityManager */
    private EntityManager $entityManager;

    /**
     * Construtor.
     */
    public function __construct(EntityManager $entityManager) {
        $this->entityManager = $entityManager;
    }

    /**
     * Retorna o EntityManager.
     */
    protected function getEntityManager(): EntityManager {
        return $this->entityManager;
    }

    /**
     * Verifica se a requisição é POST.
     */
    protected function isPost(): bool {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Verifica se a requisição é GET.
     */
    protected function isGet(): bool {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

    /**
     * Retorna os parâmetros da query string.
     */
    protected function getQueryParams(): array {
        return $_GET;
    }

    /**
     * Retorna o corpo da requisição (JSON ou POST).
     */
    protected function getBody(): array {
        if (isset($_SERVER['CONTENT_TYPE']) && str_contains($_SERVER['CONTENT_TYPE'], 'application/json')) {
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);
            return is_array($data) ? $data : [];
        }
        return $_POST;
    }

    /**
     * Retorna todos os parâmetros de entrada (GET + POST/JSON).
     */
    protected function getAllInput(): array {
        return array_merge($this->getQueryParams(), $this->getBody());
    }

    /**
     * Persiste uma entidade no banco.
     */
    protected function persist(object $entity): void {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

    /**
     * Remove uma entidade do banco.
     */
    protected function remove(object $entity): void {
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();
    }

    /**
     * Fluxo de adição (GET ou POST).
     */
    public function add() {
        if ($this->isPost()) {
            $this->mapModel();
            return $this->addPost();
        }
        return $this->addGet();
    }

    /**
     * Mapeia automaticamente os dados de entrada para os setters do modelo.
     */
    private function mapModel() {
        $data = $this->getBody();
        foreach ($data as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (method_exists($this->getModel(), $method)) {
                $this->getModel()->$method($value);
            }
        }
    }

    abstract protected function addPost();
    abstract protected function addGet();

    /**
     * Fluxo de edição (GET ou POST).
     */
    public function edit($id = null) {
        if ($this->isPost()) {
            return $this->editPost($id);
        }
        return $this->editGet($id);
    }

    abstract protected function editPost($id);
    abstract protected function editGet($id);

    /**
     * Fluxo de exclusão.
     */
    public function delete($id = null) {
        return $this->deleteAction($id);
    }

    abstract protected function deleteAction($id);

    /**
     * Fluxo de listagem (renderiza a view com dados).
     */
    public function index() {
        $response = $this->indexAction();

        $viewClass = $this->getIndexViewClass();
        $view = new $viewClass([
            'titulo' => $this->getIndexTitle(),
            'response' => $response
        ]);
        $view->render();
    }

    abstract protected function indexAction();

    /**
     * Cada controller deve informar a view da listagem.
     */
    abstract protected function getIndexViewClass(): string;

    /**
     * Título padrão da listagem (pode ser sobrescrito).
     */
    protected function getIndexTitle(): string {
        return 'Lista';
    }

    /**
     * Retorna a instância do modelo atual.
     */
    protected function getModel() {
        if (!$this->model) {
            $this->model = $this->getModelInstance();
        }
        return $this->model;
    }

    /**
     * Cria uma nova instância do modelo associado.
     */
    abstract protected function getModelInstance();
}
