<?php

namespace Tests\Feature\Models;

use App\Models\Organization;
use App\Repositories\OrganizationRepository;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrganizationTest extends TestCase
{
    /*
    |--------------------------------------------------------------------------
    | Accessors & Mutators
    |--------------------------------------------------------------------------
    |
    |
    */

    /**
     *
     */
//    public function testLogoAttribute()
//    {
//        $filename = 'gelato-logo.jpg';
//        $logo = storage_path('fixtures/' . $filename);
//
//        $user = $this->seeder->seedUserWithOrganization();
//        $org = $user->organizations()->first();
//
//        $org->addMedia($logo)
//            ->preservingOriginal()
//            ->toMediaCollection('logo');
//
//
//        $this->assertContains('gelato-logo', $org->logo);
//    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    |
    |
    */

    /**
     *
     */
    public function testTeamRelationship()
    {
        $org = $this->seeder->seedOrganization();
        $this->assertEquals($org->team_id, $org->team->id);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    |
    |
    */

    /**
     *
     */
    public function testAccountFilter()
    {
        $team = $this->seeder->seedTeam();
        $org1 = $this->seeder->seedOrganization($team);
        $org2 = $this->seeder->seedOrganization($team);

        $account1 = $this->seeder->seedAccount($team);
        $account2 = $this->seeder->seedAccount($team);

        $or = new OrganizationRepository();
        $or->addAccount($org1, $account1);
        $or->addAccount($org2, $account2);

        $this->assertCount(2, $team->organizations);

        $results = (new Organization)->accountUuidFilter($account1->uuid)->get();
        $this->assertCount(1, $results);
        $this->assertEquals($org1->id, $results[0]->id);
    }

    /**
     *
     */
    public function testAccountMissingFilter()
    {
        $or = new OrganizationRepository();

        $team = $this->seeder->seedTeam();
        $org1 = $this->seeder->seedOrganization($team);
        $org2 = $this->seeder->seedOrganization($team);

        $account = $this->seeder->seedAccount($team);
        $or->addAccount($org1, $account);

        $results = (new Organization)->accountMissingUuidFilter($account->uuid)->get();
        $this->assertCount(1, $results);
        $this->assertEquals($org2->id, $results[0]->id);
    }

    /**
     *
     */
    public function testAssetFilter()
    {
        $team = $this->seeder->seedTeam();
        $org1 = $this->seeder->seedOrganization($team);
        $org2 = $this->seeder->seedOrganization($team);
        $asset = $this->seeder->seedAsset($team);

        $this->assertCount(0, (new Organization)->assetUuidFilter($asset->uuid)->get());

        $or = new OrganizationRepository();
        $or->addAsset($org1, $asset);

        $this->assertCount(1, (new Organization)->assetUuidFilter($asset->uuid)->get());
    }

    /**
     *
     */
    public function testAssetMissingFilter()
    {
        $team = $this->seeder->seedTeam();
        $org1 = $this->seeder->seedOrganization($team);
        $org2 = $this->seeder->seedOrganization($team);

        $or = new OrganizationRepository();
        $asset = $this->seeder->seedAsset($team);
        $or->addAsset($org1, $asset);

        $results = (new Organization)->assetMissingUuidFilter($asset->uuid)->get();
        $this->assertCount(1, $results);
        $this->assertEquals($org2->id, $results[0]->id);
    }
}
