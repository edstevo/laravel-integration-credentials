<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('integration_credentials', function (Blueprint $table) {
            $table->id();

            $table->string('provider');
            $table->string('key');
            $table->text('value');
            $table->timestamp('expires_at')->nullable();

            $table->morphs('integrable');

            $table->timestamps();

            $table->unique([
                'integrable_type',
                'integrable_id',
                'provider',
                'key',
            ]);
        });
    }
};
