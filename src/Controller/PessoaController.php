<?php
namespace App\Controller;

use App\Model\Pessoa;
use App\View\PessoaFormView;
use App\View\PessoaIndexView;

/**
 * Controller responsável pelo CRUD de Pessoa.
 */
class PessoaController extends Controller {

    /**
     * Retorna uma nova instância do modelo Pessoa.
     */
    protected function getModelInstance() {
        return new Pessoa();
    }

    /**
     * Retorna o modelo Pessoa.
     */
    protected function getModel(): Pessoa {
        return parent::getModel();
    }

    /**
     * Exibe o formulário de cadastro/edição de Pessoa (GET).
     */
    protected function addGet($id = null) {
        $pessoa = $id ? $this->getPessoaById($id) : $this->getModel();

        $view = new PessoaFormView();
        $view->setParams([
            'titulo' => $id ? 'Editar Pessoa' : 'Cadastro de Pessoa',
            'pessoa' => $pessoa
        ]);
        $view->render();
    }

    /**
     * Salva uma nova Pessoa (POST).
     */
    protected function addPost() {
        $pessoa = $this->getModel();
        $data = $this->getBody();

        if (isset($data['nome'])) $pessoa->setNome($data['nome']);
        if (isset($data['cpf'])) $pessoa->setCpf($data['cpf']);

        $mensagem = null;
        if (empty($pessoa->getNome())) {
            $mensagem = "Nome é obrigatório!";
        } elseif (empty($pessoa->getCpf())) {
            $mensagem = "CPF é obrigatório!";
        } elseif (!$this->validarCPF($pessoa->getCpf())){
            $mensagem = "O CPF informado é invalido!";
        }

        header('Content-Type: application/json; charset=utf-8');

        if ($mensagem) {
            echo json_encode(['status' => 'error', 'message' => $mensagem]);
            exit;
        }

        $this->persist($pessoa);

        echo json_encode([
            'status' => 'success',
            'message' => 'Pessoa cadastrada com sucesso!',
            'data' => [
                'id' => $pessoa->getId(),
                'nome' => $pessoa->getNome(),
                'cpf' => $pessoa->getCpf()
            ]
        ]);
        exit;
    }

    /**
     * Exibe o formulário para editar uma Pessoa (GET).
     */
    protected function editGet($id) {
        $pessoa = $this->getPessoaById($id);
        $view = new PessoaFormView();

        if (!$pessoa) {
            $view->setParams([
                'titulo' => 'Editar Pessoa',
                'pessoa' => null,
                'mensagem' => 'Pessoa não encontrada',
                'alertType' => 'error'
            ]);
            $view->render();
            return;
        }

        $view->setParams([
            'titulo' => 'Editar Pessoa',
            'pessoa' => $pessoa
        ]);
        $view->render();
    }

    /**
     * Atualiza uma Pessoa (POST).
     */
    protected function editPost($id) {
        $pessoa = $this->getPessoaById($id);

        header('Content-Type: application/json; charset=utf-8');

        if (!$pessoa) {
            echo json_encode(['status' => 'error', 'message' => 'Pessoa não encontrada']);
            exit;
        }

        $data = $this->getBody();
        if (isset($data['nome'])) $pessoa->setNome($data['nome']);
        if (isset($data['cpf'])) $pessoa->setCpf($data['cpf']);

        $mensagem = null;
        if (empty($pessoa->getNome())) {
            $mensagem = "Nome é obrigatório!";
        } elseif (empty($pessoa->getCpf())) {
            $mensagem = "CPF é obrigatório!";
        } elseif (!$this->validarCPF($pessoa->getCpf())){
            $mensagem = "O CPF informado é invalido!";
        }

        if ($mensagem) {
            echo json_encode(['status' => 'error', 'message' => $mensagem]);
            exit;
        }

        $this->persist($pessoa);

        echo json_encode([
            'status' => 'success',
            'message' => 'Pessoa atualizada com sucesso!',
            'data' => [
                'id' => $pessoa->getId(),
                'nome' => $pessoa->getNome(),
                'cpf' => $pessoa->getCpf()
            ]
        ]);
        exit;
    }

    /**
     * Remove uma Pessoa.
     */
    protected function deleteAction($id) {
        $pessoa = $this->getPessoaById($id);

        header('Content-Type: application/json; charset=utf-8');

        if (!$pessoa) {
            echo json_encode(['status' => 'error', 'message' => 'Pessoa não encontrada']);
            exit;
        }

        $this->remove($pessoa);

        echo json_encode(['status' => 'success', 'message' => 'Pessoa removida com sucesso!']);
        exit;
    }

    /**
     * Lista Pessoas (com pesquisa por nome).
     */
    protected function indexAction() {
        $repo = $this->getEntityManager()->getRepository(Pessoa::class);

        $q = $_GET['q'] ?? null;
        if ($q) {
            $pessoas = $repo->buscarPorNome($q);
        } else {
            $pessoas = $repo->findAll();
        }

        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

        if ($isAjax) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'status' => 'success',
                'data' => array_map(fn($p) => [
                    'id' => $p->getId(),
                    'nome' => $p->getNome(),
                    'cpf' => $p->getCpf()
                ], $pessoas)
            ]);
            exit;
        }

        return [
            'status' => 'success',
            'data' => $pessoas,
            'titulo' => $this->getIndexTitle(),
            'busca' => $q
        ];
    }

    /**
     * Exibe uma Pessoa em modo somente leitura.
     */
    public function view($id) {
        $pessoa = $this->getPessoaById($id);

        $view = new PessoaFormView();

        if (!$pessoa) {
            $view->setParams([
                'titulo'    => 'Visualizar Pessoa',
                'pessoa'    => null,
                'mensagem'  => 'Pessoa não encontrada',
                'alertType' => 'error',
                'readonly'  => true
            ]);
            $view->render();
            return;
        }

        $view->setParams([
            'titulo'   => 'Visualizar Pessoa',
            'pessoa'   => $pessoa,
            'readonly' => true
        ]);
        $view->render();
    }

    /**
     * Retorna a classe da View de listagem.
     */
    protected function getIndexViewClass(): string {
        return PessoaIndexView::class;
    }

    /**
     * Busca uma Pessoa pelo ID.
     */
    private function getPessoaById($id) {
        return $this->getEntityManager()->getRepository(Pessoa::class)->find($id);
    }

    /**
     * Retorna o título da tela de listagem.
     */
    protected function getIndexTitle(): string {
        return 'Lista de Pessoas';
    }

    /**
     * Método utlizado para validar se o CPF da pessoa esta correto.
     * @param $cpf
     * @return bool
     */
    private function validarCPF($cpf) {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);

        if (strlen($cpf) != 11) {
            return false;
        }

        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            $soma = 0;
            for ($i = 0; $i < $t; $i++) {
                $soma += $cpf[$i] * (($t + 1) - $i);
            }
            $digito = ($soma * 10) % 11;
            if ($digito == 10) $digito = 0;

            if ($cpf[$t] != $digito) {
                return false;
            }
        }
        return true;
    }

}
