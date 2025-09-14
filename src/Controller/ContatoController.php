<?php
namespace App\Controller;

use App\Model\Contato;
use App\Model\Enum\ContatoEnum;
use App\Model\Pessoa;
use App\View\ContatoFormView;
use App\View\ContatoIndexView;

/**
 * Controller responsável pelo CRUD de Contato.
 */
class ContatoController extends Controller {

    /**
     * Retorna uma nova instância do modelo Contato.
     */
    protected function getModelInstance() {
        return new Contato();
    }

    /**
     * Retorna o modelo Contato.
     */
    protected function getModel(): Contato {
        return parent::getModel();
    }

    /**
     * Exibe o formulário de cadastro/edição de Contato (GET).
     */
    protected function addGet($id = null) {
        $contato = $id ? $this->getContatoById($id) : $this->getModel();

        $view = new ContatoFormView();
        $view->setParams([
            'titulo'  => $id ? 'Editar Contato' : 'Cadastro de Contato',
            'contato' => $contato,
            'pessoas' => $this->getEntityManager()->getRepository(Pessoa::class)->findAll()
        ]);
        $view->render();
    }

    /**
     * Salva um novo Contato (POST).
     */
    protected function addPost() {
        $contato = $this->getModel();
        $data = $this->getBody();

        if (isset($data['pessoa'])) {
            $pessoa = $this->getEntityManager()->getRepository(Pessoa::class)->find($data['pessoa']);
            $contato->setPessoa($pessoa);
        }
        if (isset($data['descricao'])) {
            $contato->setDescricao($data['descricao']);
        }
        if (isset($data['tipo'])) {
            $contato->setTipo((bool)$data['tipo']);
        }

        $mensagem = null;
        if (!$contato->getPessoa()) {
            $mensagem = "Pessoa é obrigatória!";
        } elseif (!$contato->getDescricao()) {
            $mensagem = "Descrição é obrigatória!";
        } elseif ($contato->getTipo() === null) {
            $mensagem = "Tipo de contato é obrigatório!";
        }

        header('Content-Type: application/json; charset=utf-8');

        if ($mensagem) {
            echo json_encode(['status' => 'error', 'message' => $mensagem]);
            exit;
        }

        $this->persist($contato);

        echo json_encode([
            'status'  => 'success',
            'message' => 'Contato cadastrado com sucesso!',
            'data'    => [
                'id'        => $contato->getId(),
                'pessoa'    => $contato->getPessoa()?->getNome(),
                'descricao' => $contato->getDescricao(),
                'tipo'      => $contato->getTipo() ? 'E-mail' : 'Telefone'
            ]
        ]);
        exit;
    }

    /**
     * Exibe o formulário para editar um Contato (GET).
     */
    protected function editGet($id) {
        $contato = $this->getContatoById($id);
        $view = new ContatoFormView();

        if (!$contato) {
            $view->setParams([
                'titulo'   => 'Editar Contato',
                'contato'  => null,
                'mensagem' => 'Contato não encontrado',
                'alertType'=> 'error',
                'pessoas'  => $this->getEntityManager()->getRepository(Pessoa::class)->findAll()
            ]);
            $view->render();
            return;
        }

        $view->setParams([
            'titulo'  => 'Editar Contato',
            'contato' => $contato,
            'pessoas' => $this->getEntityManager()->getRepository(Pessoa::class)->findAll()
        ]);
        $view->render();
    }

    /**
     * Atualiza um Contato (POST).
     */
    protected function editPost($id) {
        $contato = $this->getContatoById($id);

        header('Content-Type: application/json; charset=utf-8');

        if (!$contato) {
            echo json_encode(['status' => 'error', 'message' => 'Contato não encontrado']);
            exit;
        }

        $data = $this->getBody();
        if (isset($data['pessoa'])) {
            $pessoa = $this->getEntityManager()->getRepository(Pessoa::class)->find($data['pessoa']);
            $contato->setPessoa($pessoa);
        }
        if (isset($data['descricao'])) {
            $contato->setDescricao($data['descricao']);
        }
        if (isset($data['tipo'])) {
            $contato->setTipo((bool)$data['tipo']);
        }

        $mensagem = null;
        if (!$contato->getPessoa()) {
            $mensagem = "Pessoa é obrigatória!";
        } elseif (!$contato->getDescricao()) {
            $mensagem = "Descrição é obrigatória!";
        } elseif ($contato->getTipo() === null) {
            $mensagem = "Tipo de contato é obrigatório!";
        }

        if ($mensagem) {
            echo json_encode(['status' => 'error', 'message' => $mensagem]);
            exit;
        }

        $this->persist($contato);

        echo json_encode([
            'status'  => 'success',
            'message' => 'Contato atualizado com sucesso!',
            'data'    => [
                'id'        => $contato->getId(),
                'pessoa'    => $contato->getPessoa()?->getNome(),
                'descricao' => $contato->getDescricao(),
                'tipo'      => $contato->getTipo() ? 'E-mail' : 'Telefone'
            ]
        ]);
        exit;
    }

    /**
     * Remove um Contato.
     */
    protected function deleteAction($id) {
        $contato = $this->getContatoById($id);

        header('Content-Type: application/json; charset=utf-8');

        if (!$contato) {
            echo json_encode(['status' => 'error', 'message' => 'Contato não encontrado']);
            exit;
        }

        $this->remove($contato);

        echo json_encode(['status' => 'success', 'message' => 'Contato removido com sucesso!']);
        exit;
    }

    /**
     * Lista os Contatos.
     */
    protected function indexAction() {
        $repo = $this->getEntityManager()->getRepository(Contato::class);
        $contatos = $repo->search($_GET['q'] ?? null);

        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

        if ($isAjax) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'status' => 'success',
                'data'   => array_map(fn($c) => [
                    'id'        => $c->getId(),
                    'pessoa'    => $c->getPessoa()?->getNome(),
                    'descricao' => $c->getDescricao(),
                    'tipo'      => ContatoEnum::getLabel($c->getTipo())
                ], $contatos)
            ]);
            exit;
        }

        return [
            'status' => 'success',
            'data'   => $contatos,
            'titulo' => $this->getIndexTitle()
        ];
    }

    /**
     * Exibe um Contato em modo somente leitura.
     */
    public function view($id) {
        $contato = $this->getContatoById($id);

        $view = new ContatoFormView();
        $view->setParams([
            'titulo'   => 'Visualizar Contato',
            'contato'  => $contato,
            'pessoas'  => $this->getEntityManager()->getRepository(Pessoa::class)->findAll(),
            'readonly' => true
        ]);
        $view->render();
    }

    /**
     * Retorna a classe da View de listagem.
     */
    protected function getIndexViewClass(): string {
        return ContatoIndexView::class;
    }

    /**
     * Busca um Contato pelo ID.
     */
    private function getContatoById($id) {
        return $this->getEntityManager()->getRepository(Contato::class)->find($id);
    }

    /**
     * Retorna o título da tela de listagem.
     */
    protected function getIndexTitle(): string {
        return 'Lista de Contatos';
    }
}
