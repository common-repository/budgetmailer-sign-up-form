<?php

/**
 * @package BudgetMailer\Wordpress
 */
namespace BudgetMailer\Wordpress;

use BudgetMailer\Api\Cache;
use BudgetMailer\Api\Client as BudgetMailerApiClient;
use BudgetMailer\Api\Config;

/**
 * BudgetMailer PHP API Client Wrapper
 * @package BudgetMailer\Wordpress
 */
class Client
{
    /**
     * Singleton instance
     * @var Client self
     */
    protected static $instance;
    
    protected $cache;
    protected $client;
    protected $config;
    
    /**
     * Create singleton instance
     * @param array $configData client config data
     * @return Client self 
     * @throws \RuntimeException In case the instance is not initialized with config data
     */
    public static function getInstance(array $configData = array())
    {
        if (!self::$instance) {
            if (!count($configData)) {
                throw new \RuntimeException('Config data missing.');
            }
            
            self::$instance = new self($configData);
        }
        
        return self::$instance;
    }
    
    /**
     * Singleton constructor
     * @param array $configData assoc. array of config data for client
     */
    protected function __construct(array $configData)
    {
        $this->getConfigInstance($configData);
        $this->getCacheInstance();
        $this->getClientInstance();
    }
    
    /**
     * Create BudgetMailer API PHP Client Cache Instance
     */
    protected function getCacheInstance()
    {
        $this->cache = new Cache($this->config);
    }
    
    /**
     * Create BudgetMailer API PHP Client Config Instance
     */
    protected function getConfigInstance($configData)
    {
        $this->config = new Config($configData);
    }
    
    /**
     * Create BudgetMailer API PHP Client Instance
     */
    protected function getClientInstance()
    {
        $this->client = new BudgetMailerApiClient($this->cache, $this->config);
    }
    
    /**
     * Act as a method call wrapper for client... 
     * @param string $name method name
     * @param array $args method args
     * @return mixed call result
     */
    public function __call($name, $args)
    {
        $c = array($this->client, $name);
        
        if (is_callable($c)) {
            return call_user_func_array($c, $args);
        }
        
        return false;
    }
}
