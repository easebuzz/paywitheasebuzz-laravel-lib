<?php
   
    namespace Easebuzz\PayWithEasebuzzLaravel;
    use Illuminate\Support\ServiceProvider;

    global $EASEBUZZ_PATH;
    $EASEBUZZ_PATH = realpath(dirname(__FILE__));
    
    class PayWithEasebuzzServiceProvider extends ServiceProvider {
        public function boot()
        {
            $this->loadRoutesFrom(__DIR__.'/routes/web.php');
            $this->loadViewsFrom(__DIR__.'/views', 'easebuzz');
        }
        public function register()
        {
        }
   }
?>