<?php
namespace App\Model;

use App\Model\Enum\ContatoEnum;
use App\Repository\ContatoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'mzd_contato')]
#[ORM\Entity(repositoryClass: ContatoRepository::class)]
class Contato {

    #[ORM\Id]
    #[ORM\Column(name:'con_id', type: 'integer')]
    #[ORM\GeneratedValue]
    private $id;

    #[ORM\Column(name:'con_tipo', type: 'boolean')]
    private $tipo;

    #[ORM\Column(name:'con_descricao', type: 'string', length: 255)]
    private $descricao;

    #[ORM\ManyToOne(targetEntity: Pessoa::class, inversedBy: 'contatos')]
    #[ORM\JoinColumn(name: "Pessoa_id", referencedColumnName: "pes_id")]
    private $pessoa;

    /**
     * Seta o ID do contato.
     * @param int $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * Retorna o ID do contato.
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Seta o tipo do contato (0 = Telefone, 1 = E-mail).
     * @param bool $tipo
     */
    public function setTipo($tipo) {
        $this->tipo = $tipo;
    }

    /**
     * Retorna o tipo do contato.
     * @return bool
     */
    public function getTipo() {
        return $this->tipo;
    }

    /**
     * Seta a descrição do contato (ex: número ou e-mail).
     * @param string $descricao
     */
    public function setDescricao($descricao) {
        $this->descricao = $descricao;
    }

    /**
     * Retorna a descrição do contato.
     * @return string
     */
    public function getDescricao() {
        return $this->descricao;
    }

    /**
     * Seta a pessoa associada ao contato.
     * @param Pessoa $pessoa
     */
    public function setPessoa($pessoa) {
        $this->pessoa = $pessoa;
    }

    /**
     * Retorna a pessoa associada ao contato.
     * @return Pessoa
     */
    public function getPessoa() {
        return $this->pessoa;
    }
}
