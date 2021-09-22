<?php

use Illuminate\Database\Seeder;

class AdminsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('admins')->delete();
        $adminRecords=[
           [ 'id'=>1,'name'=>'admin','email'=>'admin@admin.com','password'=>'$2y$10$ZbZ.NgFoWxLBJBJu8pJqkeF0N/I.WBEqedJEfczmeNg5EUZY4/a2a'],
        ];
        // DB::table('admins')->insert($adminRecords);
        foreach($adminRecords as $key=>$record){
            \App\Admin::create($record);
        }
    }
}
