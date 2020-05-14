<?php

use WP_CLI\Utils;

/**
 * Generates starter code for a theme based on fuque.
 *
 * ## OPTIONS
 *
 * <slug>
 * : The slug for the new theme, used for prefixing functions.
 *
 * [--activate]
 * : Activate the newly downloaded theme.
 *
 * [--enable-network]
 * : Enable the newly downloaded theme for the entire network.
 *
 * [--theme_name=<title>]
 * : What to put in the 'Theme Name:' header in 'style.css'.
 *
 * [--author=<full-name>]
 * : What to put in the 'Author:' header in 'style.css'.
 *
 * [--author_uri=<uri>]
 * : What to put in the 'Author URI:' header in 'style.css'.
 *
 * [--sassify]
 * : Include stylesheets as SASS.
 *
 * [--woocommerce]
 * : Include WooCommerce boilerplate files.
 *
 * [--force]
 * : Overwrite files that already exist.
 *
 * ## EXAMPLES
 *
 *     # Generate a theme with name "Sample Theme" and author "John Doe"
 *     $ wp scaffold fuque sample-theme --theme_name="Sample Theme" --author="John Doe"
 *     Success: Created theme 'Sample Theme'.
 */
class Scaffold_Fuque_Command extends WP_CLI_Command {

	/**
	 * Generates starter code for a theme based on fuque.
	 *
	 * ## OPTIONS
	 *
	 * <slug>
	 * : The slug for the new theme, used for prefixing functions.
	 *
	 * [--activate]
	 * : Activate the newly downloaded theme.
	 *
	 * [--enable-network]
	 * : Enable the newly downloaded theme for the entire network.
	 *
	 * [--theme_name=<title>]
	 * : What to put in the 'Theme Name:' header in 'style.css'.
	 *
	 * [--author=<full-name>]
	 * : What to put in the 'Author:' header in 'style.css'.
	 *
	 * [--author_uri=<uri>]
	 * : What to put in the 'Author URI:' header in 'style.css'.
	 *
	 * [--sassify]
	 * : Include stylesheets as SASS.
	 *
	 * [--woocommerce]
	 * : Include WooCommerce boilerplate files.
	 *
	 * [--force]
	 * : Overwrite files that already exist.
	 *
	 * ## EXAMPLES
	 *
	 *     # Generate a theme with name "Sample Theme" and author "John Doe"
	 *     $ wp scaffold fuque sample-theme --theme_name="Sample Theme" --author="John Doe"
	 *     Success: Created theme 'Sample Theme'.
	 */
	public function __invoke( $args, $assoc_args ) {

		$theme_slug = $args[0];

		if ( ! preg_match( '/^[a-z_]\w+$/i', str_replace( '-', '_', $theme_slug ) ) ) {
			WP_CLI::error( 'Invalid theme slug specified. Theme slugs can only contain letters, numbers, underscores and hyphens, and can only start with a letter or underscore.' );
		}

		$defaults = array(
			'theme_slug' => $theme_slug,
			'theme_name' => ucfirst( $theme_slug ),
			'author'     => 'Me',
			'author_uri' => 'http://underscores.me/',
		);
		$data     = wp_parse_args( $assoc_args, $defaults );

		$data['textdomain'] = $theme_slug;
		$data['package']    = str_replace( ' ', '_', $data['theme_name'] );
		$data['prefix']     = str_replace( '-', '_', $data['theme_slug'] );

		$theme_dir = WP_CONTENT_DIR . "/themes/{$data['theme_slug']}";

		$error_msg = $this->check_target_directory( $theme_dir );
		if ( ! empty( $error_msg ) ) {
			WP_CLI::error( "Invalid theme slug specified. {$error_msg}" );
		}

		$force             = \WP_CLI\Utils\get_flag_value( $assoc_args, 'force' );
		$should_write_file = $this->prompt_if_files_will_be_overwritten( $theme_dir, $force );
		if ( ! $should_write_file ) {
			WP_CLI::log( 'No files created' );
			die;
		}

		$data['theme_description'] = "Custom theme: {$data['theme_name']}, developed by {$data['author']}";

		if ( \WP_CLI\Utils\get_flag_value( $assoc_args, 'sassify' ) ) {
			$data['sass'] = 1;
		}

		if ( \WP_CLI\Utils\get_flag_value( $assoc_args, 'woocommerce' ) ) {
			$data['woocommerce'] = 1;
		}

		$this->maybe_create_themes_dir();

		$files_to_create = array(
			"{$theme_dir}/bin/bundle.js"                       => file_get_contents( self::get_template_path( 'bin/bundle.js' ) ),
			"{$theme_dir}/inc/custom-header.php"               => self::mustache_render( 'inc/custom-header.mustache', $data ),
			"{$theme_dir}/inc/customizer.php"                  => self::mustache_render( 'inc/customizer.mustache', $data ),
			"{$theme_dir}/inc/jetpack.php"                     => self::mustache_render( 'inc/jetpack.mustache', $data ),
			"{$theme_dir}/inc/template-functions.php"          => self::mustache_render( 'inc/template-functions.mustache', $data ),
			"{$theme_dir}/inc/template-tags.php"               => self::mustache_render( 'inc/template-tags.mustache', $data ),
			"{$theme_dir}/js/customizer.js"                    => file_get_contents( self::get_template_path( 'js/customizer.js' ) ),
			"{$theme_dir}/js/navigation.js"                    => file_get_contents( self::get_template_path( 'js/navigation.js' ) ),
			"{$theme_dir}/js/skip-link-focus-fix.js"           => file_get_contents( self::get_template_path( 'js/skip-link-focus-fix.js' ) ),
			"{$theme_dir}/languages/readme.txt"                => file_get_contents( self::get_template_path( 'languages/readme.txt' ) ),
			"{$theme_dir}/languages/{$data['textdomain']}.pot" => file_get_contents( self::get_template_path( 'languages/textdomain.pot' ) ),
			"{$theme_dir}/template-parts/content-none.php"     => self::mustache_render( 'template-parts/content-none.mustache', $data ),
			"{$theme_dir}/template-parts/content-page.php"     => self::mustache_render( 'template-parts/content-page.mustache', $data ),
			"{$theme_dir}/template-parts/content.php"          => self::mustache_render( 'template-parts/content.mustache', $data ),
			"{$theme_dir}/template-parts/content-search.php"   => self::mustache_render( 'template-parts/content-search.mustache', $data ),
			"{$theme_dir}/.editorconfig"                       => file_get_contents( self::get_template_path( '.editorconfig' ) ),
			"{$theme_dir}/.eslintrc"                           => file_get_contents( self::get_template_path( '.eslintrc' ) ),
			"{$theme_dir}/.stylelintrc.json"                   => file_get_contents( self::get_template_path( '.stylelintrc.json' ) ),
			"{$theme_dir}/404.php"                             => self::mustache_render( '404.mustache', $data ),
			"{$theme_dir}/archive.php"                         => self::mustache_render( 'archive.mustache', $data ),
			"{$theme_dir}/comments.php"                        => self::mustache_render( 'comments.mustache', $data ),
			"{$theme_dir}/composer.json"                       => file_get_contents( self::get_template_path( 'composer.json' ) ),
			"{$theme_dir}/footer.php"                          => self::mustache_render( 'footer.mustache', $data ),
			"{$theme_dir}/functions.php"                       => self::mustache_render( 'functions.mustache', $data ),
			"{$theme_dir}/header.php"                          => self::mustache_render( 'header.mustache', $data ),
			"{$theme_dir}/index.php"                           => self::mustache_render( 'index.mustache', $data ),
			"{$theme_dir}/LICENSE"                             => file_get_contents( self::get_template_path( 'LICENSE' ) ),
			"{$theme_dir}/package.json"                        => file_get_contents( self::get_template_path( 'package.json' ) ),
			"{$theme_dir}/page.php"                            => self::mustache_render( 'page.mustache', $data ),
			"{$theme_dir}/phpcs.xml.dist"                      => file_get_contents( self::get_template_path( 'phpcs.xml.dist' ) ),
			"{$theme_dir}/README.md"                           => file_get_contents( self::get_template_path( 'README.md' ) ),
			"{$theme_dir}/readme.txt"                          => self::mustache_render( 'readme.mustache', $data ),
			"{$theme_dir}/screenshot.png"                      => file_get_contents( self::get_template_path( 'screenshot.png' ) ),
			"{$theme_dir}/search.php"                          => self::mustache_render( 'search.mustache', $data ),
			"{$theme_dir}/sidebar.php"                         => self::mustache_render( 'sidebar.mustache', $data ),
			"{$theme_dir}/single.php"                          => self::mustache_render( 'single.mustache', $data ),
			"{$theme_dir}/style-rtl.css"                       => self::mustache_render( 'style-rtl.mustache', $data ),
			"{$theme_dir}/style.css"                           => self::mustache_render( 'style.mustache', $data ),
		);
		$this->create_files( array_reverse( $files_to_create ), false );
		WP_CLI::success( "Created theme '{$data['theme_name']}'." );

		if ( \WP_CLI\Utils\get_flag_value( $assoc_args, 'activate' ) ) {
			WP_CLI::run_command( array( 'theme', 'activate', $theme_slug ) );
		} elseif ( \WP_CLI\Utils\get_flag_value( $assoc_args, 'enable-network' ) ) {
			WP_CLI::run_command( array( 'theme', 'enable', $theme_slug ), array( 'network' => true ) );
		}
	}

