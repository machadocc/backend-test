<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use App\Model\Pessoa;

/**
 * Repository personalizado para a entidade Pessoa.
 */
class PessoaRepository extends EntityRepository {

    /**
     * Busca pessoas pelo nome (parcial, case insensitive).
     *
     * @param string $nome Texto do nome a ser buscado
     * @return Pessoa[]
     */
    public function buscarPorNome(string $nome): array {
        return $this->createQueryBuilder('pessoa')
            ->where('LOWER(pessoa.nome) LIKE :nome')
            ->setParameter('nome', '%' . strtolower($nome) . '%')
            ->orderBy('pessoa.nome', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
