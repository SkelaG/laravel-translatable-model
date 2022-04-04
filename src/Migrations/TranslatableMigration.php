<?php

namespace SkelaG\LaravelTranslatableModel\Migrations;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TranslatableMigration extends \Illuminate\Database\Migrations\Migration
{
    public function translates($main_table, $columns)
    {
        Schema::table(substr($main_table, -1), function (Blueprint $table) use ($columns, $main_table) {
            $table->id();
            $table->foreign(substr($main_table, -1).'_id')->references('id')->on($main_table)->onDelete('cascade');
            foreach ($columns as $column => $type) {
                $table->{$type}($column);
            }
            $table->string('locale');
            $table->timestamps();
        });
    }
}
