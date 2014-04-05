<?php
// Service Code
namespace Tikit\TikitBundle\Service;

use Doctrine\ORM\Mapping as ORM;
use Tikit\TikitBundle\Entity\Tikit;
use Tikit\TikitBundle\Entity\TikitScore;
use Tikit\TikitBundle\Entity\SiteSpam;
use Tikit\TikitBundle\Entity\SpamUserCount;

use Doctrine\ORM\Query\ResultSetMapping;

class TikitModel
{
//
	const LINK_ATTACH_TYPE            = 'link';
	const VIDEO_ATTACH_TYPE           = 'video';
	const FILE_ATTACH_TYPE            = 'file';
	const LESSON_ATTACH_TYPE          = 'lesson';
	const MUSIC_ATTACH_TYPE           = 'music';

    const MAX_POST_MESSAGE_LENGTH     = 500;
    const MAX_POST_COMMENT_LENGTH     = 500;
    const MAX_LINK_TITLE_LENGTH       = 250;
    const MAX_LINK_DESCRIPTION_LENGTH = 500;
    const HTTP_REQUEST_TIMEOUT = 1;
//


    protected $em;

    public function __construct($em)
    {
        $this->em = $em;
    }

    /**
     * Gets Symfony-Barcelona info from Sensio Connect. Info is stored in APC during an hour to increase speed
     * @return array
     */
    public function addTikit($form_data)
    {
        $tikit = new Tikit();
        $tikit->setTikitTitle($form_data['tikit_name']);
        $tikit->setTikitUrl($form_data['tikit_url']);
        $category = $this->em->find('\Tikit\TikitBundle\Entity\Category', 1);
        $tikit->setCategory($category);
        $user = $this->em->find('\Tikit\TikitBundle\Entity\FosUser', 1);
        $tikit->setUser($user);
        $this->em->persist($tikit);
        $tikit_score = new TikitScore($tikit,$user,1);
        $tikit_score->setTikit($tikit);
        $tikit_score->setUser($user);
        $this->em->persist($tikit_score);
        $this->em->flush();
    }

