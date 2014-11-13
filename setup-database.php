<?php
require 'bootstrap/start.php';

if (
    $printjobs_config['db']['username'] == null || $printjobs_config['db']['username'] == "" ||
    $printjobs_config['db']['password'] == null || $printjobs_config['db']['password'] == "" ||
    $printjobs_config['db']['database'] == null || $printjobs_config['db']['database'] == ""
) {
    throw new \Exception("You have configured your environment variables incorrectly");
}


$capsule->getConnection('default')->getSchemaBuilder()->create('jobs', function(\Illuminate\Database\Schema\Blueprint $table) {
    $table->increments('id');

    $table->integer('job_id')->unsigned();
    $table->string('printer_name');

    $table->boolean('stuck');
    $table->timestamps();
});
