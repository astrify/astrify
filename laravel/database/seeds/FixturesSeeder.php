<?php

use App\Models\Principal;
use App\Models\Validator;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Team;
use App\Models\Account;
use App\Models\Asset;
use ZuluCrypto\StellarSdk\Keypair;
use Illuminate\Support\Carbon;
use App\Models\Organization;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Collection;
use App\Repositories\OrganizationRepository;

class FixturesSeeder extends Seeder
{
    protected $tcs;
    protected $or;

    public function __construct()
    {
        $this->tcs = new TestCaseSeeder();
        $this->or = new OrganizationRepository();
    }

    /**
     * Run the database seeds.
     *
     * @return void
     * @throws
     */
    public function run()
    {
        $user = User::where('email', 'user@example.com')->firstOrfail();
        $team = $user->currentTeam();
        $this->seedLottoGelato($team);
        // $this->seedTunnelTusk(collect([$user1, $user2]));
        // $this->seedKaleEaters(collect([$user2]));
    }


    /**
     * @param Team $team
     */
    public function seedLottoGelato(Team $team) {
        $account = factory(Account::class)->create([
            'team_id'           => $team->id,
            'alias'             => 'second-account',
            'public_key'        => 'GC4FWA3O7WTLYPTIOK73JN5JBBRAPJBKKZBLS27SIT5OFFIQNFSVFXVV',
        ]);

        $account = factory(Account::class)->create([
            'team_id'           => $team->id,
            'alias'             => 'coupon-issuer',
            'public_key'        => 'GCICTL4FMNDBOKYTCTETK25P5C3YV4Q6A6JZFTAFC7ISEENPPXXYLKXJ',
        ]);

        $org = factory(Organization::class)->create([
            'team_id'               => $team->id,
            'alias'                 => 'lotto-gelato',
            'published'             => true,
            'details'               => 'At Lotto Gelato we\'re serving up huge portions of our famous gelato flavors. Our scoops are so big when you see them you can\'t help but say "WOW! That\'s a Lotto\' Gelato!"',

            // begin sep-0001 global properties
            'federation_server'             => 'https://api.domain.com/federation',
            'auth_server'                   => 'https://api.domain.com/auth',
            'transfer_server'               => 'https://api.domain.com',
            'kyc_server'                    => 'https://api.domain.com',
            'web_auth_endpoint'             => 'https://api.domain.com',
            'signing_key_id'                => $account->id,
            'horizon_url'                   => 'https://horizon.domain.com',
            'uri_request_signing_key_id'    => $account->id,

            // begin sep-0001 documentation properties
            'name'                  => 'Lotto\' Gelato, Inc.',
            'dba'                   => 'Lotto\' Gelato',
            'url'                   => 'lottogelato.com',
            'description'           => 'Big scoops of Gelato sold at reasonable prices',
            'address'               => '{"suggestion": {"value": "2800 East Observatory Road, Los Angeles, California, United States of America"}}',
            'address_attestation'   => 'https://www.corp.att.com/newbill/images/standard-thumbnail.png',
            'phone'                 => '{"country":"US","countryCallingCode":"1","valid":true,"possible":true,"phone":"2133734253"}',
            'phone_attestation'     => 'https://www.corp.att.com/newbill/images/standard-thumbnail.png',
            'keybase'               => 'lotto-gelato',
            'twitter'               => 'lotto-gelato',
            'github'                => 'lotto-github',
            'email'                 => 'admin@lottogelato.com',
            'licensing_authority'   => 'Los Angeles County Heath Department',
            'license_type'          => 'Food and Beverage License',
            'license_number'        => 'FB-226894',
        ]);
        // set logo
        $logo = storage_path('fixtures/gelato-logo.jpg');
        $org->addMedia($logo)->preservingOriginal()->toMediaCollection('logo');

        $this->or->addAccount($org, $account);

        // set assets
        $asset1 = factory(Asset::class)->create([
            'team_id'                   => $team->id,
            'code'                      => 'FREESCOOP',
            'name'                      => 'Free Scoop',
            'account_id'                => $account->id,
            'status'                    => 'test',
            'display_decimals'           => 0,
            'desc'                      => 'This voucher entitles the user to one giant scoop of any of our delicious flavors. Now that\'s a Lotto Gelato!',
            'conditions'                => 'This offer expires on ' . Carbon::today()->toDateString(),
            'fixed_number'              => null,
            'max_number'                => null,
            'is_unlimited'              => true,
            'is_asset_anchored'         => true,
            'anchor_asset_type'         => 'good',
            'anchor_asset'              => 'cold hard (soft) gelato',
            'redemption_instructions'   => 'To redeem this voucher just bring your mobile phone with your voucher app installed into the store and mention you\'re redeeming a voucher.',
        ]);

         // set image
         $image = storage_path('fixtures/free-scoop.jpg');
         $asset1->addMedia($image)->preservingOriginal()->toMediaCollection('logo');

        $principal = factory(Principal::class)->create([
            'team_id'                   => $team->id
        ]);

        $validator = factory(Validator::class)->create([
            'team_id'                   => $team->id,
            'account_id'                => $account->id
        ]);
    }