    public function getTikit($id)
    {
        //test native query use here
        /*$rsm = new ResultSetMapping();
        $query = $this->em->createNativeQuery('SELECT t.*, u.username FROM tikit t
                                            JOIN fos_user u ON u.id = t.user_id
                                            AND t.id = 1', $rsm);
        $query->setParameter(1, $id);
        $tikit = $query->getResult();*/
        $query = $this->em->createQuery('SELECT t, u.id, u.username FROM \Tikit\TikitBundle\Entity\Tikit t
                                    JOIN \Tikit\TikitBundle\Entity\FosUser u WITH u.id = t.user
                                    AND t.id = :id')
                                    ->setMaxResults(1);
        $query->setParameters(array(
            'id' => $id,
        ));
        $tikit = $query->getResult();
        return $tikit;
    }

    public function getTotalTikits()
    {
        $count = $this->em->createQuery('SELECT COUNT(DISTINCT t.id) FROM \Tikit\TikitBundle\Entity\Tikit t WHERE t.status = 1')
                    ->getSingleResult();
        return $count[1];
    }

    //\FOS\UserBundle\Model\User
    public function getAllTikits($count_per_page,$offset)
    {
        $query = $this->em->createQuery('SELECT t, u.username FROM \Tikit\TikitBundle\Entity\Tikit t
                                    LEFT JOIN \Tikit\TikitBundle\Entity\FosUser u WITH u.id = t.user
                                    AND t.status = 1 ORDER BY t.score DESC')
                    ->setMaxResults($count_per_page)
                    ->setFirstResult($offset);
        $tikits = $query->getResult();
        return $tikits;
    }

    public function countTikitsByCategory($category)
    {
        $query = $this->em->createQuery('SELECT COUNT(DISTINCT t.id) FROM \Tikit\TikitBundle\Entity\Tikit t
            WHERE t.status = 1 AND t.category = :category');
        $query->setParameters(array(
            'category' => $category
        ));
        $count = $query->getSingleResult();
        return $count[1];
    }
        //\FOS\UserBundle\Model\User
    public function getTikitsByCategory($count_per_page,$offset,$category)
    {
        $query = $this->em->createQuery('SELECT t, u.username, s.vote FROM \Tikit\TikitBundle\Entity\Tikit t
                                    LEFT JOIN \Tikit\TikitBundle\Entity\FosUser u WITH u.id = t.user
                                    LEFT JOIN \Tikit\TikitBundle\Entity\TikitScore s
                                    WITH s.user = t.user AND s.tikit = t.id
                                    AND t.status = 1 AND t.category = :category ORDER BY t.score DESC')
                    ->setMaxResults($count_per_page)
                    ->setFirstResult($offset);
        $query->setParameters(array(
            'category' => $category
        ));
        $tikits = $query->getResult();
        return $tikits;
    }

     public function getTikitsByUser($count_per_page,$offset,$user_id)
    {
        $query = $this->em->createQuery('SELECT t, u.username FROM \Tikit\TikitBundle\Entity\Tikit t
                                    LEFT JOIN \Tikit\TikitBundle\Entity\FosUser u WITH u.id = t.user
                                    AND t.status = 1 AND t.user = :user_id ORDER BY t.score DESC')
                    ->setMaxResults($count_per_page)
                    ->setFirstResult($offset);
        $query->setParameters(array(
            'user_id' => $user_id
        ));
        $tikits = $query->getResult();
        return $tikits;
    }

    public function markTikitAsSpam($tikit_id,$user_id)
    {
        $sitespam = new SiteSpam();
        $tikit = $this->em->find('\Tikit\TikitBundle\Entity\Tikit', $tikit_id);
        $sitespam->setTikitId($tikit->getId());
        $user = $this->em->find('\Tikit\TikitBundle\Entity\FosUser', $user_id);
        $sitespam->setUser($user);
        $this->em->persist($sitespam);
        $this->em->flush();
        return 1;
    }

    public function processTikitAsSpam($tikit_id,$user_id)
    {
        $sitespam = $this->em->getRepository('\Tikit\TikitBundle\Entity\SiteSpam')->findOneBy(array('tikitId' => $tikit_id));
        $sitespam->setStatus(SiteSpam::PROCESSED);
        $tikit = $this->em->getRepository('\Tikit\TikitBundle\Entity\Tikit')->findOneBy(array('id' => $tikit_id));
        $tikit->setStatus(Tikit::STATUS_BLOCKED);
        $spamusercount = $this->em->getRepository('\Tikit\TikitBundle\Entity\SpamUserCount')->findOneBy(array('user' => $user_id));
        if(!$spamusercount)
        {
            $spamusercount = new SpamUserCount();
            $spamusercount->setUser($user);
        } else {
            $spamusercount->setSpamNumber($spamusercount->getSpamNumber()+1);
        }
        $this->em->persist($spamusercount);
        $this->em->persist($tikit);
        $this->em->persist($sitespam);
        $this->em->flush();
        return 1;
    }

    public function processTikitAsNotSpam($tikit_id,$user_id)
    {
        $query = $this->em->createQuery('DELETE \Tikit\TikitBundle\Entity\SiteSpam s
            WHERE s.tikitId = :tikit');
        $query->setParameters(array(
            'tikit' => $tikit_id
        ));
        $query->getResult();
        $query = $this->em->createQuery('UPDATE \Tikit\TikitBundle\Entity\SpamUserCount s SET s.spamNumber = s.spamNumber - 1
            WHERE s.user = :user AND s.spamNumber !=0 ');
        $query->setParameters(array(
            'user' => $user_id
        ));
        $query->getResult();
        return 1;
    }

    public function voteTikit($tikit_id,$user_id,$vote)
    {
        $tikitscore = $this->em->getRepository('\Tikit\TikitBundle\Entity\TikitScore')->findOneBy(array('user' => $user_id, 'tikit' => $tikit_id));
        if(!$tikitscore){
            $tikitscore = new TikitScore($tikit_id,$user_id,$vote);
        } else {
            if($tikitscore->getVote() == $vote)
                return 0;
            $tikitscore->setVote($vote);
        }
        $tikit = $this->em->find('\Tikit\TikitBundle\Entity\Tikit', $tikit_id);
        $tikit->setScore($tikit->getScore() + $vote);
        $tikitscore->setTikit($tikit);
        $user = $this->em->find('\Tikit\TikitBundle\Entity\FosUser', $user_id);
        $tikitscore->setUser($user);
        $this->em->persist($tikitscore);
        $this->em->persist($tikit);
        $this->em->flush();
        return 1;
    }

    /*public function removeTikitScore($tikit_id,$user_id)
    {
        $tikit = $this->em->find('\Tikit\TikitBundle\Entity\Tikit', $tikit_id);
        $tikit->setScore($tikit->getScore()-1);
        $this->em->persist($tikit);
        $this->em->flush();
        $query = $this->em->createQuery('DELETE \Tikit\TikitBundle\Entity\TikitScore t
            WHERE t.tikit = :tikit AND t.user = :user');
        $query->setParameters(array(
            'tikit' => $tikit_id,
            'user' => $user_id
        ));
        $query->getResult();
        return 1;
    }*/


    public function linkRequestContents($url, $options = array())
    {
        //Merge the default options.
        $options += array(
            'max_redirects' => 3,
            'timeout' => 30,
            'max_imagesize' => 512 * 1024,
            'min_imagesize' => 1 * 1024,
            'min_width' => 50,
            'min_height' => 50
        );

        $def_data = array(
            'images' => array(),
            'title' => self::cutString($url, self::MAX_LINK_TITLE_LENGTH),
            'description' => '',
            'url' => $url,
            'type' => self::LINK_ATTACH_TYPE
        );

        //include_once("components/scrape/http_request.php");
        $timer = self::timer_start();

        $result = self::get_http_content($url, array(
            'timeout' => $options['timeout'],
            'max_redirects' => $options['max_redirects']
        ));

        if ($result->code != 200) {
            //logText("Could not connect to {$url}, HTTP error {$result->code}");
            return $def_data;
        }

        $page_url = $url;
        if (isset($result->redirect_code) && in_array($result->redirect_code, array(301, 302, 307))) {
            $page_url = $result->redirect_url;
            $def_data['title'] = $page_url;
            $def_data['url'] = $page_url;
        }

        $data = array(
            'images' => array(),
            'title' => '',
            'description' => '',
            'url' => '',
            'type' => ''
        );
        if (stripos($result->headers['Content-Type'], 'image') !== FALSE) {
            $images = array();
            $imagesize = self::linkValidateFilesize($page_url, $options['max_imagesize'], $options['min_imagesize'], ($options['timeout'] - self::timer_read($timer) / 1000));
            if ($imagesize != -1) {
                $images[$page_url] = $imagesize;
            }

            $data['images'] = $images;
        }
        else {
            self::scrapePageData($page_url, $options, $timer, $result->data, $data);
        }
        foreach ($def_data as $key => $value) {
            if (empty($data[$key])) {
                $data[$key] = $value;
            }
        }
        $data['title'] = strip_tags($data['title']);
        $data['title'] = self::cutString($data['title'], self::MAX_LINK_TITLE_LENGTH);
        $data['description'] = strip_tags($data['description']);
        $data['description'] = self::cutString($data['description'], self::MAX_LINK_DESCRIPTION_LENGTH);
        $data['type'] = strtolower($data['type']);

        return $data;
    }


    public static function cutString($string, $length)
    {
        if (strlen($string) > $length) {
            return substr($string, 0, $length - 3) . '...';
        } else {
            return $string;
        }
    }

    public static function linkValidateFilesize($file_url, $max_size = 0, $min_size = 0, $timeout = 10, $max_redirects = 3)
    {
        $options = array(
            'method' => 'HEAD',
            'max_redirects' => $max_redirects,
            'timeout' => $timeout,
        );
        $result = self::get_http_content($file_url, $options);
        if ($result->code == 200
            && (!$max_size || (isset($result->headers['Content-Length']) && $result->headers['Content-Length'] < $max_size))
            && (!$min_size || (isset($result->headers['Content-Length']) && $result->headers['Content-Length'] > $min_size)))
        {
            return isset($result->headers['Content-Length']) ? $result->headers['Content-Length'] : 0;
        }
        return -1;
    }

    public static function scrapePageData($page_url, $options, $timer, $content, &$data)
    {
        self::linkDetectCharset($content);

        $document = new \DOMDocument();
        if (@$document->loadHTML($content) === FALSE) {
            //logText("Could not parse the content on $page_url");
            return $data;
        }

        $xpath = new \DOMXPath($document);

        $data['title'] = self::getXMLAttrVal(
            $xpath,
            array(
                "//meta[@property='og:title']",
                "//meta[@name='og:title']",
                "//title"
            )
        );

        $data['description'] = self::getXMLAttrVal(
            $xpath,
            array(
                "//meta[@property='og:description']",
                "//meta[@name='og:description']",
                "//meta[@name='description']"
            )
        );

        $data['url'] = $page_url;
        /*$data['url'] = self::getXMLAttrVal(
            $xpath,
            array(
                "//meta[@property='og:url']",
                "//meta[@name='og:url']"
            ),
            $page_url
        );*/

        $data['type'] = self::getXMLAttrVal(
            $xpath,
            array(
                "//meta[@property='og:type']",
                "//meta[@name='og:type']"
            )
        );

        $data['video'] = self::getXMLAttrVal(
            $xpath,
            array(
                array(  // For video url from hulu.com
                    "xpath" => "//link[@rel='media:video']",
                    "value" => "href"
                ),
                array(  // For video url from metacafe.com
                    "xpath" => "//link[@rel='video_src']",
                    "value" => "href"
                ),
                array(  // For video url from break.com
                    "xpath" => "//meta[@name='embed_video_url']",
                    "value" => "content"
                ),
                "//meta[@property='og:video']",
                "//meta[@name='og:video']",
                "//meta[@property='video:url']",
                "//meta[@name='video:url']"
            )
        );

        if (!empty($data['video'])) {
            $data['video'] = array('url' => $data['video']);

            $data['video']['type'] = self::getXMLAttrVal(
                $xpath,
                array(
                    "//meta[@name='video_type']",
                    "//meta[@property='og:video:type']",
                    "//meta[@name='og:video:type']"
                )
            );
            /*
            $data['video']['width'] = self::getXMLAttrVal(
                $xpath,
                array(
                    "//meta[@property='og:video:width']",
                    "//meta[@name='og:video:width']"
                )
            );
            $data['video']['height'] = self::getXMLAttrVal(
                $xpath,
                array(
                    "//meta[@property='og:video:height']",
                    "//meta[@name='og:video:height']"
                )
            );
            */
        }

        //"\xE2\x80\xA6" is the UTF8 character sequence for the ellipsis, which must be enclosed in double quotes.
        //Neither the literal binary character (?) nor the HTML entity (&hellip;) work on all operating systems.
        if (function_exists('mb_strlen')) {
            if ($options['max_title_length'] > 0 && mb_strlen($data['title']) > $options['max_title_length']) {
                $data['title'] = mb_substr($data['title'], 0, $options['max_title_length'] - 3) ."\xE2\x80\xA6";
            }
            if ($options['max_description_length'] > 0 && mb_strlen($data['description']) > $options['max_description_length']) {
                $data['description'] = mb_substr($data['description'], 0, $options['max_description_length'] - 3) ."\xE2\x80\xA6";
            }
        } else {
            if ($options['max_title_length'] > 0 && strlen($data['title']) > $options['max_title_length']) {
                $data['title'] = substr($data['title'], 0, $options['max_title_length'] - 3) ."\xE2\x80\xA6";
            }
            if ($options['max_description_length'] > 0 && strlen($data['description']) > $options['max_description_length']) {
                $data['description'] = substr($data['description'], 0, $options['max_description_length'] - 3) ."\xE2\x80\xA6";
            }
        }

        $image = self::getXMLAttrVal(
            $xpath,
            array(
                "//meta[@property='og:image']",
                "//meta[@name='og:image']",
                array(
                    "xpath" => "//link[@rel='image_src']",
                    "value" => "href"
                ),
                "//meta[@name='thumbnail']"
            )
        );
        $images = array();

        if (!empty($image)) {
            $images[] = $image;
        } else {
            $hrefs = @$xpath->evaluate("/html/body//img");

            include_once("components/scrape/url_to_absolute.php");

            for ($i = 0; $i < $hrefs->length; $i++) {
                $image_url = $hrefs->item($i)->getAttribute('src');
                if (!isset($image_url) || empty($image_url)) {
                    continue;
                }

                /*if (substr($image_url,0,7) != 'http://') {*/
                    $abs_url = url_to_absolute($page_url, $image_url);
                    /*$ext = trim(pathinfo($image_url, PATHINFO_EXTENSION));*/

                    if ($abs_url/* && ($ext != 'gif')*/) {
                        $valid = true;
                        if ($options['min_width'] || $options['min_height']) {
                            $valid = false;
                            $width = $hrefs->item($i)->getAttribute('width');
                            $height = $hrefs->item($i)->getAttribute('height');

                            if ((empty($width) && $options['min_width'])
                                || (empty($height) && $options['min_height']))
                            {
                                $img_data = @getimagesize($abs_url);

                                if ($img_data && is_array($img_data)) {
                                    list($width, $height, $type, $attr) = $img_data;
                                }
                            }
                            $width = intval($width);
                            $height = intval($height);

                            if (($options['min_width'] && ($width >= $options['min_width']))
                                    || ($options['min_height'] && ($height >= $options['min_height'])))
                            {
                                if (
                                        (($width > 0 && $height > 0 && (($width / $height) <= 3) && (($width / $height) >= 0.3)
                                                                    && (($height / $width) <= 3) && (($height / $width) >= 0.3))
                                         || ($width > 0 && $height == 0 && $width < 700)
                                         || ($width == 0 && $height > 0 && $height < 700)
                                        )
                                        /*&& strpos($img, 'logo') === false*/ )
                                {
                                    $valid = true;
                                }
                            }
                        }

                        if ($valid) {
                            $imagesize = 1;
                            if ($options['max_imagesize'] || $options['min_imagesize']) {
                                $imagesize = self::linkValidateFilesize($abs_url,
                                    $options['max_imagesize'], $options['min_imagesize'], ($options['timeout'] - self::timer_read($timer) / 1000));
                            }
                            if ($imagesize != -1) {
                                $images[$abs_url] = $imagesize;
                            }
                        }
                    }
                    if (($options['timeout'] - self::timer_read($timer) / 1000) <= 0) {
                        //logText("Request timed out for $url");
                        break;
                    }
                /*}*/
            }
            //asort($images);
            //$images = array_reverse($images, TRUE);
            $images = array_keys($images);
        }
        $data['images'] = $images;
    }


/**
 * Perform an HTTP request.
 *
 * This is a flexible and powerful HTTP client implementation. Correctly
 * handles GET, POST, PUT or any other HTTP requests. Handles redirects.
 *
 * @param $url
 *   A string containing a fully qualified URI.
 * @param $options
 *   (optional) An array which can have one or more of following keys:
 *   - headers
 *       An array containing request headers to send as name/value pairs.
 *   - method
 *       A string containing the request method. Defaults to 'GET'.
 *   - data
 *       A string containing the request body. Defaults to NULL.
 *   - max_redirects
 *       An integer representing how many times a redirect may be followed.
 *       Defaults to 3.
 *   - timeout
 *       A float representing the maximum number of seconds the function call
 *       may take. The default is 30 seconds. If a timeout occurs, the error
 *       code is set to the HTTP_REQUEST_TIMEOUT constant.
 *
 * @return
 *   An object which can have one or more of the following parameters:
 *   - request
 *       A string containing the request body that was sent.
 *   - code
 *       An integer containing the response status code, or the error code if
 *       an error occurred.
 *   - protocol
 *       The response protocol (e.g. HTTP/1.1 or HTTP/1.0).
 *   - status_message
 *       The status message from the response, if a response was received.
 *   - redirect_code
 *       If redirected, an integer containing the initial response status code.
 *   - redirect_url
 *       If redirected, a string containing the redirection location.
 *   - error
 *       If an error occurred, the error message. Otherwise not set.
 *   - headers
 *       An array containing the response headers as name/value pairs.
 *   - data
 *       A string containing the response body that was received.
 */
public static function get_http_content($url, $options = array()) {
  $result = new \stdClass();

  // Parse the URL and make sure we can handle the schema.
  $uri = @parse_url($url);

  if ($uri == FALSE) {
    $result->error = 'unable to parse URL';
    $result->code = -1001;
    return $result;
  }

  if (!isset($uri['scheme'])) {
    $result->error = 'missing schema';
    $result->code = -1002;
    return $result;
  }

  $timer = self::timer_start();

  // Merge the default options.
  $options += array(
    'headers' => array(),
    'method' => 'GET',
    'data' => NULL,
    'max_redirects' => 3,
    'timeout' => 30,
  );

  switch (strtolower($uri['scheme'])) {
    case 'http':
      $port = isset($uri['port']) ? $uri['port'] : 80;
      $host = $uri['host'] . ($port != 80 ? ':'. $port : '');
      $fp   = @fsockopen($uri['host'], $port, $errno, $errstr, $options['timeout']);
      break;

    case 'https':
      // Note: Only works when PHP is compiled with OpenSSL support.
      $port = isset($uri['port']) ? $uri['port'] : 443;
      $host = $uri['host'] . ($port != 443 ? ':'. $port : '');
      $fp   = @fsockopen('ssl://'. $uri['host'], $port, $errno, $errstr, $options['timeout']);
      break;

    default:
      $result->error = 'invalid schema '. $uri['scheme'];
      $result->code = -1003;
      return $result;
  }

  // Make sure the socket opened properly.
  if (!$fp) {
    // When a network error occurs, we use a negative number so it does not
    // clash with the HTTP status codes.
    $result->code = -$errno;
    $result->error = trim($errstr);

    return $result;
  }

  // Construct the path to act on.
  $path = isset($uri['path']) ? $uri['path'] : '/';
  if (isset($uri['query'])) {
    $path .= '?'. $uri['query'];
  }

  // Merge the default headers.
  $options['headers'] += array(
    'User-Agent' => 'WTT'
    //'User-Agent' => $_SERVER['HTTP_USER_AGENT']
  );

  // RFC 2616: "non-standard ports MUST, default ports MAY be included".
  // We don't add the standard port to prevent from breaking rewrite rules
  // checking the host that do not take into account the port number.
  $options['headers']['Host'] = $host;

  // Only add Content-Length if we actually have any content or if it is a POST
  // or PUT request. Some non-standard servers get confused by Content-Length in
  // at least HEAD/GET requests, and Squid always requires Content-Length in
  // POST/PUT requests.
  $content_length = strlen($options['data']);
  if ($content_length > 0 || $options['method'] == 'POST' || $options['method'] == 'PUT') {
    $options['headers']['Content-Length'] = $content_length;
  }

  // If the server URL has a user then attempt to use basic authentication.
  if (isset($uri['user'])) {
    $options['headers']['Authorization'] = 'Basic '. base64_encode($uri['user'] . (!empty($uri['pass']) ? ":". $uri['pass'] : ''));
  }

  $request = $options['method'] .' '. $path ." HTTP/1.0\r\n";
  foreach ($options['headers'] as $name => $value) {
    $request .= $name .': '. trim($value) ."\r\n";
  }
  $request .= "\r\n". $options['data'];
  $result->request = $request;

  fwrite($fp, $request);

  // Fetch response.
  $response = '';
  while (!feof($fp)) {
    // Calculate how much time is left of the original timeout value.
    $timeout = $options['timeout'] - self::timer_read($timer) / 1000;
    if ($timeout <= 0) {
      $result->code = self::HTTP_REQUEST_TIMEOUT;
      $result->error = 'request timed out';
      return $result;
    }
    stream_set_timeout($fp, floor($timeout), floor(1000000 * fmod($timeout, 1)));
    $response .= fread($fp, 1024);
  }
  fclose($fp);

  // Parse response headers from the response body.
  list($response, $result->data) = explode("\r\n\r\n", $response, 2);
  $response = preg_split("/\r\n|\n|\r/", $response);

  // Parse the response status line.
  list($protocol, $code, $status_message) = explode(' ', trim(array_shift($response)), 3);
  $result->protocol = $protocol;
  $result->status_message = $status_message;

  $result->headers = array();

  // Parse the response headers.
  while ($line = trim(array_shift($response))) {
    list($header, $value) = explode(':', $line, 2);
    if (isset($result->headers[$header]) && $header == 'Set-Cookie') {
      // RFC 2109: the Set-Cookie response header comprises the token Set-
      // Cookie:, followed by a comma-separated list of one or more cookies.
      $result->headers[$header] .= ','. trim($value);
    }
    else {
      $result->headers[$header] = trim($value);
    }
  }

  $responses = array(
    100 => 'Continue',
    101 => 'Switching Protocols',
    200 => 'OK',
    201 => 'Created',
    202 => 'Accepted',
    203 => 'Non-Authoritative Information',
    204 => 'No Content',
    205 => 'Reset Content',
    206 => 'Partial Content',
    300 => 'Multiple Choices',
    301 => 'Moved Permanently',
    302 => 'Found',
    303 => 'See Other',
    304 => 'Not Modified',
    305 => 'Use Proxy',
    307 => 'Temporary Redirect',
    400 => 'Bad Request',
    401 => 'Unauthorized',
    402 => 'Payment Required',
    403 => 'Forbidden',
    404 => 'Not Found',
    405 => 'Method Not Allowed',
    406 => 'Not Acceptable',
    407 => 'Proxy Authentication Required',
    408 => 'Request Time-out',
    409 => 'Conflict',
    410 => 'Gone',
    411 => 'Length Required',
    412 => 'Precondition Failed',
    413 => 'Request Entity Too Large',
    414 => 'Request-URI Too Large',
    415 => 'Unsupported Media Type',
    416 => 'Requested range not satisfiable',
    417 => 'Expectation Failed',
    500 => 'Internal Server Error',
    501 => 'Not Implemented',
    502 => 'Bad Gateway',
    503 => 'Service Unavailable',
    504 => 'Gateway Time-out',
    505 => 'HTTP Version not supported',
  );
  // RFC 2616 states that all unknown HTTP codes must be treated the same as the
  // base code in their class.
  if (!isset($responses[$code])) {
    $code = floor($code / 100) * 100;
  }
  $result->code = $code;

  switch ($code) {
    case 200:
      // OK
    case 304:
      // Not modified
      break;

    case 301:
      // Moved permanently
    case 302:
      // Moved temporarily
    case 307:
      // Moved temporarily
      $location = $result->headers['Location'];
      $options['timeout'] -= self::timer_read($timer) / 1000;
      if ($options['timeout'] <= 0) {
        $result->code = self::HTTP_REQUEST_TIMEOUT;
        $result->error = 'request timed out';
      }
      elseif ($options['max_redirects']) {
        // Redirect to the new location.
        $options['max_redirects']--;
        $result = self::get_http_content($location, $options);
        $result->redirect_code = $code;
      }
      $result->redirect_url = $location;
      break;

    default:
      $result->error = $status_message;
  }

  return $result;
}

public static function timer_start() {
  list($usec, $sec) = explode(' ', microtime());
  return (float)$usec + (float)$sec;
}

public static function timer_read($timer) {
  if (isset($timer)) {
    list($usec, $sec) = explode(' ', microtime());
    $stop = (float)$usec + (float)$sec;
    return round(($stop - $timer) * 1000, 2);
  }
}


    public static function linkDetectCharset(&$content)
    {
        if (function_exists('mb_detect_encoding')) {
            preg_match('~meta.*?charset=([-a-z0-9_]+)~i', $content, $charset);
            if (isset($charset[1])) {
                $content = mb_convert_encoding($content, 'HTML-ENTITIES', $charset[1]);
                return false;
            }

            $charset = mb_detect_encoding($content);
            if ($charset) {
                $head_pos = mb_strpos($content, '<head>');
                if ($head_pos == false) {
                    $head_pos = mb_strpos($content, '<HEAD>');
                }
                if ($head_pos !== false) {
                    $head_pos += 6;
                    $content = mb_substr($content, 0, $head_pos) .
                        '<meta http-equiv="Content-Type" content="text/html; charset='. $charset .'">'. mb_substr($content, $head_pos);
                }
                $content = mb_convert_encoding($content, 'HTML-ENTITIES', $charset);
                return true;
            }
        }

        return FALSE;
    }

    public static function getXMLAttrVal(&$xpathDOM, $xpaths, $default = '', $defaultAttrValue = 'content')
    {
        $result = $default;
        foreach ($xpaths as $attr) {
            if (is_array($attr)) {
                $xpath = $attr['xpath'];
                $attrValue = $attr['value'];
            } else {
                $xpath = $attr;
                $attrValue = $defaultAttrValue;
            }
            $values = @$xpathDOM->evaluate($xpath);
            $result = trim($values->length > 0 ? $values->item(0)->getAttribute($attrValue) : $result);
            if (!empty($result)) break;
        }
        return $result;
    }

}