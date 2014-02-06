<?php
/**
 * CarteBlanche - PHP framework package
 * Copyleft (c) 2013 Pierre Cassat and contributors
 * <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
 * License Apache-2.0 <http://www.apache.org/licenses/LICENSE-2.0.html>
 * Sources <http://github.com/php-carteblanche/carteblanche>
 */

namespace CarteBlanche\Loader;

use \CarteBlanche\CarteBlanche;
use \CarteBlanche\Interfaces\DependencyLoaderInterface;

/**
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
 */
class TemplateEngineLoader implements DependencyLoaderInterface
{

	/**
	 * Instance loader
	 *
	 * @param array $config
	 *
	 * @return object
	 */
    public function load(array $config = null, \CarteBlanche\App\Container $container)
    {
        $root_path = $container->get('kernel')->getPath('root_path');
        $web_dir = $container->get('kernel')->getPath('web_dir');
        $web_path = $container->get('kernel')->getPath('web_path');
        $views_dir = $container->get('kernel')->getPath('views_dir');
        $cbcore_dir = $container->get('kernel')->getPath('carte_blanche_core');
        $base_cache_dir = $container->get('kernel')->getPath('tmp_dir');
        if (empty($base_cache_dir)) {
            $base_cache_dir = 'www/tmp/';
        }
        $base_cache_path = $container->get('kernel')->getPath('tmp_path');
        if (empty($base_cache_path)) {
            $base_cache_path = $root_path.'www/tmp/';
        }

        $assets_tmp_path = $container->get('kernel')->getPath('asset_tmp_path');
        if (empty($assets_tmp_dir)) {
            $assets_tmp_dir = $base_cache_dir.'assets'.DIRECTORY_SEPARATOR;
            $assets_tmp_path = $base_cache_path.'assets'.DIRECTORY_SEPARATOR;
            $container->get('kernel')->addPath('asset_tmp_dir', $assets_tmp_dir);
            $container->get('kernel')->addPath('asset_tmp_path', $assets_tmp_path, true, true);
        }

        $cache_path = $container->get('kernel')->getPath('cache_path');
        if (empty($cache_path)) {
            $cache_dir = $base_cache_dir.'cache'.DIRECTORY_SEPARATOR;
            $cache_path = $base_cache_path.'cache'.DIRECTORY_SEPARATOR;
            $container->get('kernel')->addPath('cache_dir', $cache_dir);
            $container->get('kernel')->addPath('cache_path', $cache_path, true, true);
        }

        return \TemplateEngine\TemplateEngine::getInstance()
            ->guessFromAssetsLoader(\Assets\Loader::getInstance($root_path, $web_dir, $web_path))
            ->setLayoutsDir( $cbcore_dir.$views_dir )
            ->setToTemplate('setWebRootPath', $web_path )
            ->setToTemplate('setCachePath', $cache_path )
            ->setToTemplate('setAssetsCachePath', $assets_tmp_path )
            ->setToView('setIncludePath', $cbcore_dir)
            ->setToView('setIncludePath', $cbcore_dir.$views_dir )
            ;
    }

}

// Endfile