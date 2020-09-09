<?php

namespace Spatie\Newsletter\Test;

use DrewM\MailChimp\MailChimp;
use Mockery;
use PHPUnit\Framework\TestCase;
use Spatie\Newsletter\Newsletter;
use Spatie\Newsletter\NewsletterListCollection;

class NewsletterTest extends TestCase
{
    /** @var Mockery\Mock */
    protected $mailChimpApi;

    /** @var \Spatie\Newsletter\Newsletter */
    protected $newsletter;

    public function setUp(): void
    {
        $this->mailChimpApi = Mockery::mock(MailChimp::class);

        $this->mailChimpApi->shouldReceive('success')->andReturn(true);

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

    public function tearDown(): void
    {
        parent::tearDown();

        if ($container = Mockery::getContainer()) {
            $this->addToAssertionCount($container->mockery_getExpectationCount());
        }

        Mockery::close();
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
    public function it_can_subscribe_someone_as_pending()
    {
        $email = 'freek@spatie.be';

        $url = 'lists/123/members';

        $this->mailChimpApi->shouldReceive('post')->withArgs([
            $url,
            [
                'email_address' => $email,
                'status' => 'pending',
                'email_type' => 'html',
            ],
        ]);

        $this->newsletter->subscribePending($email);
    }

    /** @test */
    public function it_can_subscribe_or_update_someone()
    {
        $email = 'freek@spatie.be';

        $url = 'lists/123/members';

        $subscriberHash = 'abc123';

        $this->mailChimpApi->shouldReceive('subscriberHash')
            ->once()
            ->withArgs([$email])
            ->andReturn($subscriberHash);

        $this->mailChimpApi->shouldReceive('put')->withArgs([
            "{$url}/{$subscriberHash}",
            [
                'email_address' => $email,
                'status' => 'subscribed',
                'email_type' => 'html',
            ],
        ]);

        $this->newsletter->subscribeOrUpdate($email);
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
    public function it_can_subscribe_or_update_someone_with_merge_fields()
    {
        $email = 'freek@spatie.be';

        $mergeFields = ['FNAME' => 'Freek'];

        $url = 'lists/123/members';

        $subscriberHash = 'abc123';

        $this->mailChimpApi->shouldReceive('subscriberHash')
            ->once()
            ->withArgs([$email])
            ->andReturn($subscriberHash);

        $this->mailChimpApi->shouldReceive('put')
            ->once()
            ->withArgs([
                "{$url}/{$subscriberHash}",
                [
                    'email_address' => $email,
                    'status' => 'subscribed',
                    'merge_fields' => $mergeFields,
                    'email_type' => 'html',
                ],
            ]);

        $this->newsletter->subscribeOrUpdate($email, $mergeFields);
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
    public function it_can_subscribe_or_update_someone_to_an_alternative_list()
    {
        $email = 'freek@spatie.be';

        $url = 'lists/456/members';

        $subscriberHash = 'abc123';

        $this->mailChimpApi->shouldReceive('subscriberHash')
            ->once()
            ->withArgs([$email])
            ->andReturn($subscriberHash);

        $this->mailChimpApi->shouldReceive('put')
            ->once()
            ->withArgs([
                "{$url}/{$subscriberHash}",
                [
                    'email_address' => $email,
                    'status' => 'subscribed',
                    'email_type' => 'html',
                ],
            ]);

        $this->newsletter->subscribeOrUpdate($email, [], 'list2');
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
    public function it_can_override_the_defaults_when_subscribing_or_updating_someone()
    {
        $email = 'freek@spatie.be';

        $url = 'lists/123/members';

        $subscriberHash = 'abc123';

        $this->mailChimpApi->shouldReceive('subscriberHash')
            ->once()
            ->withArgs([$email])
            ->andReturn($subscriberHash);

        $this->mailChimpApi->shouldReceive('put')
            ->once()
            ->withArgs([
                "{$url}/{$subscriberHash}",
                [
                    'email_address' => $email,
                    'status' => 'pending',
                    'email_type' => 'text',
                ],
            ]);

        $this->newsletter->subscribeOrUpdate($email, [], '', ['email_type' => 'text', 'status' => 'pending']);
    }

    /** @test */
    public function it_can_change_the_email_address_of_a_subscriber()
    {
        $email = 'freek@spatie.be';
        $newEmail = 'phreak@spatie.be';

        $url = 'lists/123/members';

        $subscriberHash = 'abc123';

        $this->mailChimpApi->shouldReceive('subscriberHash')
            ->once()
            ->withArgs([$email])
            ->andReturn($subscriberHash);

        $this->mailChimpApi
            ->shouldReceive('patch')
            ->once()
            ->withArgs([
                "{$url}/{$subscriberHash}",
                [
                    'email_address' => $newEmail,
                ],
            ]);

        $this->newsletter->updateEmailAddress($email, $newEmail);
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
            ->shouldReceive('patch')
            ->once()
            ->withArgs([
                "lists/123/members/{$subscriberHash}",
                [
                    'status' => 'unsubscribed',
                ],
            ]);

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
            ->shouldReceive('patch')
            ->once()
            ->withArgs([
                "lists/456/members/{$subscriberHash}",
                [
                    'status' => 'unsubscribed',
                ],
            ]);

        $this->newsletter->unsubscribe('freek@spatie.be', 'list2');
    }

    /** @test */
    public function it_can_delete_someone()
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

        $this->newsletter->delete('freek@spatie.be');
    }

    /** @test */
    public function it_can_delete_someone_from_a_specific_list()
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

        $this->newsletter->delete('freek@spatie.be', 'list2');
    }

    /** @test */
    public function it_can_delete_someone_permanently()
    {
        $email = 'freek@spatie.be';

        $subscriberHash = 'abc123';

        $this->mailChimpApi->shouldReceive('subscriberHash')
            ->once()
            ->withArgs([$email])
            ->andReturn($subscriberHash);

        $this->mailChimpApi
            ->shouldReceive('post')
            ->once()
            ->withArgs(["lists/123/members/{$subscriberHash}/actions/delete-permanent"]);

        $this->newsletter->deletePermanently('freek@spatie.be');
    }

    /** @test */
    public function it_can_delete_someone_permanently_from_a_specific_list()
    {
        $email = 'freek@spatie.be';

        $subscriberHash = 'abc123';

        $this->mailChimpApi->shouldReceive('subscriberHash')
            ->once()
            ->withArgs([$email])
            ->andReturn($subscriberHash);

        $this->mailChimpApi
            ->shouldReceive('post')
            ->once()
            ->withArgs(["lists/456/members/{$subscriberHash}/actions/delete-permanent"]);

        $this->newsletter->deletePermanently('freek@spatie.be', 'list2');
    }

    /** @test */
    public function it_exposes_the_api()
    {
        $api = $this->newsletter->getApi();

        $this->assertSame($this->mailChimpApi, $api);
    }

    /** @test */
    public function it_can_get_the_list_members()
    {
        $this->mailChimpApi
            ->shouldReceive('get')
            ->once()
            ->withArgs(['lists/123/members', []]);

        $this->newsletter->getMembers();
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
    public function it_can_get_the_member_activity()
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
            ->withArgs(["lists/123/members/{$subscriberHash}/activity"]);

        $this->newsletter->getMemberActivity($email);
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
    public function it_can_create_a_campaign()
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

    /** @test */
    public function it_can_get_member_tags()
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
            ->withArgs(["lists/123/members/{$subscriberHash}/tags"])
            ->andReturn('all-the-member-tags');

        $actual = $this->newsletter->getTags($email);

        $this->assertSame('all-the-member-tags', $actual);
    }

    /** @test */
    public function it_can_add_member_tags()
    {
        $email = 'freek@spatie.be';

        $subscriberHash = 'abc123';

        $this->mailChimpApi->shouldReceive('subscriberHash')
            ->once()
            ->withArgs([$email])
            ->andReturn($subscriberHash);

        $this->mailChimpApi
            ->shouldReceive('post')
            ->once()
            ->withArgs(["lists/123/members/{$subscriberHash}/tags", ['tags' => [['name' => 'tag-1', 'status' => 'active'], ['name' => 'tag-2', 'status' => 'active']]]])
            ->andReturn('the-post-response');

        $actual = $this->newsletter->addTags(['tag-1', 'tag-2'], $email);

        $this->assertSame('the-post-response', $actual);
    }

    /** @test */
    public function it_can_remove_member_tags()
    {
        $email = 'freek@spatie.be';

        $subscriberHash = 'abc123';

        $this->mailChimpApi->shouldReceive('subscriberHash')
            ->once()
            ->withArgs([$email])
            ->andReturn($subscriberHash);

        $this->mailChimpApi
            ->shouldReceive('post')
            ->once()
            ->withArgs(["lists/123/members/{$subscriberHash}/tags", ['tags' => [['name' => 'tag-1', 'status' => 'inactive'], ['name' => 'tag-2', 'status' => 'inactive']]]])
            ->andReturn('the-post-response');

        $actual = $this->newsletter->removeTags(['tag-1', 'tag-2'], $email);

        $this->assertSame('the-post-response', $actual);
    }
}
