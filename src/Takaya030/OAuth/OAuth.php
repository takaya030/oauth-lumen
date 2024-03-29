<?php

namespace Takaya030\OAuth;

/**
 * @author     Dariusz Prz?da <artdarek@gmail.com>
 * @copyright  Copyright (c) 2013
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */

//use \Config;
//use \URL;
use \OAuth\ServiceFactory;
use \OAuth\Common\Consumer\Credentials;

class OAuth {
    /**
     * @var ServiceFactory
     */
    private $_serviceFactory;
    /**
     * Storege name from config
     *
     * @var string
     */
    private $storageClass = '\\OAuth\\Common\\Storage\\Session';
    /**
     * Client ID from config
     *
     * @var string
     */
    private $client_id;
    /**
     * Client secret from config
     *
     * @var string
     */
    private $client_secret;
    /**
     * Scope from config
     *
     * @var array
     */
    private $scope = [];
    /**
     * Constructor
     *
     * @param ServiceFactory $serviceFactory - (Dependency injection) If not provided, a ServiceFactory instance will be constructed.
     */
    public function __construct(ServiceFactory $serviceFactory = null)
    {
        if (null === $serviceFactory) {
            // Create the service factory
            $serviceFactory = new ServiceFactory();
        }
        $this->_serviceFactory = $serviceFactory;
    }
    /**
     * Detect config and set data from it
     *
     * @param string $service
     */
    public function setConfig($service)
    {
        $accessor = '::';
        // if config/oauth-lumen.php exists use this one
        if (config('oauth-lumen.consumers') != null) {
            $accessor = '.';
        }
        $this->storageClass  = config("oauth-lumen{$accessor}storage", $this->storageClass);
        $this->client_id     = config("oauth-lumen{$accessor}consumers.$service.client_id");
        $this->client_secret = config("oauth-lumen{$accessor}consumers.$service.client_secret");
        $this->scope         = config("oauth-lumen{$accessor}consumers.$service.scope", []);
    }
    /**
     * Create storage instance
     *
     * @param string $storageName
     *
     * @return OAuth\Common\\Storage
     */
    public function createStorageInstance($storageClass)
    {
        $storage = new $storageClass();
        return $storage;
    }
    /**
     * Set the http client object
     *
     * @param string $httpClientName
     *
     * @return void
     */
    public function setHttpClient($httpClientName)
    {
        $httpClientClass = "\\OAuth\\Common\\Http\\Client\\$httpClientName";
        $this->_serviceFactory->setHttpClient(new $httpClientClass());
    }
    /**
     * Register a custom service to classname mapping.
     *
     * @param string $serviceName Name of the service
     * @param string $className   Class to instantiate
     *
     * @return ServiceFactory
     *
     * @throws Exception If the class is nonexistent or does not implement a valid ServiceInterface
     */
    public function registerService($serviceName, $className)
    {
        return $this->_serviceFactory->registerService($serviceName, $className);
    }
    /**
     * @param  string $service
     * @param  string $url
     * @param  array $scope
     *
     * @return \OAuth\Common\Service\AbstractService
     */
    public function consumer($service, $url = null, $scope = null)
    {
        // get config
        $this->setConfig($service);
        // get storage object
        $storage = $this->createStorageInstance($this->storageClass);
        // create credentials object
        $credentials = new Credentials(
            $this->client_id,
            $this->client_secret,
            $url ? : url()
        );
        // check if scopes were provided
        if (is_null($scope)) {
            // get scope from config (default to empty array)
            $scope = $this->scope;
        }
        // return the service consumer object
        return $this->_serviceFactory->createService($service, $credentials, $storage, $scope);
    }
}
