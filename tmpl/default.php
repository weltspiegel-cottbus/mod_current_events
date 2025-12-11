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
    <?php foreach ($events as $id => $event) : ?>
        <?php
        $detailRoute = Route::_('index.php?option=com_cinetixx&view=event&event_id=' . $id);
        ?>
        <div class="event-item">
            <div class="event-poster">
                <?php if (!empty($event->poster)) : ?>
                    <img src="<?= $event->poster ?>" alt="<?= htmlspecialchars($event->title) ?>">
                <?php endif; ?>
            </div>
            <div class="event-content">
                <h3 class="event-title">
                    <a href="<?= $detailRoute ?>">
                        <?= htmlspecialchars($event->title) ?>
                    </a>
                </h3>
                <?php if (!empty($event->text)) : ?>
                    <div class="event-description">
                        <?= $event->text ?>
                    </div>
                <?php endif; ?>
                <div class="event-meta">
                    <?php if (!empty($event->duration)) : ?>
                        <span class="event-duration">Dauer: <?= htmlspecialchars($event->duration) ?></span>
                    <?php endif; ?>
                    <?php if (!empty($event->languageShort)) : ?>
                        <span class="event-language">Sprache: <?= htmlspecialchars($event->languageShort) ?></span>
                    <?php endif; ?>
                    <?php if (!empty($event->fsk)) : ?>
                        <span class="event-fsk">FSK: <?= htmlspecialchars($event->fsk) ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
