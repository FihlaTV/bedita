<?php
namespace BEdita\Core\Test\TestCase\Model\Table;

use BEdita\Core\Model\Table\LocationsTable;
use BEdita\Core\Utility\Database;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Model\Table\LocationsTable} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Table\LocationsTable
 */
class LocationsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \BEdita\Core\Model\Table\LocationsTable
     */
    public $Locations;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.relations',
        'plugin.BEdita/Core.relation_types',
        'plugin.BEdita/Core.objects',
        'plugin.BEdita/Core.locations'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Locations = TableRegistry::get('Locations');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Locations);

        parent::tearDown();
    }

    /**
     * Data provider for `testFindGeo` test case.
     *
     * @return array
     */
    public function findGeoProvider()
    {
        return [
            'nearPoint' => [
                [
                    'center' => '44.4944876,11.3464721',
                ],
                1,
            ],
            'nearArray' => [
                [
                    'center' => [44.4944183, 11.3464055],
                ],
                1,
            ],
            'otherFilter' => [
                [
                    'coords' => [44.4944183, 11.3464055],
                ],
                1,
            ],
        ];
    }

    /**
     * Test findGeo finder method.
     *
     * @param array $conditions Date conditions.
     * @param array|false $numExpected Number of expected results.
     * @return void
     *
     * @dataProvider findGeoProvider
     * @covers ::findGeo()
     */
    public function testFindGeo($conditions, $numExpected)
    {
        $info = Database::basicInfo();
        if ($info['vendor'] !== 'mysql' || $info['version'] < '5.7') {
            $this->markTestSkipped('Only MySQL >= 5.7 supported in testFindGeo');
        }

        $result = $this->Locations->find('geo', $conditions)->toArray();

        static::assertEquals($numExpected, count($result));
    }
}
