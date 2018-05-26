<?php

namespace App\Jobs;

use App\Models\Domain;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PublishPage implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $site;
    protected $module;
    protected $id;

    /**
     * Create a new job instance.
     *
     * @param $site
     * @param $module
     * @param $id
     */
    public function __construct($site, $module, $id)
    {
        $this->module = $module;
        $this->id = $id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->publish($this->site->default_theme);
        $this->publish($this->site->mobile_theme);
    }

    public function publish($theme)
    {
        try {
            $module = $this->module;
            $id = $this->id;

            //创建站点目录
            $path = public_path("/$theme->name");
            if (!is_dir($path)) {
                //创建模块目录
                @mkdir($path, 0755, true);
            }

            $path = public_path("/$theme->name/$module->path");
            if (!is_dir($path)) {
                //创建模块目录
                @mkdir($path, 0755, true);
            }

            $class = 'App\Http\\Controllers\\' . $module->name . 'Controller';
            $controller = new $class();
            $domain = new Domain($theme);
            $html = $controller->show($domain, $id)->__toString();
            //$html = str_replace('://localhost', '://' . $domain->site->domain, $html);

            $file_html = "$path/detail-$id.html";
            file_put_contents($file_html, $html);
        } catch (\Exception $exception) {
            \Log::debug('publish page: ' . $exception->getMessage());
        }
    }
}
