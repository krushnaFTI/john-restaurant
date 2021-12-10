<?php
namespace mprm_menu_cart\classes\misc;
/**
 * Class Loader
 *
 * @version 1.0.2
 */
class Loader {

	private static $_instance;
	/**
	 * Controller Directory Path
	 *
	 * @var array
	 * @access protected
	 */
	protected $_controllerDirectoryPath = array();
	/**
	 * Model Directory Path
	 *
	 * @var array
	 * @access protected
	 */
	protected $_modelDirectoryPath = array();
	/**
	 * Library Directory Path
	 *
	 * @var array
	 * @access protected
	 */
	protected $_libraryDirectoryPath = array();
	/**
	 * Default Directory Path
	 *
	 * @var array
	 * @access protected
	 */
	protected $_defaultDirectoryPath = array();
	/**
	 *  Library Directory namespace
	 *
	 * @var string
	 * @access protected
	 */
	protected $_namespace = 'mprm_menu_cart\classes';

	/**
	 * Constructor
	 * Constant contain my full path to Model, View, Controllers and Library-
	 * Directories.
	 *
	 * @Constant MP_RM_CLASSES_PATH
	 */

	public function __construct() {
		$this->modelDirectoryPath = MP_MENU_PLUGIN_PATH . 'classes/models/' . PATH_SEPARATOR . MP_MENU_PLUGIN_PATH . 'classes/models/parents/';
		$this->controllerDirectoryPath = MP_MENU_PLUGIN_PATH . 'classes/controllers/';
		$this->defaultDirectoryPath = MP_MENU_PLUGIN_PATH . 'classes/';
	}

	/**
	 * @return Loader
	 */
	public static function get_instance() {
		if (empty(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 *
	 */
	public function init_loader() {
		if (class_exists('MP_Restaurant_Menu_Setup_Plugin')) {
			spl_autoload_register(array($this, 'load_class'));
		}
	}


	/**
	 * @param $library
	 * @param null $param
	 *
	 * @return Loader
	 */
	public function load_library($library, $param = null) {
		if (is_string($library)) {
			return $this->initialize_class($library);
		}

		if (is_array($library)) {
			foreach ($library as $key) {
				return $this->initialize_class($library);
			}
		}
	}

	/**
	 * @param $library
	 *
	 * @return $this
	 * @throws \ISException
	 */
	public function initialize_class($library) {
		try {
			if (is_array($library)) {
				foreach ($library as $class) {
					$arrayObject = new $class;
				}
				return $this;
			}
			if (is_string($library)) {
				$stringObject = new $library;
			} else {
				throw new \ISException('Class name must be string.');
			}
			if (null == $library) {
				throw new \ISException('You must enter the name of the class.');
			}
		} catch (\Exception $exception) {
			echo $exception;
		}
	}

	/**
	 * Load class
	 *
	 * @param $class
	 */
	public function load_class($class) {

		$type = 'default';
		$matches = array();
		if ((bool)preg_match_all("/mprm_menu_cart(.*)?/", $class, $matches)) {
			if ((bool)preg_match_all("/mprm_menu_cart(.*)?classes(.*)?models/", $class, $matches)) {
				$type = 'model';
			} elseif ((bool)preg_match_all("/mprm_menu_cart(.*)?classes(.*)?controllers/", $class, $matches)) {
				$type = 'controller';
			}
			$class = $this->get_class_name($class);

			switch ($type) {
				case "model":
					$this->load_models($class);
					break;
				case "controller":
					$this->load_controller($class);
					break;
				default:
					$this->load_default($class);
					break;

			}
		}
	}

	/**
	 * Get clear class name
	 *
	 * @param $class
	 *
	 * @return mixed|string
	 */
	public function get_class_name($class) {
		$class = str_replace('_', "-", $class);

		$path = explode('\\', $class);

		if (is_array($path)) {
			$class = end($path);
		}
		$class = 'class-' . strtolower($class);
		return $class;
	}

	/**
	 * Autoload Model class
	 *
	 * @param  string $model
	 *
	 * @return object
	 */
	public function load_models($model) {
		if ($model) {
			set_include_path(get_include_path() . PATH_SEPARATOR . $this->modelDirectoryPath);
			spl_autoload($model, '.php');
			ini_restore('include_path');
		}
	}

	/**
	 * Autoload Controller class
	 *
	 * @param $controller
	 */
	public function load_controller($controller) {
		if ($controller) {
			set_include_path(get_include_path() . PATH_SEPARATOR . $this->controllerDirectoryPath);
			spl_autoload($controller, '.php');
			ini_restore('include_path');
		}
	}

	/**
	 * Autoload classes default directory
	 *
	 * @param $class
	 */
	public function load_default($class) {
		if ($class) {
			set_include_path(get_include_path() . PATH_SEPARATOR . $this->defaultDirectoryPath);
			spl_autoload($class, '.php');
			ini_restore('include_path');
		}
	}
}


