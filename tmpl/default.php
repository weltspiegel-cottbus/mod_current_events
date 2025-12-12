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

/**
 * Helper function to render show times for an event
 */
function renderShowTimes($event): void
{
    if (empty($event->shows)) {
        return;
    }

    $showsByDay = [];
    $now = time();

    // Group shows by day
    foreach ($event->shows as $show) {
        $showTime = strtotime($show->showStart);
        if ($showTime >= $now) {
            $day = date('Y-m-d', $showTime);
            if (!isset($showsByDay[$day])) {
                $showsByDay[$day] = [];
            }
            $showsByDay[$day][] = $show;
        }
    }

    // Sort days
    ksort($showsByDay);

    // Get first day and limit to 2 shows
    if (empty($showsByDay)) {
        return;
    }

    $nextDay = array_key_first($showsByDay);
    $nextShows = array_slice($showsByDay[$nextDay], 0, 2);

    $dayDate = new DateTime($nextDay);

    // Format date in German
    $formatter = new IntlDateFormatter('de_DE', IntlDateFormatter::NONE, IntlDateFormatter::NONE);
    $formatter->setPattern('EEE, dd.MM.');
    $formattedDate = $formatter->format($dayDate);

    echo '<div class="small text-muted">';
    echo '<strong>' . $formattedDate . '</strong><br>';

    foreach ($nextShows as $show) {
        $showDateTime = new DateTime($show->showStart);
        $bookingUrl = 'https://www.kinoheld.de/kino-cottbus/filmtheater-weltspiegel/vorstellung/' . $show->showId . '?mode=widget#panel-seats';

        echo '<a href="' . htmlspecialchars($bookingUrl) . '" target="_blank" rel="noopener noreferrer" class="text-decoration-none">';
        echo $showDateTime->format('H:i');
        echo '</a>';

        if ($show !== end($nextShows)) {
            echo ' | ';
        }
    }
    echo '</div>';
}

/**
 * Helper function to render an event card
 */
function renderEventCard($id, $event): void
{
    $detailRoute = Route::_('index.php?option=com_cinetixx&view=event&event_id=' . $id);
    ?>
    <div class="col-6 col-md-4 col-lg-3">
        <div class="card">
            <a href="<?= $detailRoute ?>" class="d-block" style="aspect-ratio: 2/3; overflow: hidden;">
                <?php if (!empty($event->poster)) : ?>
                    <img src="<?= $event->poster ?>"
                         alt="<?= htmlspecialchars($event->title) ?>"
                         class="card-img-top"
                         style="width: 100%; height: 100%; object-fit: cover;">
                <?php endif; ?>
            </a>
            <div class="card-body p-2">
                <?php renderShowTimes($event); ?>
            </div>
        </div>
    </div>
    <?php
}

// Separate events into categories
$categorizedEvents = [
    '3D' => [],
    '2D' => [],
    'OmU' => [],
    'OV' => []
];

foreach ($events as $id => $event) {
    // Check language property for OmU or OV
    $isOmU = !empty($event->language) && stripos($event->language, 'OmU') !== false;
    $isOV = !empty($event->language) && stripos($event->language, 'OV') !== false;

    if ($isOmU) {
        $categorizedEvents['OmU'][$id] = $event;
    } elseif ($isOV) {
        $categorizedEvents['OV'][$id] = $event;
    } elseif (!empty($event->is3D)) {
        $categorizedEvents['3D'][$id] = $event;
    } else {
        $categorizedEvents['2D'][$id] = $event;
    }
}

// Define category configuration
$categoryConfig = [
    '3D' => ['title' => 'Aktuell in 3D', 'id' => 'events-3d', 'label' => '3D'],
    '2D' => ['title' => 'Aktuell in 2D', 'id' => 'events-2d', 'label' => '2D'],
    'OmU' => ['title' => 'Aktuell in OmU', 'id' => 'events-omu', 'label' => 'OmU'],
    'OV' => ['title' => 'Aktuell in OV', 'id' => 'events-ov', 'label' => 'OV']
];

// Count categories with events
$categoryCount = count(array_filter($categorizedEvents, fn($cat) => !empty($cat)));

?>
<div class="mod-cinetixx-events">
    <?php if ($categoryCount > 1) : ?>
        <nav class="mb-3">
            <div class="d-flex gap-3 flex-wrap">
                <?php foreach ($categoryConfig as $key => $config) : ?>
                    <?php if (!empty($categorizedEvents[$key])) : ?>
                        <a href="#<?= $config['id'] ?>" class="btn btn-sm btn-outline-primary"><?= $config['label'] ?></a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </nav>
    <?php endif; ?>

    <?php foreach ($categoryConfig as $key => $config) : ?>
        <?php if (!empty($categorizedEvents[$key])) : ?>
            <h3 id="<?= $config['id'] ?>"><?= $config['title'] ?></h3>
            <div class="row g-3 mb-4">
                <?php foreach ($categorizedEvents[$key] as $id => $event) : ?>
                    <?php renderEventCard($id, $event); ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>
