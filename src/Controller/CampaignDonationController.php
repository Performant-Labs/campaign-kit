<?php

namespace Drupal\campaign_kit\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class CampaignDonationController.
 */
class CampaignDonationController extends ControllerBase {

  /**
   * Hello.
   *
   * @return string
   *   Return Hello string.
   */
  public function hello($name) {

    $build = [];
    $build['heading'] = [
      '#type' => 'markup',
      '#markup' => '<div class="process-heading">' . $this->t('My custom Form Is below but there will be alot more html than just the form. So this is just an example.') . '</div>',
    ];
    $build['form'] = $this->formBuilder()->getForm('Drupal\campaign_kit\Form\DonateForm', $name);
    return $build;

    /**
    return [
    '#type' => 'markup',
    '#markup' => $this->t('Implement method: hello with parameter(s): '.$name),
    ];**/
  }

}
