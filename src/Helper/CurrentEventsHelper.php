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
    private static function getMovies(): array|false
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

    /**
     * Retrieve the current-events sections from the component.
     *
     * Fetches the movie list and transforms it via buildSections() — the single
     * entry point the module's templates work with.
     *
     * @return  array  See buildSections()
     *
     * @throws Exception
     * @since   1.3.0
     */
    public static function getSections(): array
    {
        $movies = self::getMovies();

        return self::buildSections(is_array($movies) ? $movies : []);
    }

    /**
     * Transform a flat movie list into sections grouped by category (3D/2D/OmU/OV).
     *
     * Past shows are dropped (based on show start, the sales window is deliberately
     * not evaluated here). Within each category, movies are merged into a single
     * entry per movie, and sorted ascending by their next remaining show in that
     * very category - not by the feed order of $movies, which reflects each movie's
     * earliest show overall (including past or differently categorised ones). That
     * mismatch was the original sorting bug this method fixes.
     *
     * @param   array  $movies  Movies as returned by getMovies()
     *
     * @return  array  Sections keyed by category ('3D', '2D', 'OmU', 'OV'), each an
     *                  array keyed by movieId of ['movie' => ..., 'showsByDay' => [...]];
     *                  empty categories are omitted
     *
     * @since   1.2.0
     */
    public static function buildSections(array $movies): array
    {
        $now = time();

        $sections = ['3D' => [], '2D' => [], 'OmU' => [], 'OV' => []];

        foreach ($movies as $movie) {
            foreach ($movie->formats as $format) {
                $category = self::getCategory($format);

                foreach ($format->shows as $show) {
                    $showTime = strtotime($show->showStart);
                    if ($showTime < $now) {
                        continue;
                    }

                    $day = date('Y-m-d', $showTime);

                    if (!isset($sections[$category][$movie->movieId])) {
                        $sections[$category][$movie->movieId] = [
                            'movie'      => $movie,
                            'showsByDay' => [],
                        ];
                    }

                    $sections[$category][$movie->movieId]['showsByDay'][$day][] = $show;
                }
            }
        }

        foreach ($sections as &$sectionMovies) {
            foreach ($sectionMovies as &$data) {
                ksort($data['showsByDay']);

                foreach ($data['showsByDay'] as &$dayShows) {
                    usort(
                        $dayShows,
                        static fn ($a, $b) => strtotime($a->showStart) <=> strtotime($b->showStart)
                    );
                }
                unset($dayShows);
            }
            unset($data);

            // The actual fix: order movies by their earliest remaining show within
            // this category (first show of the first day after the sorting above),
            // instead of leaving them in feed order.
            uasort($sectionMovies, static function ($a, $b) {
                $firstDayA = array_key_first($a['showsByDay']);
                $firstDayB = array_key_first($b['showsByDay']);

                $timeA = strtotime($a['showsByDay'][$firstDayA][0]->showStart);
                $timeB = strtotime($b['showsByDay'][$firstDayB][0]->showStart);

                return $timeA <=> $timeB;
            });
        }
        unset($sectionMovies);

        return array_filter($sections, static fn ($s) => !empty($s));
    }

    /**
     * Determine the display category (3D/2D/OmU/OV) for a format variant.
     *
     * @param   object  $format  Format entry of a movie (->language, ->is3D)
     *
     * @return  string  One of '3D', '2D', 'OmU', 'OV'
     *
     * @since   1.2.0
     */
    private static function getCategory(object $format): string
    {
        $lang = strtolower($format->language ?? '');

        if (str_contains($lang, 'omu') || str_contains($lang, 'omü')) {
            return 'OmU';
        }

        if (str_contains($lang, 'ov')) {
            return 'OV';
        }

        if ($format->is3D) {
            return '3D';
        }

        return '2D';
    }
}
