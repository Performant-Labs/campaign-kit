<?php

namespace Drupal\campaign_kit\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\campaign_kit\Entity\CampaignTransactionInterface;

/**
 * Class CampaignTransactionController.
 *
 *  Returns responses for Campaign transaction routes.
 */
class CampaignTransactionController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Displays a Campaign transaction  revision.
   *
   * @param int $campaign_transaction_revision
   *   The Campaign transaction  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($campaign_transaction_revision) {
    $campaign_transaction = $this->entityManager()->getStorage('campaign_transaction')->loadRevision($campaign_transaction_revision);
    $view_builder = $this->entityManager()->getViewBuilder('campaign_transaction');

    return $view_builder->view($campaign_transaction);
  }

  /**
   * Page title callback for a Campaign transaction  revision.
   *
   * @param int $campaign_transaction_revision
   *   The Campaign transaction  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($campaign_transaction_revision) {
    $campaign_transaction = $this->entityManager()->getStorage('campaign_transaction')->loadRevision($campaign_transaction_revision);
    return $this->t('Revision of %title from %date', ['%title' => $campaign_transaction->label(), '%date' => format_date($campaign_transaction->getRevisionCreationTime())]);
  }

  /**
   * Generates an overview table of older revisions of a Campaign transaction .
   *
   * @param \Drupal\campaign_kit\Entity\CampaignTransactionInterface $campaign_transaction
   *   A Campaign transaction  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(CampaignTransactionInterface $campaign_transaction) {
    $account = $this->currentUser();
    $langcode = $campaign_transaction->language()->getId();
    $langname = $campaign_transaction->language()->getName();
    $languages = $campaign_transaction->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $campaign_transaction_storage = $this->entityManager()->getStorage('campaign_transaction');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $campaign_transaction->label()]) : $this->t('Revisions for %title', ['%title' => $campaign_transaction->label()]);
    $header = [$this->t('Revision'), $this->t('Operations')];

    $revert_permission = (($account->hasPermission("revert all campaign transaction revisions") || $account->hasPermission('administer campaign transaction entities')));
    $delete_permission = (($account->hasPermission("delete all campaign transaction revisions") || $account->hasPermission('administer campaign transaction entities')));

    $rows = [];

    $vids = $campaign_transaction_storage->revisionIds($campaign_transaction);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\campaign_kit\CampaignTransactionInterface $revision */
      $revision = $campaign_transaction_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $campaign_transaction->getRevisionId()) {
          $link = $this->l($date, new Url('entity.campaign_transaction.revision', ['campaign_transaction' => $campaign_transaction->id(), 'campaign_transaction_revision' => $vid]));
        }
        else {
          $link = $campaign_transaction->link($date);
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
              Url::fromRoute('entity.campaign_transaction.translation_revert', ['campaign_transaction' => $campaign_transaction->id(), 'campaign_transaction_revision' => $vid, 'langcode' => $langcode]) :
              Url::fromRoute('entity.campaign_transaction.revision_revert', ['campaign_transaction' => $campaign_transaction->id(), 'campaign_transaction_revision' => $vid]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.campaign_transaction.revision_delete', ['campaign_transaction' => $campaign_transaction->id(), 'campaign_transaction_revision' => $vid]),
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

    $build['campaign_transaction_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
