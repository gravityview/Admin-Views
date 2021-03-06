<?php
/** If this file is called directly, abort. */
if ( ! defined( 'GRAVITYVIEW_DIR' ) ) {
	die();
}

/**
 * The default Dashboard View Request class.
 */
class GravityView_Dashboard_Views_Request extends \GV\Request {

	public function __construct() {
		add_filter( 'gravityview/request/is_renderable', array( $this, 'declare_renderable' ), 10, 2 );

		parent::__construct();
	}

	/**
	 * Declare this class as something that is renderable.
	 */
	public function declare_renderable( $is_renderable, $instance ) {
		return get_class( $this ) === get_class( $instance );
	}

	/**
	 * The current screen is a Dashboard View.
	 *
	 * @return bool
	 */
	public function is_dashboard_view() {
		global $current_screen;

		if ( $current_screen && ! $current_screen = get_current_screen() ) {
			return false;
		}

		if ( $current_screen && 'gravityview_page_' . GravityView_Dashboard_Views::PAGE_SLUG !== $current_screen->id ) {
			return false;
		}

		if ( $this->doing_datatables_ajax_request() ) {
			return true;
		}

		return $this->is_admin() && GravityView_Dashboard_Views::PAGE_SLUG === \GV\Utils::_GET( 'page' );
	}

	/**
	 * Checks whether we're in a DT AJAX request
	 *
	 * @since 1.0
	 *
	 * @return bool True: We're inside a DataTables request in an admin View. False: We're not!
	 */
	private function doing_datatables_ajax_request() {
		return 'gv_datatables_data' === \GV\Utils::_REQUEST( 'action' );
	}

	/**
	 * The current admin screen is a View.
	 *
	 * @return false|\GV\View
	 */
	public function is_view() {
		if ( ! $this->is_dashboard_view() ) {
			return false;
		}

		return \GV\View::by_id( \GV\Utils::_GET( 'gvid' ) );
	}

	/**
	 * The current entry.
	 *
	 * @return false|\GV\Entry
	 */
	public function is_entry( $form_id = 0 ) {
		if ( ! $view = $this->is_view() ) {
			return false;
		}

		return \GV\GF_Entry::by_id( \GV\Utils::_GET( 'entry_id' ) );
	}

	/**
	 * Is this an edit entry?
	 *
	 * @return bool Yes?
	 */
	public function is_edit_entry( $form_id = 0 ) {
		if ( ! $entry = $this->is_entry( $form_id ) ) {
			return false;
		}

		if ( empty( $_GET['edit'] ) ) {
			return false;
		}

		return true;
	}
}
