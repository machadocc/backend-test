<?php
namespace App\Model;

use App\Model\Enum\ContatoEnum;
use App\Repository\PessoaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'mzd_pessoa')]
#[ORM\Entity(repositoryClass: PessoaRepository::class)]
class Pessoa {

    #[ORM\Id]
    #[ORM\Column(name: 'pes_id',type: 'integer')]
    #[ORM\GeneratedValue]
    private $id;

    #[ORM\Column(name: 'pes_nome', type: 'string')]
    private $nome;

    #[ORM\Column(name: 'pes_cpf', type: 'string')]
    private $cpf;

    #[ORM\OneToMany(targetEntity: Contato::class, mappedBy: 'pessoa', cascade: ['remove'])]
    private Collection $contatos;

    /**
     * Método construtor da classe.
     */
    public function __construct(){
        $this->contatos = new ArrayCollection();
    }

    /**
     * Seta o ID no modelo
     * @param integer $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * Retorna o ID do modelo
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * SEta o nome no modelo.
     * @param string $nome
     */
    public function setNome($nome) {
        $this->nome = $nome;
    }

    /**
     * Retorna o nome do modelo.
     * @return string
     */
    public function getNome() {
        return $this->nome;
    }

    /**
     * Seta o CPF no modelo.
     * @param string $cpf
     */
    public function setCpf($cpf) {
        $this->cpf = $cpf;
    }

    /**
     * Retorna o CPF do modelo.
     * @return string
     */
    public function getCpf() {
        return $this->cpf;
    }

    /**
     * Método para adicionar contatos vinculados ao modelo.
     * @param Contato $contato
     */
    public function addContato(Contato $contato) {
        if(!$this->contatos->contains($contato)){
            $contato->setPessoa($this);
            $this->contatos->add($contato);
        }
    }

    /**
     * Retorna os contatos do modelo.
     * @return ArrayCollection|Collection
     */
    public function getContatos() {
        return $this->contatos;
    }


}