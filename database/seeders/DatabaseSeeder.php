<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Personnel\Personnel;
use App\Models\Personnel\ErpPersonnel;
use App\Models\Personnel\ErpPerson;
use App\Models\Organisation\OrganisationsCollection;
use App\Models\Organisation\ErpOrganisation;
use App\Models\Location\LocationsCollection;
use App\Models\Location\Location;
use App\Models\Vehicle\VehicleAssetInformation;
use App\Models\Vehicle\Vehicle;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // sample organisations
        $orgCollection = OrganisationsCollection::create();
        $organisation = ErpOrganisation::create([
            'ORGANISATIONS_COLLECTION_ID' => $orgCollection->ORGANISATIONS_COLLECTION_ID,
            'MRID' => 'ORG' . rand(1000, 9999),
            'NAME' => 'Engineering Department',
            'CATEGORY' => 'Internal',
            'CODE' => 'ENG001',
            'COMPANY_REGISTRATION_NO' => '2020/' . rand(100000, 999999) . '/07',
        ]);

        // sample locations
        $locCollection = LocationsCollection::create();
        $location = Location::create([
            'LOCATIONS_COLLECTION_ID' => $locCollection->LOCATIONS_COLLECTION_ID,
            'MRID' => 'LOC' . rand(1000, 9999),
            'NAME' => 'Head Office',
            'CATEGORY' => 'Office',
            'CODE' => 'HQ001',
        ]);

        // sample vehicles
        $vehicleInfo = VehicleAssetInformation::create();
        $vehicle = Vehicle::create([
            'VEHICLE_ASSET_INFORMATION_ID' => $vehicleInfo->VEHICLE_ASSET_INFORMATION_ID,
            'MRID' => 'VEH' . rand(1000, 9999),
            'NAME' => 'Company Vehicle',
            'VEHICLE_MAKE' => 'Toyota',
            'VEHICLE_MODEL' => 'Hilux',
            'YEAR' => 2022,
            'CATEGORY' => 'Utility',
        ]);

        // sample personnel
        $personnel = Personnel::create();
        $erpPersonnel = ErpPersonnel::create([
            'PERSONNEL_ID' => $personnel->PERSONNEL_ID,
            'JOB_TITLE' => 'Senior Engineer',
            'START_DATE' => now()->subYears(2)->format('Y-m-d'),
            'KEY_PERSON_INDICATOR' => true,
        ]);

        ErpPerson::create([
            'ERP_PERSONNEL_ID' => $erpPersonnel->ERP_PERSONNEL_ID,
            'MRID' => 'EMP' . rand(1000, 9999),
            'NAME' => 'John Smith',
            'FIRST_NAME' => 'John',
            'LAST_NAME' => 'Smith',
            'GENDER' => 'Male',
            'BIRTH_DATE_TIME' => '1985-06-15 00:00:00',
            'NATIONALITY' => 'South African',
        ]);

        // linking relationships
        $personnel->organisations()->attach($organisation->ERP_ORGANISATION_ID, [
            'RELATIONSHIP_TYPE' => 'EMPLOYER'
        ]);

        $personnel->locations()->attach($location->LOCATION_ID, [
            'RELATIONSHIP_TYPE' => 'WORK_LOCATION'
        ]);

        $organisation->locations()->attach($location->LOCATION_ID, [
            'RELATIONSHIP_TYPE' => 'HEADQUARTERS'
        ]);

        $this->command->info('Sample data created successfully!');
        $this->command->info('Personnel MRID: EMP' . ($erpPersonnel->ERP_PERSONNEL_ID + 1000));
        $this->command->info('Organisation MRID: ORG' . ($organisation->ERP_ORGANISATION_ID + 1000));
        $this->command->info('Location MRID: LOC' . ($location->LOCATION_ID + 1000));
    }
}