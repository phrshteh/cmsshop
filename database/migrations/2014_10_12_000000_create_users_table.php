<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->unique();
            $table->string('password');
            $table->timestamps();
        });

        $user = \App\Models\User::create(
            [
                'name'     => 'admin',
                'username' => 'admin',
                'password' => \Illuminate\Support\Facades\Hash::make('admincms'),
            ]
        );

        $role = \Spatie\Permission\Models\Role::findByName('admin');
        $user->assignRole($role);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
