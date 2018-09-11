<?php
namespace Grav\Plugin;

use Grav\Common\Grav;
use Grav\Common\Plugin;
use Grav\Common\Uri;
use RocketTheme\Toolbox\Event\Event;

class StatsAPIPlugin extends Plugin
{
    public static function getSubscribedEvents()
    {
        return [
            'onPluginsInitialized' => ['onPluginsInitialized', 0]
        ];
    }

    public function onPluginsInitialized()
    {
        if ($this->isAdmin()) {
            return;
        }

        $uri = $this->grav['uri'];
        $route = $this->config->get('plugins.stats-api.route');

        if ($route && $route . '/daily' == $uri->path()) {
            $this->getData('daily');
        } elseif ($route && $route . '/monthly' == $uri->path()) {
            $this->getData('monthly');
        } elseif ($route && $route . '/totals' == $uri->path()) {
            $this->getData('totals');
        } elseif ($route && $route . '/visitors' == $uri->path()) {
            $this->getData('visitors');
        }
    }


    public function getData($stat)
    {
        if (!isset($_GET['AUTH_TOKEN'])) {
            return;
        }
        $this->authorize();
        $locator = $this->grav['locator'];
        $assets = $locator ->findResource('log://popularity', true);
        $data = file_get_contents($assets . DIRECTORY_SEPARATOR . $stat . '.json');
        header('Content-Type: application/json');
        echo $data;
        exit();
    }

    private function authorize()
    {
        error_reporting(0);
        $token = $this->config->get('plugins.stats-api.token');
        if (strlen($token) !== 24 || !isset($_GET['AUTH_TOKEN']) || $_GET['AUTH_TOKEN'] !== $token) {
            header('HTTP/1.1 401 Unauthorized');
            exit('401 Unauthorized');
        }
    }
}
