<?php
require_once('Alimms/mns-autoloader.php');

use AliyunMNS\Client;
use AliyunMNS\Model\SubscriptionAttributes;
use AliyunMNS\Requests\PublishMessageRequest;
use AliyunMNS\Requests\CreateTopicRequest;
use AliyunMNS\Exception\MnsException;

class CreateTopicAndPublishMessage
{
    private $ip;
    private $port;
    private $accessId;
    private $accessKey;
    private $endPoint;
    private $client;

    public function __construct($accessId, $accessKey, $endPoint)
    {
        //$this->ip = $ip;
        //$this->port = strval($port);
        $this->accessId = $accessId;
        $this->accessKey = $accessKey;
        $this->endPoint = $endPoint;
    }


    public function set($messageBody,$topicName){

        //$topicName = "Xun-Notify";

        $this->client = new Client($this->endPoint, $this->accessId, $this->accessKey);
        $topic = $this->client->getTopicRef($topicName);
        $request = new PublishMessageRequest($messageBody);
        try
        {
            $res = $topic->publishMessage($request);
            return $res;
        }
        catch (MnsException $e)
        {
            return "PublishMessage Failed: " . $e;
        }
    }

    /*
    public function run()
    {
        $topicName = "CreateTopicAndPublishMessageExample";

        $this->client = new Client($this->endPoint, $this->accessId, $this->accessKey);

        // 1. create topic
        $request = new CreateTopicRequest($topicName);
        try
        {
            $res = $this->client->createTopic($request);
            echo "TopicCreated! \n";
        }
        catch (MnsException $e)
        {
            echo "CreateTopicFailed: " . $e;
            return;
        }
        $topic = $this->client->getTopicRef($topicName);

        // 2. subscribe
        $subscriptionName = "SubscriptionExample";
        $attributes = new SubscriptionAttributes($subscriptionName, 'http://' . $this->ip . ':' . $this->port);

        try
        {
            $topic->subscribe($attributes);
            echo "Subscribed! \n";
        }
        catch (MnsException $e)
        {
            echo "SubscribeFailed: " . $e;
            return;
        }

        // 3. send message
        $messageBody = "test";
        // as the messageBody will be automatically encoded
        // the MD5 is calculated for the encoded body
        $bodyMD5 = md5(base64_encode($messageBody));
        $request = new PublishMessageRequest($messageBody);
        try
        {
            $res = $topic->publishMessage($request);
            echo "MessagePublished! \n";
        }
        catch (MnsException $e)
        {
            echo "PublishMessage Failed: " . $e;
            return;
        }

        // 4. sleep for receiving notification
        sleep(20);

        // 5. unsubscribe
        try
        {
            $topic->unsubscribe($subscriptionName);
            echo "Unsubscribe Succeed! \n";
        }
        catch (MnsException $e)
        {
            echo "Unsubscribe Failed: " . $e;
            return;
        }

        // 6. delete topic
        try
        {
            $this->client->deleteTopic($topicName);
            echo "DeleteTopic Succeed! \n";
        }
        catch (MnsException $e)
        {
            echo "DeleteTopic Failed: " . $e;
            return;
        }
    }*/
}



?>
