<?php

require_once
'config/Session.php';

require_once
'helpers/Auth.php';

require_once
'controllers/AuthController.php';
require_once
'controllers/PesananController.php';
require_once
'controllers/MenuController.php';

Session::start();

require_once
'routes/web.php';



