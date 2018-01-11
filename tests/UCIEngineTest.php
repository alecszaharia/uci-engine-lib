<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 8/18/17
 * Time: 1:12 AM
 */

namespace UCIEngine\Tests;


use PHPUnit\Framework\TestCase;
use UCIEngine\UCIEngine;
use UCIEngine\UCIProcess;

class UCIEngineTest extends TestCase
{
    const CHESS_ENGINE_PATH = __DIR__.'/chess_engine/stockfish_8_x64';

    private function setProtectedProperty($object, $property, $value)
    {
        $reflection = new \ReflectionClass($object);
        $reflection_property = $reflection->getProperty($property);
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($object, $value);
    }


    public function test_sendCommand()
    {
        $command = "position startpos";

        $uci_process = $this->getUciProcess(['write','read']);

        $uci_process->expects($this->once())
            ->method('write')
            ->with($this->equalTo($command))
	        ->willReturn(array());

        $uci_process->expects($this->once())
            ->method('read')
	        ->willReturn(array());

        $engine = $this->getEngine($uci_process);

        $lines = $engine->sendCommand($command);

        $this->assertEmpty( $lines,' The result should be empty for command "position startpos"');

        $engine->sendCommand("");
        $engine->sendCommand(null);
        $engine->sendCommand(123);

    }

    public function test_sendCommands()
    {
        $commands = ["position startpos", "go movetime 10"];

        $uci_process = $this->getUciProcess(['write', 'read']);

        $uci_process->expects($this->exactly(2))
            ->method('write')
            ->withConsecutive(
                [$this->equalTo($commands[0])],
                [$this->equalTo($commands[1])]
            )
	        ->willReturn([ [],['line1','line2','line3'] ]);

        $uci_process->expects($this->exactly(2))
            ->method('read')
	        ->willReturn([ [],['line1','line2','line3'] ]);

        $engine = $this->getEngine($uci_process);

        $lines = $engine->sendCommands($commands);

        $this->assertCount( 2, $lines,  'It should return two response sets' );

        $engine->sendCommands(["", null, 100]);
    }

    public function test_setOption()
    {
        $option_name = "MultiPV";
        $option_value = "1";

        $uci_process = $this->getUciProcess(['write', 'read']);

        $uci_process->expects($this->exactly(1))
            ->method('write')
            ->with($this->equalTo("setoption name {$option_name} value {$option_value}"));

        $uci_process->expects($this->exactly(1))
            ->method('read')
            ->willReturn([]);

        $engine = $this->getEngine($uci_process);
        $engine->setOption($option_name, $option_value);
    }

    public function test_setOptions()
    {
        $options = [
            ["name1", "value1"],
            ["name2", "value2"],
            ["name3", "value3"],
            [null, "value3"],
            ["name", null],
        ];

        $consecutiveParameters = [];
        $consecutiveReturns = [];
        foreach ($options as $option) {
            $consecutiveParameters[] = [$this->equalTo("setoption name {$option[0]} value {$option[1]}")];
            $consecutiveReturns[] = [];
        }

        $uci_process = $this->getUciProcess(['write', 'read']);

        $uci_process->expects($this->exactly(3))
            ->method('write')
            ->withConsecutive(...$consecutiveParameters);

        $uci_process->expects($this->exactly(3))
            ->method('read')
            ->willReturn(...$consecutiveReturns);


        $engine = $this->getEngine($uci_process);
        $engine->setOptions($options);
    }


//    public function test_getOption()
//    {
//        // TODO: Implement getOption() method.
//    }
//
//    public function test_getOptions()
//    {
//        // TODO: Implement getOptions() method.
//    }
//
    public function test_setPosition()
    {
        $uci_process = $this->getUciProcess(['write','read']);

        $uci_process->expects($this->exactly(5))
            ->method('write')
            ->withConsecutive(
                ['position startpos'],
                ['position startpos moves e2e4 e7e5'],
                ['position fen rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1'],
                ['position fen rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1 moves e2e4 e7e5']
            );

        $consecutiveReturns = [[],[],[],[],[]];


        //there will be only 5 valid calls
        $uci_process->expects($this->exactly(5))
            ->method('read')
            ->willReturn(...$consecutiveReturns);


        $engine = $this->getEngine($uci_process);

        // valid calls
        $engine->setPosition('startpos');
        $engine->setPosition('startpos','e2e4 e7e5');
        $engine->setPosition('rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1');
        $engine->setPosition('rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1','e2e4 e7e5');
        $engine->setPosition('rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1',null);

        // invalid calls
        $engine->setPosition(123);
        $engine->setPosition(null);
    }

    public function test_ucinewgame()
    {
        $uci_process = $this->getUciProcess(['write','read']);

        $uci_process->expects($this->exactly(1))
            ->method('write')
            ->with("ucinewgame");

        $uci_process->expects($this->exactly(1))
            ->method('read')
            ->willReturn([]);


        $engine = $this->getEngine($uci_process);
        $engine->newGame();
    }
//
//    public function test_setStartPosition()
//    {
//        // TODO: Implement setStartPosition() method.
//    }
//
//    public function test_setMovesPosition()
//    {
//        // TODO: Implement setMovesPosition() method.
//    }


    /**
     * @param $methods
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getUciProcess($methods)
    {
        $uci_process = $this->getMockBuilder(UCIProcess::class)
            ->disableOriginalConstructor()
            ->setMethods($methods)
            ->getMock();

        return $uci_process;
    }

    /**
     * @param UCIProcess $process
     * @return UCIEngine
     */
    private function getEngine(UCIProcess $process)
    {
        $engine = new UCIEngine(self::CHESS_ENGINE_PATH);
        $this->setProtectedProperty($engine, 'uci_process', $process);

        return $engine;
    }
}