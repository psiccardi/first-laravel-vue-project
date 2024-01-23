<?php

use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{

    public function mock()
    {
        $users = [
            [
                "role_id" => Role::ADMINISTRATOR,
                "first_name" => "Pietro Maria",
                "last_name" => "Siccardi",
                "email" => "pietro.siccardi@badgebox.com",
                "email_verified_at" => (new \DateTime)->format('Y-m-d H:i:s'),
                "password" => Hash::make("password"),
            ]
        ];

        foreach ($users as $user) {
            DB::table("users")->insert($user);
        }
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->bigInteger('role_id')->unsigned();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('api_token')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->foreign('role_id')->references('id')->on('roles');
        });

        $this->mock();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
