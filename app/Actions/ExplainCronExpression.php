<?php

 namespace App\Actions;

 use Cron\CronExpression;
 use Illuminate\Support\HtmlString;
 use Lorisleiva\Actions\Concerns\AsAction;
 use Orisai\CronExpressionExplainer\DefaultCronExpressionExplainer;
 use Orisai\CronExpressionExplainer\Exception\UnsupportedExpression;

 class ExplainCronExpression
 {
     use AsAction;

     public function handle(?string $expression)
     {
         if (blank($expression)) {
             return 'No cron expression provided.';
         }

         try {
             $cron = new CronExpression($expression);
         } catch (\InvalidArgumentException $e) {
             return new HtmlString('The cron expression is invalid.');
         }

         try {
             $explainer = new DefaultCronExpressionExplainer();
         } catch (UnsupportedExpression $e) {


             return new HtmlString('The cron expression is not supported.');
         }

         return $explainer->explain($expression);
     }
 }