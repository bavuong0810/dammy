<?php

namespace App\Jobs;

use App\Libraries\Helpers;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CompressionImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 1500;
    private $img_path;
    private $quality;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($img_path, $quality)
    {
        $this->img_path = $img_path;
        $this->quality = $quality;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //Tinify
//        \Tinify\setKey(env('TINIFY_API_KEY'));
//        $source = \Tinify\fromFile($this->imagePath);
//        $source->toFile($this->imagePath);
//
//        $source = \Tinify\fromFile($this->imagePath);
//        $source->toFile($this->imagePath);

        Helpers::modifyImageQuality($this->img_path, $this->quality);
    }
}
