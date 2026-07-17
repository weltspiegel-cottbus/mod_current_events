<?php

/**
 * @package     Weltspiegel\Module\CurrentEvents
 *
 * @copyright   Weltspiegel Cottbus
 * @license     MIT; see LICENSE file
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;

/**
 * @var Joomla\Registry\Registry $params
 * @var array $sections  Built by CurrentEventsHelper::getSections()
 */

if (empty($sections) || !is_array($sections)) {
    return;
}

$sectionConfig = [
    '3D'  => ['title' => 'Aktuell in 3D',  'id' => 'events-3d',  'label' => '3D'],
    '2D'  => ['title' => 'Aktuell in 2D',  'id' => 'events-2d',  'label' => '2D'],
    'OmU' => ['title' => 'Aktuell in OmU', 'id' => 'events-omu', 'label' => 'OmU'],
    'OV'  => ['title' => 'Aktuell in OV',  'id' => 'events-ov',  'label' => 'OV'],
];

$today    = (new DateTime())->format('Y-m-d');
$tomorrow = (new DateTime('+1 day'))->format('Y-m-d');
$formatter = new IntlDateFormatter('de_DE', IntlDateFormatter::NONE, IntlDateFormatter::NONE);
$formatter->setPattern('EEE, dd.MM.');

?>
<div class="mod-current-events">
    <?php if (count($sections) > 1) : ?>
        <nav class="mb-3">
            <div class="d-flex gap-3 flex-wrap">
                <?php foreach ($sectionConfig as $key => $config) : ?>
                    <?php if (isset($sections[$key])) : ?>
                        <a href="#<?= $config['id'] ?>" class="btn btn-sm btn-outline-primary"><?= $config['label'] ?></a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </nav>
    <?php endif; ?>

    <?php foreach ($sectionConfig as $key => $config) : ?>
        <?php if (empty($sections[$key])) : continue; endif; ?>

        <h3 id="<?= $config['id'] ?>"><?= $config['title'] ?></h3>
        <div class="row g-3 mb-4">
            <?php foreach ($sections[$key] as $movieId => $data) :
                $movie      = $data['movie'];
                $showsByDay = $data['showsByDay'];
                $detailRoute = Route::_('index.php?option=com_weltspiegel&view=movie&movie_id=' . $movie->movieId);

                $nextDay   = array_key_first($showsByDay);
                $nextShows = $showsByDay[$nextDay];
                $hasMoreDays = count($showsByDay) > 1;

                $dayDate = new DateTime($nextDay);
                $formattedDate = $formatter->format($dayDate);

                if ($nextDay === $today) {
                    $dayLabel = 'Heute (' . $formattedDate . ')';
                } elseif ($nextDay === $tomorrow) {
                    $dayLabel = 'Morgen (' . $formattedDate . ')';
                } else {
                    $dayLabel = $formattedDate;
                }
            ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card h-100">
                        <a href="<?= $detailRoute ?>" class="d-block" style="aspect-ratio: 2/3; overflow: hidden;">
                            <?php if (!empty($movie->poster)) : ?>
                                <img src="<?= $movie->poster ?>"
                                     alt="<?= htmlspecialchars($movie->title) ?>"
                                     class="card-img-top"
                                     style="width: 100%; height: 100%; object-fit: cover;">
                            <?php endif; ?>
                        </a>
                        <div class="card-body p-2">
                            <div class="small text-muted" style="min-height: 3rem;">
                                <div>
                                    <strong><?= $dayLabel ?></strong><br>
                                    <?php foreach ($nextShows as $i => $show) :
                                        $showDateTime = new DateTime($show->showStart);
                                        echo '<a href="' . htmlspecialchars($show->bookingLink) . '" class="text-decoration-none">'
                                            . $showDateTime->format('H:i')
                                            . '</a>';
                                        if ($i < count($nextShows) - 1) {
                                            echo ' | ';
                                        }
                                    endforeach; ?>
                                </div>
                                <?php if ($hasMoreDays) : ?>
                                    <div class="mt-1">
                                        <a href="<?= $detailRoute ?>" class="text-decoration-none">Weitere Tage</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
</div>
