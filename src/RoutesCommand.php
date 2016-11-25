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
        $api = app('Dingo\Api\Routing\Router');
        $list = array();
        $rt = $api->getAdapterRoutes();
        $version = env('API_VERSION', 'v1');
        if($rt[$version] instanceof RouteCollector) {
            $listRoutes = $rt[$version]->getData();
            foreach($listRoutes[0] as $method => $rows) {
                if($method == 'HEAD') {
                    continue;
                }
                foreach($rows as $item) {
                    unset($item['middleware'][0]);
                    $list[] = array(
                        'version' => implode(', ', $item['version']),
                        'method' => $method == 'GET' ? 'GET|HEAD' : trim($method),
                        'name' => isset($item['as']) ? trim($item['as']) : '',
                        'uri' => $item['uri'],
                        'action' => $item['uses'],
                        'middleware' => implode(', ', $item['middleware']),
                    );
                }
            }

            if(!empty($listRoutes[1])) {
                foreach($listRoutes[1] as $method => $rows) {
                    if($method == 'HEAD') {
                        continue;
                    }
                    foreach($rows as $obj) {
                        foreach($obj['routeMap'] as $obj) {
                            $item = $obj[0];
                            unset($item['middleware'][0]);
                            $list[] = array(
                                'version' => implode(', ', $item['version']),
                                'method' => $method == 'GET' ? 'GET|HEAD' : trim($method),
                                'name' => isset($item['as']) ? trim($item['as']) : '',
                                'uri' => $item['uri'],
                                'action' => $item['uses'],
                                'middleware' => implode(', ', $item['middleware']),
                            );
                        }
                    }
                }
            }
        }
        uasort($list, 'Mannv\DingoRoutesList\sort_uri');
        $headers = array('Version', 'Method', 'Uri', 'Action', 'Name', 'Middleware');
        $this->table($headers, $list);
    }
}

function sort_uri($a, $b) {
    if(strcmp($a['uri'], $b['uri']) === 0) {
        if(strcmp($a['method'], $b['method']) === 0) {
            return 0;
        }
        return strcmp($a['method'], $b['method']) == 1 ? 1 : -1;
    }
    return strcmp($a['uri'], $b['uri']) == 1 ? 1 : -1;
}
