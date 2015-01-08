# Elastification Php Client Bundle

---

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
      elasticsearch_version: 1.4.1
      repository_serializer_dic_id: elastification_php_client.serializer.native #default: elastification_php_client.serializer.native
      logging_enabled: true
      profiler_enabled: true
      
---

## DIC

The registered DIC service id for the client is **elastification_php_client**

### Serializers
Native Serializer Service Id: elastification_php_client.serializer.native

### Repositories
Document Repository Serivce Id: elastification_php_client.repository.document

---

## Versions

---

## HowTo

---
## Examples

### Example for Document repository

gets an document by id.
This code is an example that can be performed within an action of a controller.

    /** @var DocumentRepositoryInterface $docRepo */
    $docRepo = $this->get('elastification_php_client.repository.document');

    var_dump($docRepo->get('my-index', 'my-type', '1107802-001-EN-33'));

### Example for simple search query with native serializer and no preconfigured requests

create an index and type in your elasticsearch and add some sample data in there.
This code is an example that can be performed within an action of a controller.

    /** @var Client $client */
    $client = $this->get('elastification_php_client');

    $request = new SearchRequest('my-index', 'my-type', new NativeJsonSerializer());
    $response = $client->send($request);
    //get the raw deserialized data
    var_dump($response->getData()->getGatewayValue());
    //for grabbing into the result do: $response->getData()['hits']

---

## ToDo

- [x] create version for lib (0.1.0)
- [x] create version for bundle
- [] implement thrift config
- [] create jms serializer services
- [] create services for repository
- [] php-client enable/disable response (debug) output