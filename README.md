# Elastification Php Client Bundle


## Config

In your app/config.yml or environment based you can add params (full config example):

    elastification_php_client:
      host: 127.0.0.1
      port: 9200
      protocol: http # http/thrift
      logging_enabled: true
      profiler_enabled: true
      


## ToDo

- [] php-client composer.json move guzzle to require 
- [] php-client composer.json move jms_serializer to require ? 
- [] implement thrift config
- [] php-client enable/disable response (debug) output