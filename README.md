# Puzzle Server
**=========================**

SYmfony API RESt with OAuth2 

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the following command to download the latest stable version of this bundle:

`composer require webundle/pz-oauth2-server`

### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
{
    $bundles = array(
    // ...

    new FOS\OAuthServerBundle\FOSOAuthServerBundle(),
            new Nelmio\ApiDocBundle\NelmioApiDocBundle(),
            new FOS\RestBundle\FOSRestBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle(),
            new FOS\HttpCacheBundle\FOSHttpCacheBundle(),
            new Bazinga\Bundle\HateoasBundle\BazingaHateoasBundle(),
            new Hautelook\TemplatedUriBundle\HautelookTemplatedUriBundle(),
            new Bazinga\Bundle\RestExtraBundle\BazingaRestExtraBundle(),
            new Nelmio\CorsBundle\NelmioCorsBundle(),
            new Nelmio\SecurityBundle\NelmioSecurityBundle(),
            new Knp\DoctrineBehaviors\Bundle\DoctrineBehaviorsBundle(),
            new Puzzle\OAuthServerBundle\PuzzleOAuthServerBundle(),
                    );

 // ...
}

 // ...
}
```

### Step 3: Routes

Load the bundle's routing definition in the application (usually in the `app/config/routing.yml` file):

# app/config/routing.yml
```yaml

puzzle_oauth_server:
    resource: "@PuzzleOAuthServerBundle/Resources/config/routing.yml"
    prefix:   /
    host: '%host_account%'
    
# Nelmio Routing
app.swagger_ui:
    path: /doc
    methods: GET
    defaults: { _controller: nelmio_api_doc.controller.swagger_ui }
    host: '%host_apis%'
    
app.swagger:
    path: /doc.json
    methods: GET
    defaults: { _controller: nelmio_api_doc.controller.swagger }
    host: '%host_apis%'

```

### Step 4: Config

Then, enable management bundle via admin modules interface by adding it to the list of registered bundles in the `app/config/config.yml` file of your project under:

```yaml
# Client Admin
imports:
    - { resource: parameters.yml }
    - { resource: services.yml }
    - { resource: security.yml }
    - { resource: '@PuzzleOAuthServerBundle/Resources/config/services.yml' }

# Put parameters here that don't need to change on each machine where the app is deployed
# https://symfony.com/doc/current/best_practices/configuration.html#application-related-configuration
parameters:
    locale: en
    knp.doctrine_behaviors.blameable_subscriber.user_entity: Puzzle\OAuthServerBundle\Entity\User

framework:
    #esi: ~
    #translator: { fallbacks: ['%locale%'] }
    secret: '%secret%'
    router:
        resource: '%kernel.project_dir%/app/config/routing.yml'
        strict_requirements: ~
    form: ~
    csrf_protection: ~
    validation: { enable_annotations: true }
    #serializer: { enable_annotations: true }
    default_locale: '%locale%'
    trusted_hosts: ~
    session:
        # https://symfony.com/doc/current/reference/configuration/framework.html#handler-id
#        handler_id: session.handler.native_file
        save_path: '%kernel.project_dir%/var/sessions/%kernel.environment%'
    fragments: ~
    http_method_override: true
    assets: ~
    php_errors:
        log: true
    templating:
        engines: ['twig'] 

# Twig Configuration
twig:
    debug: '%kernel.debug%'
    strict_variables: '%kernel.debug%'
    
# Assetic
assetic:
    debug:          '%kernel.debug%'
    use_controller: '%kernel.debug%'
    filters:
        cssrewrite: ~
# Doctrine Configuration
doctrine:
    dbal:
        driver: pdo_mysql
        host: '%database_host%'
        port: '%database_port%'
        dbname: '%database_name%'
        user: '%database_user%'
        password: '%database_password%'
        charset: UTF8
        # if using pdo_sqlite as your database driver:
        #   1. add the path in parameters.yml
        #     e.g. database_path: '%kernel.project_dir%/var/data/data.sqlite'
        #   2. Uncomment database_path in parameters.yml.dist
        #   3. Uncomment next line:
        #path: '%database_path%'

    orm:
        auto_generate_proxy_classes: '%kernel.debug%'
        naming_strategy: doctrine.orm.naming_strategy.underscore
        auto_mapping: true

# Swiftmailer Configuration
swiftmailer:
    transport: '%mailer_transport%'
    host: '%mailer_host%'
    username: '%mailer_user%'
    password: '%mailer_password%'
    spool: { type: memory }

# SensioFrameworkExtra Configuration
sensio_framework_extra:
    request: { converters: true }

