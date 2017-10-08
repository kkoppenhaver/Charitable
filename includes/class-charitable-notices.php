<?php
/**
 * Contains the class that is used to register and retrieve notices like errors, warnings, success messages, etc.
 *
 * @version		1.0.0
 * @package		Charitable/Classes/Charitable_Notices
 * @author 		Eric Daams
 * @copyright 	Copyright (c) 2017, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! class_exists( 'Charitable_Notices' ) ) :

	/**
	 * Charitable_Notices
	 *
	 * @since   1.0.0
	 */
	class Charitable_Notices {

		/**
		 * The single instance of this class.
		 *
		 * @var 	Charitable_Notices|null
		 */
		private static $instance = null;

		/**
		 * The array of notices.
		 *
		 * @var 	array
		 */
		protected $notices;

		/**
		 * Returns and/or create the single instance of this class.
		 *
		 * @since   1.0.0
		 *
		 * @return  Charitable_Notices
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Create class object. A private constructor, so this is used in a singleton context.
		 *
		 * @since   1.0.0
		 *
		 * @return  void
		 */
		private function __construct() {
			/* Retrieve the notices from the session */
			$this->notices = charitable_get_session()->get_notices();

			/* Remove the notices from the session. */
			charitable_get_session()->remove( 'notices' );
		}

		/**
		 * Adds a notice message.
		 *
		 * @since   1.0.0
		 *
		 * @param   string $message The message to display.
		 * @param   string $type    The type of message.
		 * @param   string $key 	Optional. If not set, next numeric key is used.
		 * @return  void
		 */
		public function add_notice( $message, $type, $key = false ) {
			if ( false === $key ) {
				$this->notices[ $type ][] = $message;
			} else {
				$this->notices[ $type ][ $key ] = $message;
			}
		}

		/**
		 * Add multiple notices at once.
		 *
		 * @since   1.0.0
		 *
		 * @param   array  $messages Array of messages.
		 * @param   string $type     Type of message we're adding.
		 * @return  void
		 */
		public function add_notices( $messages, $type ) {
			if ( ! is_array( $messages ) ) {
				$messages = array( $messages );
			}

			$this->notices[ $type ] = array_merge( $this->notices[ $type ], $messages );
		}

		/**
		 * Adds an error message.
		 *
		 * @since   1.0.0
		 *
		 * @param   string $message The error message to add.
		 * @param   string $key 	Optional. If not set, next numeric key is used.
		 * @return  void
		 */
		public function add_error( $message, $key = false ) {
			$this->add_notice( $message, 'error', $key );
		}

		/**
		 * Adds a warning message.
		 *
		 * @since   1.0.0
		 *
		 * @param   string $message The warning message to add.
		 * @param   string $key 	Optional. If not set, next numeric key is used.
		 * @return  void
		 */
		public function add_warning( $message, $key = false ) {
			$this->add_notice( $message, 'warning', $key );
		}

		/**
		 * Adds a success message.
		 *
		 * @since   1.0.0
		 *
		 * @param   string $message The success message to add.
		 * @param   string $key 	Optional. If not set, next numeric key is used.
		 * @return  void
		 */
		public function add_success( $message, $key = false ) {
			$this->add_notice( $message, 'success', $key );
		}

		/**
		 * Adds an info message.
		 *
		 * @since   1.0.0
		 *
		 * @param   string $message The info message to add.
		 * @param   string $key 	Optional. If not set, next numeric key is used.
		 * @return  void
		 */
		public function add_info( $message, $key = false ) {
			$this->add_notice( $message, 'info', $key );
		}

		/**
		 * Receives a WP_Error object and adds the error messages to our array.
		 *
		 * @since   1.0.0
		 *
		 * @param   WP_Error $error The WP_Error object to add to the messages queue.
		 * @return  void
		 */
		public function add_errors_from_wp_error( WP_Error $error ) {
			$this->add_notices( $error->get_error_messages(), 'error' );
		}

		/**
		 * Return all errors as an array.
		 *
		 * @since   1.0.0
		 *
		 * @return  array
		 */
		public function get_errors() {
			return $this->notices['error'];
		}

		/**
		 * Return all warnings as an array.
		 *
		 * @since   1.0.0
		 *
		 * @return  array
		 */
		public function get_warnings() {
			return $this->notices['warning'];
		}

		/**
		 * Return all successs as an array.
		 *
		 * @since   1.0.0
		 *
		 * @return  array
		 */
		public function get_success_notices() {
			return $this->notices['success'];
		}

		/**
		 * Return all infos as an array.
		 *
		 * @since   1.0.0
		 *
		 * @return  array
		 */
		public function get_info_notices() {
			return $this->notices['info'];
		}

		/**
		 * Return all notices as an array.
		 *
		 * @since   1.0.0
		 *
		 * @return  array
		 */
		public function get_notices() {
			return $this->notices;
		}

		/**
		 * Clear out all existing notices.
		 *
		 * @since   1.4.0
		 *
		 * @return  void
		 */
		public function clear() {
			$clear = array(
				'error'		=> array(),
				'warning'	=> array(),
				'success'	=> array(),
				'info'		=> array(),
			);

			$this->notices = $clear;

			charitable_get_session()->set( 'notices', $clear );
		}
	}

endif;
