<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Deal;
use App\Models\Lead;
use App\Models\Note;
use App\Models\Pipeline;
use App\Models\Stage;
use App\Models\Task;
use App\Models\User;
use App\Support\PermissionCatalog;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Liberu\Foundation\Organizations\Models\Team;

/**
 * Seeds the "Nile Properties" demo dataset: a fictional Cairo real-estate
 * agency with a realistic sales pipeline. Deterministic — no faker — so
 * screenshots and sales demos stay consistent across resets.
 */
class NilePropertiesDemoSeeder extends Seeder
{
    private function demoEmail(): string
    {
        return (string) config('demo.user.email', 'demo@orcatech.test');
    }

    private function demoPassword(): string
    {
        return (string) config('demo.user.password', 'OrcaTech-Demo-2026!');
    }


    public function run(): void
    {
        // Demo data must not trigger NewLead / DealClosed notifications.
        $dispatcher = \Illuminate\Database\Eloquent\Model::getEventDispatcher();
        \Illuminate\Database\Eloquent\Model::unsetEventDispatcher();

        try {
            $this->runWithoutEvents();
        } finally {
            \Illuminate\Database\Eloquent\Model::setEventDispatcher($dispatcher);
        }
    }

    private function runWithoutEvents(): void
    {
        PermissionCatalog::sync();

        $team = $this->seedTeam();
        $staff = $this->seedUsers($team);
        [$pipeline, $stages] = $this->seedPipeline($team);
        $companies = $this->seedCompanies($team);
        $contacts = $this->seedContacts($team, $companies);
        $leads = $this->seedLeads($team, $contacts, $staff);
        $deals = $this->seedDeals($team, $contacts, $staff, $pipeline, $stages);
        $this->seedTasks($team, $contacts, $deals, $staff);
        $this->seedActivities($team, $contacts, $staff);
        $this->seedNotes($contacts);

        $this->command?->info('Nile Properties demo data seeded.');
    }

    private function seedTeam(): Team
    {
        Team::query()->where('name', 'Default')->delete();

        $admin = User::query()->updateOrCreate(
            ['email' => $this->demoEmail()],
            [
                'name' => 'Omar El-Sayed',
                'password' => Hash::make($this->demoPassword()),
                'email_verified_at' => now(),
                'locale' => 'en',
            ],
        );

        return Team::query()->updateOrCreate(
            ['name' => 'Nile Properties'],
            ['personal_team' => false, 'user_id' => $admin->id],
        );
    }

    /**
     * @return array<string, User>
     */
    private function seedUsers(Team $team): array
    {
        $people = [
            $this->demoEmail() => ['Omar El-Sayed', 'manager'],
            'salma@nileproperties.demo' => ['Salma Adel', 'manager'],
            'yasmine@nileproperties.demo' => ['Yasmine Fouad', 'sales_rep'],
            'karim@nileproperties.demo' => ['Karim Mansour', 'sales_rep'],
            'tarek@nileproperties.demo' => ['Tarek Naguib', 'sales_rep'],
        ];

        $users = [];

        foreach ($people as $email => [$name, $role]) {
            /** @var User $user */
            $user = User::query()->updateOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => Hash::make($this->demoPassword()),
                    'email_verified_at' => now(),
                    'locale' => 'en',
                    'current_team_id' => $team->id,
                ],
            );

            setPermissionsTeamId($team->id);
            $user->assignRole($role);
            $user->teams()->syncWithoutDetaching([$team->id]);

