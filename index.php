<?php
namespace Application;
use Application\FrontController;

error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once('controllers/front.php');

$ctrl                                 = new FrontController;

$action                               = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'index';

$ctrl->$action();

