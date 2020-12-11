<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMagicTokensTable extends Migration
{
    protected $table;

    public function __construct()
    {
        $this->table = config('magictoken.database.table_name', 'magic_tokens');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->string('token');
            $table->string('code');
            $table->string('receiver');
            $table->text('action');
            $table->unsignedTinyInteger('num_tries')->default(0);
            $table->unsignedTinyInteger('max_tries');
            $table->timestamps();

            $table->unique(['token', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->table);
    }
}
