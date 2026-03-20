<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
 public function up()
{
    Schema::table('users', function (Blueprint $table) {
        // This adds the email column after the 'name' column
        $table->string('email')->unique()->after('name')->nullable();
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        // This removes the column if you ever need to rollback
        $table->dropColumn('email');
    });
}
};
