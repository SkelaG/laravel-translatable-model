<?php

namespace SkelaG\LaravelTranslatableModel\Migrations;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TranslatableMigration extends \Illuminate\Database\Migrations\Migration
{
    public function translates($main_table, $columns)
    {
        Schema::create(mb_substr($main_table, 0, -1).'_translations',
            function (Blueprint $table) use ($columns, $main_table) {
                $foreign = mb_substr($main_table, 0, -1).'_id';
                $table->id();
                $table->unsignedBigInteger($foreign)->index();
                $table->foreign($foreign)->references('id')->on($main_table)->onDelete('cascade');
                foreach ($columns as $column => $type) {
                    $table->{$type}($column);
                }
                $table->string('locale');
                $table->timestamps();
            });
    }

    public function dropTranslates($table)
    {
        Schema::dropIfExists(mb_substr($table, 0, -1).'_translations');
    }
}
