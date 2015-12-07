<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         0.0.1
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Cake\ElasticSearch\Test;

use Cake\ElasticSearch\FilterBuilder;
use Cake\TestSuite\TestCase;
use Elastica\Filter;

/**
 * Tests the FilterBuilder class
 *
 */
class FilterBuilderTest extends TestCase
{

    /**
     * Tests the between() filter
     *
     * @return void
     */
    public function testBetween()
    {
        $builder = new FilterBuilder;
        $result = $builder->between('price', 10, 100);
        $expected = [
            'range' => ['price' => ['gte' => 10, 'lte' => 100]]
        ];
        $this->assertEquals($expected, $result->toArray());

        $result = $builder->between('price', '2014', '2015');
        $expected = [
            'range' => ['price' => ['gte' => '2014', 'lte' => '2015']]
        ];
        $this->assertEquals($expected, $result->toArray());
    }

    /**
     * Tests the bool() filter
     *
     * @return void
     */
    public function testBool()
    {
        $builder = new FilterBuilder;
        $result = $builder->bool();
        $this->assertInstanceOf('Elastica\Filter\BoolFilter', $result);
    }

    /**
     * Tests the exists() filter
     *
     * @return void
     */
    public function testExists()
    {
        $builder = new FilterBuilder;
        $result = $builder->exists('comments');
        $expected = [
            'exists' => ['field' => 'comments']
        ];
        $this->assertEquals($expected, $result->toArray());
    }

    /**
     * Tests the geoBoundingBox() filter
     *
     * @return void
     */
    public function testGeoBoundingBox()
    {
        $builder = new FilterBuilder;
        $result = $builder->geoBoundingBox('location', [40.73, -74.1], [40.01, -71.12]);
        $expected = [
            'geo_bounding_box' => [
                'location' => [
                    'top_left' => [40.73, -74.1],
                    'bottom_right' => [40.01, -71.12]
                ]
            ]
        ];
        $this->assertEquals($expected, $result->toArray());
    }

    /**
     * Tests the geoDistance() filter
     *
     * @return void
     */
    public function testGeoDistance()
    {
        $builder = new FilterBuilder;
        $result = $builder->geoDistance('location', ['lat' => 40.73, 'lon' => -74.1], '10km');
        $expected = [
            'geo_distance' => [
                'location' => ['lat' => 40.73, 'lon' => -74.1],
                'distance' => '10km'
            ]
        ];
        $this->assertEquals($expected, $result->toArray());

        $result = $builder->geoDistance('location', 'dr5r9ydj2y73', '10km');
        $expected = [
            'geo_distance' => [
                'location' => 'dr5r9ydj2y73',
                'distance' => '10km'
            ]
        ];
        $this->assertEquals($expected, $result->toArray());
    }

    /**
     * Tests the geoDistanceRange() filter
     *
     * @return void
     */
    public function testGeoDistanceRange()
    {
        $builder = new FilterBuilder;
        $result = $builder->geoDistanceRange('location', ['lat' => 40.73, 'lon' => -74.1], '5km', '6km');
        $expected = [
            'geo_distance_range' => [
                'location' => ['lat' => 40.73, 'lon' => -74.1],
                'gte' => '5km',
                'lte' => '6km',
            ]
        ];
        $this->assertEquals($expected, $result->toArray());

        $result = $builder->geoDistanceRange('location', 'dr5r9ydj2y73', '10km', '15km');
        $expected = [
            'geo_distance_range' => [
                'location' => 'dr5r9ydj2y73',
                'gte' => '10km',
                'lte' => '15km',
            ]
        ];
        $this->assertEquals($expected, $result->toArray());
    }

    /**
     * Tests the geoPolygon() filter
     *
     * @return void
     */
    public function testGeoPolygon()
    {
        $builder = new FilterBuilder;
        $result = $builder->geoPolygon('location', [
            ['lat' => 40, 'lon' => -70],
            ['lat' => 30, 'lon' => -80],
            ['lat' => 20, 'lon' => -90],
        ]);
        $expected = [
            'geo_polygon' => [
                'location' => [
                    'points' => [
                        ['lat' => 40, 'lon' => -70],
                        ['lat' => 30, 'lon' => -80],
                        ['lat' => 20, 'lon' => -90]
                    ]
                ]
            ]
        ];
        $this->assertEquals($expected, $result->toArray());
    }

