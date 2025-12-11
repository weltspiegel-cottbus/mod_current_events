<?php

/**
 * @package     Weltspiegel\Module\CinetixxEvents
 *
 * @copyright   Weltspiegel Cottbus
 * @license     MIT; see LICENSE file
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;

/**
 * @var Joomla\CMS\WebAsset\WebAssetManager $wa
 * @var Joomla\Registry\Registry $params
 * @var array $events
 */

// Don't display anything if there are no events
if (empty($events) || !is_array($events)) {
    return;
}

?>
<div class="mod-cinetixx-events">
    <div class="row g-3">
        <?php foreach ($events as $id => $event) : ?>
            <?php
            $detailRoute = Route::_('index.php?option=com_cinetixx&view=event&event_id=' . $id);
            ?>
            <div class="col-6 col-md-4 col-lg-3">
                <a href="<?= $detailRoute ?>" class="d-block">
                    <?php if (!empty($event->poster)) : ?>
                        <img src="<?= $event->poster ?>"
                             alt="<?= htmlspecialchars($event->title) ?>"
                             class="img-fluid rounded">
                    <?php endif; ?>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</div>
