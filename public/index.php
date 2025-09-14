<?php
// public/index.php

require __DIR__ . '/../config/doctrine.php';

// Roda a aplicação pelo Core
/** @var \Doctrine\ORM\EntityManager $entityManager */
\App\Core\Core::run($entityManager);
