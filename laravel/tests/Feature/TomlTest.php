<?php

namespace Tests\Feature;

use App\Repositories\OrganizationRepository;
use App\Services\TomlService;
use Tests\TestCase;

class TomlTest extends TestCase
{
    public function testToml()
    {
        $team = $this->seeder->seedTeam();
        $org = $this->seeder->seedOrganization($team);

        $or = new OrganizationRepository();

        $acc1 = $this->seeder->seedAccount($team);
        $acc2 = $this->seeder->seedAccount($team);
        $or->addAccount($org, $acc1);
        $or->addAccount($org, $acc2);

        $ass1 = $this->seeder->seedAsset($team);
        $ass2 = $this->seeder->seedAsset($team);
        $or->addAsset($org, $ass1);
        $or->addAsset($org, $ass2);

        $p1 = $this->seeder->seedPrincipal($team);
        $p2 = $this->seeder->seedPrincipal($team);
        $or->addPrincipal($org, $p1);
        $or->addPrincipal($org, $p2);

        $v1 = $this->seeder->seedValidator($team);
        $v2 = $this->seeder->seedValidator($team);
        $or->addValidator($org, $v1);
        $or->addValidator($org, $v2);

        $ts = new TomlService($org);

        dd($ts->generateToml()->getTomlString());
    }
}