    /**
     * Tests the geoShape() filter
     *
     * @return void
     */
    public function testGeoShape()
    {
        $builder = new FilterBuilder;
        $result = $builder->geoShape('location', [
            ['lat' => 40, 'lon' => -70],
            ['lat' => 30, 'lon' => -80],
        ], 'linestring');
        $expected = [
            'geo_shape' => [
                'location' => [
                    'shape' => [
                        'type' => 'linestring',
                        'relation' => 'intersects',
                        'coordinates' => [
                            ['lat' => 40, 'lon' => -70],
                            ['lat' => 30, 'lon' => -80],
                        ]
                    ]
                ]
            ]
        ];
        $this->assertEquals($expected, $result->toArray());
    }

    /**
     * Tests the geoShapeIndex() filter
     *
     * @return void
     */
    public function testGeoShapeIndex()
    {
        $builder = new FilterBuilder;
        $result = $builder->geoShapeIndex('location', 'DEU', 'countries', 'shapes', 'location');
        $expected = [
            'geo_shape' => [
                'location' => [
                    'relation' => 'intersects',
                    'indexed_shape' => [
                        'id' => 'DEU',
                        'type' => 'countries',
                        'index' => 'shapes',
                        'path' => 'location'
                    ]
                ]
            ]
        ];
        $this->assertEquals($expected, $result->toArray());
    }

    /**
     * Tests the geoHashCell() filter
     *
     * @return void
     */
    public function testGeoHashCell()
    {
        $builder = new FilterBuilder;
        $result = $builder->geoHashCell('location', ['lat' => 40.73, 'lon' => -74.1], 3);
        $expected = [
            'geohash_cell' => [
                'location' => ['lat' => 40.73, 'lon' => -74.1],
                'precision' => 3,
                'neighbors' => false,
            ]
        ];
        $this->assertEquals($expected, $result->toArray());

        $result = $builder->geoHashCell('location', 'dr5r9ydj2y73', '50m', true);
        $expected = [
            'geohash_cell' => [
                'location' => 'dr5r9ydj2y73',
                'precision' => '50m',
                'neighbors' => true,
            ]
        ];
        $this->assertEquals($expected, $result->toArray());
    }

    /**
     * Tests the gt() filter
     *
     * @return void
     */
    public function testGt()
    {
        $builder = new FilterBuilder;
        $result = $builder->gt('price', 10);
        $expected = [
            'range' => ['price' => ['gt' => 10]]
        ];
        $this->assertEquals($expected, $result->toArray());

        $result = $builder->gt('year', '2014');
        $expected = [
            'range' => ['year' => ['gt' => '2014']]
        ];
        $this->assertEquals($expected, $result->toArray());
    }

    /**
     * Tests the gte() filter
     *
     * @return void
     */
    public function testGte()
    {
        $builder = new FilterBuilder;
        $result = $builder->gte('price', 10);
        $expected = [
            'range' => ['price' => ['gte' => 10]]
        ];
        $this->assertEquals($expected, $result->toArray());

        $result = $builder->gte('year', '2014');
        $expected = [
            'range' => ['year' => ['gte' => '2014']]
        ];
        $this->assertEquals($expected, $result->toArray());
    }

    /**
     * Tests the hasChild() filter
     *
     * @return void
     */
    public function testHashChild()
    {
        $builder = new FilterBuilder;
        $result = $builder->hasChild($builder->term('user', 'john'), 'comment');
        $expected = [
            'has_child' => [
                'type' => 'comment',
                'filter' => ['term' => ['user' => 'john']]
            ]
        ];
        $this->assertEquals($expected, $result->toArray());
    }

