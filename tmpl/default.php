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

// Separate events into 3D, 2D, OmU, and OV
$events3D = [];
$events2D = [];
$eventsOmU = [];
$eventsOV = [];

foreach ($events as $id => $event) {
    // Check language property for OmU or OV
    $isOmU = !empty($event->language) && stripos($event->language, 'OmU') !== false;
    $isOV = !empty($event->language) && stripos($event->language, 'OV') !== false;

    if ($isOmU) {
        $eventsOmU[$id] = $event;
    } elseif ($isOV) {
        $eventsOV[$id] = $event;
    } elseif (!empty($event->is3D)) {
        $events3D[$id] = $event;
    } else {
        $events2D[$id] = $event;
    }
}

// Count how many categories have events
$categoryCount = 0;
if (!empty($events3D)) $categoryCount++;
if (!empty($events2D)) $categoryCount++;
if (!empty($eventsOmU)) $categoryCount++;
if (!empty($eventsOV)) $categoryCount++;

?>
<div class="mod-cinetixx-events">
    <?php if ($categoryCount > 1) : ?>
        <nav class="mb-3">
            <div class="d-flex gap-3 flex-wrap">
                <?php if (!empty($events3D)) : ?>
                    <a href="#events-3d" class="btn btn-sm btn-outline-primary">3D</a>
                <?php endif; ?>
                <?php if (!empty($events2D)) : ?>
                    <a href="#events-2d" class="btn btn-sm btn-outline-primary">2D</a>
                <?php endif; ?>
                <?php if (!empty($eventsOmU)) : ?>
                    <a href="#events-omu" class="btn btn-sm btn-outline-primary">OmU</a>
                <?php endif; ?>
                <?php if (!empty($eventsOV)) : ?>
                    <a href="#events-ov" class="btn btn-sm btn-outline-primary">OV</a>
                <?php endif; ?>
            </div>
        </nav>
    <?php endif; ?>

    <?php if (!empty($events3D)) : ?>
        <h3 id="events-3d">Aktuell in 3D</h3>
        <div class="row g-3 mb-4">
            <?php foreach ($events3D as $id => $event) : ?>
                <?php
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
                            <?php
                            // Get next show dates (group by day, take first day with up to 2 shows)
                            if (!empty($event->shows)) {
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
                                if (!empty($showsByDay)) {
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
                            }
                            ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($events2D)) : ?>
        <h3 id="events-2d">Aktuell in 2D</h3>
        <div class="row g-3 mb-4">
            <?php foreach ($events2D as $id => $event) : ?>
                <?php
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
                            <?php
                            // Get next show dates (group by day, take first day with up to 2 shows)
                            if (!empty($event->shows)) {
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
                                if (!empty($showsByDay)) {
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
                            }
                            ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($eventsOmU)) : ?>
        <h3 id="events-omu">Aktuell in OmU</h3>
        <div class="row g-3 mb-4">
            <?php foreach ($eventsOmU as $id => $event) : ?>
                <?php
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
                            <?php
                            // Get next show dates (group by day, take first day with up to 2 shows)
                            if (!empty($event->shows)) {
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
                                if (!empty($showsByDay)) {
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
                            }
                            ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($eventsOV)) : ?>
        <h3 id="events-ov">Aktuell in OV</h3>
        <div class="row g-3">
            <?php foreach ($eventsOV as $id => $event) : ?>
                <?php
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
                            <?php
                            // Get next show dates (group by day, take first day with up to 2 shows)
                            if (!empty($event->shows)) {
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
                                if (!empty($showsByDay)) {
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
                            }
                            ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
