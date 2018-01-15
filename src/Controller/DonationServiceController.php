<?php

namespace Drupal\campaign_kit\Controller;

use Drupal\campaign_kit\Entity\Campaign;
use GuzzleHttp\Exception\RequestException;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class DonationServiceController.
 */
class DonationServiceController extends ControllerBase {

  /**
   * Save Campaign.
   *
   * @return array
   * Return Hello string.
   */
  public function saveCampaign() {
    // SEND THE REQUEST
    try {
      $response = \Drupal::httpClient()->get('https://api.webconnex.com/v2/public/forms', [
        'headers' => [
          'apikey' => '1a91ac5a8b2147ddacccc68f698cc77a',
        ],
      ]);
      // Expected result.
      // getBody() returns an instance of Psr\Http\Message\StreamInterface.
      // @see http://docs.guzzlephp.org/en/latest/psr7.html#body
      $data = (string) $response->getBody();
    }
    catch (RequestException $e) {
      watchdog_exception('campaign_kit', $e);
    }

    // Array
    $forms = json_decode($data, true);
    $forms2 = json_decode($data);

    // Create campaign if it is new
    // If status is 'open'
    //$resStatus = array();
    $num = 0;
    foreach ($forms['data'] as $key => $value) {
      if ($value['status'] == 'open'){
        //$resStatus = $value['status'];
        //\Drupal::logger('campaign_kit')->error($value['status']);

        // Verify if it is new in DB
        // Obtener una entidad mediante consulta
        $ids = \Drupal::entityQuery('campaign')
          ->condition('external_id', $value['id'], '=')
          ->execute();
        //$AccountPlan = AccountPlan::load(reset($ids));

        if ($ids) {
          drupal_set_message("We didn't create any campaign");
        } else{
          $values = array(
            'name' => $value['name'],
            'donate_path' => array(
              'uri' => 'http://'.$value['publishedPath'],
              'title' => $value['publishedPath'],
            ),
            'external_id' => $value['id'],
            'campaign_status' => $value['status'],
          );
          $campaign = Campaign::create($values);
          $campaign->save();
          $num++;
          drupal_set_message("Campaign ".$value['name']." created; reason => status: ".$value['status']);
        }
      }else{
        //$resStatus = $value['status'];
        //\Drupal::logger('campaign_kit')->error($value['status']);
        drupal_set_message("Campaign ".$value['name']." cannot be created; reason => status: ".$value['status']);
      }
    }


    $res = array(
      '#theme' => 'donation_service',
      '#name' => $forms['responseCode'],
      '#type' => gettype($forms),
      '#type2' => gettype($forms2),
    );

    drupal_set_message("It's been created : ".$num);

    return $res;
  }

}
