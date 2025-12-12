<?php

/**
 * @package     Weltspiegel\Module\CinetixxEvents
 *
 * @copyright   Weltspiegel Cottbus
 * @license     MIT; see LICENSE file
 */

namespace Weltspiegel\Module\CinetixxEvents\Site\Dispatcher;

\defined('_JEXEC') or die;

use Joomla\CMS\Dispatcher\AbstractModuleDispatcher;
use Weltspiegel\Module\CinetixxEvents\Site\Helper\CinetixxEventsHelper;

/**
 * Dispatcher class for mod_cinetixx_events
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
     * @since 0.1.0
     */
    protected function getLayoutData(): array
    {
        $data = parent::getLayoutData();

        // Get events from the component using our helper
        $data['events'] = CinetixxEventsHelper::getEvents();

        return $data;
    }
}
