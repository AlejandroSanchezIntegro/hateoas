<?php
/**
 * @copyright 2014 Integ S.A.
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @author Javier Lorenzana <javier.lorenzana@gointegro.com>
 */

namespace GoIntegro\Hateoas\JsonApi\Serializer;

// Mocks.
use Codeception\Util\Stub;

class PaginationMetadataSerializerTest extends \PHPUnit_Framework_TestCase
{
    const RESOURCE_TYPE = 'resources';

    public function testSerializingPaginatedDocument()
    {
        /* Given... (Fixture) */
        $size = 3;
        $offset = 10;
        $resources = self::createResourcesMock($size, $offset);
        $pagination = Stub::makeEmpty(
            'GoIntegro\Hateoas\JsonApi\DocumentPagination',
            [
                'total' => 1000,
                'size' => $size,
                'page' => 5,
                'offset' => $offset
            ]
        );
        $document = Stub::makeEmpty(
            'GoIntegro\Hateoas\JsonApi\Document',
            [
                'wasCollection' => TRUE, // Key to this test.
                'resources' => $resources,
                'getResourceMeta' => function() { return []; },
                'pagination' => $pagination
            ]
        );
        $serializer = new PaginationMetadataSerializer;
        /* When... (Action) */
        $json = $serializer->serialize($document);
        /* Then... (Assertions) */
        $this->assertEquals([
            'page' => 5,
            'size' => 3,
            'total' => 1000
        ], $json);
    }

    public function testSerializingEmptyPaginatedDocument()
    {
        /* Given... (Fixture) */
        $offset = 10;
        $resources = self::createResourcesMock(0, $offset);
        $pagination = Stub::makeEmpty(
            'GoIntegro\Hateoas\JsonApi\DocumentPagination',
            [
                'total' => 0,
                'size' => 0,
                'page' => 0,
                'offset' => $offset
            ]
        );
        $document = Stub::makeEmpty(
            'GoIntegro\Hateoas\JsonApi\Document',
            [
                'wasCollection' => TRUE, // Key to this test.
                'resources' => $resources,
                'getResourceMeta' => function() { return []; },
                'pagination' => $pagination
            ]
        );
        $serializer = new PaginationMetadataSerializer;
        /* When... (Action) */
        $json = $serializer->serialize($document);
        /* Then... (Assertions) */
        $this->assertEquals([
            'page' => 0,
            'size' => 0,
            'total' => 0
        ], $json);
    }

    /**
     * @param integer $amount
     * @param integer $offset
     * @return \GoIntegro\Hateoas\JsonApi\ResourceCollection
     */
    private static function createResourcesMock($amount, $offset = 0)
    {
        $metadata = Stub::makeEmpty(
            'GoIntegro\Hateoas\Metadata\Resource\ResourceMetadata',
            [
                'type' => self::RESOURCE_TYPE,
                'subtype' => self::RESOURCE_TYPE,
                'fields' => []
            ]
        );

        $resources = [];
        for ($i = 0; $i < $amount; ++$i) {
            $resources[] = Stub::makeEmpty(
                'GoIntegro\Hateoas\JsonApi\EntityResource',
                [
                    'id' => (string) $offset,
                    'getMetadata' => function() use ($metadata) {
                        return $metadata;
                    }
                ]
            );
            ++$offset;
        }

        $collection = Stub::makeEmpty(
            'GoIntegro\Hateoas\JsonApi\ResourceCollection',
            [
                'getMetadata' => function() use ($metadata) {
                    return $metadata;
                },
                'getIterator' => function() use ($resources) {
                    return new \ArrayIterator($resources);
                },
                'count' => function() use ($resources) {
                    return count($resources);
                }
            ]
        );

        return $collection;
    }
}
