<?php

namespace concepture\yii2handbook\components\cache;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use concepture\yii2handbook\services\DynamicElementsService;

class CacheService extends Component
{
    const CALLBACK_TYPE_SERVICE = 'service';
    const CALLBACK_TYPE_WIDGET = 'widget';
    const COMPONENT_NAME = 'cacheService';

    /**
     * @var string
     */
    private $prefix = '';
    /**
     * @var string
     */
    private $prefix_tag = 'tag:';
    /**
     * @var string
     */
    private $prefix_callback = 'callback:';
    /**
     * @var array
     */
    private $keysList = [];
    /**
     * @var array
     */
	private $data = [];
    /**
     * @var array
     */
	private $tags = [];
    /**
     * @var array
     */
    private $callback = [];
    /**
     * @var integer
     */
    private $ttl = 0;
    /**
     * @var boolean
     */
    private $nocache = false;

    /**
     * @var string
     */
    public $cacheComponent = 'redis';
    /**
     * @var string
     */
    public $queueComponent = 'queue';
    /**
     * @var 
     */
    public $queueName;
    /**
     * @var string
     */
    public $locale;
    /**
     * @var string
     */
    public $env;

    /**
     * @var boolean
     */
    private $disabled = false;

	public function init()
	{
	    parent::init();
        if(! $this->queueName) {
            throw new InvalidConfigException('`queueName` must be set');
        }

        if(! $this->locale) {
            throw new InvalidConfigException('`locale` must be set');
        }

        if(! $this->env) {
            throw new InvalidConfigException('`env` must be set');
        }

		$this->prefix = $this->env . ':';
		$this->prefix_tag .= $this->prefix;
		$this->prefix_callback .= $this->prefix;
		$this->nocache = (bool)getenv('NOCACHE') || isset($_GET['nc']);
	}

    /**
     * @param bool $value
     */
	public function setDisabled(bool $value)
    {
        $this->disabled = $value;
    }

    /**
     * @return bool
     */
    public function isDisabled()
    {
        return $this->disabled;
    }

	public function getClient()
    {
        if(! Yii::$app->has($this->cacheComponent)) {
            throw new InvalidConfigException('`cacheComponent` must be set');
        }

        return \Yii::$app->{$this->cacheComponent};
    }

    public function getQueue()
    {
        if(! Yii::$app->has($this->queueComponent)) {
            throw new InvalidConfigException('`queueComponent` must be set');
        }

        return \Yii::$app->{$this->queueComponent};
    }

    /**
     * @return DynamicElementsService
     */
    public function getDynamicElementsService()
    {
        return Yii::$app->dynamicElementsService;
    }
    
    public function setArray($key, $val, $ttl = 0)
    {
        if($ttl){}
        $this->set($key, json_encode($val, JSON_UNESCAPED_UNICODE), $ttl);
    }
    
    public function getArray($key)
    {
        $result = $this->get($key);

        return $result !== null ? json_decode($result, true) : null; 
    }
	
	public function set($key, $val, $ttl = 0)
	{
	    if($this->isDisabled()) {
	        return ;
        }

		if($ttl < 1 && $this->ttl > 0) {
			$ttl = $this->ttl;
		}
		
		if(is_array($val)) {
			throw new \Exception('@cache service SET command allowed only scalar values. Use setArray instead.');	
		}
		
		$this->keysList[] = $key;
		
		if(isset($this->data[$key])) {
			unset($this->data[$key]);
		}
		
		$val = (string)$val;
		
		// если указаны теги то сохраняем их в специальный спейс с тегами
		$this->addCacheTags($key);
		
		$callback = false;
		if($this->callback){
			$callback = true;
			$this->addCallback($key);
		}
		
		if($ttl > 0) {
			// сохраняем время сброса (перегенерации) кэша в отдельный спейc
			// если есть callback то кэш будет перегенерирован, а если нет то сброшен через указанное время
			if($callback) {
				$callback = false;
				$this->addTtl($key, $ttl);
				$ttl = 0; // что бы редис не ставил свой expire
			}
		}
		
        if(php_sapi_name() != "cli") {
            $this->data[$key] = $val;
        }
		
    	$key = $this->prefix . $key;
    	$this->getClient()->set($key, $val);
    	// если есть ttl, хранилище редис, и нет коллбэка то сброс стандартными средствами редиса
    	if($ttl > 0) {
    		$this->getClient()->expire($key, $ttl);
    		$this->ttl = 0;
    	}
	}
	
