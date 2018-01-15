<?php

namespace Drupal\campaign_kit\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\campaign_kit\Entity\CampaignUpdateInterface;

/**
 * Class CampaignUpdateController.
 *
 *  Returns responses for Campaign update routes.
 */
class CampaignUpdateController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Displays a Campaign update  revision.
   *
   * @param int $campaign_update_revision
   *   The Campaign update  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($campaign_update_revision) {
    $campaign_update = $this->entityManager()->getStorage('campaign_update')->loadRevision($campaign_update_revision);
    $view_builder = $this->entityManager()->getViewBuilder('campaign_update');

    return $view_builder->view($campaign_update);
  }

  /**
   * Page title callback for a Campaign update  revision.
   *
   * @param int $campaign_update_revision
   *   The Campaign update  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($campaign_update_revision) {
    $campaign_update = $this->entityManager()->getStorage('campaign_update')->loadRevision($campaign_update_revision);
    return $this->t('Revision of %title from %date', ['%title' => $campaign_update->label(), '%date' => format_date($campaign_update->getRevisionCreationTime())]);
  }

  /**
   * Generates an overview table of older revisions of a Campaign update .
   *
   * @param \Drupal\campaign_kit\Entity\CampaignUpdateInterface $campaign_update
   *   A Campaign update  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(CampaignUpdateInterface $campaign_update) {
    $account = $this->currentUser();
    $langcode = $campaign_update->language()->getId();
    $langname = $campaign_update->language()->getName();
    $languages = $campaign_update->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $campaign_update_storage = $this->entityManager()->getStorage('campaign_update');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $campaign_update->label()]) : $this->t('Revisions for %title', ['%title' => $campaign_update->label()]);
    $header = [$this->t('Revision'), $this->t('Operations')];

    $revert_permission = (($account->hasPermission("revert all campaign update revisions") || $account->hasPermission('administer campaign update entities')));
    $delete_permission = (($account->hasPermission("delete all campaign update revisions") || $account->hasPermission('administer campaign update entities')));

    $rows = [];

    $vids = $campaign_update_storage->revisionIds($campaign_update);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\campaign_kit\CampaignUpdateInterface $revision */
      $revision = $campaign_update_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $campaign_update->getRevisionId()) {
          $link = $this->l($date, new Url('entity.campaign_update.revision', ['campaign_update' => $campaign_update->id(), 'campaign_update_revision' => $vid]));
        }
        else {
          $link = $campaign_update->link($date);
        }

        $row = [];
        $column = [
          'data' => [
            '#type' => 'inline_template',
            '#template' => '{% trans %}{{ date }} by {{ username }}{% endtrans %}{% if message %}<p class="revision-log">{{ message }}</p>{% endif %}',
            '#context' => [
              'date' => $link,
              'username' => \Drupal::service('renderer')->renderPlain($username),
              'message' => ['#markup' => $revision->getRevisionLogMessage(), '#allowed_tags' => Xss::getHtmlTagList()],
            ],
          ],
        ];
        $row[] = $column;

        if ($latest_revision) {
          $row[] = [
            'data' => [
              '#prefix' => '<em>',
              '#markup' => $this->t('Current revision'),
              '#suffix' => '</em>',
            ],
          ];
          foreach ($row as &$current) {
            $current['class'] = ['revision-current'];
          }
          $latest_revision = FALSE;
        }
        else {
          $links = [];
          if ($revert_permission) {
            $links['revert'] = [
              'title' => $this->t('Revert'),
              'url' => $has_translations ?
              Url::fromRoute('entity.campaign_update.translation_revert', ['campaign_update' => $campaign_update->id(), 'campaign_update_revision' => $vid, 'langcode' => $langcode]) :
              Url::fromRoute('entity.campaign_update.revision_revert', ['campaign_update' => $campaign_update->id(), 'campaign_update_revision' => $vid]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.campaign_update.revision_delete', ['campaign_update' => $campaign_update->id(), 'campaign_update_revision' => $vid]),
            ];
          }

          $row[] = [
            'data' => [
              '#type' => 'operations',
              '#links' => $links,
            ],
          ];
        }

        $rows[] = $row;
      }
    }

    $build['campaign_update_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
