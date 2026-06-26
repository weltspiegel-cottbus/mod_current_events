<?php

/**
 * @package     Weltspiegel\Module\CurrentEvents
 *
 * @copyright   Weltspiegel Cottbus
 * @license     MIT; see LICENSE file
 */

namespace Weltspiegel\Module\CurrentEvents\Site\Helper;

\defined('_JEXEC') or die;

use Exception;
use Joomla\CMS\Factory;

/**
 * Helper for mod_current_events
 *
 * @since 0.1.0
 */
class CurrentEventsHelper
{
	/**
	 * Retrieve list of movies from the component
	 *
	 * @return  array|false  Array of movies or false on failure
	 *
	 * @throws Exception
	 * @since   1.0.0
	 */
    public static function getMovies(): array|false
    {
        $app = Factory::getApplication();

        $component  = $app->bootComponent('com_weltspiegel');
        $mvcFactory = $component->getMVCFactory();

        $model = $mvcFactory->createModel('Movies', 'Site');

        if (!$model) {
            return false;
        }

        return $model->getItems();
    }
}
