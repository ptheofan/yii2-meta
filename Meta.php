<?php
/**
 * Created by PhpStorm.
 * User: ptheofan
 * Date: 23/10/14
 * Time: 17:51
 */

namespace ptheofan\meta;

use yii;
use yii\base\Component;
use yii\helpers\Json;

class Meta extends Component
{
    /**
     * Default meta data values. These override any other metadata set
     * @var array
     */
    public $defaults = [
        'og:type' => 'website',
    ];

    /**
     * Component ID representing the database
     * @var string
     */
    public $db = 'db';

    /**
     * Component ID representing the cache
     * @var string
     */
    public $cache = 'cache';

    /**
     * The Component ID for this module (used to mark cache segments)
     * @var string
     */
    public $componentId = 'meta';

    /**
     * After how long (seconds) will the routes caching expire.
     * @var int
     */
    public $cacheDuration = 3600;

    /**
     * @var string
     */
    private $activeRoute = null;

    /**
     * @param $route
     * @param array $params
     * @return string
     */
    protected function createRouteIndexKey($route, array $params = [])
    {
        array_unshift($params, $route);
        return Json::encode($params);
    }

    /**
     * @return array
     */
    public function getRoutes()
    {
        $cache = Yii::$app->{$this->cache};
        $routes = $cache->get($this->componentId . '|routes');
        if ($routes)
            return $routes;

        /** @var models\Meta[] $models */
        $models = models\Meta::find()->all(Yii::$app->{$this->db});
        $routes = [];
        foreach($models as $model) {
            $key = $this->createRouteIndexKey($model->route, $model->params);
            $routes[$key] = $model->toArray([
                'robots_index', 'robots_follow', 'title', 'keywords', 'description'
            ]);
        }

        $cache->set($this->componentId . '|routes', $routes, $this->cacheDuration);
        return $routes;
    }

    /**
     * @param array $route
     * @return array
     */
    public function getMeta(array $route)
    {
        $routes = $this->getRoutes();
        $route = Json::encode($route);

        // Uncomment the next line to debug routes vs activeRoute
        // yii\helpers\VarDumper::dump([$routes, $route], 10);die;

        return isset($routes[$route]) ? $routes[$route] : [];
    }

    /**
     * @param $route
     * @param array $params
     */
    public function setActiveRoute($route, array $params = [])
    {
        array_unshift($params, trim($route, '/'));
        $this->activeRoute = $params;
    }

    /**
     * @return string
     */
    public function getActiveRoute()
    {
        if ($this->activeRoute === null) {
            $route = Yii::$app->controller->getRoute();
            $params = Yii::$app->controller->actionParams;
            $this->setActiveRoute($route, $params);
        }
        return $this->activeRoute;
    }

    /**
     * Register the robots meta
     * $index must be index or noindex or empty/null
     * $follow must be follow or nofollow or empty/null
     * @param string $index
     * @param string $follow
     */
    public function setRobots($index = null, $follow = null)
    {
        $v = [];
        if (!empty($index))
            $v[] = $index;
        if (!empty($follow))
            $v[] = $follow;

        if (!empty($v))
            Yii::$app->view->registerMetaTag(['name' => 'robots', 'content' => strtolower(implode(',', $v))], 'robots');
    }

    /**
     * Register the author meta
     * @param string $author
     */
    public function setAuthor($author)
    {
        if (!empty($author))
            Yii::$app->view->registerMetaTag(['name' => 'author', 'content' => $author], 'author');
    }

    /**
     * Register Open Graph Type meta
     * @param string $type
     */
    public function setOpenGraphType($type)
    {
        if (!empty($type))
            Yii::$app->view->registerMetaTag(['name' => 'og:type', 'content' => $type], 'og:type');
    }

    /**
     * Register title meta and open graph title meta
     * @param string $title
     */
    public function setTitle($title)
    {
        if (!empty($title)) {
            Yii::$app->view->registerMetaTag(['name' => 'title', 'content' => $title], 'title');
            Yii::$app->view->registerMetaTag(['name' => 'og:title', 'content' => $title], 'og:title');
            Yii::$app->view->title = $title;
        }
    }

    /**
     * Register description meta and open graph description meta
     * @param string $description
     */
    public function setDescription($description)
    {
        if (!empty($description)) {
            Yii::$app->view->registerMetaTag(['name' => 'description', 'content' => $description], 'description');
            Yii::$app->view->registerMetaTag(['name' => 'og:description', 'content' => $description], 'og:description');
        }
    }

    /**
     * Register keywords meta
     * @param string $keywords
     */
    public function setKeywords($keywords)
    {
        if (!empty($keywords))
            Yii::$app->view->registerMetaTag(['name' => 'keywords', 'content' => $keywords], 'keywords');
    }

    /**
     * Register Canonical url
     * @param string $url
     */
    public function setCanonical($url)
    {
        Yii::$app->view->registerLinkTag(['href' => $url, 'rel' => 'canonical'], 'canonical');
    }

    /**
     * Register Open Graph Page Url
     * @param string $url
     */
    public function setOpenGraphUrl($url)
    {
        Yii::$app->view->registerMetaTag(['name' => 'og:url', 'content' => $url], 'og:url');
    }

    /**
     * @param array $metadata
     */
    public function setMeta($metadata = [])
    {
        // Merge route meta with passed parameter meta
        $metadata = array_merge($this->getMeta($this->getActiveRoute()), $metadata);

        // Override meta with the defaults via merge
        $metadata = array_merge($metadata, $this->defaults);

        $this->setRobots(@$metadata['robots_index'], @$metadata['robots_follow']);
        $this->setAuthor(@$metadata['author']);
        $this->setTitle(@$metadata['title']);
        $this->setDescription(@$metadata['description']);
        $this->setKeywords(@$metadata['keywords']);
        $this->setOpenGraphType(@$metadata['og:type']);

        $params = Yii::$app->controller->actionParams;
        $params[0] = Yii::$app->controller->getRoute();
        $url = Yii::$app->getUrlManager()->createAbsoluteUrl($params);
        if ($url !== Yii::$app->request->absoluteUrl)
            $this->setCanonical($url);

        $this->setOpenGraphUrl($url);
    }
}