    /**
     * @param Team $team
     * @throws Exception
     */
    public function seedTunnelTusk(Team $team) {

        $org = factory(Organization::class)->create([
            'name'                  => 'Tunnel Tusk, Ltd',
            'dba'                   => 'Tunnel Tusk',
            'url'                   => 'tunneltusk.com',
            'slug'                  => 'tunneltusk',
            'description'           => 'Secure VPN service provider',
            'physical'              => '{"suggestion": {"value": "611 Folsom Street in San Francisco, California, United States of America"}}',
            'phone'                 => '{"country":"US","countryCallingCode":"1","valid":true,"possible":true,"phone":"2133734253"}',
            'published'             => true,
            'details'               => 'The team at Tunnel Tusk is dedicated to providing fast and secure VPN service at a reasonable price. All our plans offer a guaranteed 641 megabit connection direct to our servers located at 611 Folsom Street in San Francisco, CA. Unlike our competition we take privacy very seriously. So seriously in fact that we don\'t keep any logs. Consequently we have no idea what\'s happening on our network!',
            'keybase'               => 'tunneltusk',
            'twitter'               => 'tunneltusk',
            'github'                => 'tunneltusk',
            'email'                 => 'notahoneypot@nsa.gov',
            'licensing_authority'   => 'FISA Amendments Act of 2008',
            'license_type'          => 'PRISM',
            'license_number'        => 'US-984XN',
            'phone_attestation'     => 'https://www.corp.att.com/newbill/images/standard-thumbnail.png',
            'address_attestation'   => 'https://www.corp.att.com/newbill/images/standard-thumbnail.png',
        ]);
        // set logo
        $logo = storage_path('fixtures/tusk-logo.jpg');
        $org->addMedia($logo)->preservingOriginal()->toMediaCollection('logo');

        // set organization users
        $users->each(function($user) use ($org){
            $org->users()->attach($user, ['id' => Uuid::uuid4()]);
        });

        $account = factory(Account::class)->create([
            'name'              => 'Tunnel Tusk',
            'organization_id'   => $org->id,
            'public_key'        => Keypair::newFromSeed(config('stellar.accounts.TUNNEL_TUSK.secret'))->getAccountId(),
        ]);

        // set account users
        $users->each(function($user) use ($account){
            $user->accounts()->attach($account, ['id' => Uuid::uuid4()]);
        });

        // set assets
        $asset1 = factory(Asset::class)->create([
            'name'                      => 'Narwhal Plan',
            'code'                      => 'NARWHAL',
            'published'                 => true,
            'account_id'                => $account->id,
            'status'                    => 'test',
            'display_decimals'           => 0,
            'desc'                      => 'Get one month of fast VPN access and secure your network traffic today!',
            'conditions'                => 'This voucher may be redeemed for one month of our vpn service. By redeeming this voucher you agree not to hold the National Security Agency doing business as Tunnel Tusk liable for any damages resulting from the use of this service. This offer expires on ' . Carbon::today()->toDateString() . ' and cannot be exchanged.',
            'fixed_number'              => null,
            'max_number'                => null,
            'is_unlimited'              => true,
            'is_asset_anchored'         => true,
            'anchor_asset_type'         => 'service',
            'anchor_asset'              => 'semi-private vpn service',
            'redemption_instructions'   => 'To redeem this voucher head over to your account settings page and select billing. Then select redeem voucher and follow the instructions from there.',
        ]);

        $asset2 = factory(Asset::class)->create([
            'name'                      => 'Big Woolly Plan',
            'code'                      => 'WOOLLY',
            'account_id'                => $account->id,
            'status'                    => 'test',
            'display_decimals'           => 0,
            'desc'                      => 'Get one year of fast VPN access and secure your network traffic today!',
            'conditions'                => 'This voucher may be redeemed for one year of our vpn service. By redeeming this voucher you agree not to hold the National Security Agency doing business as Tunnel Tusk liable for any damages resulting from the use of this service. This offer expires on ' . Carbon::today()->toDateString() . ' and cannot be exchanged.',
            'fixed_number'              => null,
            'max_number'                => null,
            'is_unlimited'              => true,
            'is_asset_anchored'         => true,
            'anchor_asset_type'         => 'service',
            'anchor_asset'              => 'semi-private vpn service',
            'redemption_instructions'   => 'To redeem this voucher head over to your account settings page and select billing. Then select redeem voucher and follow the instructions from there.',
        ]);

        // set image
        $image = storage_path('fixtures/free-scoop.jpg');
        $asset1->addMedia($image)->preservingOriginal()->toMediaCollection('image');
    }

