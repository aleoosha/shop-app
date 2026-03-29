<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*

|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------

|
| Здесь мы говорим Pest, что Feature-тесты должны использовать базовый 
| класс TestCase от Laravel, чтобы у нас работал метод $this->get().

|
*/

uses(TestCase::class, RefreshDatabase::class)
    ->beforeEach(function () {      
        $this->setUpElasticsearch();
    })
    ->in('Feature');


/*
|--------------------------------------------------------------------------
| Expectations

|--------------------------------------------------------------------------
|
| Здесь можно добавить свои "ожидания" (например, expect($user)->toBeAdmin()).
|
*/
