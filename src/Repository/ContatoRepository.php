<?php
namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use App\Model\Contato;

/**
 * Repository personalizado para a entidade Contato.
 */
class ContatoRepository extends EntityRepository {

    /**
     * Busca contatos com filtro opcional por descrição ou nome da pessoa.
     *
     * @param string|null $q Texto a ser buscado
     * @return Contato[]
     */
    public function search(?string $q = null): array {
        $qb = $this->createQueryBuilder('contato')
            ->leftJoin('contato.pessoa', 'pessoa')
            ->addSelect('pessoa');

        if (!empty($q)) {
            $q = strtolower(trim($q));
            $qb->where('LOWER(contato.descricao) LIKE :q')
                ->orWhere('LOWER(pessoa.nome) LIKE :q')
                ->setParameter('q', '%' . $q . '%');
        }

        return $qb->orderBy('contato.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
