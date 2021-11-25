<?php

namespace Spatie\Newsletter;

use Illuminate\Console\Command;
use Newsletter;

class NewsletterPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'newsletter:permissions {list?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get the marketing permissions of an audience';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $listName = $this->argument('list') ?? '';

        $permissions = Newsletter::getMarketingPermissions($listName);

        if (! $permissions) {
            $this->error('Permissions Failed');

            return Command::FAILURE;
        }

        $this->table(
            ['Marketing Permission', 'ID'],
            $permissions
        );

        return Command::SUCCESS;
    }
}
