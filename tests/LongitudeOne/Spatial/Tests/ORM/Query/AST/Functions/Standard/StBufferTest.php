<?php
/**
 * This file is part of the doctrine spatial extension.
 *
 * PHP 8.1
 *
 * (c) Alexandre Tranchant <alexandre.tranchant@gmail.com> 2017 - 2022
 * (c) Longitude One 2020 - 2022
 * (c) 2015 Derek J. Lambert
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace LongitudeOne\Spatial\Tests\ORM\Query\AST\Functions\Standard;

use LongitudeOne\Spatial\Tests\Helper\PointHelperTrait;
use LongitudeOne\Spatial\Tests\OrmTestCase;

/**
 * ST_Buffer DQL function tests.
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://alexandre-tranchant.mit-license.org MIT
 *
 * @group dql
 *
 * @internal
 *
 * @coversDefaultClass
 */
class StBufferTest extends OrmTestCase
{
    use PointHelperTrait;

    /**
     * Setup the function type test.
     */
    protected function setUp(): void
    {
        $this->usesEntity(self::POINT_ENTITY);
        $this->supportsPlatform('postgresql');

        parent::setUp();
    }

    /**
     * Test a DQL containing function to test in the select.
     *
     * @group geometry
     */
    public function testSelectStBuffer()
    {
        $pointO = $this->persistPointO();
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
            'SELECT p, ST_AsText(ST_Buffer(p.point, 4, :p)) FROM LongitudeOne\Spatial\Tests\Fixtures\PointEntity p'
        );

        $query->setParameter('p', 'quad_segs=4', 'string');

        $result = $query->getResult();

        static::assertCount(1, $result);
        static::assertEquals($pointO, $result[0][0]);
        // too many error between OS, this test doesn't have to check the result (double float, etc.),
        // but it has to check that point becomes a polygon.
        static::assertStringStartsWith('POLYGON((4 0', $result[0][1]);
    }
}
