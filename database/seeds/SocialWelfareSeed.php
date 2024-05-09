<?php

use App\SocialWelfare;
use Illuminate\Database\Seeder;

class SocialWelfareSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $socialWelfare = SocialWelfare::create([
            'name' => 'Social Security System'
        ]);
        $socialWelfare = SocialWelfare::create([
            'name' => 'PhilHealth'
        ]);
        $socialWelfare = SocialWelfare::create([
            'name' => 'Pag-ibig'
        ]);
    }
}
