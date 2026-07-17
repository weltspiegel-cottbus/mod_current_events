<?php

/**
 * @package     Weltspiegel\Module\CurrentEvents
 *
 * @copyright   Weltspiegel Cottbus
 * @license     MIT; see LICENSE file
 */

namespace Weltspiegel\Module\CurrentEvents\Site\Dispatcher;

\defined('_JEXEC') or die;

use Exception;
use Joomla\CMS\Dispatcher\AbstractModuleDispatcher;
use Weltspiegel\Module\CurrentEvents\Site\Helper\CurrentEventsHelper;

/**
 * Dispatcher class for mod_current_events
 *
 * @since 0.1.0
 */
class Dispatcher extends AbstractModuleDispatcher
{
	/**
	 * Returns the layout data.
	 *
	 * @return array
	 *
	 * @throws Exception
	 * @since 0.1.0
	 */
    protected function getLayoutData(): array
    {
        $data = parent::getLayoutData();

        $data['sections'] = CurrentEventsHelper::getSections();

        return $data;
    }
}
