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

        $newsletterLists = NewsletterListCollection::makeForConfig(
            [
                'lists' =>
                    [
                        'list1' => ['id' => 123],
                    ],
                'defaultListName' => 'list1'
            ]

        );

        $this->newsletter = new Newsletter($this->mailChimpApi, $newsletterLists);
    }

    /** @test */
    public function it_can_subscribe_someone()
    {
        $email = 'freek@spatie.be';

        $url = "list/123/members";

        $this->mailChimpApi->shouldReceive('post')->withArgs([
            $url,
            [
                'email_address' => $email,
                'status' => 'subscribed',
                'merge_fields' => [],
                'email_type' => 'html'
            ]
        ]);

        $this->newsletter->subscribe($email);
    }

    /** @test */
    public function it_can_subscribe_someone_with_merge_fields()
    {
        $email = 'freek@spatie.be';

        $mergeFields = ['FNAME' => 'Freek'];

        $url = "list/123/members";

        $this->mailChimpApi->shouldReceive('post')->withArgs([
            $url,
            [
                'email_address' => $email,
                'status' => 'subscribed',
                'merge_fields' => $mergeFields,
                'email_type' => 'html'
            ]
        ]);

        $this->newsletter->subscribe($email, $mergeFields);
    }

    /** @test */
    public function it_can_override_the_defaults_when_subscribing_someone()
    {
        $email = 'freek@spatie.be';

        $url = "list/123/members";

        $this->mailChimpApi->shouldReceive('post')->withArgs([
            $url,
            [
                'email_address' => $email,
                'status' => 'pending',
                'merge_fields' => [],
                'email_type' => 'text'
            ]
        ]);

        $this->newsletter->subscribe($email, [], '', ['email_type' => 'text', 'status' => 'pending']);
    }

}
