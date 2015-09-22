<?php

global $config;

$config = array();

require_once 'config/google.php';

require_once 'lib/google-api-php-client/src/Google/autoload.php';
require_once 'lib/formatted-summary-replace-helper.php';

function fetch_results($url, $params = array()) {
    global $config;

    $client = new Google_Client();
    $client->setApplicationName( "DEV_Performance" );
    $client->setDeveloperKey( $config['google']['key'] );

    $service = new Google_Service_Pagespeedonline($client);
    $optParams = array('filter' => 'free-ebooks');
    $results = $service->pagespeedapi->runpagespeed($url, $params);

    return $results; 
}

$url = isset($_GET['url'])?$_GET['url']:"http://www.mohanjith.com";

$result = fetch_results($url, array('locale' => 'en_GB'));

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset=utf-8 />
    <title>Page Speed Results for <?php echo $result->id; ?></title>
    <link rel="stylesheet" type="text/css" media="screen" href="css/master.css" />
</head>
<body>
    <header>
        <h1>Page Speed Results for <?php echo $result->id; ?></h1>
    </header>
    <section>
        <h2>Summary</h2>
        <article>
            <dl>
                <dt>Title</dt>
                <dd><?php echo $result->title; ?></dd>

                <dt>Response Code</dt>
                <dd><?php echo $result->responseCode; ?></dd>
            </dl>
        </article>
    </section>
    <section>
        <?php $pageStats = $result->getPageStats(); ?>
        <h2>Page Stats</h2>
        <article>
            <dl>

                <dt>Total request size</dt>
                <dd><?php echo $pageStats->totalRequestBytes; ?></dd>

                <dt>HTML response size</dt>
                <dd><?php echo $pageStats->htmlResponseBytes; ?></dd>

                <dt>CSS response size</dt>
                <dd><?php echo $pageStats->cssResponseBytes; ?></dd>

                <dt>Javascript response size</dt>
                <dd><?php echo $pageStats->javascriptResponseBytes; ?></dd>

                <dt>Image response size</dt>
                <dd><?php echo $pageStats->imageResponseBytes; ?></dd>

                <dt>Other response size</dt>
                <dd><?php echo $pageStats->otherResponseBytes; ?></dd>

                <dt>Number of hosts</dt>
                <dd><?php echo $pageStats->numberHosts; ?></dd>

                <dt>Number of resources</dt>
                <dd><?php echo $pageStats->numberResources; ?></dd>

                <dt>Number of static resources</dt>
                <dd><?php echo $pageStats->numberStaticResources; ?></dd>

                <dt>Number of javascript resources</dt>
                <dd><?php echo $pageStats->numberJsResources; ?></dd>

                <dt>Number of CSS resources</dt>
                <dd><?php echo $pageStats->numberCssResources; ?></dd>

            </dl>
        </article>
    </section>
    <?php 
        $ruleResults = $result->getFormattedResults()->getRuleResults();
        $rules = array(
            'AvoidLandingPageRedirects',
            'EnableGzipCompression',
            'LeverageBrowserCaching',
            'MainResourceServerResponseTime',
            'MinifyCss',
            'MinifyHTML',
            'MinifyJavaScript',
            'MinimizeRenderBlockingResources',
            'OptimizeImages',
            'PrioritizeVisibleContent',
        );

    foreach ($rules as $rule) {
        $ruleGroups = $ruleResults[$rule]->getGroups();
    ?>
    <section>
        <h2><?php echo $ruleResults[$rule]->getLocalizedRuleName(); ?> (<?php echo $ruleResults[$rule]->getRuleImpact(); ?>)</h2>

        <article>
            <?php 
            $summary = $ruleResults[$rule]->getSummary();
            if ($summary) {
            ?>
            <p><?php 
                $replaceHelper = new Formatted_Summary_Replace_Helper($summary->getArgs());
                echo preg_replace_callback( '/\{\{([A-Z_]+)\}\}/', array($replaceHelper, 'callback'), $summary->getFormat());
            ?></p>
            <?php
            }
            ?>

            Affects:
            <ul>
            <?php foreach ($ruleResults[$rule]->getGroups() as $group) { ?>
                <li><?php echo $group; ?></li>
            <?php } ?>
            </ul>
        </article>
    </section>
    <?php
    }
    ?>
    <aside>
        <h2>Score</h2>
        <?php $ruleGroups = $result->getRuleGroups(); ?>
        <h3><?php echo $ruleGroups['SPEED']->getScore(); ?></h3>
    </aside>
</body>
</html>
