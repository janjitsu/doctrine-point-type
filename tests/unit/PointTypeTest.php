<?php

use Doctrine\Tests\DBAL\Mocks\MockPlatform;
use Doctrine\DBAL\Types\Type;

require __DIR__ . '/../bootstrap.php';

/**
 * Class PointTypeTest
 */
class PointTypeTest extends \Doctrine\Tests\DbalTestCase
{
    /**
     * @var \Viny\PointType $type
     */
    protected $platform, $type;

    public static function setUpBeforeClass()
    {
        Type::addType('point', 'Viny\\PointType');
    }

    public function setUp()
    {
        $this->platform = new MockPlatform();
        $this->type = Type::getType('point');
    }

    public function testConvertToPHPValue()
    {
        $point = $this->type->convertToPHPValue('POINT(-13.33444 -22.00444)', $this->platform);

        $this->assertInstanceOf("Viny\\Point", $point);
        $this->assertInstanceOf("Doctrine\\DBAL\\Types\\Type", $this->type);
        $this->assertEquals(-13.33444, $point->getLongitude());
        $this->assertEquals(-22.00444, $point->getLatitude());
        $this->assertTrue($this->type->canRequireSQLConversion(), "Point type require SQL conversion to work.");
        $this->assertEquals('PointFromText(POINT(-22.004440 -13.334440))', $this->type->convertToDatabaseValueSQL($point, $this->platform));
        $this->assertEquals('AsText(POINT(-22.004440 -13.334440))', $this->type->convertToPHPValueSQL($point, $this->platform));
        $this->assertEquals(\Viny\PointType::POINT, $this->type->getName());
        $this->assertEquals(strtoupper(\Viny\PointType::POINT), $this->type->getSQLDeclaration([], $this->platform));
        $this->assertEquals('POINT(-13.334440 -22.004440)', $this->type->convertToDatabaseValue($point, $this->platform));
        $this->assertInstanceOf('Viny\\Point', $this->type->convertToPHPValue($point, $this->platform));
    }

    public function testNullValue()
    {
        $point = $this->type->convertToPHPValue(null, $this->platform);

        $this->assertEquals(null, $point);
    }
}