    /**
     * @param Collection $users
     * @throws Exception
     */
    public function seedKaleEaters(Collection $users) {
        $org = factory(Organization::class)->create([
            'name'                  => 'Kale Eaters Of America - Pacific Heights Chapter',
            'dba'                   => 'KEOA-PHC',
            'url'                   => 'phc.keoa.org',
            'slug'                  => 'phc-keoa',
            'description'           => 'A group of like minded individuals who are "Krazy about Kale"',
            'physical_address'      => '{"suggestion": {"value": "611 Folsom Street in San Francisco, California, United States of America"}}',
            'phone_number'          => '{"country":"US","countryCallingCode":"1","valid":true,"possible":true,"phone":"2133734253"}',
            'published'             => false,
            'details'               => 'They say you are born twice. Once at birth and a second time when you become a member of your local KEOA chapter.',
            'keybase'               => 'phc-keoa',
            'twitter'               => 'phc-keoa',
            'github'                => 'phc-keoa',
            'official_email'        => 'phc@keoa.org'
        ]);

        // set users
        $users->each(function($user) use ($org){
            $org->users()->attach($user, ['id' => Uuid::uuid4()]);
        });

        $account = factory(Account::class)->create([
            'name'              => 'Kale Eaters Of America',
            'organization_id'   => $org->id,
            'public_key'        => Keypair::newFromSeed(config('stellar.accounts.KALE_EATERS.secret'))->getAccountId(),
        ]);

        // set account users
        $users->each(function($user) use ($account){
            $user->accounts()->attach($account, ['id' => Uuid::uuid4()]);
        });

        // set assets
        $asset1 = factory(Asset::class)->create([
            'name'                      => 'Semi Annual Open House',
            'code'                      => 'OPEN_HOUSE',
            'account_id'                => $account->id,
            'status'                    => 'test',
            'display_decimals'           => 0,
            'desc'                      => 'Open house for non-members to meet fellow kale enthusiasts, try new recipes, and learn more about our beloved Brassica Oleracea.',
            'conditions'                => 'Reserve your spot now. Demand is high and this event sells out every year. This year is expected to be our biggest event ever as we\'re combining our event with the Cow Hollow chapter.',
            'fixed_number'              => null,
            'max_number'                => null,
            'is_unlimited'              => true,
            'is_asset_anchored'         => true,
            'anchor_asset_type'         => 'service',
            'anchor_asset'              => 'semi-private vpn service',
            'redemption_instructions'   => 'This voucher may be redeemed for entry for one person into our Semi Annual Open House being held on ' . Carbon::today()->addMonths(6)->toDateString(),
        ]);

        // set image
        $image = storage_path('fixtures/free-scoop.jpg');
        $asset1->addMedia($image)->preservingOriginal()->toMediaCollection('image');
    }
}
