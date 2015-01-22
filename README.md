# Elastification Php Client Bundle
[![Build Status](https://travis-ci.org/elastification/php-client-bundle.svg?branch=master)](https://travis-ci.org/elastification/php-client-bundle)


---

## Config

Here is the configuration section for using the bundle

### Composer

Required in *composer.json*
    
   ```"elastification/php-client-bundle": "<1.0"```
   
   
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
```yml
elastification_php_client:
  host: 127.0.0.1
  port: 9200
  protocol: http # http/thrift
  elasticsearch_version: 1.4.1
  repository_serializer_dic_id: elastification_php_client.serializer.native #default: elastification_php_client.serializer.native
  replace_version_of_tagged_requests: true #default: false
  logging_enabled: true
  profiler_enabled: true
  jms_serializer_class_map:
      - {index: my-index, type: my-type, class: AppBundle\Entity\MyEntity}
```

---

## DIC

The registered DIC service id for the client is **elastification_php_client**


### Serializers

Native Serializer Service Id: **elastification_php_client.serializer.native**

Jms Serializer Search Service Id: **elastification_php_client.serializer.jms.search**

Jms Serializer Document Service Id: **elastification_php_client.serializer.jms.document**


### Repositories

Document Repository Serivce Id: **elastification_php_client.repository.document**

Search Repository Serivce Id: **elastification_php_client.repository.search**

Index Repository Serivce Id: **elastification_php_client.repository.index**


### Tagged request
If you want to register request services you can tag them with: **elastification_php_client.request**


---

## Versions

---

## HowTo

---
## Examples

For all examples should create some sample data in your elasticsearch.
 
### Example for Search Repository

Performs a simple search.
This code is an example that can be performed within an action of a controller.

```php
/** @var SearchRepositoryInterface $searchRepo */
$searchRepo = $this->get('elastification_php_client.repository.search');

$query = array(
    'query' => array(
        'term' => array(
            'country' => array(
                'value' => 'germany'
            )
        )
    )
);

$searchRepo->search('my-index', 'my-type', $query);
var_dump($response->getHits());
```

### Example for Document Repository

Gets a single document by id.
This code is an example that can be performed within an action of a controller.

```php
/** @var DocumentRepositoryInterface $docRepo */
$docRepo = $this->get('elastification_php_client.repository.document');

var_dump($docRepo->get('my-index', 'my-type', 'yourDocumentId'));
```
    
### Examples for Index Repository

Checks if an index exists
This code is an example that can be performed within an action of a controller.

```php
/** @var IndexRepositoryInterface $indexRepo */
$indexRepo = $this->get('elastification_php_client.repository.index');
var_dump($indexRepo->exists('my-index'));
```

Creates an index
This code is an example that can be performed within an action of a controller.`

```php
/** @var IndexRepositoryInterface $indexRepo */
$indexRepo = $this->get('elastification_php_client.repository.index');
var_dump($indexRepo->create('my-index'));
```
### Example for simple search query with native serializer and no preconfigured requests

This code is an example that can be performed within an action of a controller.

```php
/** @var Client $client */
$client = $this->get('elastification_php_client');

$request = new SearchRequest('my-index', 'my-type', new NativeJsonSerializer());
$response = $client->send($request);
//get the raw deserialized data
var_dump($response->getData()->getGatewayValue());
//for grabbing into the result do: $response->getData()['hits']
```

### Example for tagging request services and using the request manager
 
Here is an example of a tagged request as service. The id parameter is optional. If this is not set, the request service id will be used.
If the config parameter replace_version_of_tagged_requests is set to true. All registered requests will be parsed and set to the configured version.

```php
request.getdocument:
    class: "Elastification\Client\Request\V090x\GetDocumentRequest"g
    arguments: ["my-index", "my-type", @elastification_php_client.serializer.native]
    public: false
    tags:
      - { name: elastification_php_client.request, id: get.service.text }
```
    
Using a registered request and perform a request.
This code is an example that can be performed within an action of a controller.

```php
$request = $client->getRequest('get.service.text');
$request->setId('yourDocumentId');
$response = $client->send($request);
var_dump($response->getData()->getGatewayValue());
``` 
---

## ToDo

- [x] create version for lib (0.1.0)
- [x] create version for bundle
- [] implement thrift config
- [x] create jms serializer services
- [x] create services for document repository
- [x] create services for search repository
- [x] create jms serializer service if jms serializer is available
- [x] client lib: create jms document entity
- [x] create document jms serializer service
- [] php-client enable/disable response (debug) output ?