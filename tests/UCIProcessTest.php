<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 8/18/17
 * Time: 1:12 AM
 */

namespace UCIEngine\Tests;


use PHPUnit\Framework\TestCase;
use UCIEngine\UCIProcess;

class UCIProcessTest extends TestCase
{

    const CHESS_ENGINE_PATH = '../chess_engine/stockfish_8_x64';

    /**
     * @expectedException \InvalidArgumentException
     *
     * @throws \InvalidArgumentException
     */
    public function test_constructor()
    {
        $t = new UCIProcess(null);
    }

    /**
     * @expectedException \Exception
     *
     * @throws \Exception
     */
    public function test_constructor_with_invalid_path()
    {
        $t = new UCIProcess('invalid path');
    }

    public function test_getProcessDescriptors()
    {
        $t = new UCIProcess(self::CHESS_ENGINE_PATH);
        $descriptors = $t->getProcessDescriptors();

        $this->assertEquals(
            [
                array('pipe', 'r'),
                array('pipe', 'w'), // stdout
                array('pipe', 'w'), // stderr
            ],
            $descriptors,
            'Invalid process descriptors returned'
        );
    }

    public function test_read()
    {
        $expected_str = 'Stockfish 8 64 by T. Romstad, M. Costalba, J. Kiiski, G. Linscott';

        $t = new UCIProcess(self::CHESS_ENGINE_PATH);
        $value = $t->read();

        $this->assertEquals([$expected_str],$value,"It should read '{$expected_str}'");
    }

//    public function test_write()
//    {
//        $t = new UCIProcess(self::CHESS_ENGINE_PATH);
//        $value = $t->read();
//
//        $this->assertEquals(['Stockfish 8 64 by T. Romstad, M. Costalba, J. Kiiski, G. Linscott'],$value,'It should read "123"');
//    }


}