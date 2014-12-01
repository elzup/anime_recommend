<?php

/* composer modules */
require_once('./vendor/autoload.php');

require_once('./lib/simple_html_dom.php');

require_once './config/config.php';
require_once './config/constants.php';
require_once './config/keys.php';

/* include controllers */
require_once('./controllers/page_controller.php');
require_once('./controllers/crawl_controller.php');

/* include models */
require_once('./models/anireco_model.php');

/* include classes */
require_once('./classes/title.php');
require_once('./classes/best.php');
require_once('./classes/rank.php');

require_once('./helpers/functions.php');

$app = new \Slim\Slim(array(
    'debug'              => true,
    'log.level'          => \Slim\Log::DEBUG,
    'log.enabled'        => true,
    'cookies.encrypt'    => true,    //cookie
));

// views
$app->get('/', '\PageController:showIndex');
$app->get('/cf', '\PageController:cf_check');
$app->get('/reco', '\PageController:showReco');
$app->post('/reco', '\PageController:showReco');

// crawls
$app->get('/get_titles', '\CrawlController:getTitles2');
$app->get('/get_bests', '\CrawlController:getBests');
$app->get('/get_ranks', '\CrawlController:getRanks');

// update
$app->get('/update_ranks', '\CrawlController:updateRanks');
$app->get('/update_descriptions', '\CrawlController:updateDescriptions');

//$app->get('/job/d', '\JobController:tweet_day');
//$app->get('/job/r/:word', function($word) {
//	(new JobController()).regist_word($word);
//});

$app->run();
