<?php

require_once("./vendor/autoload.php");
use Httpful\Bootstrap;
use Httpful\Request;

/**
 * Set global vars
 *
 * @see Slack API Token
 */
$token = "xoxp-0000000000-0000000000000-0000000000000-00000000000";
$slack_channel_ids = array(
    "C00000",
    "C00001"
);

/**
 * Gets the messages from the api
 *
 * @param string $token
 * @param string $channel
 * @param $history
 * @return object
 */
function getMessages($token, $channel, $history) {
    if ($history) {
        $url = "https://slack.com/api/channels.history?token=" . $token . "&channel=" . $channel . "&count=1000&latest=" . $history;
    } else {
        $url = "https://slack.com/api/channels.history?token=" . $token . "&channel=" . $channel . "&count=1000";
    }

    Bootstrap::init();

    $response = Request::get($url)
        ->send();

    $responseDone = json_decode($response);

    return $responseDone;
}

foreach ($slack_channel_ids as $channel) {
    $more_data = true;
    $process = 0;
    $output = [];
    $history = false;

    while ($more_data) {
        echo "Process: " . $process . "\n";
        $process++;

        $slack = getMessages($token, $channel, $history);

        $output = array_merge($output, $slack->messages);

        $history = end($slack->messages)->ts;

        $more_data = $slack->has_more;
    }

    file_put_contents("./data/" . $channel . ".json", json_encode($output));
}
