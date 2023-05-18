<?php

namespace App\Http\Controllers\Line;

use Illuminate\Http\Request;
use LINE\LINEBot;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;

class Messaging
{
    private LINEBot $bot;

    public function __construct()
    {
        $httpClient = new CurlHTTPClient(env('LINE_BOT_CHANNEL_ACCESS_TOKEN'));
        $this->bot = new LINEBot($httpClient, ['channelSecret' => env('LINE_CHANNEL_SECRET')]);
    }

    public function __invoke(Request $request): \Illuminate\Http\JsonResponse
    {
        $events = $request->input('events');

        foreach ($events as $event) {
            if ($event['type'] === 'message' && $event['message']['type'] === 'text') {
                $this->handleTextMessage($event);
            }
        }

        return response()->json(['success' => true]);
    }

    private function handleTextMessage($event): void
    {
        $replyToken = $event['replyToken'];
        $text = $event['message']['text'];

        if ($text === 'Menu') {
            $this->sendMenuMessage($replyToken);
        } else {
            $response = $this->processMessage($text);
            $message = new TextMessageBuilder($response);
            $this->bot->replyMessage($replyToken, $message);
        }
    }

    private function sendMenuMessage($replyToken): void
    {
        $buttonTemplate = new ButtonTemplateBuilder(
            'Title',
            'Describe',
            'https://example.com/menu_image.jpg',
            [
                new UriTemplateActionBuilder('option 1', 'https://example.com/option1'),
                new UriTemplateActionBuilder('option 2', 'https://example.com/option2'),
                new UriTemplateActionBuilder('option 3', 'https://example.com/option3'),
            ]
        );

        $templateMessage = new TemplateMessageBuilder('Menu', $buttonTemplate);

        $this->bot->replyMessage($replyToken, $templateMessage);
    }

    private function processMessage($message): string
    {
        return 'Talk：' . $message;
    }
}