# FOSOauthServer Configuration
fos_oauth_server:
    db_driver: orm
    client_class:         Puzzle\OAuthServerBundle\Entity\Client
    access_token_class:   Puzzle\OAuthServerBundle\Entity\AccessToken
    refresh_token_class:  Puzzle\OAuthServerBundle\Entity\RefreshToken
    auth_code_class:      Puzzle\OAuthServerBundle\Entity\AuthCode
    model_manager_name:         ~ # change it to the name of your entity/document manager if you don't want to use the default one.
    authorize:
        form:
            type:               fos_oauth_server_authorize
            handler:            fos_oauth_server.authorize.form.handler.default
            name:               fos_oauth_server_authorize_form
            validation_groups:
                # Defaults:
                - Authorize
                - Default
    service:
        storage:                fos_oauth_server.storage.default
        client_manager:         fos_oauth_server.client_manager.default
        access_token_manager:   fos_oauth_server.access_token_manager.default
        refresh_token_manager:  fos_oauth_server.refresh_token_manager.default
        auth_code_manager:      fos_oauth_server.auth_code_manager.default
        user_provider:          pos.provider.user
        options:
            # Prototype
            key:                    []
            supported_scopes:       
                - user
            access_token_lifetime:  1296000 #15jours
            refresh_token_lifetime: 1299600 #16jours
            #auth_code_lifetime: 30

            # Token type to respond with. Currently only "Bearer" supported.
            #token_type: string

            #realm:

            # Enforce redirect_uri on input for both authorize and token steps.
            enforce_redirect: true #or false

            # Enforce state to be passed in authorization (see RFC 6749, section 10.12)
            enforce_state: true #or false

# Nelmio API Doc Configuration
nelmio_api_doc:
    documentation:
        host:                   '%host_apis%'
        schemes:                [http, https]
        info:
            title:              Puzzle API Documentation
            description:        Puzzle API Documentation !
            version:            1.0.0
        securityDefinitions:
            Bearer:
                type:           apiKey
                description:    'Value: Bearer {jwt}'
                name:           Authorization
                in:             header
        security:
            - Bearer: []
    areas:
        path_patterns: # an array of regexps
            - ^/(?!/doc$)
        host_patterns:
            - ^api\.

# FOSRest Configuration
fos_rest:
    disable_csrf_role: ROLE_API
    body_listener:
        enabled: true
