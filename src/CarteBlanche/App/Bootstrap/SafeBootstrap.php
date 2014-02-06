<?php
/**
 * CarteBlanche - PHP framework package
 * Copyleft (c) 2013 Pierre Cassat and contributors
 * <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
 * License Apache-2.0 <http://www.apache.org/licenses/LICENSE-2.0.html>
 * Sources <http://github.com/php-carteblanche/carteblanche>
 *
 * Default safe application bootstrap
 */

namespace { function getContainer() { return \CarteBlanche\App\Container::getInstance(); } }

namespace CarteBlanche\App\Bootstrap {

	class SafeBootstrap {

		protected $kernel;
		
		protected $app_stacks = array();
		
		protected $bundles = array();
		
		protected $prod_stacks = array();
		
		protected $dev_stacks = array(
//			'unit_test'         =>'initUnitTest'
		);
		
		function __construct( \CarteBlanche\App\Kernel $kernel ) {
			$this->kernel = $kernel;
			$this->container = \CarteBlanche\App\Container::getInstance();
			$this->init();
		}
		
		function init() { 
			foreach( $this->app_stacks as $obj_name=>$obj_callback ) {
				if (method_exists($this, $obj_callback))
					$this->container
					    ->set($obj_name, call_user_func(array($this, $obj_callback)) );
//echo "<br />assigning object '$obj_name' to container";
			}
			foreach( $this->bundles as $bundle_namespace=>$bundle_dir ) {
				if (!is_string($bundle_namespace)) $bundle_namespace = $bundle_dir;
				if (method_exists($this, 'initBundle')) {
					$this->container->setBundle(
						$bundle_namespace, 
						$this->initBundle( $bundle_namespace, $bundle_dir ) 
					);
				}
//echo "<br />assigning bundle '$bundle_namespace' in directory '$bundle_dir' to container";
			}
			if (defined('_APP_MODE')) {
				$_p=_APP_MODE.'_stacks';
				if (property_exists($this, $_p))
				foreach( $this->$_p as $obj_name=>$obj_callback ) {
					if (method_exists($this, $obj_callback))
						$this->container
						    ->set($obj_name, call_user_func(array($this, $obj_callback)) );
//echo "<br />assigning object '$obj_name' to container";
				}
			}
//echo "<br />container contains : ";getContainer()->debug();
//exit('yo');
//getContainer()->debug(1);
			return $this->container;
		}

		function initUnitTest()
		{
			return new \UnitTest\Lib\UnitTest;
		}

		function initBundle( $space,$dir )
		{
			return new \CarteBlanche\App\Bundle( $space,$dir );
		}

	} //!class ContainerBootstrap

} //!namespace \App\Bootstrap

// Endfile