    /**
     * Tests the hasParent() filter
     *
     * @return void
     */
    public function testHashParent()
    {
        $builder = new FilterBuilder;
        $result = $builder->hasParent($builder->term('name', 'john'), 'user');
        $expected = [
            'has_parent' => [
                'type' => 'user',
                'filter' => ['term' => ['name' => 'john']]
            ]
        ];
        $this->assertEquals($expected, $result->toArray());
    }

    /**
     * Tests the ids() filter
     *
     * @return void
     */
    public function testIds()
    {
        $builder = new FilterBuilder;
        $result = $builder->ids([1, 2, 3], 'user');
        $expected = [
            'ids' => [
                'type' => 'user',
                'values' => [1, 2, 3]
            ]
        ];
        $this->assertEquals($expected, $result->toArray());
    }

    /**
     * Tests the indices() filter
     *
     * @return void
     */
    public function testIndices()
    {
        $builder = new FilterBuilder;
        $result = $builder->indices(
            ['a', 'b'],
            $builder->term('user', 'mark'),
            $builder->term('tag', 'wow')
        );
        $expected = [
            'indices' => [
                'indices' => ['a', 'b'],
                'filter' => ['term' => ['user' => 'mark']],
                'no_match_filter' => ['term' => ['tag' => 'wow']]
            ]
        ];
        $this->assertEquals($expected, $result->toArray());
    }

    /**
     * Tests the limit() filter
     *
     * @return void
     */
    public function testLimit()
    {
        $builder = new FilterBuilder;
        $result = $builder->limit(10);
        $expected = [
            'limit' => ['value' => 10]
        ];
        $this->assertEquals($expected, $result->toArray());
    }

    /**
     * Tests the matchAll() filter
     *
     * @return void
     */
    public function testMatchAll()
    {
        $builder = new FilterBuilder;
        $result = $builder->matchAll();
        $expected = [
            'match_all' => new \stdClass
        ];
        $this->assertEquals($expected, $result->toArray());
    }

    /**
     * Tests the lt() filter
     *
     * @return void
     */
    public function testLt()
    {
        $builder = new FilterBuilder;
        $result = $builder->lt('price', 10);
        $expected = [
            'range' => ['price' => ['lt' => 10]]
        ];
        $this->assertEquals($expected, $result->toArray());

        $result = $builder->lt('year', '2014');
        $expected = [
            'range' => ['year' => ['lt' => '2014']]
        ];
        $this->assertEquals($expected, $result->toArray());
    }

    /**
     * Tests the lte() filter
     *
     * @return void
     */
    public function testLte()
    {
        $builder = new FilterBuilder;
        $result = $builder->lte('price', 10);
        $expected = [
            'range' => ['price' => ['lte' => 10]]
        ];
        $this->assertEquals($expected, $result->toArray());

        $result = $builder->lte('year', '2014');
        $expected = [
            'range' => ['year' => ['lte' => '2014']]
        ];
        $this->assertEquals($expected, $result->toArray());
    }

    /**
     * Tests the missing() filter
     *
     * @return void
     */
    public function testMissing()
    {
        $builder = new FilterBuilder;
        $result = $builder->missing('comments');
        $expected = [
            'missing' => ['field' => 'comments']
        ];
        $this->assertEquals($expected, $result->toArray());
    }

    /**
     * Tests the nested() filter
     *
     * @return void
     */
    public function testNested()
    {
        $this->markTestSkipped('Waiting for elastica issue : https://github.com/ruflin/Elastica/issues/1001');
        $builder = new FilterBuilder;
        $result = $builder->nested('comments', $builder->term('author', 'mark'));
        $expected = [
            'nested' => [
                'path' => 'comments',
                'filter' => ['term' => ['author' => 'mark']]]
        ];
        $this->assertEquals($expected, $result->toArray());
    }

    /**
     * Tests the nested() filter
     *
     * @return void
     */
    public function testNestedWithQuery()
    {
        $builder = new FilterBuilder;
        $result = $builder->nested(
            'comments',
            new \Elastica\Query\SimpleQueryString('great')
        );
        $expected = [
            'nested' => [
                'path' => 'comments',
                'query' => ['simple_query_string' => ['query' => 'great']]]
        ];
        $this->assertEquals($expected, $result->toArray());
    }