	/**
	 * Checks that the `$target_dir` is a child directory of the WP themes directory.
	 *
	 * @param string $target_dir The theme directory to check.
	 *
	 * @return null|string Returns null on success, error message on error.
	 */
	private function check_target_directory( $target_dir ) {
		$parent_dir = dirname( self::canonicalize_path( str_replace( '\\', '/', $target_dir ) ) );

		if ( str_replace( '\\', '/', WP_CONTENT_DIR . '/themes' ) !== $parent_dir ) {
			return sprintf( 'The target directory \'%1$s\' is not in \'%2$s\'.', $target_dir, WP_CONTENT_DIR . '/themes' );
		}

		// Success.
		return null;
	}

	protected function create_files( $files_and_contents, $force ) {
		$wp_filesystem = $this->init_wp_filesystem();
		$wrote_files   = array();

		foreach ( $files_and_contents as $filename => $contents ) {
			$should_write_file = $this->prompt_if_files_will_be_overwritten( $filename, $force );
			if ( ! $should_write_file ) {
				continue;
			}

			$wp_filesystem->mkdir( dirname( $filename ) );

			if ( ! $wp_filesystem->put_contents( $filename, $contents ) ) {
				WP_CLI::error( "Error creating file: {$filename}" );
			} elseif ( $should_write_file ) {
				$wrote_files[] = $filename;
			}
		}
		return $wrote_files;
	}

