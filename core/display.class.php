<?php
/**
 * File defining class for plugin display
 *
 * @author Alexandre Techer <alexandre.techer@gmail.com>
 * @version 0.1
 * @package librairies
 * @subpackage core
 */

/** Check if the plugin version is defined. If not defined script will be stopped here */
if ( !defined( 'WP_EASY_SURVEY' ) ) {
	die( __("You are not allowed to use this service.", 'wp_easy_survey') );
}

/**
 * Display management class
 *
 * @author Alexandre Techer <alexandre.techer@gmail.com>
 * @version 0.1
 * @package librairies
 * @subpackage core
 */
class wpes_display {

	var $template_dir = WPES_TPL_DIR;

	/** Define the var that will contains all template elements	*/
	private $wpes_admin_tpl_element = array();

	/** Define the var that will contains form utilities	*/
	var $wpes_form = null;

	/**
	 * Initialise display utilities.
	 * Get all existing template defined in different part of plugin, and store them for use withoutre-call the definition file each time
	 */
	function __construct() {
		/**	Store all available templates for backend	*/
		if ( is_file(WPEASYSURVEY_BACKEND_TPL_DIR . 'tpl_elements.tpl.php') ) {
			require( WPEASYSURVEY_BACKEND_TPL_DIR . 'tpl_elements.tpl.php' );
			$this->wpes_admin_tpl_element['backend'] = $tpl_element;
		}
		/**	Store all available templates for frontend	*/
		if ( is_file(WPEASYSURVEY_FRONTEND_TPL_DIR . 'tpl_elements.tpl.php') ) {
			require( WPEASYSURVEY_FRONTEND_TPL_DIR . 'tpl_elements.tpl.php' );
			$this->wpes_admin_tpl_element['frontend'] = $tpl_element;
		}
		/**	Store all available templates for both frontend and backend	*/
		if ( is_file(WPEASYSURVEY_COMMON_TPL_DIR . 'tpl_elements.tpl.php') ) {
			require( WPEASYSURVEY_COMMON_TPL_DIR . 'tpl_elements.tpl.php' );
			$this->wpes_admin_tpl_element['common'] = $tpl_element;
		}

		/**	Instanciate a new form 	*/
	//	$this->wpes_form = new wp_easy_survey_form( $this );
	}


	/**
	 * Check and get the template file path to use for a given display part
	 *
	 * @uses locate_template()
	 * @uses get_template_part()
	 *
	 * @param string $side The website part were the template will be displayed. Backend or frontend
	 * @param string $slug The slug name for the generic template.
	 * @param string $name The name of the specialised template.
	 *
	 * @return string The template file path to use
	 */
	function get_template_part( $side, $slug, $name=null ) {

		switch ( $side ) {
			case "backend":
			case "common":
				$path = '';

				$templates = array();
				$name = (string)$name;
				if ( '' !== $name )
					$templates[] = "{$side}/{$slug}-{$name}.php";
				$templates[] = "{$side}/{$slug}.php";

				$path = locate_template( $templates, false );

				if ( empty( $path ) ) {
					foreach ( (array) $templates as $template_name ) {
						if ( !$template_name )
							continue;
						if ( file_exists($this->template_dir . $template_name)) {
							$path = $this->template_dir . $template_name;
							break;
						}
					}
				}

				return $path;
				break;

			case "frontend":
				get_template_part( $slug, $name );
				break;
		}

	}


	/**
	 * Generate output for a given element
	 *
	 * @param string $template_element The template structure to
	 * @param array $template_element_content Available list of component for template feeding
	 *
	 * @return string The template completed with the different element given
	 */
	function display( $template_element_def, $template_element_content, $args = array() ) {
		/** Get the good template element defined by first parameter	*/
		$template_element = $this->check_special_template( $template_element_def, $args );

		/** Read given template parameters for structure completion	*/
		foreach ( $template_element_content as $tpl_component_key => $tpl_component_content) {
			$template_element = str_replace( '{WPES_TPL_' . $tpl_component_key . '}', $tpl_component_content, $template_element);
		}

		/**	Return output template completly feeded	*/
		return $template_element;
	}

	/**
	 * Check in all existing defined template the template to take for the current element asked to be displayed
	 *
	 * @param string $template_element_def The template identifier to check in existing template
	 * @param array $args_to_check A list of specific arguments to get
	 *
	 * @return mixed The template to take for
	 */
	function check_special_template( $template_element_def, $args ) {
		$template_to_take = null;

		/**	Check if requested element is from backend or frontend	*/
		if ( !is_admin() && !empty($this->wpes_admin_tpl_element['frontend']) && !empty($this->wpes_admin_tpl_element['frontend'][$template_element_def]) ) {
			$template_to_take = $this->getValue($this->wpes_admin_tpl_element['frontend'][$template_element_def], $template_element_def, $args);
		}
		else if ( !empty($this->wpes_admin_tpl_element['backend']) && !empty($this->wpes_admin_tpl_element['backend'][$template_element_def]) ) {
			$template_to_take = $this->getValue($this->wpes_admin_tpl_element['backend'][$template_element_def], $template_element_def, $args);
		}

		/**	If nothing was found in above case, take the default element in common template	*/
		if ( empty($template_to_take) && !empty($this->wpes_admin_tpl_element['common']) && !empty($this->wpes_admin_tpl_element['common'][$template_element_def]) ) {
			$template_to_take = $this->getValue($this->wpes_admin_tpl_element['common'][$template_element_def], $template_element_def, $args);
		}

		return !empty($template_to_take) ? $template_to_take : sprintf( __('The asked template could not be found in any of existing templates. Check for "%s" template', 'wp_easy_survey'), $template_element_def );
	}

	function fixArray(&$lastFound, &$array, $choosen_template) {
		$i = 0;
		$isBroken = true;
		while ($isBroken && $i < count($array)) {
			if (array_key_exists($array[$i], $choosen_template)) {
				$isBroken = false;
				$lastFound = $choosen_template[$array[$i]];

				unset($array[$i]);
				$array = array_values($array);
			}
			else {
				$i++;
			}
		}

		return ($isBroken) ? false : true;
	}

	function getValue($complete_template, $template_component_to_take, $array = array())	{
		$lastFound = null;
		$mustStop = false;

		$choosen_template = !empty($complete_template) && !empty($complete_template[$template_component_to_take]) ? $complete_template[$template_component_to_take] : $complete_template;

		if ( !empty($array) ) {

			if (array_key_exists($array[0], $choosen_template)) {
				$lastFound = $choosen_template[$array[0]];
				unset($array[0]);
				$array = array_values($array);
			}
			else {
				$isFixed = $this->fixArray($lastFound, $array, $choosen_template);
				$mustStop = ($isFixed ? false : true );
			}

			while (!$mustStop && count($array) > 0) {
				if (is_array($lastFound)) {
					if (array_key_exists($array[0], $lastFound)) {
						$lastFound = $lastFound[$array[0]];
						unset($array[0]);
						$array = array_values($array);
					}
					else {
						$isFixed = $this->fixArray($lastFound, $array, $lastFound);
						$mustStop = ($isFixed ? false : true );
					}
				}
				else {
					$mustStop = true;
				}
			}

		}

		return !empty($lastFound['tpl']) ? $lastFound['tpl'] : ((!empty($choosen_template) && !empty($choosen_template['tpl'])) ? $choosen_template['tpl'] : '');
	}

}