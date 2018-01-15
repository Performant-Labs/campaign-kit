<?php

namespace Drupal\campaign_kit\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\campaign_kit\Entity\DonationInterface;

/**
 * Class DonationController.
 *
 *  Returns responses for Donation routes.
 */
class DonationController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Displays a Donation  revision.
   *
   * @param int $donation_revision
   *   The Donation  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($donation_revision) {
    $donation = $this->entityManager()->getStorage('donation')->loadRevision($donation_revision);
    $view_builder = $this->entityManager()->getViewBuilder('donation');

    return $view_builder->view($donation);
  }

  /**
   * Page title callback for a Donation  revision.
   *
   * @param int $donation_revision
   *   The Donation  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($donation_revision) {
    $donation = $this->entityManager()->getStorage('donation')->loadRevision($donation_revision);
    return $this->t('Revision of %title from %date', ['%title' => $donation->label(), '%date' => format_date($donation->getRevisionCreationTime())]);
  }

  /**
   * Generates an overview table of older revisions of a Donation .
   *
   * @param \Drupal\campaign_kit\Entity\DonationInterface $donation
   *   A Donation  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(DonationInterface $donation) {
    $account = $this->currentUser();
    $langcode = $donation->language()->getId();
    $langname = $donation->language()->getName();
    $languages = $donation->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $donation_storage = $this->entityManager()->getStorage('donation');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $donation->label()]) : $this->t('Revisions for %title', ['%title' => $donation->label()]);
    $header = [$this->t('Revision'), $this->t('Operations')];

    $revert_permission = (($account->hasPermission("revert all donation revisions") || $account->hasPermission('administer donation entities')));
    $delete_permission = (($account->hasPermission("delete all donation revisions") || $account->hasPermission('administer donation entities')));

    $rows = [];

    $vids = $donation_storage->revisionIds($donation);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\campaign_kit\DonationInterface $revision */
      $revision = $donation_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $donation->getRevisionId()) {
          $link = $this->l($date, new Url('entity.donation.revision', ['donation' => $donation->id(), 'donation_revision' => $vid]));
        }
        else {
          $link = $donation->link($date);
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
              Url::fromRoute('entity.donation.translation_revert', ['donation' => $donation->id(), 'donation_revision' => $vid, 'langcode' => $langcode]) :
              Url::fromRoute('entity.donation.revision_revert', ['donation' => $donation->id(), 'donation_revision' => $vid]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.donation.revision_delete', ['donation' => $donation->id(), 'donation_revision' => $vid]),
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

    $build['donation_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
