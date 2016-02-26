<?php

namespace Bethel\TutorLabsBundle\Helper;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

require_once __DIR__.'/wsrest_username.php';

  class WsRestUsernameWrapper{

    public function testSomething($username='ejc84332'){


      $auth = array('api_key' => 'your API key');
      $wrap = new WS_REST_Username($auth);
      $result = $wrap->get_roles($username);
      $result = "test";
      echo "<pre>" . print_r(get_declared_classes()) . "</pre>";

    }
  }