	private function addCallback($key, $space = null)
	{
		$callback = json_encode($this->callback, JSON_UNESCAPED_UNICODE);
		$this->getClient()->set($this->prefix_callback . $key, $callback);
		$this->callback = [];
	}
	
	private function addCacheTags($key)
	{
		$this->tags = array_filter(array_unique($this->tags));
		if($this->tags) {
			foreach($this->tags as $tag) {
				$this->getClient()->sadd($this->prefix_tag . $tag, $key);
			}

			$this->tags = [];
		}
	}
	
	public function get($key)
	{
		// тут нужно именно $_GET т.к. может не быть симфониевского $request-а
		if($this->nocache || $this->isDisabled()) {
			return null;
		}

		if(isset($this->data[$key])) {
			return $this->data[$key];
		}
		$timer_info = [
		    'group' => 'redis', 
		    'name' => $key,
		];
		
	    /*
	    if(!$this->getClient()->exists($this->prefix . $key)){
            $this->addLog("KEY IS NOT EXISTS: ". $this->prefix . $key); 
        }
        */
        # todo: убрал htmlspecialchars_decode - помониторить
		$result = $this->getClient()->get($this->prefix . $key) ?? null;
		$result = !$result ? null : $result;
		
		if($result !== null && php_sapi_name() != "cli") {
			$this->data[$key] = $result;
		}

        return $result;
	}


    /**
     * Проверка существует ли ключ в кеше
     * проверка на редис взята из метода get
     *
     * @param $key
     * @return boolean
     */
    public function exists($key)
    {
 		return (boolean) $this->getClient()->exists($this->prefix . $key);
    }

    /**
     * @param string $type
     * @param string $class_or_service_id widget class or service id 
     * @param string $method
     * @param array $options
     */
    public function callback($type, $class_or_service_id, $method, $options = [], $params_list = false)
    {
    	$this->callback = [
    		'type' => $type,
    		'class' => $class_or_service_id,
    		'method' => $method,
    		'options' => $options,
    	    'params_list' => $params_list,
    	];
    }
    
    public function tags($tags)
    {
    	$this->tags = $tags;

    	return $this;
    }
    
    private function getCallback($key)
    {
    	$out = [];
		$this->addLog('Get callback by key: %s from redis', $key);
		$callback = $this->getClient()->get($this->prefix_callback . $key);
		if($callback) {
			$arr = json_decode($callback, true);
			if(is_array($arr) && isset($arr['type'])) {
				$this->addLog('Found callback in redis %s', $callback);
				$out = $arr;
			}
		}
		
	    return $out;
    }
    
    public function remove($key)
    {
    	$this->_remove($key);
    }
    
    private function _remove($key, $tag = null)
    {
    	try {
    		$callback = $this->getCallback($key);
	    	if($callback) {
                $this->runCallback($key, $callback);
	    	} else {
                $this->getClient()->del($this->prefix . $key);
	    	}
    	} catch (\Exception $e) {
    		$this->addLog($e->getMessage());
    		$this->getClient()->del($this->prefix . $key);
    		$this->getClient()->del($this->prefix_callback . $key);
            print $e->getMessage();
		}

    	if(isset($this->data[$key])){
    		unset($this->data[$key]);
    	}
    }
    
