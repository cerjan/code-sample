<?php

declare(strict_types=1);

use App\Config;
use App\Entity\Shipment;
use App\Repository\ShipmentRepository;
use App\Utils\JsonDateTimeFormatter;
use App\Utils\Upload\LabelUpload;
use DI\Bridge\Slim\Bridge;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Monolog\Processor\HostnameProcessor;
use Monolog\Processor\WebProcessor;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\ServerRequest;
use Nyholm\Psr7Server\ServerRequestCreator;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Channel\AMQPChannel;
use Psr\Container\ContainerInterface;
use Scienta\DoctrineJsonFunctions\Query\AST\Functions\Mysql\JsonExtract;
use Slim\App;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use function DI\autowire;

return [
    Config::class => fn (ContainerInterface $container) => Config::from($container->get('config')),
    App::class => function (ContainerInterface $container) {
        $app = Bridge::create($container);

        (require __DIR__ . '/routes.php')($app);
        (require __DIR__ . '/middlewares.php')($app, $container->get('config'), $container->get(Logger::class));

        return $app;
    },
    EntityManager::class => function (ContainerInterface $container) {
        $config = $container->get('config');

        $queryCache = new FilesystemAdapter(namespace: 'doctrine_queries', directory: __DIR__ . '/../var');
        $metadataCache = new FilesystemAdapter(namespace: 'doctrine_metadata', directory: __DIR__ . '/../var');

        $setup = new Configuration();
        $setup->addCustomStringFunction(JsonExtract::FUNCTION_NAME, JsonExtract::class);

        if (!$config['debug']) {
            $setup->setMetadataCache($metadataCache);
            $setup->setQueryCache($queryCache);
        }

        $setup->setNamingStrategy(new UnderscoreNamingStrategy(CASE_LOWER));

        $driverImpl = new AttributeDriver([__DIR__ . '/../src/Entity']);
        $setup->setMetadataDriverImpl($driverImpl);

        $setup->setProxyDir(__DIR__ . '/../src/Proxy');
        $setup->setProxyNamespace('App\Proxy');

        $setup->setAutoGenerateProxyClasses($config['debug']);

        return EntityManager::create($config['database']['doctrine'], $setup);
    },
    ServerRequest::class => function () {
        $psr17Factory = new Psr17Factory();

        $creator = new ServerRequestCreator(
            $psr17Factory,
            $psr17Factory,
            $psr17Factory,
            $psr17Factory
        );

        return $creator->fromGlobals();
    },
    AMQPChannel::class => function (ContainerInterface $container) {
        $config = $container->get('config');
        $connection = new AMQPStreamConnection(...$config['rabbitmq']['connection']);

        return $connection->channel();
    },
    Logger::class => function (Config $config) {
        $logger = new Logger($config->appName);

        $fileHandler = new RotatingFileHandler($config->logFile, 1);
        $fileHandler->setFormatter(new JsonDateTimeFormatter());
        $logger->pushHandler($fileHandler);
        $logger->pushProcessor(new WebProcessor());
        $logger->pushProcessor(new HostnameProcessor());

        return $logger;
    },
    Serializer::class => function () {
        $encoders = [new JsonEncoder()];
        $extractor = new PropertyInfoExtractor(typeExtractors: [new PhpDocExtractor(), new ReflectionExtractor()]);
        $normalizers = [new ObjectNormalizer(propertyTypeExtractor: $extractor), new ArrayDenormalizer()];

        return new Serializer($normalizers, $encoders);
    },
    ValidatorInterface::class => fn () =>
        Validation::createValidatorBuilder()
            ->enableAnnotationMapping()
            ->addDefaultDoctrineAnnotationReader()
            ->getValidator(),
    LabelUpload::class => fn (Config $config) => new LabelUpload($config->shareDir),

    'App\Facade\*Facade' => autowire('App\Facade\*Facade'),
    'App\Services\Carrier\*Carrier' => autowire('App\Service\Carrier\*Carrier'),

    ShipmentRepository::class => fn (EntityManager $em) => $em->getRepository(Shipment::class),
];