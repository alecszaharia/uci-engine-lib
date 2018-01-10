<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 8/18/17
 * Time: 1:12 AM
 */

namespace UCIEngine;


class UCIEngine implements UCIEngineInterface {
	/**
	 * @var string
	 */
	private $engine_path;

	/**
	 * @var UCIProcess
	 */
	private $uci_process;

	/**
	 * UCIEngine constructor.
	 *
	 * @param $engine_path
	 */
	public function __construct( $engine_path ) {
		$this->engine_path = $engine_path;

		$this->uci_process = new UCIProcess( $this->engine_path );

	}

	/**
	 * @param string $command
	 *
	 * @return array|UCIEngineInterface
	 * @throws \Exception
	 */
	public function sendCommand( $command ) {
		if ( $this->isValidString( $command ) ) {
			$this->uci_process->write( $command );

			return $this->uci_process->read();
		}
	}

	/**
	 * @param string[] $commands
	 *
	 * @return array|UCIEngineInterface
	 * @throws \Exception
	 */
	public function sendCommands( $commands ) {
		if ( is_array( $commands ) ) {
			$results = array();
			foreach ( $commands as $command ) {
				$results[] = $this->sendCommand( $command );
			}

			return $results;
		}
	}

	/**
	 * @param string $name
	 * @param string $value
	 *
	 * @return array|UCIEngineInterface
	 * @throws \Exception
	 */
	public function setOption( $name, $value ) {
		if ( $this->isValidString( $name ) && $this->isValidString( $value ) ) {
			return $this->sendCommand( "setoption name {$name} value {$value}" );
		}
	}

	/**
	 * @param $options
	 *
	 * @return array|mixed
	 * @throws \Exception
	 */
	public function setOptions( $options ) {
		if ( is_array( $options ) ) {
			$results = array();
			foreach ( $options as $option ) {
				$results[] = $this->setOption( ...$option );
			}

			return $results;
		}
	}

	/**
	 * @param $position
	 * @param null $moves
	 *
	 * @return array|mixed|null|UCIEngineInterface
	 * @throws \Exception
	 */
	public function setPosition( $position, $moves = null ) {
		if ( $this->isValidString( $position ) ) {

			$movesCommand = "";
			if ( $this->isValidString( $moves ) ) {
				$movesCommand = " moves {$moves}";
			}

			$result = null;

			if ( $position == 'startpos' ) {
				$result = $this->sendCommand( "position {$position}{$movesCommand}" );
			} else {
				$result = $this->sendCommand( "position fen {$position}{$movesCommand}" );
			}

			return $result;
		}
	}

	/**
	 * @return array|mixed|UCIEngineInterface
	 * @throws \Exception
	 */
	public function newGame() {
		return $this->sendCommand( "ucinewgame" );
	}

	/**
	 * @param $string
	 *
	 * @return bool
	 */
	private function isValidString( $string ) {
		return is_string( $string ) && ! empty( $string );
	}
}