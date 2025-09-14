<?php
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;
use Doctrine\ORM\Tools\Console\ConsoleRunner;

require_once __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../bootstrap.php';

/**
 * @var $entityManager
 */
ConsoleRunner::run(
    new SingleManagerProvider($entityManager)
);
