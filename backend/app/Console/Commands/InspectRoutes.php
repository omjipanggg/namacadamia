<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use ReflectionFunction;
use Closure;

class InspectRoutes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'route:inspect';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inspect and display all registered routes in the application, including their HTTP methods, URIs, names, and associated controller actions.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $routes = Route::getRoutes()->getRoutes();
        $rows = [];

        foreach ($routes as $route) {
            $uriMethod = implode('|', $route->methods());
            $uri = $route->uri();
            $action = $route->getAction();

            $type = 'Unknown';
            $details = '';

            if (isset($action['uses'])) {
                if ($action['uses'] instanceof Closure) {
                    $type = 'Closure / Function';
                    $ref = new ReflectionFunction($action['uses']);
                    $returnType = $ref->getReturnType();
                    $details = $returnType ? "Returns: {$returnType}" : 'No explicit return type';
                } elseif (is_string($action['uses'])) {
                    $type = 'Controller';
                    $details = $action['uses'];
                }
            }

            // 2. Check Action / Controller Validity
            $isValid = true;
            $statusNote = 'Valid';

            if (is_string($action['uses']) && str_contains($action['uses'], '@')) {
                [$controller, $method] = explode('@', $action['uses']);
                if (!class_exists($controller)) {
                    $isValid = false;
                    $statusNote = "Missing Controller: {$controller}";
                } elseif (!method_exists($controller, $method)) {
                    $isValid = false;
                    $statusNote = "Missing Method: {$method}()";
                }
            }

            $rows[] = [
                $uriMethod,
                $uri,
                $type,
                $details,
                $isValid ? '<fg=green>Valid</>' : "<fg=red>{$statusNote}</>",
            ];
        }
        $this->table(['Method', 'Route (HTTP + URI)', 'Type', 'Details', 'Status'], $rows);
    }
}