    /**
     * Tests the not() filter
     *
     * @return void
     */
    public function testNot()
    {
        $builder = new FilterBuilder;
        $result = $builder->not($builder->term('title', 'cake'));
        $expected = [
            'not' => [
                'filter' => ['term' => ['title' => 'cake']]
            ]
        ];
        $this->assertEquals($expected, $result->toArray());
    }

    /**
     * Tests the prefix() filter
     *
     * @return void
     */
    public function testPrefix()
    {
        $builder = new FilterBuilder;
        $result = $builder->prefix('user', 'ki');
        $expected = [
            'prefix' => [
                'user' => 'ki'
            ]
        ];
        $this->assertEquals($expected, $result->toArray());
    }

    /**
     * Tests the query() filter
     *
     * @return void
     */
    public function testQuery()
    {
        $builder = new FilterBuilder;
        $result = $builder->query(new \Elastica\Query\SimpleQueryString('awesome'));
        $expected = [
            'query' => [
                'simple_query_string' => ['query' => 'awesome']
            ]
        ];
        $this->assertEquals($expected, $result->toArray());
    }

    /**
     * Tests the range() filter
     *
     * @return void
     */
    public function testRange()
    {
        $builder = new FilterBuilder;
        $result = $builder->range('created', [
            'gte' => '2012-01-01',
            'lte' => 'now',
            'format' => 'dd/MM/yyyy||yyyy'
        ]);
        $expected = [
            'range' => [
                'created' => [
                    'gte' => '2012-01-01',
                    'lte' => 'now',
                    'format' => 'dd/MM/yyyy||yyyy'
                ]
            ]
        ];
        $this->assertEquals($expected, $result->toArray());
    }

    /**
     * Tests the regexp() filter
     *
     * @return void
     */
    public function testRegexp()
    {
        $builder = new FilterBuilder;
        $result = $builder->regexp('name.first', 'mar[c|k]', [
            'flags' => 'INTERSECTION|COMPLEMENT|EMPTY',
            'max_determinized_states' => 200
        ]);
        $expected = [
            'regexp' => [
                'name.first' => [
                    'value' => 'mar[c|k]',
                    'flags' => 'INTERSECTION|COMPLEMENT|EMPTY',
                    'max_determinized_states' => 200
                ]
            ]
        ];
        $this->assertEquals($expected, $result->toArray());
    }

    /**
     * Tests the script() filter
     *
     * @return void
     */
    public function testScript()
    {
        $builder = new FilterBuilder;
        $result = $builder->script("doc['foo'] > 2");
        $expected = [
            'script' => ['script' => "doc['foo'] > 2"]
        ];
        $this->assertEquals($expected, $result->toArray());
    }

    /**
     * Tests the term() filter
     *
     * @return void
     */
    public function testTerm()
    {
        $builder = new FilterBuilder;
        $result = $builder->term('user.name', 'jose');
        $expected = [
            'term' => ['user.name' => 'jose']
        ];
        $this->assertEquals($expected, $result->toArray());
    }

    /**
     * Tests the terms() filter
     *
     * @return void
     */
    public function testTerms()
    {
        $builder = new FilterBuilder;
        $result = $builder->terms('user.name', ['mark', 'jose']);
        $expected = [
            'terms' => ['user.name' => ['mark', 'jose']]
        ];
        $this->assertEquals($expected, $result->toArray());
    }

    /**
     * Tests the type() filter
     *
     * @return void
     */
    public function testType()
    {
        $builder = new FilterBuilder;
        $result = $builder->type('products');
        $expected = [
            'type' => ['value' => 'products']
        ];
        $this->assertEquals($expected, $result->toArray());
    }

    /**
     * Tests the and() method
     *
     * @return void
     */
    public function testAnd()
    {
        $builder = new FilterBuilder;
        $result = $builder->and(
            $builder->term('user', 'jose'),
            $builder->gte('age', 29),
            $builder->missing('tags')
        );
        $expected = [
            'bool' => [
                'must' => [
                    ['term' => ['user' => 'jose']],
                    ['range' => ['age' => ['gte' => 29]]],
                    ['missing' => ['field' => 'tags']],
                ]
            ]
        ];
        $this->assertEquals($expected, $result->toArray());
    }

