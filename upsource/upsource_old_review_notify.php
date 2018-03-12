<?php
if (!file_exists('config.php')) {
    echo 'Not exists "config.php"' . PHP_EOL;
    echo 'Copy file "config.sample.php" to "config.php" and setup config info' . PHP_EOL;
    exit(1);
}

require 'config.php';

$not_closed_reviews = getNotClosedReviews();
$notify_users = filterNotifyUsersAndBuildMessage($not_closed_reviews);

if (count($notify_users) === 0) {
    echo 'Good! No notify Reviews!!' . PHP_EOL;
    exit;
}

$slack_messages = buildSlackMessages($notify_users);

sendSlackIncommingWebhook($slack_messages);


function getNotClosedReviews()
{
    $param = [
        'query' => 'state: open',
        'limit' => 100,
    ];

    $reviews = getDataFromUpsource('getReviews', $param);
    $not_closed_reviews = [];
    foreach ($reviews['result']['reviews'] as $r) {
        if ($r['isReadyToClose']) {
            continue;
        }
        $r['createdAt'] = affectTimeZone($r['createdAt']);
        $r['updatedAt'] = affectTimeZone($r['updatedAt']);
        $not_closed_reviews[] = $r;
    }
    // print_r($not_closed_reviews);

    return $not_closed_reviews;
}

function affectTimeZone(string $upsource_timestamp)
{
    if (Config::UPSOURCE_TIME_ZONE !== '') {
        $timestamp = strtotime(Config::UPSOURCE_TIME_ZONE, $upsource_timestamp / 1000);
    } else {
        $timestamp = $upsource_timestamp / 1000;
    }

    return strtotime(date('Y-m-d', $timestamp));
}

function filterNotifyUsersAndBuildMessage(array $not_closed_reviews)
{
    $notify = [];
    $validate_time = strtotime(date('Y-m-d', strtotime(Config::REVIEW_NOTIFY_CREATE_DATE_FROM)));
    foreach ($not_closed_reviews as $r) {
        if ($r['createdAt'] >= $validate_time) {
            continue;
        }
        $upsource_link = Config::UPSOURCE_HOST . '/' . $r['reviewId']['projectId'] . '/review/' . $r['reviewId']['reviewId'];
        $review_text = '<' . $upsource_link . '|' . $r['title'] . ' (' . $r['reviewId']['reviewId'] . ')>';
        foreach ($r['participants'] as $u) {
            if ((int)$u['role'] !== 2 || (int)$u['state'] >= 3) {
                continue;
            }
            $notify[$u['userId']][] = $review_text;
        }
    }

    return $notify;
}

function buildSlackMessages(array $notify_users)
{
    $users = getUsersByNotifyUsers($notify_users);
    $messages = [];
    foreach ($notify_users as $user_id => $review_text) {
        $u = $users[$user_id];
        $messages[] = [
            'title' => '@' . $u['login'] . ' (' . $u['name'] . ')',
            'value' => implode(PHP_EOL, $review_text)
        ];
    }

    return $messages;
}

function getUsersByNotifyUsers(array $notify_users)
{
    $param = [
        'ids' => array_values(array_filter(array_keys($notify_users))),
    ];
    $user_request = getDataFromUpsource('getUserInfo', $param);
    $users = [];
    foreach ($user_request['result']['infos'] as $u) {
        $users[$u['userId']] = $u;
    }

    return $users;
}

function getDataFromUpsource(string $method, array $data)
{
    $user = Config::UPSOURCE_USER_ID . ':' . Config::UPSOURCE_USER_PASSWORD;
    $host = Config::UPSOURCE_HOST;
    $url = '/~rpc/' . $method;

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $host . $url);

    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5); // Read timeout
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  //to suppress the curl output

    // header
    $headers = [
        'Authorization: Basic ' . base64_encode($user),
    ];
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    // body
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $result = curl_exec($ch);
    curl_close($ch);

    return json_decode($result, true);
}

function sendSlackIncommingWebhook(array $messages)
{
    $webhook_url = Config::SLACK_WEBHOOK_URL;
    $payload = [
        'attachments' => [
            [
                'fallback' => Config::SLACK_WEBHOOK_MESSAGE_HEADER,
                'pretext' => Config::SLACK_WEBHOOK_MESSAGE_HEADER,
                'color' => 'good',
                'fields' => $messages,
            ],
        ],
    ];
    $payload = json_encode($payload);

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $webhook_url);

    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5); // Read timeout

    // body
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

    curl_exec($ch);
    curl_close($ch);
}
