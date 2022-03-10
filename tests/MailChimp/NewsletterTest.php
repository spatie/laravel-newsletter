<?php

namespace Spatie\Newsletter\Test;

use DrewM\MailChimp\MailChimp;
use Mockery;
use Spatie\Newsletter\Newsletter;
use Spatie\Newsletter\NewsletterListCollection;

beforeEach(function () {
    $this->mailChimpApi = Mockery::mock(MailChimp::class);

    $this->mailChimpApi->shouldReceive('success')->andReturn(true);

    $newsletterLists = NewsletterListCollection::createFromConfig(
        [
            'lists' => [
                'list1' => [
                    'id' => 123,
                    'marketing_permissions' => [
                        'email' => 'abc123',
                    ],
                ],
                'list2' => [
                    'id' => 456,
                    'marketing_permissions' => [
                        'email' => 'abc456',
                    ],
                ],
            ],
            'defaultListName' => 'list1',
        ]
    );

    $this->newsletter = new Newsletter($this->mailChimpApi, $newsletterLists);
});
afterEach(function () {
    if ($container = Mockery::getContainer()) {
        $this->addToAssertionCount($container->mockery_getExpectationCount());
    }

    Mockery::close();
});

it('can subscribe someone', function () {
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
});

it('can subscribe someone as pending', function () {
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
});

it('can subscribe or update someone', function () {
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
});

it('can subscribe someone with merge fields', function () {
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
});

it('can subscribe or update someone with merge fields', function () {
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
});

it('can subscribe someone to an alternative list', function () {
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
});

it('can subscribe or update someone to an alternative list', function () {
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
});

it('can override the defaults when subscribing someone', function () {
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
});

it('can override the defaults when subscribing or updating someone', function () {
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
});

it('can change the email address of a subscriber', function () {
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
});

it('can unsubscribe someone', function () {
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
});

it('can unsubscribe someone from a specific list', function () {
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
});

it('can delete someone', function () {
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
});

it('can delete someone from a specific list', function () {
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
});

it('can delete someone permanently', function () {
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
});

it('can delete someone permanently from a specific list', function () {
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
});

it('exposes the api', function () {
    $api = $this->newsletter->getApi();

    $this->assertSame($this->mailChimpApi, $api);
});

it('can get the list members', function () {
    $this->mailChimpApi
        ->shouldReceive('get')
        ->once()
        ->withArgs(['lists/123/members', []]);

    $this->newsletter->getMembers();
});

it('can get the member', function () {
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
});

it('can get the member activity', function () {
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
});

it('can get the member from a specific list', function () {
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
});

it('can create a campaign', function () {
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
});

it('can get member tags', function () {
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
});

it('can add member tags', function () {
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
});

it('can remove member tags', function () {
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
});

it('can get the marketing permissions', function () {
    $members = [
        [
            'marketing_permissions' => [
                [
                    'marketing_permission_id' => 'abc123',
                    'text' => 'Email',
                    'enabled' => false,
                ],
            ],
        ],
    ];

    $this->mailChimpApi
        ->shouldReceive('get')
        ->once()
        ->withArgs(['lists/123/members'])
        ->andReturn(['members' => $members]);

    $response = $this->newsletter->getMarketingPermissions();

    $expectedResponse = [
        [
            'text' => 'Email',
            'id' => 'abc123',
        ],
    ];

    $this->assertSame($expectedResponse, $response);
});

it('can get the marketing permissions of a specific list', function () {
    $members = [
        [
            'marketing_permissions' => [
                [
                    'marketing_permission_id' => 'abc456',
                    'text' => 'Email',
                    'enabled' => false,
                ],
            ],
        ],
    ];

    $this->mailChimpApi
        ->shouldReceive('get')
        ->once()
        ->withArgs(['lists/456/members'])
        ->andReturn(['members' => $members]);

    $response = $this->newsletter->getMarketingPermissions('list2');

    $expectedResponse = [
        [
            'text' => 'Email',
            'id' => 'abc456',
        ],
    ];

    $this->assertSame($expectedResponse, $response);
});

it('can set a marketing permission', function () {
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
                'status' => 'subscribed',
                'email_type' => 'html',
                'marketing_permissions' => [
                    [
                        'marketing_permission_id' => 'abc123',
                        'enabled' => true,
                    ],
                ],
            ],
        ]);

    $this->newsletter->setMarketingPermission($email, 'email', true);
});

it('can set a marketing permission in a specific list', function () {
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
                'marketing_permissions' => [
                    [
                        'marketing_permission_id' => 'abc456',
                        'enabled' => true,
                    ],
                ],
            ],
        ]);

    $this->newsletter->setMarketingPermission($email, 'email', true, 'list2');
});
