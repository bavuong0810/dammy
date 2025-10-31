<?php

namespace App\Console\Commands;

use App\Constants\BaseConstants;
use App\Libraries\Helpers;
use App\Models\Story;
use App\Models\Chapter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ConvertCharacterChapter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'convertCharacterChapter:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * Execute the console command.
     *
     * @return int
     */

    public function handle()
    {
        $stories = Story::whereHas('user', function ($q) {
            $q->where('active', BaseConstants::ACTIVE);
        })
            ->where('status', BaseConstants::ACTIVE)
            ->get();
        foreach ($stories as $story) {
            $chapters = Chapter::where('story_id', $story->id)
                ->get();
            foreach ($chapters as $chapter) {
                $content = htmlspecialchars_decode($chapter->content);
                $content = Helpers::replaceCharacterChapter($content);
                Chapter::where('id', $chapter->id)
                    ->update(['content' => htmlspecialchars($content)]);
            }
        }
        return Command::SUCCESS;
    }
}
