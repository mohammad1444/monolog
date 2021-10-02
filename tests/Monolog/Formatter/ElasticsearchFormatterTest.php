<?php declare(strict_types=1);

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Formatter;

use Monolog\Logger;

use Monolog\Utils;

class ElasticsearchFormatterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers Monolog\Formatter\ElasticsearchFormatter::__construct
     * @covers Monolog\Formatter\ElasticsearchFormatter::format
     * @covers Monolog\Formatter\ElasticsearchFormatter::getDocument
     */
    public function testFormat()
    {
        // Test log message
        $msg = [
            'level' => Logger::ERROR,
            'level_name' => 'ERROR',
            'channel' => 'meh',
            'context' => ['foo' => 7, 'bar', 'class' => new \stdClass],
            'datetime' => new \DateTimeImmutable("@0"),
            'extra' => [],
            'message' => 'log',
        ];

        // Expected values
        $expected = $msg;
        $expected['datetime'] = '1970-01-01T00:00:00+0000';
        $expected['context'] = Utils::jsonEncode([
            'foo' => 7,
            0 => 'bar',
            'class' => ['stdClass' => []],
        ]);

        // Format log message
        $formatter = new ElasticsearchFormatter('my_index', 'doc_type');
        $doc = $formatter->format($msg);
        $this->assertIsArray($doc);

        // Record parameters
        $this->assertEquals('my_index', $doc['_index']);
        $this->assertEquals('doc_type', $doc['_type']);

        // Record data values
        foreach (array_keys($expected) as $key) {
            $this->assertEquals($expected[$key], $doc[$key]);
        }
    }

    /**
     * @covers Monolog\Formatter\ElasticsearchFormatter::getIndex
     * @covers Monolog\Formatter\ElasticsearchFormatter::getType
     */
    public function testGetters()
    {
        $formatter = new ElasticsearchFormatter('my_index', 'doc_type');
        $this->assertEquals('my_index', $formatter->getIndex());
        $this->assertEquals('doc_type', $formatter->getType());
    }
}
