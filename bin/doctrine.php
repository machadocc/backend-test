#!/usr/bin/env php
<?php
/**
 * CLI do Doctrine ORM
 *
 * Esse arquivo expõe os comandos do Doctrine (schema-tool, migrations, etc.)
 * usando o EntityManager configurado em /config/doctrine.php.
 *
 * Uso:
 *   php bin/doctrine.php list
 *   php bin/doctrine.php orm:schema-tool:create
 *   php bin/doctrine.php orm:schema-tool:drop --force
 *   php bin/doctrine.php orm:schema-tool:update --force
 */

use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;

require_once __DIR__ . '/../vendor/autoload.php';

$entityManager = require __DIR__ . '/../config/doctrine.php';

ConsoleRunner::run(
    new SingleManagerProvider($entityManager)
);
