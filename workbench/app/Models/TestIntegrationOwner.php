<?php

namespace Workbench\App\Models;

use EdStevo\LaravelIntegrationCredentials\Models\Concerns\MorphManyIntegrationCredentials;
use Illuminate\Database\Eloquent\Model;

class TestIntegrationOwner extends Model
{
    use MorphManyIntegrationCredentials;

    protected $table = 'test_integration_owners';
    protected $guarded = [];
}
