<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\Traits\InteractsWithElasticsearch;

/**
 * @method void refreshIndex()
 * @method void setupElasticsearch()
 * @mixin InteractsWithElasticsearch 
 */
abstract class TestCase extends BaseTestCase
{
    use InteractsWithElasticsearch;
}
