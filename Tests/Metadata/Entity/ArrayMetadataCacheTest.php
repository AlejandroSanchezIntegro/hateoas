<?php
/**
 * @copyright 2014 Integ S.A.
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @author Javier Lorenzana <javier.lorenzana@gointegro.com>
 */

namespace Metadata\Entity;

// Mocks.
use Codeception\Util\Stub;
// Metadata.
use GoIntegro\Hateoas\Metadata\Entity\ArrayMetadataCache;

class ArrayMetadataCacheTest extends \PHPUnit_Framework_TestCase
{
    public function testGettingReflectionForClass()
    {
        /* Given... (Fixture) */
        $entityManager = Stub::makeEmpty(
            'Doctrine\ORM\EntityManagerInterface'
        );
        $metadataCache = new ArrayMetadataCache($entityManager);
        /* When... (Action) */
        $classReflection = $metadataCache->getReflection('ReflectionClass');
        $sameReflection = $metadataCache->getReflection('ReflectionClass');
        /* Then... (Assertions) */
        $this->assertInstanceOf('ReflectionClass', $classReflection);
        $this->assertSame($classReflection, $sameReflection);
    }

    public function testGettingMappingForEntity()
    {
        /* Given... (Fixture) */
        $callOnce = Stub::once(function() { return "The mapping."; });
        $entityManager = Stub::makeEmpty(
            'Doctrine\ORM\EntityManagerInterface',
            ['getClassMetadata' => $callOnce]
        );
        $metadataCache = new ArrayMetadataCache($entityManager);
        /* When... (Action) */
        $classMapping
            = $metadataCache->getMapping('GoIntegro\Hateoas\JsonApi\ResourceEntityInterface');
        $sameMapping
            = $metadataCache->getMapping('GoIntegro\Hateoas\JsonApi\ResourceEntityInterface');
        /* Then... (Assertions) */
        $this->assertEquals('The mapping.', $classMapping);
        $this->assertEquals($classMapping, $sameMapping);
    }
}
