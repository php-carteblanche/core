<?php
/**
 * CarteBlanche - PHP framework package
 * (c) Pierre Cassat and contributors
 * 
 * Sources <http://github.com/php-carteblanche/carteblanche>
 *
 * License Apache-2.0
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CarteBlanche\Loader;

use \CarteBlanche\CarteBlanche;
use \CarteBlanche\Interfaces\DependencyLoaderInterface;
use \Library\Helper\Directory as DirectoryHelper;

/**
 * @author 		Piero Wbmstr <piwi@ateliers-pierrot.fr>
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
        $views_dir = $container->get('kernel')->getPath('views_dir');
        $cbcore_dir = $container->get('kernel')->getPath('carte_blanche_core');
        $base_cache_dir = $container->get('kernel')->getPath('web_tmp_dir');
        if (empty($base_cache_dir)) {
            $base_cache_dir = 'www'.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR;
        }
        $base_cache_path = DirectoryHelper::slashDirname($root_path).$base_cache_dir;

        $assets_tmp_path = $container->get('kernel')->getPath('asset_tmp_path');
        if (empty($assets_tmp_dir)) {
            $assets_tmp_path = DirectoryHelper::slashDirname($base_cache_path).'assets'.DIRECTORY_SEPARATOR;
            $container->get('kernel')->addPath('asset_tmp_dir', $assets_tmp_path, true, true);
        }

        $cache_path = $container->get('kernel')->getPath('web_cache_dir');
        if (empty($cache_path)) {
            $cache_path = DirectoryHelper::slashDirname($base_cache_path).'cache'.DIRECTORY_SEPARATOR;
            $container->get('kernel')->addPath('web_cache_dir', $cache_path, true, true);
        }
/*
echo '<br />'; var_export($root_path);
echo '<br />'; var_export(basename($web_dir));
echo '<br />'; var_export($web_dir);
echo '<br />'; var_export($cbcore_dir.$views_dir );
echo '<br />'; var_export($web_dir );
echo '<br />'; var_export($cache_path );
echo '<br />'; var_export($assets_tmp_path );
echo '<br />'; var_export($cbcore_dir);
echo '<br />'; var_export(DirectoryHelper::slashDirname($cbcore_dir).$views_dir );
//exit('yo');
//*/
        return \TemplateEngine\TemplateEngine::getInstance()
            ->guessFromAssetsLoader(\Assets\Loader::getInstance($root_path, basename($web_dir), $web_dir))
            ->setLayoutsDir( $cbcore_dir.$views_dir )
            ->setToTemplate('setWebRootPath', $web_dir )
            ->setToTemplate('setCachePath', $cache_path )
            ->setToTemplate('setAssetsCachePath', $assets_tmp_path )
            ->setToView('setIncludePath', $cbcore_dir)
            ->setToView('setIncludePath', DirectoryHelper::slashDirname($cbcore_dir).$views_dir )
            ;
    }

}

// Endfile