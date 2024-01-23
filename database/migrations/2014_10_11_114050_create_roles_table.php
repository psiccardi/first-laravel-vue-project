<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function mock()
    {
        $roles = [
            [
                'name' => 'administrator',
            ],
            [
                'name' => 'operator'
            ]
        ];

        foreach ($roles as $role) {
            DB::table("roles")->insert($role);
        }
    }
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            // $table->timestamps();
        });

        $this->mock();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
