<?php

declare(strict_types=1);

namespace GraphQL\Doctrine\Factory;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Persistence\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\EntityManager;
use GraphQL\Doctrine\Exception;
use GraphQL\Doctrine\Types;

/**
 * Abstract factory to be aware of types and entityManager
 */
abstract class AbstractFactory
{
    /**
     * @var Types
     */
    protected $types;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct(Types $types, EntityManager $entityManager)
    {
        $this->types = $types;
        $this->entityManager = $entityManager;
    }

    /**
     * Get annotation reader
     *
     * @return Reader
     */
    final protected function getAnnotationReader(): Reader
    {
        $mappingDriver = $this->entityManager->getConfiguration()->getMetadataDriverImpl();
+        if ($mappingDriver instanceof AnnotationDriver) {
+            return $mappingDriver->getReader();
+        }
+        if ($mappingDriver instanceof MappingDriverChain) {
+            foreach ($mappingDriver->getDrivers() as $driver) {
+                if ($driver instanceof AnnotationDriver) {
+                    return $driver->getReader();
+                }
+            }
+        }
+
+        throw new \Exception('AnnotationDriver not found in the entityManager');
    }
}