#        array_normalizer:
#            service: fos_rest.normalizer.camel_keys_with_leading_underscore
#            forms: true
    body_converter:
        enabled: true
    format_listener:
        rules:
            - { path: ^/doc, host: '%host_apis%', priorities: [ json, html, xml ], fallback_format: xml, prefer_extension: true }
            - { path: ^/, host: '%host_apis%', priorities: [ json, html, xml ], fallback_format: json, prefer_extension: true }
    param_fetcher_listener: true
    routing_loader:
       default_format: json
       include_format: true
    view:
        mime_types:
            json: ['application/javascript+jsonp', 'application/json', 'application/json;version=1.0', 'application/json;version=1.1']
        view_response_listener: 'force'
        formats:
            json: true
            xml:  true
            html: true
        templating_formats:
            html: true
        force_redirects:
            html: true
        failed_validation: HTTP_BAD_REQUEST
    versioning:
            enabled: true
            resolvers:
                media_type:
                    enabled: true
    exception:
        codes:
            'Symfony\Component\Routing\Exception\ResourceNotFoundException': 404
            'Symfony\Component\Security\Core\Exception\AuthenticationException': 401
            'Symfony\Component\Security\Core\Exception\AccessDeniedException' : 403
            'Doctrine\ORM\OptimisticLockException': HTTP_CONFLICT
        messages:
            'Symfony\Component\Routing\Exception\ResourceNotFoundException': true
            'Symfony\Component\Security\Core\Exception\AuthenticationException': true
            'Symfony\Component\Security\Core\Exception\AccessDeniedException' : true
            'Doctrine\ORM\OptimisticLockException': true
    allowed_methods_listener: true
    access_denied_listener:
        json: true
    zone:
        - { host:'%host_apis%', path: ^/v1/* }

fos_http_cache:
    cache_control:
        rules:
            # the controls section values are used in a call to Response::setCache();
            -
                match:
                    path: ^/v1
                    methods: [GET]
                headers:
                    cache_control: { public: true, max_age: 3600, s_maxage: 3600 }
                    last_modified: "-1 hour"
                    vary: [Accept-Encoding, Accept-Language]

# Nelmio CORS Configuration
nelmio_cors:
    defaults:
        allow_credentials: false
        allow_origin: []
        allow_headers: []
        allow_methods: []
        expose_headers: []
        max_age: 0
        hosts: []
        origin_regex: false
    paths:
        '^/v1/':
            allow_origin: ['*']
            allow_headers: ['Origin', 'X-Requested-With', 'Content-Type', 'Accept', 'X-Custom-Auth', 'Authorization']
            allow_methods: ['POST', 'PUT', 'GET', 'DELETE', 'OPTIONS']
            max_age: 3600
        
        '^/public/':
            allow_origin: ['*']
            allow_headers: ['Origin', 'X-Requested-With', 'Content-Type', 'Accept', 'X-Custom-Auth', 'Authorization']
            allow_methods: ['GET']
            max_age: 60
            
        '^/uploads/':
            allow_origin: ['*']
            allow_headers: ['Origin', 'X-Requested-With', 'Content-Type', 'Accept', 'X-Custom-Auth', 'Authorization']
            allow_methods: ['GET']
            max_age: 3600
            hosts: ['^apis\.']
            
        '^/':
            origin_regex: true
            allow_origin: ['^http://localhost:[0-9]+']
            allow_headers: ['Origin', 'X-Requested-With', 'Content-Type', 'Accept','X-Custom-Auth', 'Authorization']
            allow_methods: ['POST', 'PUT', 'GET', 'DELETE', 'PATCH', 'OPTIONS']
            max_age: 3600
            hosts: ['^apis\.']  

# Nelmio Security
nelmio_security:
    clickjacking:
        paths:
            '^/.*': DENY

# JMS Serializer
jms_serializer:
    handlers:
        datetime:
            default_format: "Y-m-d\\TH:i:sP" # ATOM
            default_timezone: "UTC" # defaults to whatever timezone set in php.ini or via date_default_timezone_set
        array_collection:
            initialize_excluded: false

    subscribers:
        doctrine_proxy:
            initialize_virtual_types: false
            initialize_excluded: false

    object_constructors:
        doctrine:
            fallback_strategy: "null" # possible values ("null" | "exception" | "fallback")

    property_naming:
        id: 'jms_serializer.identical_property_naming_strategy'
#        separator:  _
#        lower_case: true
#        enable_cache: true

    metadata:
        cache: file
        debug: "%kernel.debug%"
        file_cache:
            dir: "%kernel.cache_dir%/serializer"
        auto_detection: true
        warmup:
            paths:
                included: []
                excluded: []
                
    expression_evaluator:
        id: jms_serializer.expression_evaluator # auto detected

    default_context:
        serialization:
            serialize_null: true
            version: ~
            attributes: {}
            groups: ['Default']
            enable_max_depth_checks: false
        deserialization:
            serialize_null: true
            version: ~
            attributes: {}
            groups: ['Default']
            enable_max_depth_checks: false

# Knp\DoctrineBehaviors
knp_doctrine_behaviors:
    blameable:      true
    geocodable:     ~     # Here null is converted to false
    loggable:       ~
    sluggable:      true
    timestampable:  true
    soft_deletable: true
    # All others behaviors are disabled
```

### Step 5: Parameters

Then, set parameters in the `app/config/parameters.yml` file of your project under:

```yaml
# Multi-host
host_app: exemple.com
host_apis: apis.%host_app%
host_account: accounts.%hosy_app%

# OauthServer Default Options
pos.default_scope: user
pos.interne_client: true

# Global
user.mail: 'user@exemple.com'
```

### Step 6: Security

Then, configure security in the `app/config/security.yml` file of your project under:

```yaml
security:
    encoders:
        Puzzle\OAuthServerBundle\Entity\User: 
            algorithm:                        sha512
            encode_as_base64:                 false
            iterations:                       1
            
    role_hierarchy:
        ROLE_ADMIN:                           ROLE_USER
        ROLE_SUPER_ADMIN:                     [ROLE_ALLOWED_TO_SWITCH, ROLE_ADMIN]

    providers:
        user_provider:
            id:                               pos.provider.user
             
    firewalls:
        secured_area: 
            pattern:                          ^/demo/secured/
            form_login:
                provider:                     user_provider
                check_path:                   _security_check
                login_path:                   _demo_login
            logout: 
                path:                         _demo_logout
                target:                       _demo
        
        dev:
            pattern:                          ^/(_(profiler|wdt)|css|images|js)/
            security:                         false
        
        public:
            host:                             '%host_apis%'
            pattern:                          '^/public'
            anonymous:                        ~    
            
        api:
            host:                             '%host_apis%'
            pattern:                          '^/v1'
            fos_oauth:                        true
            stateless:                        true
            anonymous:                        true
            
        oauth_token:
            host: "%host_account%"
            pattern:    ^/oauth/v2/token
            security:       false
            
        oauth_authorize:
            host:                             '%host_account%'
            pattern:                          '^/oauth/v2'
#            entry_point:                     pos.security.authentication.form_entry_point
            entry_point:                      null
            logout_on_user_change:            true
            form_login:
                provider:                     user_provider
                check_path:                   oauth_login_check
                login_path:                   oauth_login
                success_handler:              pos.handler.authentication_success
                username_parameter:           _username
                password_parameter:           _password
                csrf_parameter:               _csrf_token
                csrf_token_id:                authenticate
                post_only:                    true
                remember_me:                  true
                require_previous_session:     true
            switch_user:
                provider:                     user_provider
                parameter:                    _swu
                role:                         ROLE_ALLOWED_TO_SWITCH
            remember_me:
                secret:                       '%secret%'
                path:                         /
                domain:                       '%host_account%'
                secure:                       false
                httponly:                     true
                lifetime:                     31536000
                remember_me_parameter:        _remember_me
            logout:
                path:                         logout
                target:                       pos_oauth_login
                invalidate_session:           true
                delete_cookies:
                    a:                        { path: /, domain: ~ }
            anonymous:                        true

        main:
            host:                             '%host_account%'
            pattern:                          '^/'
            entry_point:                     pos.security.authentication.form_entry_point
            entry_point:                      null
            logout_on_user_change:            true
            form_login:
                provider:                     user_provider
                check_path:                   login_check
                login_path:                   login
                success_handler:              pos.handler.authentication_success
                username_parameter:           _username
                password_parameter:           _password
                csrf_parameter:               _csrf_token
                csrf_token_id:                authenticate
                post_only:                    true
                remember_me:                  true
                require_previous_session:     true
            switch_user:
                provider:                     user_provider
                parameter:                    _swu
                role:                         ROLE_ALLOWED_TO_SWITCH
            remember_me:
                secret:                       '%secret%'
                path:                         /
                domain:                       '%host_account%'
                secure:                       false
                httponly:                     true
                lifetime:                     31536000
                remember_me_parameter:        _remember_me
            logout:
                path:                         logout
                target:                       login
                invalidate_session:           true
                delete_cookies:
                    a:                        { path: /, domain: ~ }
            anonymous:                        true

    access_control:
        - { path: ^/oauth/v2/login, roles: IS_AUTHENTICATED_ANONYMOUSLY, host: '%host_account%' }
        - { path: ^/login, roles: IS_AUTHENTICATED_ANONYMOUSLY, host: '%host_account%' }
        - { path: ^/register, roles: IS_AUTHENTICATED_ANONYMOUSLY, host: '%host_account%' }
        - { path: ^/, roles: ROLE_USER, host: '%host_account%' }
        # API Zone
        - { path: ^/doc, roles: IS_AUTHENTICATED_ANONYMOUSLY, host: '%host_apis%' }
        - { path: ^/public, roles: IS_AUTHENTICATED_ANONYMOUSLY, host: '%host_apis%' }
        - { path: ^/v1, roles: ROLE_USER, host: '%host_apis%' }

```


### Step 7: Services

Then, set parameters in the `app/config/services.yml` file of your project under:

```yaml
services:
    # default configuration for services in *this* file
    _defaults:
        # automatically injects dependencies in your services
        autowire: true
        # automatically registers your services as commands, event subscribers, etc.
        autoconfigure: true
        # this means you cannot fetch services directly from the container via $container->get()
        # if you need to do this, you can override this setting on individual services
        public: false
    
    # Default Services
    Puzzle\OAuthServerBundle\:
        resource: '../../src/Puzzle/OAuthServerBundle/*'
        public: true
        exclude: '../../src/Puzzle/OAuthServerBundle/{Entity,Repository,Tests,Form,Twig,Event,EventListener,Security,Traits}'
    
    # Controller
    Puzzle\OAuthServerBundle\Controller\:
        resource: '../../src/Puzzle/OAuthServerBundle/Controller'
        public: true
        tags: ['controller.service_arguments']
    
    # Alias OAuthServer
    Puzzle\OAuthServerBundle\Provider\UserProvider: '@pos.provider.user'
    Puzzle\OAuthServerBundle\Service\OAuth2: '@fos_oauth_server.server'
    Puzzle\OAuthServerBundle\Service\OAuthStorage: '@fos_oauth_server.storage.default'
    Puzzle\OAuthServerBundle\Form\FormConfig: '@fos_oauth_server.authorize.form_config'
    Puzzle\OAuthServerBundle\Form\Form: '@fos_oauth_server.authorize.form'
    Puzzle\OAuthServerBundle\Form\Handler\AuthorizeFormHandler: '@fos_oauth_server.authorize.form.handler.default'
    FOS\OAuthServerBundle\Model\TokenManagerInterface: '@fos_oauth_server.access_token_manager'
    
    # Alias API
    Puzzle\OAuthServerBundle\Service\Repository: '@papis.repository'
    Puzzle\Api\MediaBundle\Service\MediaManager: '@papis.media_manager'
    Puzzle\Api\MediaBundle\Service\MediaUploader: '@papis.media_uploader'
    Puzzle\OAuthServerBundle\Service\ErrorFactory: '@papis.error_factory'
```
