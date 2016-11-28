<?php

namespace Mannv\DingoRoutesList;

use FastRoute\RouteCollector;
use Illuminate\Console\Command;

class RoutesCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'route:list';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'route:list {version?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'anhmantk: Display all registered routes.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $version = $this->argument('version');
        if ($version == NULL) {
            $version = env('API_VERSION', 'v1');
        }
        $api = app('Dingo\Api\Routing\Router');
        $list = array();
        $rt = $api->getAdapterRoutes();
        if ($rt[$version] instanceof RouteCollector) {
            $listRoutes = $rt[$version]->getData();
            foreach ($listRoutes[0] as $method => $rows) {
                if ($method == 'HEAD') {
                    continue;
                }
                foreach ($rows as $item) {
                    unset($item['middleware'][0]);

                    $list[] = array(
                        'version' => implode(', ', $item['version']),
                        'method' => $method == 'GET' ? 'GET|HEAD' : trim($method),
                        'name' => isset($item['as']) ? trim($item['as']) : '',
                        'uri' => $item['uri'],
                        'action' => isset($item['uses']) ? $item['uses'] : 'Closure',
                        'middleware' => implode(', ', $item['middleware']),
                    );
                }
            }

            if (!empty($listRoutes[1])) {
                foreach ($listRoutes[1] as $method => $rows) {
                    if ($method == 'HEAD') {
                        continue;
                    }
                    foreach ($rows as $obj) {
                        foreach ($obj['routeMap'] as $obj) {
                            $item = $obj[0];
                            unset($item['middleware'][0]);
                            $list[] = array(
                                'version' => implode(', ', $item['version']),
                                'method' => $method == 'GET' ? 'GET|HEAD' : trim($method),
                                'name' => isset($item['as']) ? trim($item['as']) : '',
                                'uri' => $item['uri'],
                                'action' => isset($item['uses']) ? $item['uses'] : 'Closure',
                                'middleware' => implode(', ', $item['middleware']),
                            );
                        }
                    }
                }
            }
        }
        $this->sortListRouter($list);
        $headers = array('Version', 'Method', 'Name', 'Uri', 'Action', 'Middleware');
        $this->table($headers, $list);
    }

    private function sortListRouter(&$routes)
    {
        usort($routes, function ($a, $b) {
            if (strcmp($a['uri'], $b['uri']) === 0) {
                if (strcmp($a['method'], $b['method']) === 0) {
                    return 0;
                }
                return strcmp($a['method'], $b['method']) > 0 ? 1 : -1;
            }
            return strcmp($a['uri'], $b['uri']) > 0 ? 1 : -1;
        });
    }
}