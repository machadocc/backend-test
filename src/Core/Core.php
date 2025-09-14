<?php

namespace App\Core;

use App\Controller\Controller;
use Doctrine\ORM\EntityManager;

/**
 * Classe Core responsável por interpretar a URL e direcionar
 * a requisição para o controlador e método correspondentes.
 */
class Core {

    /**
     * Executa o roteamento principal da aplicação.
     *
     * @param EntityManager $entityManager Instância do EntityManager do Doctrine
     * @return void
     */
    public static function run(EntityManager $entityManager): void {
        $isCli = php_sapi_name() === 'cli';

        $url = $isCli ? '/' : ($_SERVER['REQUEST_URI'] ?? '/');
        $parsedUrl = parse_url($url);
        $path = trim($parsedUrl['path'] ?? '/', '/');

        $segments = explode('/', $path);

        $controllerName = !empty($segments[0]) ? ucfirst($segments[0]) . 'Controller' : 'HomeController';
        $methodName = $segments[1] ?? 'index';
        $id = $segments[2] ?? null;

        $className = "App\\Controller\\$controllerName";

        if (!class_exists($className)) {
            echo "Controlador '$className' não encontrado.";
            return;
        }

        $reflection = new \ReflectionClass($className);

        if ($reflection->isAbstract()) {
            echo "Não é possível instanciar controlador abstrato '$className'.";
            return;
        }

        /** @var Controller $controller */
        $controller = new $className($entityManager);

        if (!method_exists($controller, $methodName)) {
            echo "Método '$methodName' não encontrado em $className.";
            return;
        }

        $reflectionMethod = new \ReflectionMethod($controller, $methodName);
        $numParams = $reflectionMethod->getNumberOfParameters();

        if ($numParams === 0) {
            $controller->$methodName();
        } elseif ($numParams === 1) {
            $controller->$methodName($id);
        } else {
            echo "Método '$methodName' com número de parâmetros inesperado.";
        }
    }
}
