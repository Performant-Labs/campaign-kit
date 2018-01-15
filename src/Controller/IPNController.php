<?php

namespace Drupal\campaign_kit\Controller;

use Drupal\Core\Controller\ControllerBase;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;

/**
 * Class PaymentController.
 */
class IPNController extends ControllerBase {

  /**
   * @var bool $use_sandbox     Indicates if the sandbox endpoint is used.
   */
  private $use_sandbox = false;
  /**
   * @var bool $use_local_certs Indicates if the local certificates are used.
   */
  private $use_local_certs = false;
  /** Production Postback URL */
  const VERIFY_URI = 'https://ipnpb.sandbox.paypal.com/cgi-bin/webscr';
  /** Sandbox Postback URL */
  const SANDBOX_VERIFY_URI = 'https://ipnpb.sandbox.paypal.com/cgi-bin/webscr';
  /** Response from PayPal indicating validation was successful */
  const VALID = 'VERIFIED';
  /** Response from PayPal indicating validation failed */
  const INVALID = 'INVALID';

  /*
   * Create local variable
   *
   */
  protected $flood;
  protected $userStorage;
  protected $csrfToken;
  protected $userAuth;
  protected $routeProvider;
  protected $serializer;
  protected $serializerFormats = [];

  /*
   *  Instantiate my service
   *
   */
  public function __construct(Serializer $serializer, array $serializer_formats) {
    $this->serializer = $serializer;
    $this->serializerFormats = $serializer_formats;
  }

  /*
   *  Override the ControllerBase's create method
   *  When your controller needs to access services from the container
   *
   */
  public static function create(ContainerInterface $container) {
    if ($container->hasParameter('serializer.formats') && $container->has('serializer')) {
      $serializer = $container->get('serializer');
      $formats = $container->getParameter('serializer.formats');
    }
    else {
      $formats = ['json'];
      $encoders = [new JsonEncoder()];
      $serializer = new Serializer([], $encoders);
    }

    return new static(
      $serializer,
      $formats
    );
  }

  /**
   * Sets the IPN verification to sandbox mode (for use when testing,
   * should not be enabled in production).
   * @return void
   */
  public function useSandbox()
  {
    $this->use_sandbox = true;
  }
  /**
   * Sets curl to use php curl's built in certs (may be required in some
   * environments).
   * @return void
   */
  public function usePHPCerts()
  {
    $this->use_local_certs = false;
  }
  /**
   * Determine endpoint to post the verification data to.
   * @return string
   */
  public function getPaypalUri()
  {
    if ($this->use_sandbox) {
      return self::SANDBOX_VERIFY_URI;
    } else {
      return self::VERIFY_URI;
    }
  }


  /**
   * @param $name
   *
   * @return array
   */
  public function registerPayment($name) {
    $build = [];

    $build['intro'] = [
      '#markup' => t("This page list the payment processor plugins we've created and the variable name: ".$name. ".")
    ];
    \Drupal::logger('campaign_kit')->notice($name);

    return $build;
  }

  /**
   * @param \Symfony\Component\HttpFoundation\Request $request
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   * @throws \Exception
   */
  public function savePayment(Request $request) {
    //$format = $this->getRequestFormat($request);

    //$content = $request->getContent();
    //$credentials = $this->serializer->decode($content, $format);

    /*
    if (!isset($credentials['idProperty']) && !isset($credentials['idUser'])) {
      throw new BadRequestHttpException('Missing credentials.');
    }
    // Verify if exists these parameters
    if (!isset($credentials['idProperty'])) {
      throw new BadRequestHttpException('Missing credentials: idProperty.');
    }
    if (!isset($credentials['idUser'])) {
      throw new BadRequestHttpException('Missing credentials. idUser.');
    }*/

    if ( ! count($_POST)) {
      throw new \Exception("Missing POST Data");
    }
    $raw_post_data = file_get_contents('php://input');
    $raw_post_array = explode('&', $raw_post_data);
    $myPost = array();
    foreach ($raw_post_array as $keyval) {
      $keyval = explode('=', $keyval);
      if (count($keyval) == 2) {
        // Since we do not want the plus in the datetime string to be encoded to a space, we manually encode it.
        if ($keyval[0] === 'payment_date') {
          if (substr_count($keyval[1], '+') === 1) {
            $keyval[1] = str_replace('+', '%2B', $keyval[1]);
          }
        }
        $myPost[$keyval[0]] = urldecode($keyval[1]);
      }
    }
    // Build the body of the verification post request, adding the _notify-validate command.
    $req = 'cmd=_notify-validate';
    $get_magic_quotes_exists = false;
    if (function_exists('get_magic_quotes_gpc')) {
      $get_magic_quotes_exists = true;
    }
    foreach ($myPost as $key => $value) {
      if ($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
        $value = urlencode(stripslashes($value));
      } else {
        $value = urlencode($value);
      }
      $req .= "&$key=$value";
    }
    // Post the data back to PayPal, using curl. Throw exceptions if errors occur.
    $ch = curl_init($this->getPaypalUri());
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
    curl_setopt($ch, CURLOPT_SSLVERSION, 6);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    // This is often required if the server is missing a global cert bundle, or is using an outdated one.
    if ($this->use_local_certs) {
      curl_setopt($ch, CURLOPT_CAINFO, __DIR__ . "/cert/cacert.pem");
    }
    curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
    $res = curl_exec($ch);
    if ( ! ($res)) {
      $errno = curl_errno($ch);
      $errstr = curl_error($ch);
      curl_close($ch);
      throw new \Exception("cURL error: [$errno] $errstr");
    }
    $info = curl_getinfo($ch);
    $http_code = $info['http_code'];
    if ($http_code != 200) {
      throw new \Exception("PayPal responded with http code $http_code");
    }
    curl_close($ch);
    // Check if PayPal verifies the IPN data, and if so, return true.
    if ($res == self::VALID) {
      header("HTTP/1.1 200 OK");

      $test_text = "";
      if ($_POST["test_ipn"] == 1) {
        $test_text = "Test ";
      }

      \Drupal::logger('campaign_kit')->notice($test_text);

      
      //return true;
      $res_properties = array('status' => 'ok');
      return new JsonResponse($res_properties);
    } else {
      //return false;
      $res_properties = array('status' => 'error');
      return new JsonResponse($res_properties);
    }
  }

  /**
   * Gets the format of the current request.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request.
   *
   * @return string
   *   The format of the request.
   */
  protected function getRequestFormat(Request $request) {
    $format = $request->getRequestFormat();
    if (!in_array($format, $this->serializerFormats)) {
      throw new BadRequestHttpException("Unrecognized format: $format.");
    }
    return $format;
  }
}