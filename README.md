# Elastification Php Client Bundle


## Config

Here is the configuration section for using the bundle

### Composer

Required in *composer.json*
    
   ```"elastification/php-client-bundle": "dev-master"```
   
   
You have choose the right package for your transport. If you do not have installed thrift you should use guzzle

  ```"guzzlehttp/guzzle": "~4.2"```
  
  
For Thrift transport

  ```"munkie/elasticsearch-thrift-php": "~1.4"```
  
  
Don't forget to run composer update after you finished your composer.json

  
### AppKernel  

In your *app/config/AppHernel.php* file you should activate the bundle by adding it to the array

    $bundles[] = new Elastification\Bundle\ElastificationPhpClientBundle\ElastificationPhpClientBundle();


### App Config

In your app/config.yml or environment based you can add params (full config example):
    
    elastification_php_client:
      host: 127.0.0.1
      port: 9200
      protocol: http # http/thrift
      logging_enabled: true
      profiler_enabled: true
      

## DIC

The registered DIC service id is **elastification_php_client**


## Versions

## HowTo

### Example for simple search query with native serializer and no preconfigured requests

create an index and type in your elasticsearch and add some sample data in there.
This code is an examle that can be performed within an action of a controller.

    /** @var Client $client */
    $client = $this->get('elastification_php_client');

    $request = new SearchRequest('ank-develop-service', 'text', new NativeJsonSerializer());
    $response = $client->send($request);
    //get the raw deserialized data
    var_dump($response->getData()->getGatewayValue());
    //for grabbing into the result do: $response->getData()['hits']


## ToDo

- [] create version for lib
- [] create version for bundle
- [] implement thrift config
- [] create services for repository
- [] php-client enable/disable response (debug) output