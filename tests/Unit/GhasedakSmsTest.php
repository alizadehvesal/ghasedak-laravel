<?php

namespace Vestra\Ghasedak\Tests\Unit;

use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use Vestra\Ghasedak\Services\GhasedakSms;
use Vestra\Ghasedak\Tests\TestCase;

class GhasedakSmsTest extends TestCase
{
    public function test_it_sends_a_template_message(): void
    {
        Http::fake([
            'api.ghasedaksms.com/*' => Http::response([
                'result' => 'success',
                'messageids' => 11509774,
            ], 200),
        ]);

        $result = app(GhasedakSms::class)->sendUsingTemplate(
            '09122222222',
            'verification',
            ['123456'],
        );

        $this->assertTrue($result->successful());
        $this->assertSame(11509774, $result->firstMessageId());

        Http::assertSent(function ($request) {
            return $request->url() === 'http://api.ghasedaksms.com/v2/send/verify'
                && $request->header('apikey')[0] === 'test-api-key'
                && $request['type'] == 1
                && $request['receptor'] === '09122222222'
                && $request['template'] === 'login_code'
                && $request['param1'] === '123456';
        });
    }

    public function test_it_supports_three_parameters(): void
    {
        Http::fake(['*' => Http::response(['result' => 'success', 'messageids' => 1001])]);

        app(GhasedakSms::class)->sendTemplate(
            '09122222222',
            'order',
            ['A', 'B', 'C'],
        );

        Http::assertSent(function ($request) {
            return $request['param1'] === 'A'
                && $request['param2'] === 'B'
                && $request['param3'] === 'C';
        });
    }

    public function test_it_rejects_more_than_three_parameters(): void
    {
        $this->expectException(InvalidArgumentException::class);

        app(GhasedakSms::class)->sendTemplate('09122222222', 'test', ['1', '2', '3', '4']);
    }

    public function test_it_throws_for_api_error(): void
    {
        Http::fake([
            '*' => Http::response([
                'result' => 'error',
                'message' => 'invalid template',
            ], 400),
        ]);

        $this->expectExceptionMessage('invalid template');

        app(GhasedakSms::class)->sendTemplate('09122222222', 'wrong', ['123']);
    }
}
