<?php

namespace Drupal\campaign_kit\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\campaign_kit\Entity\CampaignInterface;

/**
 * Class CampaignController.
 *
 *  Returns responses for Campaign routes.
 */
class CampaignController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Displays a Campaign  revision.
   *
   * @param int $campaign_revision
   *   The Campaign  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($campaign_revision) {
    $campaign = $this->entityManager()->getStorage('campaign')->loadRevision($campaign_revision);
    $view_builder = $this->entityManager()->getViewBuilder('campaign');

    return $view_builder->view($campaign);
  }

  /**
   * Page title callback for a Campaign  revision.
   *
   * @param int $campaign_revision
   *   The Campaign  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($campaign_revision) {
    $campaign = $this->entityManager()->getStorage('campaign')->loadRevision($campaign_revision);
    return $this->t('Revision of %title from %date', ['%title' => $campaign->label(), '%date' => format_date($campaign->getRevisionCreationTime())]);
  }

  /**
   * Generates an overview table of older revisions of a Campaign .
   *
   * @param \Drupal\campaign_kit\Entity\CampaignInterface $campaign
   *   A Campaign  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(CampaignInterface $campaign) {
    $account = $this->currentUser();
    $langcode = $campaign->language()->getId();
    $langname = $campaign->language()->getName();
    $languages = $campaign->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $campaign_storage = $this->entityManager()->getStorage('campaign');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $campaign->label()]) : $this->t('Revisions for %title', ['%title' => $campaign->label()]);
    $header = [$this->t('Revision'), $this->t('Operations')];

    $revert_permission = (($account->hasPermission("revert all campaign revisions") || $account->hasPermission('administer campaign entities')));
    $delete_permission = (($account->hasPermission("delete all campaign revisions") || $account->hasPermission('administer campaign entities')));

    $rows = [];

    $vids = $campaign_storage->revisionIds($campaign);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\campaign_kit\CampaignInterface $revision */
      $revision = $campaign_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $campaign->getRevisionId()) {
          $link = $this->l($date, new Url('entity.campaign.revision', ['campaign' => $campaign->id(), 'campaign_revision' => $vid]));
        }
        else {
          $link = $campaign->link($date);
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
              Url::fromRoute('entity.campaign.translation_revert', ['campaign' => $campaign->id(), 'campaign_revision' => $vid, 'langcode' => $langcode]) :
              Url::fromRoute('entity.campaign.revision_revert', ['campaign' => $campaign->id(), 'campaign_revision' => $vid]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.campaign.revision_delete', ['campaign' => $campaign->id(), 'campaign_revision' => $vid]),
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

    $build['campaign_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
