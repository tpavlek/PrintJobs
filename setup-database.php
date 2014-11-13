<?php
require 'bootstrap/start.php';

$capsule->getConnection('default')->getSchemaBuilder()->create('jobs', function(\Illuminate\Database\Schema\Blueprint $table) {
    $table->increments('id');

    $table->integer('job_id')->unsigned();
    $table->string('printer_name');

    $table->boolean('stuck');
    $table->timestamps();
});
