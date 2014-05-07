<?php

namespace CL\Atlas\Test\Compiler;

use CL\Atlas\Test\AbstractTestCase;
use CL\Atlas\Compiler;
use CL\Atlas\Query;
use CL\Atlas\SQL\SQL;

/**
 * @group compiler
 * @group compiler.insert
 */
class InsertTest extends AbstractTestCase
{


    public function dataInsert()
    {
        $rows = array();
        $args = array();

        // ROW 1
        // --------------------

        $args[0] = new Query\Insert;
        $args[0]
            ->type('IGNORE')
            ->into('table1')
            ->set(array('name' => 10, 'email' => 'email@example.com'));

        $args[1] = <<<SQL
INSERT IGNORE INTO table1 SET name = ?, email = ?
SQL;
        $args[2] = array(10, 'email@example.com');
        $rows[] = $args;


        // ROW 2
        // --------------------
        $select = new Query\Select;
        $select
            ->from('table2')
            ->where('name', '10');

        $args[0] = new Query\Insert;
        $args[0]
            ->into('table1')
            ->columns(array('id', 'name'))
            ->select($select);

        $args[1] = <<<SQL
INSERT INTO table1 (id, name) SELECT * FROM table2 WHERE (name = ?)
SQL;
        $args[2] = array(10);
        $rows[] = $args;

        // ROW 3
        // --------------------
        $args[0] = new Query\Insert;
        $args[0]
            ->into('table1')
            ->columns(array('id', 'name'))
            ->values(array(1, 'name1'))
            ->values(array(2, 'name2'));

        $args[1] = <<<SQL
INSERT INTO table1 (id, name) VALUES (?, ?), (?, ?)
SQL;
        $args[2] = array(1, 'name1', 2, 'name2');
        $rows[] = $args;

        return $rows;
    }

    /**
     * @covers CL\Atlas\Compiler\Insert::render
     * @covers CL\Atlas\Compiler\Insert::parameters
     * @dataProvider dataInsert
     */
    public function testInsert($query, $expectedSql, $expectedParameters)
    {
        $this->assertEquals($expectedSql, Compiler\Insert::render($query));
        $this->assertEquals($expectedParameters, Compiler\Insert::parameters($query));
    }
}
