<?php
/**
 * @var modX $modx
 * @package modx
 */

session_cache_limiter('public');
define('MODX_CONNECTOR_INCLUDED', 1);
require_once dirname(__DIR__).'/index.php';
$_SERVER['HTTP_MODAUTH'] = $modx->user->getUserToken($modx->context->get('key'));
$modx->request->handleRequest(array('location' => 'system','action' => 'phpthumb'));
