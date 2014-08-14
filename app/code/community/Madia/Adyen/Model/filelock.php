<?php
/**
 *	Author: Marc 'Foddex' Oude Kotte <foddex@foddex.net>
 *	Date: January 6th, 2012
 *	License: free to use for any purpose, but on your own risk (MIT license)
 *
 */
 
define( 'LOCKFILE_SUCCESS', 1 );
define( 'LOCKFILE_ERROR_ALREADY_LOCKED', -1 );
define( 'LOCKFILE_ERROR_FAIL_TO_OBTAIN_LOCK', -2 );

class Filelock {
	public static $lockpath = '/tmp/';
	private $name;
	private $filename;
	private $i_locked = false;
	
	public function __construct( $name ) {
		$this->name = $name;
		$this->filename = Filelock::$lockpath . preg_replace( '/\W/', '_', $name ) . '.lock';
	}
	public function is_locked() {
		return file_exists( $this->filename );
	}
	public function try_lock() {
		if ($this->is_locked())
			return LOCKFILE_ERROR_ALREADY_LOCKED;
		
		// write tempfile
		$tempfile = $this->filename . '.pid.' . getmypid() . '.mtrand.' . mt_rand();
		if (!@file_put_contents( $tempfile, serialize( $this->bake_data() ) ))
			return LOCKFILE_ERROR_FAILED_OBTAINING_LOCK;
			
		// try to hard link tempfile to lockpath
		if (!@link( $tempfile, $this->filename )) {
			@unlink( $tempfile );
			return LOCKFILE_ERROR_ALREADY_LOCKED;
		}
			
		// cleanup
		$this->i_locked = true;
		@unlink( $tempfile );
		return LOCKFILE_SUCCESS;
	}
	public function lock() {
		do {
			$res = $this->try_lock();
			if ($res != LOCKFILE_ERROR_ALREADY_LOCKED)
				break;
			usleep( 100000 );	// try again after 0.1 seconds
		} while (true);
		return $res;
	}
	public function unlock( $force=false ) {
		if ($force || $this->i_locked) {
			$this->i_locked = false;
			@unlink( $this->filename );
		}
	}
	public function bake_data() {
		return array(
			'mypid'		=> getmypid(),
			'timestamp'	=> date( 'd-m-Y H:i:s' ),
			'name'		=> $this->name,
		);
	}
}