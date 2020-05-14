<?php

if ( ! class_exists( 'WP_CLI' ) ) {
	return;
}

$scaffold_fuque_autoloader = dirname( __FILE__ ) . '/vendor/autoload.php';
if ( file_exists( $scaffold_fuque_autoloader ) ) {
	require_once $scaffold_fuque_autoloader;
}

WP_CLI::add_command( 'scaffold fuque', 'Scaffold_Fuque_Command' );
