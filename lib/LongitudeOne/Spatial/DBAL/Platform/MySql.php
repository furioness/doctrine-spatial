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

namespace LongitudeOne\Spatial\DBAL\Platform;

use LongitudeOne\Spatial\DBAL\Types\AbstractSpatialType;
use LongitudeOne\Spatial\PHP\Types\Geography\GeographyInterface;

/**
 * MySql8 platform. A dirty change for SRID
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://dlambert.mit-license.org MIT
 */
class MySql extends AbstractPlatform
{
    public const DEFAULT_SRID = 4326;

    /**
     * Convert to database value.
     *
     * @param AbstractSpatialType $type    The spatial type
     * @param string              $sqlExpr The SQL expression
     *
     * @return string
     */
    public function convertToDatabaseValueSql(AbstractSpatialType $type, $sqlExpr)
    {
        $srid = self::DEFAULT_SRID;
        return sprintf('ST_GeomFromText(%s, %d)', $sqlExpr, $srid);
    }

    /**
     * Convert to php value to SQL.
     *
     * @param AbstractSpatialType $type    The spatial type
     * @param string              $sqlExpr The SQL expression
     *
     * @return string
     */
    public function convertToPhpValueSql(AbstractSpatialType $type, $sqlExpr)
    {
        return sprintf('ST_AsBinary(%s)', $sqlExpr);
    }

    /**
     * Gets the SQL declaration snippet for a field of this type.
     *
     * @param array $fieldDeclaration array SHALL contains 'type' as key
     *
     * @return string
     */
    public function getSqlDeclaration(array $fieldDeclaration)
    {
        if (GeographyInterface::GEOGRAPHY === $fieldDeclaration['type']->getSQLType()) {
            return 'GEOMETRY';
        }

        return mb_strtoupper($fieldDeclaration['type']->getSQLType());
    }
}
