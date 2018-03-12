<?php

class Config
{
    const UPSOURCE_HOST = 'http://localhost:8080';
    const UPSOURCE_USER_ID = 'admin';
    const UPSOURCE_USER_PASSWORD = 'abcd';
    const UPSOURCE_TIME_ZONE = '+9 hour'; // if upsource timezone != execute server timezone

    const SLACK_WEBHOOK_URL = ''; // slack incomming webhook url
    const SLACK_WEBHOOK_MESSAGE_HEADER = '';

    const REVIEW_NOTIFY_CREATE_DATE_FROM = '-4 day'; // notify review check - created date
}