    private function runCallback($key, $callback)
    {
    	try {
	    	switch($callback['type']) {
		    	case self::CALLBACK_TYPE_WIDGET:
		    		$message = '[cache] execWidget terminated with error: %s, widgetClass: %s, file: %s on line %s';
		    		$this->execWidget($callback);
				break;
		    	case self::CALLBACK_TYPE_SERVICE:
		    		$message = '[cache] runServiceMethod terminated with error: %s, service %s, file: %s on line %s';
		    		$this->runServiceMethod($callback);
		    	break;
		    	default:
		    	    $this->getClient()->del($this->prefix . $key);
		    	    $this->getClient()->del($this->prefix_callback . $key);
			}
		} catch(\Exception $e) {
            $this->addLog(sprintf($message, $e->getMessage(), $callback['class'], $e->getFile(), $e->getLine()));
            $this->addLog($e->getTraceAsString());
    		$this->getClient()->del($this->prefix . $key);
    		$this->getClient()->del($this->prefix_callback . $key);
            print $e->getMessage();
		}
    }
    
    private function execWidget($params)
    {
    	$this->addLog('Exec widget %s', json_encode($params));
    	$object = new $params['class']();
    	$options = $params['options'] ?? null;
        $this->getDynamicElementsService()->setCurrentRouteHash(null);
    	if($options) {
    	    if(isset($options['current_route_data'])) {
                $this->getDynamicElementsService()->setRouteData($options['current_route_data']);
            }

            if(isset($options['current_route_hash'])) {
                $this->getDynamicElementsService()->setCurrentRouteHash($options['current_route_hash']);
            }

            $object->setOptions($options);
            foreach($options as $key => $value) {
                if(! property_exists($object, $key)) {
                    continue;
                }

                $object->{$key} = $value;
            }
    	}

    	$method = $params['method'];
    	if($method === 'run') {
            if ($object->beforeRun()) {
                $result = $object->run(false);
                $object->afterRun($result);
            }
        } else {
            $object->{$method}();
        }
    }
    
    private function runServiceMethod($params)
    {
    	$this->addLog('Run service method %s', json_encode($params));
    	$service = \Yii::$app->get($params['class']);
    	if(isset($params['options']) && $params['options']) {
    	    if($params['params_list']) {
    	        call_user_method_array ($params['method'], $service, $params['options']);
    	    } else {
    		    $service->{$params['method']}($params['options']);
    	    }
    	} else {
    		$service->{$params['method']}();
    	}
    }
    
    public function removeByTagQueue($tag)
    {
    	$this->addLog('Add to queue. Remove by tag: %s', $tag);
        $args = [
            ['tag' => $tag],
        ];
        $this->getQueue()->putIn($this->queueName, $args);
    }
    
    public function getKeysByTag($tag)
    {
    	$this->addLog('Get keys by tag <%s>', $tag);
        $keys = $this->getClient()->smembers($this->prefix_tag . $tag);
    	if($keys){
    		$this->addLog('Found keys (%d): %s', sizeof($keys), implode(', ', $keys));
    	}
    	return $keys;
    }
    
    public function removeByTag($tag)
    {
    	$keys = $this->getKeysByTag($tag);


    	if($keys){
    		$this->addLog('Remove by tag: %s', $tag);
    		// remove tag
            $this->getClient()->del($this->prefix_tag . $tag);
    		foreach($keys as $key){
    			$this->_remove($key, $tag);
    		}
    	}
    	return sizeof($keys);
    }
    
    public function removeKey($key)
    {
        // сброс кэша через очередь и на локальном тоже
        $args = [
            ['key' => $key],  
        ];
        // add job to queue
        $this->getQueue()->putIn($this->queueName, $args);
    }
    
    public function ttl($ttl)
    {
    	$this->ttl = $ttl;

    	return $this;
    }
    
    public function clearAllCache()
    {
    	$this->getClient()->flushdb();
    }
    
    private function addLog($str) 
    {
    	$attrs = func_get_args();
    	\Yii::warning(vsprintf($str, sizeof($attrs) > 1 ? array_slice($attrs, 1) : []));
    }
    
}









