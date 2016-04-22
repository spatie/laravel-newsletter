<?php

namespace Spatie\Newsletter\Test;

use DrewM\MailChimp\MailChimp;
use Mockery;
use PHPUnit_Framework_TestCase;
use Spatie\Newsletter\Newsletter;
use Spatie\Newsletter\NewsletterListCollection;

class NewsletterTest extends PHPUnit_Framework_TestCase
{
    /** @var Mockery\Mock */
    protected $mailChimpApi;

    /** @var \Spatie\Newsletter\Newsletter */
    protected $newsletter;

    public function setUp()
    {
        $this->mailChimpApi = Mockery::mock(MailChimp::class);

        $this->mailChimpApi->shouldReceive('getLastError')->andReturn(false);

        $newsletterLists = NewsletterListCollection::createFromConfig(
            [
                'lists' => [
                    'list1' => ['id' => 123],
                    'list2' => ['id' => 456],
                ],
                'defaultListName' => 'list1',
            ]

        );

        $this->newsletter = new Newsletter($this->mailChimpApi, $newsletterLists);
    }

    public function tearDown()
    {
        Mockery::close();

        parent::tearDown();
    }

    /** @test */
    public function it_can_subscribe_someone()
    {
        $email = 'freek@spatie.be';

        $url = 'lists/123/members';

        $this->mailChimpApi->shouldReceive('post')->withArgs([
            $url,
            [
                'email_address' => $email,
                'status' => 'subscribed',
                'email_type' => 'html',
            ],
        ]);

        $this->newsletter->subscribe($email);
    }

    /** @test */
    public function it_can_subscribe_someone_with_merge_fields()
    {
        $email = 'freek@spatie.be';

        $mergeFields = ['FNAME' => 'Freek'];

        $url = 'lists/123/members';

        $this->mailChimpApi->shouldReceive('post')
            ->once()
            ->withArgs([
                $url,
                [
                    'email_address' => $email,
                    'status' => 'subscribed',
                    'merge_fields' => $mergeFields,
                    'email_type' => 'html',
                ],
            ]);

        $this->newsletter->subscribe($email, $mergeFields);
    }

    /** @test */
    public function it_can_subscribe_someone_to_an_alternative_list()
    {
        $email = 'freek@spatie.be';

        $url = 'lists/456/members';

        $this->mailChimpApi->shouldReceive('post')
            ->once()
            ->withArgs([
                $url,
                [
                    'email_address' => $email,
                    'status' => 'subscribed',
                    'email_type' => 'html',
                ],
            ]);

        $this->newsletter->subscribe($email, [], 'list2');
    }

    /** @test */
    public function it_can_override_the_defaults_when_subscribing_someone()
    {
        $email = 'freek@spatie.be';

        $url = 'lists/123/members';

        $this->mailChimpApi->shouldReceive('post')
            ->once()
            ->withArgs([
                $url,
                [
                    'email_address' => $email,
                    'status' => 'pending',
                    'email_type' => 'text',
                ],
            ]);

        $this->newsletter->subscribe($email, [], '', ['email_type' => 'text', 'status' => 'pending']);
    }

    /** @test */
    public function it_can_unsubscribe_someone()
    {
        $email = 'freek@spatie.be';

        $subscriberHash = 'abc123';

        $this->mailChimpApi->shouldReceive('subscriberHash')
            ->once()
            ->withArgs([$email])
            ->andReturn($subscriberHash);

        $this->mailChimpApi
            ->shouldReceive('delete')
            ->once()
            ->withArgs(["lists/123/members/{$subscriberHash}"]);

        $this->newsletter->unsubscribe('freek@spatie.be');
    }

    /** @test */
    public function it_can_unsubscribe_someone_from_a_specific_list()
    {
        $email = 'freek@spatie.be';

        $subscriberHash = 'abc123';

        $this->mailChimpApi->shouldReceive('subscriberHash')
            ->once()
            ->withArgs([$email])
            ->andReturn($subscriberHash);

        $this->mailChimpApi
            ->shouldReceive('delete')
            ->once()
            ->withArgs(["lists/456/members/{$subscriberHash}"]);

        $this->newsletter->unsubscribe('freek@spatie.be', 'list2');
    }

    /** @test */
    public function it_exposes_the_api()
    {
        $api = $this->newsletter->getApi();

        $this->assertSame($this->mailChimpApi, $api);
    }

    /** @test */
    public function it_can_get_the_member()
    {
        $email = 'freek@spatie.be';

        $subscriberHash = 'abc123';

        $this->mailChimpApi->shouldReceive('subscriberHash')
            ->once()
            ->withArgs([$email])
            ->andReturn($subscriberHash);

        $this->mailChimpApi
            ->shouldReceive('get')
            ->once()
            ->withArgs(["lists/123/members/{$subscriberHash}"]);

        $this->newsletter->getMember($email);
    }

    /** @test */
    public function it_can_get_the_member_from_a_specific_list()
    {
        $email = 'freek@spatie.be';

        $subscriberHash = 'abc123';

        $this->mailChimpApi->shouldReceive('subscriberHash')
            ->once()
            ->withArgs([$email])
            ->andReturn($subscriberHash);

        $this->mailChimpApi
            ->shouldReceive('get')
            ->once()
            ->withArgs(["lists/456/members/{$subscriberHash}"]);

        $this->newsletter->getMember($email, 'list2');
    }

    /** @test */
    public function is_can_create_a_campaign()
    {
        $fromName = 'Spatie';
        $replyTo = 'info@spatie.be';
        $subject = 'This is a subject';
        $html = '<b>This is the content</b>';
        $listName = 'list1';
        $options = ['extraOption' => 'extraValue'];
        $contentOptions = ['plain text' => 'this is the plain text content'];

        $campaignId = 'newCampaignId';

        $this->mailChimpApi
            ->shouldReceive('post')
            ->once()
            ->withArgs(
                [
                    'campaigns',
                    [
                        'type' => 'regular',
                        'recipients' => [
                            'list_id' => 123,
                        ],
                        'settings' => [
                            'subject_line' => $subject,
                            'from_name' => $fromName,
                            'reply_to' => $replyTo,
                        ],
                        'extraOption' => 'extraValue',
                    ],
                ]
            )
            ->andReturn(['id' => $campaignId]);

        $this->mailChimpApi
            ->shouldReceive('put')
            ->once()
            ->withArgs([
                "campaigns/{$campaignId}/content",
                [
                    'html' => $html,
                    'plain text' => 'this is the plain text content',
                ],
            ]);

        $this->newsletter->createCampaign($fromName, $replyTo, $subject, $html, $listName, $options, $contentOptions);
    }
}
