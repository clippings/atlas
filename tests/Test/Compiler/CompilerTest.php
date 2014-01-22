<?php namespace Openbuildings\Cherry\Test\Compiler;

use Openbuildings\Cherry\Test\TestCase;
use Openbuildings\Cherry\Compiler\Compiler;

/**
 * @group compiler
 */
class CompilerTest extends TestCase {

	public function dataToPlaceholders()
	{
		return array(
			array(array('name1', 'name2'), '(?, ?)'),
			array(array(), '()'),
			array(array(1, 2, 3, 4), '(?, ?, ?, ?)'),
		);
	}

	/**
	 * @dataProvider dataToPlaceholders
	 * @covers Openbuildings\Cherry\Compiler\Compiler::toPlaceholders
	 */
	public function testToPlaceholders($array, $expected)
	{
		$this->assertEquals($expected, Compiler::toPlaceholders($array));
	}

	public function dataExpression()
	{
		return array(
			array(array('SELECT', '*', NULL, 'WHERE i = 1'), 'SELECT * WHERE i = 1'),
			array(array(NULL, NULL, NULL), NULL),
			array(array('DELETE', 'FROM test'), 'DELETE FROM test'),
		);
	}

	/**
	 * @dataProvider dataExpression
	 * @covers Openbuildings\Cherry\Compiler\Compiler::expression
	 */
	public function testExpression($array, $expected)
	{
		$this->assertEquals($expected, Compiler::expression($array));
	}

	public function dataWord()
	{
		return array(
			array('SELECT', NULL, NULL),
			array('FROM', 'table', 'FROM table'),
		);
	}

	/**
	 * @dataProvider dataWord
	 * @covers Openbuildings\Cherry\Compiler\Compiler::word
	 */
	public function testWord($word, $statement, $expected)
	{
		$this->assertEquals($expected, Compiler::word($word, $statement));
	}

	public function dataBraced()
	{
		return array(
			array('SELECT test', '(SELECT test)'),
			array(NULL, NULL),
		);
	}

	/**
	 * @dataProvider dataBraced
	 * @covers Openbuildings\Cherry\Compiler\Compiler::braced
	 */
	public function testBraced($statement, $expected)
	{
		$this->assertEquals($expected, Compiler::braced($statement));
	}

	public function dataHumanize()
	{
		return array(
			array('SELECT test', array(), 'SELECT test'),
			array('SELECT test FROM table1 WHERE name = ?', array('param'), 'SELECT test FROM table1 WHERE name = "param"'),
			array('SELECT test FROM table1 WHERE name = ? AND id IS ?', array('param', NULL), 'SELECT test FROM table1 WHERE name = "param" AND id IS NULL'),
			array('SELECT test FROM table1 WHERE name = ? AND id IS NOT ?', array(10, NULL), 'SELECT test FROM table1 WHERE name = 10 AND id IS NOT NULL'),
		);
	}

	/**
	 * @dataProvider dataHumanize
	 * @covers Openbuildings\Cherry\Compiler\Compiler::humanize
	 */
	public function testHumanize($statement, $parameters, $expected)
	{
		$this->assertEquals($expected, Compiler::humanize($statement, $parameters));
	}
}