    /**
     * Tests the and() method with boolean collapsing
     *
     * @return void
     */
    public function testAndWithCollapsedBoolean()
    {
        $builder = new FilterBuilder;
        $result = $builder->and(
            $builder->term('user', 'jose'),
            $builder->gte('age', 29),
            $builder->and(
                $builder->missing('tags'),
                $builder->exists('comments')
            )
        );
        $expected = [
            'bool' => [
                'must' => [
                    ['missing' => ['field' => 'tags']],
                    ['exists' => ['field' => 'comments']],
                    ['term' => ['user' => 'jose']],
                    ['range' => ['age' => ['gte' => 29]]],
                ]
            ]
        ];
        $this->assertEquals($expected, $result->toArray());
    }

    /**
     * Tests the or() method
     *
     * @return void
     */
    public function testOr()
    {
        $builder = new FilterBuilder;
        $result = $builder->or(
            $builder->term('user', 'jose'),
            $builder->gte('age', 29),
            $builder->missing('tags')
        );
        $expected = [
            'or' => [
                ['term' => ['user' => 'jose']],
                ['range' => ['age' => ['gte' => 29]]],
                ['missing' => ['field' => 'tags']],
            ]
        ];
        $this->assertEquals($expected, $result->toArray());
    }

    /**
     * Tests the parse() method
     *
     * @return void
     */
    public function testParseSingleArray()
    {
        $builder = new FilterBuilder;
        $filter = $builder->parse([
            'name' => 'jose',
            'age >=' => 29,
            'age <=' => 50,
            'salary >' => 50,
            'salary <' => 60,
            'interests in' => ['cakephp', 'food'],
            'interests not in' => ['boring stuff', 'c#'],
            'profile is' => null,
            'tags is not' => null,
            'address is' => 'something',
            'address is not' => 'something else',
            'last_name !=' => 'gonzalez',
        ]);
        $expected = [
            $builder->term('name', 'jose'),
            $builder->gte('age', 29),
            $builder->lte('age', 50),
            $builder->gt('salary', 50),
            $builder->lt('salary', 60),
            $builder->terms('interests', ['cakephp', 'food']),
            $builder->not($builder->terms('interests', ['boring stuff', 'c#'])),
            $builder->missing('profile'),
            $builder->exists('tags'),
            $builder->term('address', 'something'),
            $builder->not($builder->term('address', 'something else')),
            $builder->not($builder->term('last_name', 'gonzalez'))
        ];
        $this->assertEquals($expected, $filter);
    }

    /**
     * Tests the parse() method for generating or conditions
     *
     * @return void
     */
    public function testParseOr()
    {
        $builder = new FilterBuilder;
        $filter = $builder->parse([
            'or' => [
                'name' => 'jose',
                'age >' => 29
            ]
        ]);
        $expected = [
            $builder->or(
                $builder->term('name', 'jose'),
                $builder->gt('age', 29)
            )
        ];
        $this->assertEquals($expected, $filter);
    }

    /**
     * Tests the parse() method for generating and conditions
     *
     * @return void
     */
    public function testParseAnd()
    {
        $builder = new FilterBuilder;
        $filter = $builder->parse([
            'and' => [
                'name' => 'jose',
                'age >' => 29
            ]
        ]);
        $expected = [
            $builder->and(
                $builder->term('name', 'jose'),
                $builder->gt('age', 29)
            )
        ];
        $this->assertEquals($expected, $filter);
    }

    /**
     * Tests the parse() method for generating not conditions
     *
     * @return void
     */
    public function testParseNot()
    {
        $builder = new FilterBuilder;
        $filter = $builder->parse([
            'not' => [
                'name' => 'jose',
                'age >' => 29
            ]
        ]);
        $expected = [
            $builder->not(
                $builder->and(
                    $builder->term('name', 'jose'),
                    $builder->gt('age', 29)
                )
            )
        ];
        $this->assertEquals($expected, $filter);
    }
}