            $users[$name] = $user;
        }

        return $users;
    }

    /**
     * @return array{0: Pipeline, 1: \Illuminate\Support\Collection<int, Stage>}
     */
    private function seedPipeline(Team $team): array
    {
        $pipeline = Pipeline::query()->updateOrCreate(
            ['team_id' => $team->id, 'name' => 'Property Sales'],
            ['description' => 'Residential sales pipeline for Nile Properties', 'is_active' => true],
        );

        $stageNames = ['New Inquiry', 'Viewing Scheduled', 'Negotiation', 'Contract', 'Closing'];

        $stages = collect();

        foreach (array_values($stageNames) as $index => $name) {
            $stages->push(Stage::query()->updateOrCreate(
                ['pipeline_id' => $pipeline->id, 'name' => $name],
                ['order' => $index + 1, 'team_id' => $team->id],
            ));
        }

        return [$pipeline, $stages];
    }

    /**
     * @return \Illuminate\Support\Collection<int, Company>
     */
    private function seedCompanies(Team $team): \Illuminate\Support\Collection
    {
        $data = [
            ['Hassan Real Estate Investments', 'Real Estate', 'Cairo', 'hassanrealestate.com'],
            ['Zamalek Property Group', 'Real Estate', 'Cairo', 'zamalekproperty.com'],
            ['New Cairo Developments', 'Construction', 'New Cairo', 'newcairodev.com'],
            ['Alexandria Coastal Estates', 'Real Estate', 'Alexandria', 'coastalestates.com'],
            ['Giza Heights Co.', 'Construction', 'Giza', 'gizaheights.com'],
            ['Maadi Living', 'Property Management', 'Maadi', 'maadiliving.com'],
            ['Sinai Resorts & Hotels', 'Hospitality', 'Sharm El-Sheikh', 'sinairesorts.com'],
            ['Nasr City Housing', 'Real Estate', 'Cairo', 'nasrcityhousing.com'],
            ['Sheikh Zayed Properties', 'Real Estate', 'Sheikh Zayed', 'zayedproperties.com'],
            ['Red Sea Villas', 'Real Estate', 'Hurghada', 'redseavillas.com'],
            ['October Gardens Realty', 'Real Estate', '6th of October', 'octobergardens.com'],
            ['Downtown Cairo Heritage', 'Real Estate', 'Cairo', 'downtownheritage.com'],
            ['Smart Village Offices', 'Commercial Real Estate', '6th of October', 'smartvillageoffices.com'],
            ['El Rehab Housing', 'Real Estate', 'El Rehab', 'rehabhousing.com'],
            ['North Coast Escapes', 'Hospitality', 'Sidi Abdel Rahman', 'northcoastescapes.com'],
            ['HelioPark Residences', 'Real Estate', 'Heliopolis', 'heliopark.com'],
            ['Aswan Riverside Homes', 'Real Estate', 'Aswan', 'riversidehomes.com'],
            ['Luxor Valley Properties', 'Real Estate', 'Luxor', 'luxorvalley.com'],
            ['Port Said Maritime Homes', 'Real Estate', 'Port Said', 'maritimehomes.com'],
            ['Fayoum Oasis Estates', 'Real Estate', 'Fayoum', 'oasisestates.com'],
        ];

        return collect($data)->map(function (array $row, int $i) use ($team): Company {
            [$name, $industry, $city, $domain] = $row;

            /** @var Company $company */
            $company = Company::query()->updateOrCreate(
                ['team_id' => $team->id, 'name' => $name],
                [
                    'industry' => $industry,
                    'city' => $city,
                    'location' => "{$city}, Egypt",
                    'domain' => $domain,
                    'website' => "https://{$domain}",
                    'size' => ((($i % 4) + 1) * 25).' employees',
                    'annual_revenue' => (($i % 8) + 2) * 5_000_000,
                    'phone_number' => sprintf('+20 2 %04d %04d', 2500 + $i, 1000 + $i * 7),
                    'description' => "{$name} is a {$industry} company based in {$city}, Egypt.",
                ],
            );

            return $company;
        });
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Company>  $companies
     * @return \Illuminate\Support\Collection<int, Contact>
     */
    private function seedContacts(Team $team, \Illuminate\Support\Collection $companies): \Illuminate\Support\Collection
    {
        $firstNames = [
            'Ahmed', 'Mohamed', 'Fatima', 'Layla', 'Hassan', 'Nour', 'Amira', 'Khaled',
            'Mona', 'Hesham', 'Rana', 'Samer', 'Dina', 'Waleed', 'Farida', 'Youssef',
            'Heba', 'Mostafa', 'Aya', 'Sherif', 'Nadia', 'Emad', 'Rania', 'Bassem',
            'Zeinab', 'Adham', 'Mariam', 'Tamer', 'Salwa', 'Hazem', 'Injy', 'Magued',
            'Ola', 'Ramzi', 'Yasmin', 'Selim', 'Hoda', 'Ali', 'Mayssa', 'Zaki',
            'Reem', 'Osama', 'Sawsan', 'Bahaa', 'Ghada', 'Fouad', 'Nagwa', 'Raouf',
        ];

        $lastNames = [
            'Abdelrahman', 'El-Masry', 'Shafik', 'Kamel', 'Darwish', 'Halim', 'Sabry',
            'Farouk', 'Aziz', 'Rashad', 'Zaki', 'Hamdi', 'Lotfy', 'Nabil', 'Saad',
            'Tawfik', 'Younis', 'Barakat', 'Saleh', 'Gaber', 'Habib', 'Idris',
            'Metwally', 'Qassem', 'Shaaban', 'Zaher', 'Ezzat', 'Fahmy', 'Ghanem',
            'Helmy', 'Kotb', 'Mahmoud', 'Nassef', 'Othman', 'Reda', 'Selim',
        ];

        $statuses = ['active', 'prospect', 'lead', 'inactive'];
        $sources = ['website', 'referral', 'social_media', 'direct'];

        return collect(range(1, 48))->map(function (int $i) use ($team, $companies, $firstNames, $lastNames, $statuses, $sources): Contact {
            $first = $firstNames[$i % count($firstNames)];
            $last = $lastNames[($i * 7 + 3) % count($lastNames)];
            $company = $companies[$i % $companies->count()];

            /** @var Contact $contact */
            $contact = Contact::query()->create([
                'team_id' => $team->id,
                'name' => $first,
                'last_name' => $last,
                'email' => strtolower("{$first}.{$last}@{$company->domain}"),
                'phone_number' => sprintf('+20 10 %04d %04d', 2000 + $i, 3000 + $i * 13),
                'status' => $statuses[$i % count($statuses)],
                'source' => $sources[$i % count($sources)],
                'industry' => $company->industry,
                'lifecycle_stage' => ['lead', 'marketing_qualified_lead', 'sales_qualified_lead', 'customer'][$i % 4],
                'company_id' => $company->id,
                'custom_fields' => null,
            ]);

            $contact->forceFill(['created_at' => CarbonImmutable::now()->subDays(90 - $i)->setTime(9 + $i % 8, ($i * 11) % 60)]);
            $contact->saveQuietly();

            return $contact;
        });
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Contact>  $contacts
     * @return \Illuminate\Support\Collection<int, Lead>
     */
    private function seedLeads(Team $team, \Illuminate\Support\Collection $contacts, array $staff): \Illuminate\Support\Collection
    {
        $statuses = array_fill(0, 40, 'new') + array_fill(40, 30, 'contacted') + array_fill(70, 20, 'qualified') + array_fill(90, 14, 'lost');
        $sources = ['website', 'referral', 'social_media', 'direct'];
        $stages = ['lead', 'marketing_qualified_lead', 'sales_qualified_lead'];
        $repNames = array_slice(array_keys($staff), 1);
        $propertyTypes = ['Apartment', 'Villa', 'Townhouse', 'Duplex', 'Penthouse', 'Chalet', 'Studio', 'Office Unit'];

        return collect(range(0, 103))->map(function (int $i) use ($team, $contacts, $staff, $statuses, $sources, $stages, $repNames, $propertyTypes): Lead {
            $status = $statuses[$i] ?? 'new';
            $contact = $contacts[$i % $contacts->count()];
            $rep = $staff[$repNames[$i % count($repNames)]];
            $type = $propertyTypes[$i % count($propertyTypes)];
            $districts = ['New Cairo', 'Sheikh Zayed', 'Maadi', 'Nasr City', 'Heliopolis', 'Zamalek', 'El Rehab', 'North Coast'];

            /** @var Lead $lead */
            $lead = Lead::query()->create([
                'team_id' => $team->id,
                'status' => $status,
                'source' => $sources[$i % count($sources)],
                'potential_value' => (500_000 + (($i * 137_000) % 4_500_000)),
                'expected_close_date' => CarbonImmutable::now()->addDays(($i % 45) + 5)->toDateString(),
                'lifecycle_stage' => $stages[$i % count($stages)],
                'score' => min(99, 35 + (($i * 13) % 60)),
                'contact_id' => $contact->id,
                'user_id' => $rep->id,
            ]);

            $createdAt = CarbonImmutable::now()->subDays(85 - ($i % 80))->setTime(8 + $i % 10, ($i * 17) % 60);
            $lead->forceFill(['created_at' => $createdAt, 'updated_at' => $createdAt])->saveQuietly();

            return $lead;
        });
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Contact>  $contacts
     * @return \Illuminate\Support\Collection<int, Deal>
     */
    private function seedDeals(Team $team, \Illuminate\Support\Collection $contacts, array $staff, Pipeline $pipeline, \Illuminate\Support\Collection $stages): \Illuminate\Support\Collection
    {
        $distribution = [
            ['prospect', 8], ['proposal', 6], ['negotiation', 5],
            ['won', 5], ['lost', 4],
        ];

        $names = [
            'Zamalek River View Apartment', 'New Cairo Family Villa', 'Maadi Garden Duplex',
            'Heliopolis Penthouse', 'Sheikh Zayed Townhouse', 'North Coast Chalet',
            'Nasr City Investment Flat', 'El Rehab Compound Villa', 'Downtown Heritage Office',
            'Giza Heights Apartment', 'Zayed Twin House', 'Katameya Dunes Villa',
            'Madinet Nasr Retail Unit', 'Maadi Corniche Studio', 'New Capital Apartment',
            'Sokhna Resort Chalet', 'Allegria Villa', 'Palm Hills Apartment',
            'Mountain View i-Villa', 'Badya Townhouse', 'Cairo Business Plaza Suite',
            'Hyde Park Residence', 'Mivida Compound Home', 'Taj City Apartment',
            'Sarai Compound Villa', 'Silicon Waha Office', 'Green Revolution Unit',
            'Crimson Coast Villa',
        ];

        $deals = collect();
        $dealIndex = 0;

        foreach ($distribution as [$stage, $count]) {
            foreach (range(1, $count) as $n) {
                $contact = $contacts[($dealIndex * 3) % $contacts->count()];
                $rep = $staff['Salma Adel'];

                if ($dealIndex % 2 === 0) {
                    $rep = $staff['Yasmine Fouad'];
                } elseif ($dealIndex % 3 === 0) {
                    $rep = $staff['Karim Mansour'];
                } elseif ($dealIndex % 5 === 0) {
                    $rep = $staff['Tarek Naguib'];
                }

                $value = 1_800_000 + (($dealIndex * 431_000) % 6_200_000);
                $probability = match ($stage) {
                    'prospect' => 20,
                    'proposal' => 45,
                    'negotiation' => 70,
                    'won' => 100,
                    default => 0,
                };

                /** @var Deal $deal */
                $deal = Deal::query()->create([
                    'team_id' => $team->id,
                    'name' => $names[$dealIndex % count($names)],
                    'value' => $value,
                    'stage' => $stage,
                    'close_date' => CarbonImmutable::now()
                        ->addDays(match ($stage) {
                            'won', 'lost' => -((($dealIndex * 11) % 50) + 2),
                            default => ((($dealIndex * 7) % 40) + 10),
                        })
                        ->toDateString(),
                    'probability' => $probability,
                    'contact_id' => $contact->id,
                    'user_id' => $rep->id,
                    'pipeline_id' => $pipeline->id,
                    'stage_id' => $stages[$dealIndex % max(1, $stages->count())]->id,
                ]);

                $createdAt = CarbonImmutable::now()->subDays(75 - ($dealIndex % 60));
                $deal->forceFill(['created_at' => $createdAt, 'updated_at' => $createdAt])->saveQuietly();

                $deals->push($deal);
                $dealIndex++;
            }
        }

        return $deals;
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Contact>  $contacts
     * @param  \Illuminate\Support\Collection<int, Deal>  $deals
     */
    private function seedTasks(Team $team, \Illuminate\Support\Collection $contacts, \Illuminate\Support\Collection $deals, array $staff): void
    {
        $tasks = [
            ['Follow up on viewing request', '+2 days'],
            ['Call to schedule property visit', '+1 day'],
            ['Send updated price list', '-1 day'],
            ['Prepare offer letter', '+3 days'],
            ['Collect signed contract', '-2 days'],
            ['Confirm mortgage pre-approval', '+5 days'],
            ['Arrange maintenance inspection', '+7 days'],
            ['Send thank-you note after closing', '+1 day'],
            ['Qualify new website lead', 'now'],
            ['Share compound brochure', '+4 days'],
            ['Negotiate final payment terms', '+2 days'],
            ['Book second viewing with family', '+6 days'],
        ];

        $owners = array_slice(array_keys($staff), 1);

        foreach ($tasks as $i => [$name, $offset]) {
            $due = $offset === 'now'
                ? CarbonImmutable::now()->setTime(16, 0)
                : CarbonImmutable::now()->modify($offset)->setTime(10 + $i % 6, 30);

            $taskData = [
                'team_id' => $team->id,
                'name' => $name,
                'description' => "Auto-generated demo follow-up for {$name}.",
                'due_date' => $due,
                'status' => $due->isPast() ? 'completed' : 'pending',
                'assigned_to' => $staff[$owners[$i % count($owners)]]->id,
            ];

            if ($i < 8 && isset($contacts[$i])) {
                $taskData['contact_id'] = $contacts[$i]->id;
            }

            Task::query()->create($taskData);
        }

        $overdue = [
            ['Chase pending documents from client', '-3 days'],
            ['Update listing photos for Zamalek unit', '-5 days'],
            ['Review Q3 pipeline forecast', '-1 day'],
        ];

        foreach ($overdue as $i => [$name, $offset]) {
            Task::query()->create([
                'team_id' => $team->id,
                'name' => $name,
                'description' => 'Overdue demo task.',
                'due_date' => CarbonImmutable::now()->modify($offset)->setTime(12, 0),
                'status' => 'pending',
                'assigned_to' => $staff[$owners[$i % count($owners)]]->id,
                'contact_id' => $contacts[$i + 8]?->id,
            ]);
        }
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Contact>  $contacts
     */
    private function seedActivities(Team $team, \Illuminate\Support\Collection $contacts, array $staff): void
    {
        $templates = [
            ['call', 'Discovery call about %s requirements'],
            ['meeting', 'Property viewing at %s'],
            ['email', 'Sent brochure and pricing to %s'],
            ['call', 'Negotiation call regarding offer'],
            ['meeting', 'Contract signing meeting'],
            ['note', 'Logged site visit feedback'],
            ['call', 'Post-viewing follow-up call'],
            ['meeting', 'Second viewing with family'],
            ['email', 'Shared comparable market analysis'],
            ['call', 'Discussed payment plan options'],
        ];

        $areas = ['New Cairo', 'Sheikh Zayed', 'Maadi', 'Heliopolis', 'North Coast', 'Zamalek'];
        $owners = array_slice(array_keys($staff), 1);

        foreach (range(0, 39) as $i) {
            [$type, $template] = $templates[$i % count($templates)];
            $description = str_contains($template, '%s')
                ? sprintf($template, $areas[$i % count($areas)])
                : $template;

            Activity::query()->create([
                'team_id' => $team->id,
                'type' => $type,
                'date' => CarbonImmutable::now()->subHours(($i * 9) % 480)->setTime(9 + $i % 8, ($i * 23) % 60),
                'description' => $description,
                'outcome' => $i % 3 === 0 ? 'Positive response' : ($i % 3 === 1 ? 'Awaiting reply' : 'Meeting booked'),
                'activitable_id' => $contacts[$i % $contacts->count()]->id,
                'activitable_type' => Contact::class,
            ]);
        }
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Contact>  $contacts
     */
    private function seedNotes(\Illuminate\Support\Collection $contacts): void
    {
        $notes = [
            'Prefers WhatsApp over calls. Best reached after 6 PM.',
            'Budget is flexible if the compound offers a payment plan.',
            'Relocating from Dubai in Q4 — needs furnished options.',
            'Compared three compounds; our listing has the best location.',
            'Family of five looking for a minimum of 3 bedrooms.',
            'Asked about rental yield before committing to purchase.',
            'Decision-maker is the spouse; include them in next viewing.',
            'Requested a second viewing this weekend.',
        ];

        foreach (range(0, 19) as $i) {
            Note::query()->create([
                'team_id' => $contacts[$i]->team_id,
                'content' => $notes[$i % count($notes)],
                'contact_id' => $contacts[$i]->id,
            ]);
        }
    }
}
