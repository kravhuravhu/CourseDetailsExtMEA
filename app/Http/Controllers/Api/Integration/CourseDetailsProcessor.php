<?php

namespace App\Services\Integration;

use App\Models\Personnel\Personnel;
use App\Models\Personnel\ErpPersonnel;
use App\Models\Personnel\ErpPerson;
use App\Models\Personnel\AccessCard;
use App\Models\Personnel\Craft;
use App\Models\Personnel\ErpCompetency;
use App\Models\Personnel\Skill;
use App\Models\Organisations\OrganisationsCollection;
use App\Models\Organisations\ErpOrganisation;
use App\Models\Organisations\ElectronicAddress;
use App\Models\Organisations\CustomerData;
use App\Models\Organisations\SupplierData;
use App\Models\Locations\LocationsCollection;
use App\Models\Locations\Location;
use App\Models\Locations\PostalAddress;
use App\Models\Locations\StreetAddress;
use App\Models\Vehicles\VehicleAssetInformation;
use App\Models\Vehicles\Vehicle;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CourseDetailsProcessor
{
    /**
     * Process course details from integration.
     *
     * @param array $data
     * @param string $messageType
     * @return array
     */
    public function process(array $data, string $messageType): array
    {
        DB::beginTransaction();

        try {
            $processedRecords = [];

            // Process Personnel data
            if (isset($data['payload']['personnel'])) {
                $personnelResult = $this->processPersonnelData($data['payload']['personnel']);
                $processedRecords['personnel'] = $personnelResult['count'];
                
                if (!$personnelResult['success']) {
                    throw new \Exception($personnelResult['error']);
                }
            }

            // Process Organisations data
            if (isset($data['payload']['organisations'])) {
                $orgResult = $this->processOrganisationsData($data['payload']['organisations']);
                $processedRecords['organisations'] = $orgResult['count'];
                
                if (!$orgResult['success']) {
                    throw new \Exception($orgResult['error']);
                }
            }

            // Process Locations data
            if (isset($data['payload']['locations'])) {
                $locationResult = $this->processLocationsData($data['payload']['locations']);
                $processedRecords['locations'] = $locationResult['count'];
                
                if (!$locationResult['success']) {
                    throw new \Exception($locationResult['error']);
                }
            }

            // Process Vehicle Asset Information
            if (isset($data['payload']['vehicle_asset_information'])) {
                $vehicleResult = $this->processVehicleData($data['payload']['vehicle_asset_information']);
                $processedRecords['vehicles'] = $vehicleResult['count'];
                
                if (!$vehicleResult['success']) {
                    throw new \Exception($vehicleResult['error']);
                }
            }

            DB::commit();

            return [
                'success' => true,
                'data' => [
                    'processed_records' => array_sum($processedRecords),
                    'details' => $processedRecords,
                    'message_type' => $messageType,
                    'message_id' => $data['header']['message_id'] ?? null,
                ]
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('CourseDetailsProcessor Error: ' . $e->getMessage(), [
                'data' => $data,
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'errors' => []
            ];
        }
    }

    /**
     * Process personnel data.
     *
     * @param array $personnelData
     * @return array
     */
    private function processPersonnelData(array $personnelData): array
    {
        try {
            $count = 0;

            foreach ($personnelData as $personnelItem) {
                // Create or update Personnel
                $personnel = Personnel::create();

                // Process ErpPersonnel
                if (isset($personnelItem['erp_personnel'])) {
                    $erpPersonnelData = $this->mapPersonnelData($personnelItem['erp_personnel']);
                    $erpPersonnelData['personnel_id'] = $personnel->id;
                    
                    $erpPersonnel = ErpPersonnel::create($erpPersonnelData);

                    // Process ErpPerson
                    if (isset($personnelItem['erp_personnel']['person_data'])) {
                        $personData = $this->mapPersonData($personnelItem['erp_personnel']['person_data']);
                        $personData['erp_personnel_id'] = $erpPersonnel->id;
                        
                        ErpPerson::create($personData);
                    }

                    // Process Access Cards
                    if (isset($personnelItem['erp_personnel']['access_cards'])) {
                        foreach ($personnelItem['erp_personnel']['access_cards'] as $accessCardData) {
                            $mappedData = $this->mapAccessCardData($accessCardData);
                            $mappedData['erp_personnel_id'] = $erpPersonnel->id;
                            
                            AccessCard::create($mappedData);
                        }
                    }

                    // Process Crafts
                    if (isset($personnelItem['erp_personnel']['crafts'])) {
                        foreach ($personnelItem['erp_personnel']['crafts'] as $craftData) {
                            $mappedData = $this->mapCraftData($craftData);
                            $mappedData['erp_personnel_id'] = $erpPersonnel->id;
                            
                            Craft::create($mappedData);
                        }
                    }

                    // Process Competencies
                    if (isset($personnelItem['erp_personnel']['competencies'])) {
                        foreach ($personnelItem['erp_personnel']['competencies'] as $competencyData) {
                            $mappedData = $this->mapCompetencyData($competencyData);
                            $mappedData['erp_personnel_id'] = $erpPersonnel->id;
                            
                            ErpCompetency::create($mappedData);
                        }
                    }

                    // Process Skills
                    if (isset($personnelItem['erp_personnel']['skills'])) {
                        foreach ($personnelItem['erp_personnel']['skills'] as $skillData) {
                            $mappedData = $this->mapSkillData($skillData);
                            $mappedData['erp_personnel_id'] = $erpPersonnel->id;
                            
                            Skill::create($mappedData);
                        }
                    }
                }

                $count++;
            }

            return ['success' => true, 'count' => $count];
        } catch (\Exception $e) {
            throw new \Exception('Personnel processing failed: ' . $e->getMessage());
        }
    }

    /**
     * Process organisations data.
     *
     * @param array $organisationsData
     * @return array
     */
    private function processOrganisationsData(array $organisationsData): array
    {
        try {
            $count = 0;

            foreach ($organisationsData as $orgItem) {
                // Create OrganisationsCollection
                $orgCollection = OrganisationsCollection::create();

                // Process ErpOrganisation
                if (isset($orgItem['erp_organisation'])) {
                    $orgData = $this->mapOrganisationData($orgItem['erp_organisation']);
                    $orgData['organisations_collection_id'] = $orgCollection->id;
                    
                    $erpOrganisation = ErpOrganisation::create($orgData);

                    // Process Electronic Addresses
                    if (isset($orgItem['erp_organisation']['electronic_addresses'])) {
                        foreach ($orgItem['erp_organisation']['electronic_addresses'] as $electronicAddressData) {
                            $mappedData = $this->mapElectronicAddressData($electronicAddressData);
                            $mappedData['erp_organisation_id'] = $erpOrganisation->id;
                            
                            ElectronicAddress::create($mappedData);
                        }
                    }

                    // Process Customer Data
                    if (isset($orgItem['erp_organisation']['customer_data'])) {
                        $customerData = $this->mapCustomerData($orgItem['erp_organisation']['customer_data']);
                        $customerData['erp_organisation_id'] = $erpOrganisation->id;
                        
                        CustomerData::create($customerData);
                    }

                    // Process Supplier Data
                    if (isset($orgItem['erp_organisation']['supplier_data'])) {
                        $supplierData = $this->mapSupplierData($orgItem['erp_organisation']['supplier_data']);
                        $supplierData['erp_organisation_id'] = $erpOrganisation->id;
                        
                        SupplierData::create($supplierData);
                    }
                }

                $count++;
            }

            return ['success' => true, 'count' => $count];
        } catch (\Exception $e) {
            throw new \Exception('Organisations processing failed: ' . $e->getMessage());
        }
    }

    /**
     * Process locations data.
     *
     * @param array $locationsData
     * @return array
     */
    private function processLocationsData(array $locationsData): array
    {
        try {
            $count = 0;

            foreach ($locationsData as $locationItem) {
                // Create LocationsCollection
                $locationCollection = LocationsCollection::create();

                // Process Location
                if (isset($locationItem['location'])) {
                    $locationData = $this->mapLocationData($locationItem['location']);
                    $locationData['locations_collection_id'] = $locationCollection->id;
                    
                    $location = Location::create($locationData);

                    // Process Postal Address
                    if (isset($locationItem['location']['postal_address'])) {
                        $postalData = $this->mapPostalAddressData($locationItem['location']['postal_address']);
                        $postalData['location_id'] = $location->id;
                        
                        PostalAddress::create($postalData);
                    }

                    // Process Street Address
                    if (isset($locationItem['location']['street_address'])) {
                        $streetData = $this->mapStreetAddressData($locationItem['location']['street_address']);
                        $streetData['location_id'] = $location->id;
                        
                        StreetAddress::create($streetData);
                    }
                }

                $count++;
            }

            return ['success' => true, 'count' => $count];
        } catch (\Exception $e) {
            throw new \Exception('Locations processing failed: ' . $e->getMessage());
        }
    }

    /**
     * Process vehicle data.
     *
     * @param array $vehicleData
     * @return array
     */
    private function processVehicleData(array $vehicleData): array
    {
        try {
            $count = 0;

            foreach ($vehicleData as $vehicleItem) {
                // Create VehicleAssetInformation
                $vehicleAssetInfo = VehicleAssetInformation::create();

                // Process Vehicle
                if (isset($vehicleItem['vehicle'])) {
                    $vehicleInfo = $this->mapVehicleData($vehicleItem['vehicle']);
                    $vehicleInfo['vehicle_asset_information_id'] = $vehicleAssetInfo->id;
                    
                    Vehicle::create($vehicleInfo);
                }

                $count++;
            }

            return ['success' => true, 'count' => $count];
        } catch (\Exception $e) {
            throw new \Exception('Vehicle processing failed: ' . $e->getMessage());
        }
    }

    /**
     * Map personnel data from integration format to database format.
     */
    private function mapPersonnelData(array $data): array
    {
        return [
            'administration_indicator' => $data['administration_indicator'] ?? false,
            'deemed_start_date_time' => $data['deemed_start_date_time'] ?? null,
            'finish_date' => $data['finish_date'] ?? null,
            'job_code' => $data['job_code'] ?? null,
            'job_restriction_codes' => $data['job_restriction_codes'] ?? null,
            'job_title' => $data['job_title'] ?? null,
            'key_person_indicator' => $data['key_person_indicator'] ?? false,
            'overtime_eligible_indicator' => $data['overtime_eligible_indicator'] ?? false,
            'payment_method' => $data['payment_method'] ?? null,
            'responsibility' => $data['responsibility'] ?? null,
            'start_date' => $data['start_date'] ?? null,
            'transfer_benefits_payable_indicator' => $data['transfer_benefits_payable_indicator'] ?? false,
        ];
    }

    /**
     * Map person data from integration format to database format.
     */
    private function mapPersonData(array $data): array
    {
        return [
            'mrid' => $data['mrid'] ?? null,
            'alias_name' => $data['alias_name'] ?? null,
            'description' => $data['description'] ?? null,
            'local_name' => $data['local_name'] ?? null,
            'name' => $data['name'] ?? null,
            'path_name' => $data['path_name'] ?? null,
            'birth_date_time' => $data['birth_date_time'] ?? null,
            'category' => $data['category'] ?? null,
            'death_date_time' => $data['death_date_time'] ?? null,
            'ethnicity' => $data['ethnicity'] ?? null,
            'first_name' => $data['first_name'] ?? null,
            'gender' => $data['gender'] ?? null,
            'initials' => $data['initials'] ?? null,
            'last_name' => $data['last_name'] ?? null,
            'maiden_name' => $data['maiden_name'] ?? null,
            'marital_status' => $data['marital_status'] ?? null,
            'marriage_type' => $data['marriage_type'] ?? null,
            'm_name' => $data['m_name'] ?? null,
            'nationality' => $data['nationality'] ?? null,
            'nickname' => $data['nickname'] ?? null,
            'prefix' => $data['prefix'] ?? null,
            'special_needs' => $data['special_needs'] ?? null,
            'suffix' => $data['suffix'] ?? null,
        ];
    }

    private function mapAccessCardData(array $data): array
    {
        return [
            'access_type' => $data['access_type'] ?? null,
            'application_date' => $data['application_date'] ?? null,
            'mrid' => $data['mrid'] ?? null,
            // ... map other fields
        ];
    }

    private function mapOrganisationData(array $data): array
    {
        return [
            'mrid' => $data['mrid'] ?? null,
            'name' => $data['name'] ?? null,
            'bee_rating' => $data['bee_rating'] ?? null,
            'category' => $data['category'] ?? null,
            'code' => $data['code'] ?? null,
            // ... map other fields
        ];
    }

    private function mapLocationData(array $data): array
    {
        return [
            'mrid' => $data['mrid'] ?? null,
            'name' => $data['name'] ?? null,
            'category' => $data['category'] ?? null,
            'code' => $data['code'] ?? null,
            // ... map other fields
        ];
    }

    private function mapVehicleData(array $data): array
    {
        return [
            'mrid' => $data['mrid'] ?? null,
            'name' => $data['name'] ?? null,
            'vehicle_make' => $data['vehicle_make'] ?? null,
            'vehicle_model' => $data['vehicle_model'] ?? null,
            'vehicle_type' => $data['vehicle_type'] ?? null,
            // ... map other fields
        ];
    }
}