	protected function prompt_if_files_will_be_overwritten( $filename, $force ) {
		$should_write_file = true;
		if ( ! file_exists( $filename ) ) {
			return true;
		}

		WP_CLI::warning( 'File already exists.' );
		WP_CLI::log( $filename );
		if ( ! $force ) {
			do {
				$answer      = cli\prompt(
					'Skip this file, or replace it with scaffolding?',
					$default = false,
					$marker  = '[s/r]: '
				);
			} while ( ! in_array( $answer, array( 's', 'r' ), true ) );
			$should_write_file = 'r' === $answer;
		}

		$outcome = $should_write_file ? 'Replacing' : 'Skipping';
		WP_CLI::log( $outcome . PHP_EOL );

		return $should_write_file;
	}

	/**
	 * Creates the themes directory if it doesn't already exist.
	 */
	protected function maybe_create_themes_dir() {

		$themes_dir = WP_CONTENT_DIR . '/themes';
		if ( ! is_dir( $themes_dir ) ) {
			wp_mkdir_p( $themes_dir );
		}

	}

	/**
	 * Initializes WP_Filesystem.
	 */
	protected function init_wp_filesystem() {
		global $wp_filesystem;
		WP_Filesystem();

		return $wp_filesystem;
	}

	/**
	 * Localizes the template path.
	 */
	private static function mustache_render( $template, $data = array() ) {
		return Utils\mustache_render( dirname( dirname( __FILE__ ) ) . "/templates/{$template}", $data );
	}

	/**
	 * Gets the template path based on installation type.
	 */
	private static function get_template_path( $template ) {
		$command_root  = Utils\phar_safe_path( dirname( __DIR__ ) );
		$template_path = "{$command_root}/templates/{$template}";

		if ( ! file_exists( $template_path ) ) {
			WP_CLI::error( "Couldn't find {$template}" );
		}

		return $template_path;
	}

	/*
	 * Returns the canonicalized path, with dot and double dot segments resolved.
	 *
	 * Copied from Symfony\Component\DomCrawler\AbstractUriElement::canonicalizePath().
	 * Implements RFC 3986, section 5.2.4.
	 *
	 * @param string $path The path to make canonical.
	 *
	 * @return string The canonicalized path.
	 */
	private static function canonicalize_path( $path ) {
		if ( '' === $path || '/' === $path ) {
			return $path;
		}

		if ( '.' === substr( $path, -1 ) ) {
			$path .= '/';
		}

		$output = array();

		foreach ( explode( '/', $path ) as $segment ) {
			if ( '..' === $segment ) {
				array_pop( $output );
			} elseif ( '.' !== $segment ) {
				$output[] = $segment;
			}
		}

		return implode( '/', $output );
	}
}
