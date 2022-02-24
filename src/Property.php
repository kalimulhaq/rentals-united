<?php

namespace Kalimulhaq\RentalsUnited;

class Property extends RentalsUnited
{
    /**
     * Put building
     *
     * <p>This method inserts a single building into the Rentals United system.</p>
     *
     * @param  string $name
     * @return int|bool Building ID on success, false if fail
     */
    public function putBuilding(string $name): ?int
    {
        $body = '<Push_PutBuilding_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '<BuildingName>' . $name . '</BuildingName>';
        $body .= '</Push_PutBuilding_RQ>';

        $response = $this->sendRequest($body);
        $result = false;
        if ($response->successful() && $this->isRuSuccessful()) {
            $XMLObject = new \SimpleXMLElement($response->body());
            $result = (int)((string) $XMLObject->BuildingID);
        }

        $this->result = $result;
        return $result;
    }

    /**
     * This method returns a list of buildings with properties assigned.
     *
     * @return array
     */
    public function listBuildings(): ?array
    {
        $body = '<Pull_ListBuildings_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '</Pull_ListBuildings_RQ>';

        $response = $this->sendRequest($body);
        $result = array();
        if ($response->successful() && $this->isRuSuccessful()) {
            $XMLObject = new \SimpleXMLElement($response->body());
            $collection = $XMLObject->Buildings->Building;
            foreach ($collection as $item) {
                $building = $this->xmlToArray($item, false);
                $building['PropertyID'] = [];
                foreach ($item->PropertyID as $prop) {
                    $building['PropertyID'][] = (int)(string) $prop;
                }
                unset($building['Building']);
                $result[] = $building;
            }
        }

        $this->result = $result;
        return $result;
    }

    /**
     * This method inserts a single owner into Rentals United.
     * Each owner represents a property contact information i.e. arrival instructions to a property.
     *
     * @param  string $first_name
     * @param  string $sur_name
     * @param  string $email
     * @param  string $phone
     * @return int|bool Owner ID on success, false if fail
     */
    public function putOwner(string $first_name, string $sur_name, string $email, string $phone): ?int
    {
        $body = '<Push_PutOwner_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '<Owner>';
        $body .= '<FirstName>' . $first_name . '</FirstName>';
        $body .= '<SurName>' . $sur_name . '</SurName>';
        $body .= '<Email>' . $email . '</Email>';
        $body .= '<Phone>' . $phone . '</Phone>';
        $body .= '</Owner>';
        $body .= '</Push_PutOwner_RQ>';

        $response = $this->sendRequest($body);
        $result = false;
        if ($response->successful() && $this->isRuSuccessful()) {
            $XMLObject = new \SimpleXMLElement($response->body());
            $result = (int)((string) $XMLObject->OwnerID);
        }

        $this->result = $result;
        return $result;
    }

    /**
     * This method returns a list of property owners available on your account.
     * Owner represents contact information for a property.
     *
     * @return array
     */
    public function listAllOwners(): ?array
    {
        $body = '<Pull_ListAllOwners_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '</Pull_ListAllOwners_RQ>';

        $response = $this->sendRequest($body);
        $result = array();
        if ($response->successful() && $this->isRuSuccessful()) {
            $XMLObject = new \SimpleXMLElement($response->body());
            $collection = $XMLObject->Owners->Owner;
            foreach ($collection as $item) {
                $arr =  $this->xmlToArray($item, false);
                $owner =  $arr['Owner'];
                $owner['OwnerID'] =  $arr['OwnerID'];
                $result[] =  $owner;
            }
        }

        $this->result = $result;
        return $result;
    }

    /**
     * This method returns detailed contact information of a specific property owner.
     *
     * @param int $ownerID
     * @return array
     */
    public function getOwnerDetails(int $ownerID): ?array
    {
        $body = '<Pull_GetOwnerDetails_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '<OwnerID>' . $ownerID . '</OwnerID>';
        $body .= '</Pull_GetOwnerDetails_RQ>';

        $response = $this->sendRequest($body);
        $result = array();
        if ($response->successful() && $this->isRuSuccessful()) {
            $XMLObject = new \SimpleXMLElement($response->body());
            $arr =  $this->xmlToArray($XMLObject->Owner, false);
            $result =  $arr['Owner'];
            $result['OwnerID'] =  $arr['OwnerID'];
        }

        $this->result = $result;
        return $result;
    }

    /**
     * This method returns a list of all available Sales Channels.
     * If you are a PMS, use this method to identify a reservation creator within reservations/leads retrieval API methods.
     *
     * @return array
     */
    public function getAgents(): ?array
    {
        $body = '<Pull_GetAgents_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '</Pull_GetAgents_RQ>';

        $response = $this->sendRequest($body);
        $result = array();
        if ($response->successful() && $this->isRuSuccessful()) {
            $XMLObject = new \SimpleXMLElement($response->body());

            $_owner =  $this->xmlToArray($XMLObject->Owner, false);
            $owner =  $_owner['Owner'];
            $owner['OwnerID'] =  $_owner['OwnerID'];
            $result['Owner'] = $owner;

            $result['Agents'] = array();
            $collection = $XMLObject->Agents->Agent;
            foreach ($collection as $item) {
                $agent = $this->xmlToArray($item, false);
                $result['Agents'][] = isset($agent['Agent']) ? $agent['Agent'] : $agent;
            }
        }

        $this->result = $result;
        return $result;
    }

    /**
     * Put property
     *
     * <p>This method has been designed to create and update a property in Rentals United. Update mode is triggered when Push_PutProperty_RQ/Property/ID is provided in the XML request.</p>
     * <p>Elements marked with M are mandatory for property creation. They may be skipped in update XML requests if they are marked with X. Elements marked with O are optional for property creation and update. If they are not sent, no changes will be made in Rentals United. If they are sent as empty nodes, data will be erased from Rentals United.</p>
     * <p>Elements and attributes marked as X can be omitted in update mode - in such case values in Rentals United will remain unchanged. If you send an empty node for an element marked with X, value of that element will be removed from Rentals United</p>
     *
     * @param array|null $PUID
     * @param string|null $Name
     * @param string|null $OwnerID
     * @param array|null $DetailedLocationID
     * @param bool|null $IsActive
     * @param bool|null $IsArchived
     * @param int|null $Space
     * @param int|null $StandardGuests
     * @param int|null $CanSleepMax
     * @param int|null $PropertyTypeID
     * @param int|null $ObjectTypeID
     * @param int|null $NoOfUnits
     * @param int|null $Floor
     * @param string|null $Street
     * @param string|null $ZipCode
     * @param array|null $Coordinates
     * @param array|null $Distances
     * @param array|null $Amenities
     * @param array|null $CompositionRoomsAmenities
     * @param array|null $Images
     * @param array|null $ArrivalInstructions
     * @param array|null $CheckInOut
     * @param array|null $PaymentMethods
     * @param array|null $Deposit
     * @param array|null $SecurityDeposit
     * @param array|null $CancellationPolicies
     * @param array|null $Descriptions
     * @param array|null $AdditionalFees
     * @param array|null $LicenceInfo
     * @param int|null $PreparationTimeBeforeArrivalInHours
     * @param int|null $NumberOfStars = null
     * @return int|bool
     */
    public function putProperty(
        ?int $ID = null,
        ?array $PUID = null,
        ?string $Name = null,
        ?string $OwnerID = null,
        ?array $DetailedLocationID = null,
        ?bool $IsActive = null,
        ?bool $IsArchived = null,
        ?int $Space = null,
        ?int $StandardGuests = null,
        ?int $CanSleepMax = null,
        ?int $PropertyTypeID = null,
        ?int $ObjectTypeID = null,
        ?int $NoOfUnits = null,
        ?int $Floor = null,
        ?string $Street = null,
        ?string $ZipCode = null,
        ?array $Coordinates = null,
        ?array $Distances = null,
        ?array $Amenities = null,
        ?array $CompositionRoomsAmenities = null,
        ?array $Images = null,
        ?array $ImageCaptions = null,
        ?array $ArrivalInstructions = null,
        ?array $CheckInOut = null,
        ?array $PaymentMethods = null,
        ?array $Deposit = null,
        ?array $SecurityDeposit = null,
        ?array $CancellationPolicies = null,
        ?array $Descriptions = null,
        ?array $AdditionalFees = null,
        ?array $LicenceInfo = null,
        ?int $PreparationTimeBeforeArrivalInHours = null,
        ?int $NumberOfStars = null
    ): ?int {
        $body = '<Push_PutProperty_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '<Property>';

        if (!is_null($ID)) {
            $body .= '<ID>' . $ID . '</ID>';
        }
        if (!is_null($PUID)) {
            $BuildingID = isset($PUID['BuildingID']) ? 'BuildingID="' . $PUID['BuildingID'] . '"' : '';
            $body .= '<PUID ' . $BuildingID . '>' . $PUID['PUID'] . '</PUID>';
        }
        if (!is_null($Name)) {
            $body .= '<Name><![CDATA[' . $Name . ']]></Name>';
        }
        if (!is_null($OwnerID)) {
            $body .= '<OwnerID>' . $OwnerID . '</OwnerID>';
        }
        if (!is_null($DetailedLocationID)) {
            $TypeID = isset($DetailedLocationID['TypeID']) ? 'TypeID="' . $DetailedLocationID['TypeID'] . '"' : '';
            $body .= '<DetailedLocationID ' . $TypeID . '>' . $DetailedLocationID['DetailedLocationID'] . '</DetailedLocationID>';
        }
        if (!is_null($IsActive)) {
            $body .= '<IsActive>' . ($IsActive ? 1 : 0) . '</IsActive>';
        }
        if (!is_null($IsArchived)) {
            $body .= '<IsArchived>' . ($IsArchived ? 1 : 0) . '</IsArchived>';
        }
        if (!is_null($Space)) {
            $body .= '<Space>' . round($Space, 0) . '</Space>';
        }
        if (!is_null($StandardGuests)) {
            $body .= '<StandardGuests>' . $StandardGuests . '</StandardGuests>';
        }
        if (!is_null($CanSleepMax)) {
            $body .= '<CanSleepMax>' . $CanSleepMax . '</CanSleepMax>';
        }
        if (!is_null($PropertyTypeID)) {
            $body .= '<PropertyTypeID>' . $PropertyTypeID . '</PropertyTypeID>';
        }
        if (!is_null($ObjectTypeID)) {
            $body .= '<ObjectTypeID>' . $ObjectTypeID . '</ObjectTypeID>';
        }
        if (!is_null($NoOfUnits)) {
            $body .= '<NoOfUnits>' . $NoOfUnits . '</NoOfUnits>';
        }
        if (!is_null($Floor)) {
            $body .= '<Floor>' . $Floor . '</Floor>';
        }
        if (!is_null($Street)) {
            $body .= '<Street>' . $Street . '</Street>';
        }
        if (!is_null($ZipCode)) {
            $body .= '<ZipCode>' . $ZipCode . '</ZipCode>';
        }
        if (!is_null($Coordinates)) {
            $body .= '<Coordinates>';
            $body .= '<Latitude>' . $Coordinates['Latitude'] . '</Latitude>';
            $body .= '<Longitude>' . $Coordinates['Longitude'] . '</Longitude>';
            $body .= '</Coordinates>';
        }
        if (!is_null($Distances)) {
            $body .= '<Distances>';
            foreach ($Distances as $item) {
                $body .= '<Distance>';
                $body .= '<DestinationID>' . $item['DestinationID'] . '</DestinationID>';
                $body .= '<DistanceUnitID>' . $item['DistanceUnitID'] . '</DistanceUnitID>';
                $body .= '<DistanceValue>' . round($item['DistanceValue'], 2) . '</DistanceValue>';
                $body .= '</Distance>';
            }
            $body .= '</Distances>';
        }
        if (!is_null($Amenities)) {
            $body .= '<Amenities>';
            foreach ($Amenities as $item) {
                $Count = 'Count="' . (!empty($item['Count']) ? $item['Count'] : 1) . '"';
                $body .= '<Amenity ' . $Count . '>' . $item['Amenity'] . '</Amenity>';
            }
            $body .= '</Amenities>';
        }
        if (!is_null($CompositionRoomsAmenities)) {
            $body .= '<CompositionRoomsAmenities>';
            foreach ($CompositionRoomsAmenities as $room) {
                $body .= '<CompositionRoomAmenities CompositionRoomID="' . $room['CompositionRoomID'] . '">';
                if (isset($room['Amenities'])) {
                    $body .= '<Amenities>';
                    foreach ($room['Amenities'] as $item) {
                        $Count = 'Count="' . (!empty($item['Count']) ? $item['Count'] : 1) . '"';
                        $body .= '<Amenity ' . $Count . '>' . $item['Amenity'] . '</Amenity>';
                    }
                    $body .= '</Amenities>';
                }
                $body .= '</CompositionRoomAmenities>';
            }
            $body .= '</CompositionRoomsAmenities>';
        }
        if (!is_null($Images)) {
            $body .= '<Images>';
            foreach ($Images as $item) {
                $ImageTypeID = 'ImageTypeID="' .  $item['ImageTypeID']  . '"';
                $ImageReferenceID = isset($item['ImageReferenceID']) ? 'ImageReferenceID="' .  $item['ImageReferenceID']  . '"' : '';
                $body .= '<Image ' . $ImageTypeID . ' ' . $ImageReferenceID . '>' . $item['Image'] . '</Image>';
            }
            $body .= '</Images>';
        }
        if (!is_null($ImageCaptions)) {
            $body .= '<ImageCaptions>';
            foreach ($ImageCaptions as $item) {
                $ImageReferenceID = 'ImageReferenceID="' .  $item['ImageReferenceID']  . '"';
                $LanguageID = 'LanguageID="' .  $item['LanguageID']  . '"';
                $body .= '<ImageCaption ' . $ImageReferenceID . ' ' . $LanguageID . '>' . $item['ImageCaption'] . '</ImageCaption>';
            }
            $body .= '</ImageCaptions>';
        }
        if (!is_null($ArrivalInstructions)) {
            $body .= '<ArrivalInstructions>';
            if (isset($ArrivalInstructions['Landlord'])) {
                $body .= '<Landlord>' . $ArrivalInstructions['Landlord'] . '</Landlord>';
            }
            if (isset($ArrivalInstructions['Email'])) {
                $body .= '<Email>' . $ArrivalInstructions['Email'] . '</Email>';
            }
            if (isset($ArrivalInstructions['Phone'])) {
                $body .= '<Phone>' . $ArrivalInstructions['Phone'] . '</Phone>';
            }
            if (isset($ArrivalInstructions['DaysBeforeArrival'])) {
                $body .= '<DaysBeforeArrival>' . $ArrivalInstructions['DaysBeforeArrival'] . '</DaysBeforeArrival>';
            }
            if (isset($ArrivalInstructions['HowToArrive'])) {
                $body .= '<HowToArrive>';
                foreach ($ArrivalInstructions['HowToArrive'] as $item) {
                    $body .= '<Text LanguageID="' . $item['LanguageID'] . '"><![CDATA[' . $item['Text'] . ']]></Text>';
                }
                $body .= '</HowToArrive>';
            }
            if (isset($ArrivalInstructions['PickupService'])) {
                $body .= '<PickupService>';
                foreach ($ArrivalInstructions['PickupService'] as $item) {
                    $body .= '<Text LanguageID="' . $item['LanguageID'] . '"><![CDATA[' . $item['Text'] . ']]></Text>';
                }
                $body .= '</PickupService>';
            }
            $body .= '</ArrivalInstructions>';
        }
        if (!is_null($CheckInOut)) {
            $body .= '<CheckInOut>';
            if (isset($CheckInOut['CheckInFrom'])) {
                $body .= '<CheckInFrom>' . $CheckInOut['CheckInFrom'] . '</CheckInFrom>';
            }
            if (isset($CheckInOut['CheckInTo'])) {
                $body .= '<CheckInTo>' . $CheckInOut['CheckInTo'] . '</CheckInTo>';
            }
            if (isset($CheckInOut['CheckOutUntil'])) {
                $body .= '<CheckOutUntil>' . $CheckInOut['CheckOutUntil'] . '</CheckOutUntil>';
            }
            if (isset($CheckInOut['Place'])) {
                $body .= '<Place>' . $CheckInOut['Place'] . '</Place>';
            }
            if (isset($CheckInOut['LateArrivalFees'])) {
                $body .= '<LateArrivalFees>';
                foreach ($CheckInOut['LateArrivalFees'] as $item) {
                    $body .= '<LateArrivalFee From="' . $item['From'] . '" To="' . $item['To'] . '">' . $item['LateArrivalFee'] . '</LateArrivalFee>';
                }
                $body .= '</LateArrivalFees>';
            }
            if (isset($CheckInOut['EarlyDepartureFees'])) {
                $body .= '<EarlyDepartureFees>';
                foreach ($CheckInOut['EarlyDepartureFees'] as $item) {
                    $body .= '<EarlyDepartureFee From="' . $item['From'] . '" To="' . $item['To'] . '">' . $item['EarlyDepartureFee'] . '</EarlyDepartureFee>';
                }
                $body .= '</EarlyDepartureFees>';
            }
            $body .= '</CheckInOut>';
        }
        if (!is_null($PaymentMethods)) {
            $body .= '<PaymentMethods>';
            foreach ($PaymentMethods as $item) {
                $body .= '<PaymentMethod PaymentMethodID="' . $item['PaymentMethodID'] . '">' . $item['PaymentMethod'] . '</PaymentMethod>';
            }
            $body .= '</PaymentMethods>';
        }
        if (!is_null($Deposit)) {
            $body .= '<Deposit DepositTypeID="' . $Deposit['DepositTypeID'] . '">' . $Deposit['Deposit'] . '</Deposit>';
        }
        if (!is_null($SecurityDeposit)) {
            $body .= '<SecurityDeposit DepositTypeID="' . $SecurityDeposit['DepositTypeID'] . '">' . $SecurityDeposit['SecurityDeposit'] . '</SecurityDeposit>';
        }
        if (!is_null($CancellationPolicies)) {
            $body .= '<CancellationPolicies>';
            foreach ($CancellationPolicies as $item) {
                $body .= '<CancellationPolicy ValidFrom="' . $item['ValidFrom'] . '" ValidTo="' . $item['ValidTo'] . '">' . $item['CancellationPolicy'] . '</CancellationPolicy>';
            }
            $body .= '</CancellationPolicies>';
        }
        if (!is_null($Descriptions)) {
            $body .= '<Descriptions>';
            foreach ($Descriptions as $item) {
                $body .= '<Description LanguageID="' . $item['LanguageID'] . '">';
                if (isset($item['Text'])) {
                    $body .= '<Text><![CDATA[' . $item['Text'] . ']]></Text>';
                }
                if (isset($item['Image'])) {
                    $body .= '<Image>' . $item['Image'] . '</Image>';
                }
                $body .= '</Description>';
            }
            $body .= '</Descriptions>';
        }
        if (!is_null($AdditionalFees)) {
            $body .= '<AdditionalFees>';
            foreach ($AdditionalFees as $item) {
                $extra = '';
                $extra .= isset($item['CollectTime']) ? ' CollectTime="' .  $item['CollectTime']  . '"' : '';
                $extra .= isset($item['Optional']) ? ' Optional="' .  $item['Optional']  . '"' : '';
                $extra .= isset($item['Name']) ? ' Name="' .  $item['Name']  . '"' : '';
                $extra .= isset($item['Order']) ? ' Order="' .  $item['Order']  . '"' : '';
                $body .= '<AdditionalFee FeeTaxType="' . $item['FeeTaxType'] . '" DiscriminatorID="' . $item['DiscriminatorID'] . '"' . $extra . '>';
                $body .= '<Value>' . $item['Value'] . '</Value>';
                $body .= '</AdditionalFee>';
            }
            $body .= '</AdditionalFees>';
        }
        if (!is_null($LicenceInfo)) {
            $body .= '<LicenceInfo>';
            if (isset($LicenceInfo['IsExempt'])) {
                $body .= '<IsExempt>' . $LicenceInfo['IsExempt'] . '</IsExempt>';
            }
            if (isset($LicenceInfo['LicenceNumber'])) {
                $body .= '<LicenceNumber>' . $LicenceInfo['LicenceNumber'] . '</LicenceNumber>';
            }
            if (isset($LicenceInfo['IssueDate'])) {
                $body .= '<IssueDate>' . $LicenceInfo['IssueDate'] . '</IssueDate>';
            }
            if (isset($LicenceInfo['ExpirationDate'])) {
                $body .= '<ExpirationDate>' . $LicenceInfo['ExpirationDate'] . '</ExpirationDate>';
            }
            if (isset($LicenceInfo['IsVATRegistered'])) {
                $body .= '<IsVATRegistered>' . $LicenceInfo['IsVATRegistered'] . '</IsVATRegistered>';
            }
            if (isset($LicenceInfo['ExemptionReason'])) {
                $body .= '<ExemptionReason>' . $LicenceInfo['ExemptionReason'] . '</ExemptionReason>';
            }
            if (isset($LicenceInfo['IsManagedByOwner'])) {
                $body .= '<IsManagedByOwner>' . $LicenceInfo['IsManagedByOwner'] . '</IsManagedByOwner>';
            }
            if (isset($LicenceInfo['IsManagedByPrivatePerson'])) {
                $body .= '<IsManagedByPrivatePerson>' . $LicenceInfo['IsManagedByPrivatePerson'] . '</IsManagedByPrivatePerson>';
            }
            if (isset($LicenceInfo['BrazilianLicenceInfo'])) {
                $body .= '<BrazilianLicenceInfo>' . $LicenceInfo['BrazilianLicenceInfo'] . '</BrazilianLicenceInfo>';
            }
            if (isset($LicenceInfo['CityHallInfoId'])) {
                $body .= '<CityHallInfoId>' . $LicenceInfo['CityHallInfoId'] . '</CityHallInfoId>';
            }
            if (isset($LicenceInfo['JapaneseLicenceInfo'])) {
                $body .= '<JapaneseLicenceInfo>';
                if (isset($LicenceInfo['JapaneseLicenceInfo']['FrenchLicenceInfo'])) {
                    $body .= '<FrenchLicenceInfo>' . $LicenceInfo['JapaneseLicenceInfo']['FrenchLicenceInfo'] . '</FrenchLicenceInfo>';
                }
                if (isset($LicenceInfo['JapaneseLicenceInfo']['IsRegisteredAtTradeCommercialRegister'])) {
                    $body .= '<IsRegisteredAtTradeCommercialRegister>' . $LicenceInfo['JapaneseLicenceInfo']['IsRegisteredAtTradeCommercialRegister'] . '</IsRegisteredAtTradeCommercialRegister>';
                }
                if (isset($LicenceInfo['JapaneseLicenceInfo']['PropertyTypeForTaxPurposes'])) {
                    $body .= '<PropertyTypeForTaxPurposes>' . $LicenceInfo['JapaneseLicenceInfo']['PropertyTypeForTaxPurposes'] . '</PropertyTypeForTaxPurposes>';
                }
                if (isset($LicenceInfo['JapaneseLicenceInfo']['DeclaresRevenuesAsProfessionalForDirectTaxPurposes'])) {
                    $body .= '<DeclaresRevenuesAsProfessionalForDirectTaxPurposes>' . $LicenceInfo['JapaneseLicenceInfo']['DeclaresRevenuesAsProfessionalForDirectTaxPurposes'] . '</DeclaresRevenuesAsProfessionalForDirectTaxPurposes>';
                }
                if (isset($LicenceInfo['JapaneseLicenceInfo']['TypeOfResidence'])) {
                    $body .= '<TypeOfResidence>' . $LicenceInfo['JapaneseLicenceInfo']['TypeOfResidence'] . '</TypeOfResidence>';
                }
                if (isset($LicenceInfo['JapaneseLicenceInfo']['CityTaxCategory'])) {
                    $body .= '<CityTaxCategory>' . $LicenceInfo['JapaneseLicenceInfo']['CityTaxCategory'] . '</CityTaxCategory>';
                }
                $body .= '</JapaneseLicenceInfo>';
            }
            if (isset($LicenceInfo['TasmanianLicenceInfo'])) {
                $body .= '<TasmanianLicenceInfo>';
                if (isset($LicenceInfo['TasmanianLicenceInfo']['TasmanianLicenceInfoTypeOfResidence'])) {
                    $body .= '<TasmanianLicenceInfoTypeOfResidence>' . $LicenceInfo['TasmanianLicenceInfo']['TasmanianLicenceInfoTypeOfResidence'] . '</TasmanianLicenceInfoTypeOfResidence>';
                }
                $body .= '</TasmanianLicenceInfo>';
            }
            $body .= '</LicenceInfo>';
        }
        if (!is_null($PreparationTimeBeforeArrivalInHours)) {
            $body .= '<PreparationTimeBeforeArrivalInHours>' . round($PreparationTimeBeforeArrivalInHours, 0) . '</PreparationTimeBeforeArrivalInHours>';
        }
        if (!is_null($NumberOfStars)) {
            $body .= '<NumberOfStars>' . round($NumberOfStars, 0) . '</NumberOfStars>';
        }

        $body .= '</Property>';
        $body .= '</Push_PutProperty_RQ>';

        $response = $this->sendRequest($body);
        $result = false;
        if ($response->successful() && $this->isRuSuccessful()) {
            $XMLObject = new \SimpleXMLElement($response->body());
            $result = (int)((string) $XMLObject->ID);
        }

        $this->result = $result;
        return $result;
    }

    /**
     * Put Property By Arry
     *
     * <p>This is the same method like putProperty only receive all the parameters in one array</p>
     *
     * @param array $fields
     * @param bool
     */
    public function putPropertyByArray(array $fields): ?bool
    {
        return $this->putProperty(
            isset($fields['ID']) ? $fields['ID'] : null,
            isset($fields['PUID']) ? $fields['PUID'] : null,
            isset($fields['Name']) ? $fields['Name'] : null,
            isset($fields['OwnerID']) ? $fields['OwnerID'] : null,
            isset($fields['DetailedLocationID']) ? $fields['DetailedLocationID'] : null,
            isset($fields['IsActive']) ? $fields['IsActive'] : null,
            isset($fields['IsArchived']) ? $fields['IsArchived'] : null,
            isset($fields['Space']) ? $fields['Space'] : null,
            isset($fields['StandardGuests']) ? $fields['StandardGuests'] : null,
            isset($fields['CanSleepMax']) ? $fields['CanSleepMax'] : null,
            isset($fields['PropertyTypeID']) ? $fields['PropertyTypeID'] : null,
            isset($fields['ObjectTypeID']) ? $fields['ObjectTypeID'] : null,
            isset($fields['NoOfUnits']) ? $fields['NoOfUnits'] : null,
            isset($fields['Floor']) ? $fields['Floor'] : null,
            isset($fields['Street']) ? $fields['Street'] : null,
            isset($fields['ZipCode']) ? $fields['ZipCode'] : null,
            isset($fields['Coordinates']) ? $fields['Coordinates'] : null,
            isset($fields['Distances']) ? $fields['Distances'] : null,
            isset($fields['Amenities']) ? $fields['Amenities'] : null,
            isset($fields['CompositionRoomsAmenities']) ? $fields['CompositionRoomsAmenities'] : null,
            isset($fields['Images']) ? $fields['Images'] : null,
            isset($fields['ImageCaptions']) ? $fields['ImageCaptions'] : null,
            isset($fields['ArrivalInstructions']) ? $fields['ArrivalInstructions'] : null,
            isset($fields['CheckInOut']) ? $fields['CheckInOut'] : null,
            isset($fields['PaymentMethods']) ? $fields['PaymentMethods'] : null,
            isset($fields['Deposit']) ? $fields['Deposit'] : null,
            isset($fields['SecurityDeposit']) ? $fields['SecurityDeposit'] : null,
            isset($fields['CancellationPolicies']) ? $fields['CancellationPolicies'] : null,
            isset($fields['Descriptions']) ? $fields['Descriptions'] : null,
            isset($fields['AdditionalFees']) ? $fields['AdditionalFees'] : null,
            isset($fields['LicenceInfo']) ? $fields['LicenceInfo'] : null,
            isset($fields['PreparationTimeBeforeArrivalInHours']) ? $fields['PreparationTimeBeforeArrivalInHours'] : null,
            isset($fields['NumberOfStars']) ? $fields['NumberOfStars'] : null
        );
    }

    /**
     * This method allows you to set status for multiple properties in Rentals United.
     *
     * @param bool $isActive
     * @param bool $isArchived
     * @param array $propertyIDs
     * @return bool
     */
    public function setPropertiesStatus(bool $isActive, bool $isArchived, array $propertyIDs): ?bool
    {
        $body = '<Push_SetPropertiesStatus_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '<IsActive>' . ($isActive ? 1 : 0) . '</IsActive>';
        $body .= '<IsArchived>' . ($isArchived ? 1 : 0) . '</IsArchived>';
        $body .= '<PropertyIDs>';
        foreach ($propertyIDs as $propertyID) {
            $body .= '<PropertyID>' . $propertyID . '</PropertyID>';
        }
        $body .= '</PropertyIDs>';
        $body .= '</Push_SetPropertiesStatus_RQ>';

        $response = $this->sendRequest($body);
        $result = false;
        if ($response->successful() && $this->isRuSuccessful()) {
            $XMLObject = new \SimpleXMLElement($response->body());
            $result = true;
        }

        $this->result = $result;
        return $result;
    }

    /**
     * This method is designed for Sales Channels to provide information about listing status in the Sales Channel.
     * Use this method to insert current status of a given property in your Sales Channel.
     *
     * @param array $properties
     * @return bool
     */
    public function putPropertyExternalListing(array $properties): ?bool
    {
        $body = '<Push_PutPropertyExternalListing_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '<Properties>';
        foreach ($properties as $item) {
            $body .= '<Property ID="' . $item['ID'] . '">';
            $body .= '<Url>' . $item['Url'] . '</Url>';
            $body .= '<Status>' . $item['Status'] . '</Status>';
            $body .= '<Description>' . $item['Description'] . '</Description>';
            $body .= '</Property>';
        }
        $body .= '</Properties>';
        $body .= '</Push_PutPropertyExternalListing_RQ>';

        $response = $this->sendRequest($body);
        $result = false;
        if ($response->successful() && $this->isRuSuccessful()) {
            $XMLObject = new \SimpleXMLElement($response->body());
            $result = true;
        }

        $this->result = $result;
        return $result;
    }

    /**
     * This method has been designed to update number of people included in the seasonal price for a given property.
     * It can be used by property providers and Revenue Management Systems.
     *
     * @param int $ID
     * @param int $StandardGuests
     * @return bool
     */
    public function standardNumberOfGuests(int $ID, int $StandardGuests): ?bool
    {
        $body = '<Push_StandardNumberOfGuests_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '<Property>';
        $body .= '<ID>' . $ID . '</ID>';
        $body .= '<StandardGuests>' . $StandardGuests . '</StandardGuests>';
        $body .= '</Property>';
        $body .= '</Push_StandardNumberOfGuests_RQ>';

        $response = $this->sendRequest($body);
        $result = false;
        if ($response->successful() && $this->isRuSuccessful()) {
            $XMLObject = new \SimpleXMLElement($response->body());
            $result = true;
        }

        $this->result = $result;
        return $result;
    }

    /**
     * An alise method of standardNumberOfGuests
     *
     * @param int $ID
     * @param int $StandardGuests
     * @return bool
     */
    public function putStandardGuests(int $ID, int $StandardGuests): ?bool
    {
        return $this->standardNumberOfGuests($ID, $StandardGuests);
    }

    /**
     * Use this method to upload availability, minimum stay and changeover for a property and specified date ranges.
     * If your property is multiunit, you can set number of available units.
     * In order to remove minimum stay restriction for a given date range, push minimum stay value of 1.
     *
     * Rentals United default changeover day restriction value is 4, which translates to both check-in and check-out are allowed that day.
     * If you send prices and availability for a given date range and not specify changeover day restrictions, default value of 4 (Both) will be set. The default minimum stay setting is 1 night.
     * If you want to remove changeover day restriction, sent value of 4.
     *
     * Number of available units cannot be higher than max units set for a property via Push_PutProperty_RQ/Property/Units.
     * Initial calendar push should include availability for all future dates up to 2 years ahead (we will not save dates further than 3 years in advance). Subsequent updates should only include date ranges for which availability, minimum stay or changeover settings have changed in your system (delta update). Rentals United overwrites only dates you specify in the XML request and leaves other dates intact.
     *
     * DateFrom and DateTo date range values are inclusive. Date ranges provided have to be separable. (periods cannot overlap)
     * Rentals United processes each date range element separately. Altough if one of them is invalid the whole request is aborted.
     * Rentals United will allow updates of availability up to 3 years ahead. In case request contains information about further dates it is aborted.
     *
     * @param int $propertyID
     * @param array $dates
     * @return bool
     */
    public function putAvbUnits(int $propertyID, array $dates): ?bool
    {
        $body = '<Push_PutAvbUnits_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '<MuCalendar PropertyID="' . $propertyID . '">';
        foreach ($dates as $date) {
            $body .= '<Date From="' . $date['From'] . '" To="' . $date['To'] . '">';
            $body .= '<U>' . $date['U'] . '</U>';
            $body .= '<MS>' . $date['MS'] . '</MS>';
            $body .= '<C>' . $date['C'] . '</C>';
            $body .= '</Date>';
        }
        $body .= '</MuCalendar>';
        $body .= '</Push_PutAvbUnits_RQ>';

        $response = $this->sendRequest($body);
        $result = false;
        if ($response->successful() && $this->isRuSuccessful()) {
            $XMLObject = new \SimpleXMLElement($response->body());
            $result = true;
        }

        $this->result = $result;
        return $result;
    }

    /**
     * Use this mehod to upload rates for specified date ranges.
     * Make sure you are familiar with VR price calculation logic before you code this method.
     * Initial push should include rates for all future dates. Subsequent updates should only include date ranges for which rates have changed in your system (delta update). Rentals United overwrites only dates you specify in the XML request and leaves other dates intact.
     * It is not possible to remove rates; it is only possible to overwrite them with new values.
     * Rentals United processes each date range element separately. As a consequence, it may happen that only part of changes sent in your XML Request will be processed and saved into Rentals United. Unprocessed date ranges will be ignored, and appropriate error response sent in Notifs collection. See API response status for details.
     * Dates with no prices set are deemed as not available.
     * Rentals United pricing logic is very robust. Make sure that you and your development team are familiar with VR price calculation logic before coding. This will make your rates consistent with all types of Sales Channels. We are confident our pricing logic covers all rate strategies available on the VR Market. If you believe your model cannot be translated into Rentals United pricing logic, please reach out to Rentals United API Support team.
     * Rentals United supports: seasonal rates, occupancy pricing, extra guest pricing, length of stay pricing (LOS) and allows usage of multiple of these together. Rentals United also supports Full Stay Pricing model.
     * Please note that Rentals United will allow pricing for stays up to 35 nights. If update for length of stay(LOS) or full stay pricing (FSP) will have prices for stays longer than 35 nights - update will get rejected. There is also a limit that pricing inserted to Rentals United cannot be further in future than next 3 years.
     *
     * @param int $propertyID
     * @param array $seasons
     * @param bool $isFullStayPricing
     * @return bool
     */
    public function putPrices(int $propertyID, array $seasons, bool $isFullStayPricing = true): ?bool
    {
        $body = '<Push_PutPrices_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '<Prices PropertyID="' . $propertyID . '">';
        if ($isFullStayPricing) {
            foreach ($seasons as $season) {
                $body .= '<FSPSeason  Date="' . $season['Date'] . '" DefaultPrice="' . $season['DefaultPrice'] . '">';
                $body .= '<FSPRows>';
                foreach ($season['FSPRows'] as $row) {
                    $body .= '<FSPRow  NrOfGuests="' . $row['NrOfGuests'] . '">';
                    $body .= '<Prices>';
                    foreach ($row['Prices'] as $price) {
                        $body .= '<Price NrOfNights="' . $price['NrOfNights'] . '">' . $price['Price'] . '</Price>';
                    }
                    $body .= '</Prices>';
                    $body .= '</FSPRow>';
                }
                $body .= '</FSPRows>';
                $body .= '</FSPSeason>';
            }
        } else {
            foreach ($seasons as $season) {
                $body .= '<Season DateFrom="' . $season['DateFrom'] . '" DateTo="' . $season['DateTo'] . '">';
                if (isset($season['Price'])) {
                    $body .= '<Price>' . $season['Price'] . '</Price>';
                }
                if (isset($season['Extra'])) {
                    $body .= '<Extra>' . $season['Extra'] . '</Extra>';
                }
                if (isset($season['EGPS'])) {
                    $body .= '<EGPS>';
                    foreach ($season['EGPS'] as $item) {
                        $body .= '<EGP ExtraGuests="' . $item['ExtraGuests'] . '">';
                        $body .= '<Price>' . $item['Price'] . '</Price>';
                        $body .= '</EGP>';
                    }
                    $body .= '</EGPS>';
                }
                if (isset($season['LOSS'])) {
                    $body .= '<LOSS>';
                    foreach ($season['LOSS'] as $item) {
                        $body .= '<LOS Nights="' . $item['Nights'] . '">';
                        $body .= '<Price>' . $item['Price'] . '</Price>';
                        if (isset($item['LOSPS'])) {
                            $body .= '<LOSPS>';
                            foreach ($item['LOSPS'] as $item2) {
                                $body .= '<LOSP NrOfGuests="' . $item2['NrOfGuests'] . '">';
                                $body .= '<Price>' . $item2['Price'] . '</Price>';
                                $body .= '</LOSP>';
                            }
                            $body .= '</LOSPS>';
                        }
                        $body .= '</LOS>';
                    }
                    $body .= '</LOSS>';
                }
                $body .= '</Season>';
            }
        }
        $body .= '</Prices>';
        $body .= '</Push_PutPrices_RQ>';

        $response = $this->sendRequest($body);
        $result = false;
        if ($response->successful() && $this->isRuSuccessful()) {
            $XMLObject = new \SimpleXMLElement($response->body());
            $result = true;
        }

        $this->result = $result;
        return $result;
    }

    /**
     * Use this method to upload length of stay discounts for specified date ranges.
     * Length of stay discounts are defined by setting % discount eligible for stays longer than Bigger parameter and shorter than Smaller parameter. Bigger and Smaller parameters can contain the same value, if you want to add the discount for a specific length of stay.
     * Smaller value can be maximum 180 days.
     * Make sure you are familiar with VR price calculation logic before you code this method.
     * Length of stay discounts updates should only include date ranges for which length of stay discounts have changed in your system (delta update). Rentals United overwrites only dates you specify in the XML request and leaves other dates intact.
     * In order to remove discounts, push 0 or empty value for a given date range.
     *
     * @param int $PropertyID
     * @param array $LongStays
     * @return bool
     */
    public function putLongStayDiscounts(int $PropertyID, array $LongStays): ?bool
    {
        $body = '<Push_PutLongStayDiscounts_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '<LongStays PropertyID="' . $PropertyID . '">';
        foreach ($LongStays as $item) {
            $body .= '<LongStay DateFrom="' . $item['DateFrom'] . '" DateTo="' . $item['DateTo'] . '" Bigger="' . $item['Bigger'] . '" Smaller="' . $item['Smaller'] . '">' . ($item['Discount'] ?? $item['LongStay']) . '</LongStay>';
        }
        $body .= '</LongStays>';
        $body .= '</Push_PutLongStayDiscounts_RQ>';

        $response = $this->sendRequest($body);
        $result = false;
        if ($response->successful() && $this->isRuSuccessful()) {
            $XMLObject = new \SimpleXMLElement($response->body());
            $result = true;
        }

        $this->result = $result;
        return $result;
    }

    /**
     * Use this method to upload length of stay discounts for specified date ranges.
     * Length of stay discounts are defined by setting % discount eligible for stays longer than Bigger parameter and shorter than Smaller parameter. Bigger and Smaller parameters can contain the same value, if you want to add the discount for a specific length of stay.
     * Smaller value can be maximum 180 days.
     * Make sure you are familiar with VR price calculation logic before you code this method.
     * Length of stay discounts updates should only include date ranges for which length of stay discounts have changed in your system (delta update). Rentals United overwrites only dates you specify in the XML request and leaves other dates intact.
     * In order to remove discounts, push 0 or empty value for a given date range.
     *
     * @param int $PropertyID
     * @param array $LongStays
     * @return bool
     */
    public function putLastMinuteDiscounts(int $PropertyID, array $LastMinutes): ?bool
    {
        $body = '<Push_PutLastMinuteDiscounts_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '<LastMinutes PropertyID="' . $PropertyID . '">';
        foreach ($LastMinutes as $item) {
            $body .= '<LastMinute DateFrom="' . $item['DateFrom'] . '" DateTo="' . $item['DateTo'] . '" DaysToArrivalFrom="' . $item['DaysToArrivalFrom'] . '" DaysToArrivalTo="' . $item['DaysToArrivalTo'] . '">' . ($item['Discount'] ?? $item['LastMinute'])  . '</LastMinute>';
        }
        $body .= '</LastMinutes>';
        $body .= '</Push_PutLastMinuteDiscounts_RQ>';

        $response = $this->sendRequest($body);
        $result = false;
        if ($response->successful() && $this->isRuSuccessful()) {
            $XMLObject = new \SimpleXMLElement($response->body());
            $result = true;
        }

        $this->result = $result;
        return $result;
    }

    /**
     * This method returns a list of properties in a given location.
     * Use IncludeNLA to identify archived (no longer available) properties.
     *
     * @param int $locationId
     * @param bool $includeNLA Include no longer available properties
     * @return array
     */
    public function listProp(int $locationId = 0, bool $includeNLA = true): ?array
    {
        $body = '<Pull_ListProp_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '<LocationID>' . $locationId . '</LocationID>';
        $body .= '<IncludeNLA>' . ($includeNLA ? 1 : 0) . '</IncludeNLA>';
        $body .= '</Pull_ListProp_RQ>';

        $response = $this->sendRequest($body);
        $result = array();
        if ($response->successful() && $this->isRuSuccessful()) {
            $XMLObject = new \SimpleXMLElement($response->body());
            $collection = $XMLObject->Properties->Property;
            foreach ($collection as $item) {
                $result[] = $this->xmlToArray($item, false);
            }
        }

        $this->result = $result;
        return $result;
    }

    /**
     * An alise method for ListProp
     *
     * @param int $locationId
     * @param bool $includeNLA Include no longer available properties
     * @return array
     */
    public function listProperties(int $locationId, bool $includeNLA = true): ?array
    {
        return $this->listProp($locationId, $includeNLA);
    }

    /**
     * This method returns a list of properties that belong to a specified owner when you use OwnerId parameter.
     * However, if you supply Username parameter instead, response will contain properties of all owners underlying
     * this very Username. Using Username parameter can help you to reduce the number of calls performed.
     * Use IncludeNLA to identify archived (no longer available) properties.
     * It's also worth mentioning that not active properties available to you are still listed in response.
     *
     * @param string $username Username or OwnerID
     * @param bool $includeNLA Include no longer available properties
     * @return array
     */
    public function listOwnerProp(string $username, bool $includeNLA = true): ?array
    {
        $body = '<Pull_ListOwnerProp_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '<Username>' . $username . '</Username>';
        $body .= '<IncludeNLA>' . ($includeNLA ? 1 : 0) . '</IncludeNLA>';
        $body .= '</Pull_ListOwnerProp_RQ>';

        $response = $this->sendRequest($body);
        $result = array();
        if ($response->successful() && $this->isRuSuccessful()) {
            $XMLObject = new \SimpleXMLElement($response->body());
            $collection = $XMLObject->Properties->Property;
            foreach ($collection as $item) {
                $result[] = $this->xmlToArray($item, false);
            }
        }

        $this->result = $result;
        return $result;
    }

    /**
     * An alise method for listOwnerProp
     *
     * @param string $username Username or OwnerID
     * @param bool $includeNLA Include no longer available properties
     * @return array
     */
    public function listOwnerProperties(string $username, bool $includeNLA = true): ?array
    {
        return $this->listOwnerProp($username, $includeNLA);
    }

    /**
     * This method returns a list of properties in a given location.
     * Use IncludeNLA to identify archived (no longer available) properties.
     *
     * @param DateTime $creationFrom
     * @param DateTime $creationTo
     * @param bool $includeNLA Include no longer available properties
     * @return array
     */
    public function listPropByCreationDate(\DateTime $creationFrom, \DateTime $creationTo, bool $includeNLA = true): ?array
    {
        $body = '<Pull_ListPropByCreationDate_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '<CreationFrom>' . $creationFrom->format('Y-m-d') . '</CreationFrom>';
        $body .= '<CreationTo>' . $creationTo->format('Y-m-d') . '</CreationTo>';
        $body .= '<IncludeNLA>' . ($includeNLA ? 1 : 0) . '</IncludeNLA>';
        $body .= '</Pull_ListPropByCreationDate_RQ>';

        $response = $this->sendRequest($body);
        $result = array();
        if ($response->successful() && $this->isRuSuccessful()) {
            $XMLObject = new \SimpleXMLElement($response->body());
            $collection = $XMLObject->Properties->Property;
            foreach ($collection as $item) {
                $result[] = $this->xmlToArray($item, false);
            }
        }

        $this->result = $result;
        return $result;
    }

    /**
     * An alise method for listPropByCreationDate
     *
     * @param DateTime $creationFrom
     * @param DateTime $creationTo
     * @param bool $includeNLA Include no longer available properties
     * @return array
     */
    public function listPropertiesByCreationDate(\DateTime $creationFrom, \DateTime $creationTo, bool $includeNLA = true): ?array
    {
        return $this->listPropByCreationDate($creationFrom, $creationTo, $includeNLA);
    }

    /**
     * This method returns a property's static details.
     * If you are a Sales Channel you will retrieve full property information and create a listing in your system.
     *
     * Please note that not all properties in Rentals United have to have amenities distributed across rooms.
     * Therefore this XML response can have two structures. In case a property has amenities specified for each room,
     * the XML response will include CompositionRoomsAmenities.
     * In case a property has amenities not specified for each room, the XML response will include CompositionRooms.
     *
     * CompositionRoomsAmenities and CompositionRooms elements are never used simultaneously.
     *
     * @param int $propertyID
     * @return array
     */
    public function listSpecProp(int $propertyID): ?array
    {
        $body = '<Pull_ListSpecProp_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '<PropertyID>' . $propertyID . '</PropertyID>';
        $body .= '</Pull_ListSpecProp_RQ>';

        $response = $this->sendRequest($body);
        $result = array();
        if ($response->successful() && $this->isRuSuccessful()) {
            $XMLObject = new \SimpleXMLElement($response->body());

            $result = $this->propertyToArray($XMLObject->Property);

            // $_result = $this->xmlToArray($XMLObject->Property, false);
            // $result = $_result['Property'];
            // $result['Currency'] = $_result['Currency'];
        }

        $this->result = $result;
        return $result;
    }

    /**
     * An alise method for listSpecProp
     *
     * @param int $propertyID
     * @return array
     */
    public function getProperty(int $propertyID): ?array
    {
        return $this->listSpecProp($propertyID);
    }

    /**
     * This method returns a list of properties and their status on the connected Sales Channels.
     *
     * @param list $propertyIDs one property id per parameter
     * @return array
     */
    public function getPropertyExternalListing(...$propertyIDs): ?array
    {
        $body = '<Pull_GetPropertyExternalListing_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '<Properties>';
        foreach ($propertyIDs as $propertyID) {
            $body .= '<PropertyID>' . $propertyID . '</PropertyID>';
        }
        $body .= '</Properties>';
        $body .= '</Pull_GetPropertyExternalListing_RQ>';

        $response = $this->sendRequest($body);
        $result = array();
        if ($response->successful() && $this->isRuSuccessful()) {
            $XMLObject = new \SimpleXMLElement($response->body());
            $collection = $XMLObject->Properties->Property;
            foreach ($collection as $item) {
                $result[] = $this->xmlToArray($item, false);
            }
        }

        $this->result = $result;
        return $result;
    }

    /**
     * This method returns blocked (unavailable) periods in properties' availability calendars.
     * For multiunit type properties, availability is blocked when 0 units are available.
     *
     * Both DateFrom and DateTo are inclusive. This method does not trim response periods based on the XML request.
     * You can receive a block for a period longer than you requested.
     *
     * @param int $locationID
     * @param DateTime $dateFrom
     * @param DateTime $dateTo
     * @param bool $includeNLA Include no longer available properties
     * @return array
     */
    public function listPropertiesBlocks(int $locationID, \DateTime $dateFrom, \DateTime $dateTo, bool $includeNLA = true): ?array
    {
        $body = '<Pull_ListPropertiesBlocks_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '<LocationID>' . $locationID . '</LocationID>';
        $body .= '<DateFrom>' . $dateFrom->format('Y-m-d') . '</DateFrom>';
        $body .= '<DateTo>' . $dateTo->format('Y-m-d') . '</DateTo>';
        $body .= '<IncludeNLA>' . ($includeNLA ? 1 : 0) . '</IncludeNLA>';
        $body .= '</Pull_ListPropertiesBlocks_RQ>';

        $response = $this->sendRequest($body);
        $result = array();
        if ($response->successful() && $this->isRuSuccessful()) {
            $XMLObject = new \SimpleXMLElement($response->body());
            $collection = $XMLObject->Properties->Property;
            foreach ($collection as $item) {
                $result[] = $this->xmlToArray($item, false);
            }
        }

        $this->result = $result;
        return $result;
    }

    /**
     * This method returns night-by-night calendar including number of available units, minimum stay and changeover day settings.
     *
     * @param int $propertyId
     * @param DateTime $dateFrom
     * @param DateTime $dateTo
     * @return array
     */
    public function listPropertyAvailabilityCalendar(int $propertyId, \DateTime $dateFrom, \DateTime $dateTo): ?array
    {
        $body = '<Pull_ListPropertyAvailabilityCalendar_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '<PropertyID>' . $propertyId . '</PropertyID>';
        $body .= '<DateFrom>' . $dateFrom->format('Y-m-d') . '</DateFrom>';
        $body .= '<DateTo>' . $dateTo->format('Y-m-d') . '</DateTo>';
        $body .= '</Pull_ListPropertyAvailabilityCalendar_RQ>';

        $response = $this->sendRequest($body);
        $result = array();
        if ($response->successful() && $this->isRuSuccessful()) {
            $XMLObject = new \SimpleXMLElement($response->body());
            $result = $this->xmlToArray($XMLObject->PropertyCalendar, false);
        }

        $this->result = $result;
        return $result;
    }

    /**
     * This method returns properties' minimum stay settings in a date periods manner.
     *
     * Minimum stay dates periods are separate to seasonal rates dates periods.
     * Date periods with a default minimum stay of 1 night may not be returned in the XML Response.
     * Remember to set up 1 night minimum stay for all dates which are not returned in the XML Response.
     *
     * @param int $propertyId
     * @param DateTime $dateFrom
     * @param DateTime $dateTo
     * @return array
     */
    public function listPropertyMinStay(int $propertyId, \DateTime $dateFrom, \DateTime $dateTo): ?array
    {
        $body = '<Pull_ListPropertyMinStay_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '<PropertyID>' . $propertyId . '</PropertyID>';
        $body .= '<DateFrom>' . $dateFrom->format('Y-m-d') . '</DateFrom>';
        $body .= '<DateTo>' . $dateTo->format('Y-m-d') . '</DateTo>';
        $body .= '</Pull_ListPropertyMinStay_RQ>';

        $response = $this->sendRequest($body);
        $result = array();
        if ($response->successful() && $this->isRuSuccessful()) {
            $XMLObject = new \SimpleXMLElement($response->body());
            $result = $this->xmlToArray($XMLObject->PropertyMinStay, false);
        }

        $this->result = $result;
        return $result;
    }

    /**
     * This method returns seasonal rates configuration for a property.
     * Additional fees and discounts are covered separately and are not included in this API method.
     *
     * Note that setting pricingModel parameter to 0 or 1 will always return Standard or Full Stay Pricing.
     * That could result in price model conversion unless prices are inserted using the same model.
     * With pricingModel set to 2, ListPropertyPrices request will return prices in the same pricing model in which they were inserted.
     * That parameter value won't result in price model conversion, but you will not be able to determine in advance whether
     * prices will be returned using Standard or Full Stay Pricing model.
     * Omitting this parameter in request will result in getting response in Standard pricing (as if 0 would be inserted).
     *
     * @param int $propertyId
     * @param DateTime $dateFrom
     * @param DateTime $dateTo
     * @param int $pricingModel Specifies in what pricing model prices will be returned in response. Possible values: 0 - Standard pricing (daily/los pricing), 1 - Full Stay Pricing, 2 - Pricing model set by PutPrices request
     * @return array
     */
    public function listPropertyPrices(int $propertyId, \DateTime $dateFrom, \DateTime $dateTo, int $pricingModel = 0): ?array
    {
        $body = '<Pull_ListPropertyPrices_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '<PropertyID>' . $propertyId . '</PropertyID>';
        $body .= '<DateFrom>' . $dateFrom->format('Y-m-d') . '</DateFrom>';
        $body .= '<DateTo>' . $dateTo->format('Y-m-d') . '</DateTo>';
        $body .= '<PricingModelMode>' . $pricingModel . '</PricingModelMode>';
        $body .= '</Pull_ListPropertyPrices_RQ>';

        $response = $this->sendRequest($body);
        $result = array();
        if ($response->successful() && $this->isRuSuccessful()) {
            $XMLObject = new \SimpleXMLElement($response->body());
            $result = array(
                'Seasons' => array(),
                'FSPSeasons' => array(),
            );

            $seasons = $XMLObject->Prices->Season;
            foreach ($seasons as $item) {
                $result['Seasons'][] = $this->xmlToArray($item, false);
            }

            $FSPSeasons = $XMLObject->Prices->FSPSeasons->FSPSeason;
            foreach ($FSPSeasons as $Season) {
                $season = $this->xmlLeafToArray($Season);
                $season['FSPSeason'] = array();
                foreach ($Season->FSPRows->FSPRow as $Row) {
                    $row = $this->xmlLeafToArray($Row);
                    $row['FSPRow'] = array();
                    foreach ($Row->Prices->Price as $Price) {
                        $row['FSPRow'][] = $this->xmlToArray($Price);
                    }

                    $season['FSPSeason'][] = $row;
                }

                $result['FSPSeasons'][] = $season;
            }
        }

        $this->result = $result;
        return $result;
    }

    /**
     * This method returns a final price calculation for a given stay.
     * You will receive an empty response when the property is either not available or there are no seasonal rates set.
     * In both cases a reservation cannot be created.
     *
     * @param int $propertyId
     * @param DateTime $dateFrom
     * @param DateTime $dateTo
     * @return array
     */
    public function getPropertyAvbPrice(int $propertyId, \DateTime $dateFrom, \DateTime $dateTo): ?array
    {
        $body = '<Pull_GetPropertyAvbPrice_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '<PropertyID>' . $propertyId . '</PropertyID>';
        $body .= '<DateFrom>' . $dateFrom->format('Y-m-d') . '</DateFrom>';
        $body .= '<DateTo>' . $dateTo->format('Y-m-d') . '</DateTo>';
        $body .= '</Pull_GetPropertyAvbPrice_RQ>';

        $response = $this->sendRequest($body);
        $result = array();
        if ($response->successful() && $this->isRuSuccessful()) {
            $XMLObject = new \SimpleXMLElement($response->body());
            $result = $this->xmlToArray($XMLObject->PropertyPrices, false);
        }

        $this->result = $result;
        return $result;
    }

    /**
     * This method returns length of stay and last-minute discounts settings.
     *
     * @param int $propertyId
     * @return array
     */
    public function listPropertyDiscounts(int $propertyId): ?array
    {
        $body = '<Pull_ListPropertyDiscounts_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '<PropertyID>' . $propertyId . '</PropertyID>';
        $body .= '</Pull_ListPropertyDiscounts_RQ>';

        $response = $this->sendRequest($body);
        $result = array();
        if ($response->successful() && $this->isRuSuccessful()) {
            $XMLObject = new \SimpleXMLElement($response->body());
            $result = $this->xmlToArray($XMLObject->Discounts, false);
        }

        $this->result = $result;
        return $result;
    }

    /**
     * This method returns changeover days setup for a given property in a given time frame.
     * The XML response node Changeover returns a string composed of one digit for each day.
     *
     * @param int $propertyId
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return array
     */
    public function getChangeoverDays(int $propertyId, \DateTime $startDate, \DateTime $endDate): ?array
    {
        $body = '<Pull_GetChangeoverDays_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '<PropertyID>' . $propertyId . '</PropertyID>';
        $body .= '<StartDate>' . $startDate->format('Y-m-d') . '</StartDate>';
        $body .= '<EndDate>' . $endDate->format('Y-m-d') . '</EndDate>';
        $body .= '</Pull_GetChangeoverDays_RQ>';

        $response = $this->sendRequest($body);
        $result = array();
        if ($response->successful() && $this->isRuSuccessful()) {
            $XMLObject = new \SimpleXMLElement($response->body());
            $Changeover = (string) $XMLObject->Changeover;
            $result['Changeover'] = $Changeover;

            $interval = \DateInterval::createFromDateString('1 day');
            $period = new \DatePeriod($startDate, $interval, $endDate);

            $list = str_split($Changeover);
            foreach ($period as $k => $dt) {
                $result['Changeovers'][] = array('Date' => $dt->format('Y-m-d'), 'Changeover' => (int) $list[$k]);
            }
        }

        $this->result = $result;
        return $result;
    }

    /**
     * Delta methods
     * These methods have been created in order to provide you with information on last change within property data in Rentals United. There are five types of changes:
     *
     * Static property data
     * Pricing
     * Availability
     * Images
     * Description
     * Use these methods to quickly identify latest changes in Rentals United platform to save resources on your side.
     */

    /**
     * This method provides the exact date and time (UTC) of the last change for each data type for an individual property.
     *
     * @param int $propertyId
     * @return array
     */
    public function listPropertyChangeLog(int $propertyId): ?array
    {
        $body = '<Pull_ListPropertyChangeLog_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '<PropertyID>' . $propertyId . '</PropertyID>';
        $body .= '</Pull_ListPropertyChangeLog_RQ>';

        $response = $this->sendRequest($body);
        $result = array();
        if ($response->successful() && $this->isRuSuccessful()) {
            $XMLObject = new \SimpleXMLElement($response->body());
            $result = $this->xmlToArray($XMLObject->ChangeLog, false);
        }

        $this->result = $result;
        return $result;
    }

    /**
     * This method provides the exact date and time (UTC) of the last change for each data type for multiple properties.
     *
     * @param int  ...$propertyIds Multipal properties id, one id per parameter
     * @return array
     */
    public function listPropertiesChangeLog(int ...$propertyIds): ?array
    {
        $body = '<Pull_ListPropertiesChangeLog_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '<PropertyIDs>';
        foreach ($propertyIds as $propertyID) {
            $body .= '<PropertyID>' . $propertyID . '</PropertyID>';
        }
        $body .= '</PropertyIDs>';
        $body .= '</Pull_ListPropertiesChangeLog_RQ>';

        $response = $this->sendRequest($body);
        $result = array();
        if ($response->successful() && $this->isRuSuccessful()) {
            $XMLObject = new \SimpleXMLElement($response->body());
            $collection = $XMLObject->ChangeLogs->ChangeLog;
            foreach ($collection as $item) {
                $result[] = $this->xmlToArray($item, false);
            }
        }

        $this->result = $result;
        return $result;
    }

    /**
     * This method returns the dates for which price in Rentals United has changed since a timestamp provided in the request.
     * Price changes for past dates are never returned
     *
     * @param int $propertyId
     * @return array
     */
    public function listPropertyPriceChanges(int $propertyId): ?array
    {
        $body = '<Pull_ListPropertyPriceChanges_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '<PropertyID>' . $propertyId . '</PropertyID>';
        $body .= '</Pull_ListPropertyPriceChanges_RQ>';

        $response = $this->sendRequest($body);
        $result = array();
        if ($response->successful() && $this->isRuSuccessful()) {
            $XMLObject = new \SimpleXMLElement($response->body());
            $collection = $XMLObject->PriceChanges->Day;
            foreach ($collection as $item) {
                $result[] = $this->xmlLeafToArray($item);
            }
        }

        $this->result = $result;
        return $result;
    }

    /**
     * This method returns the dates for which availability in Rentals United has changed since a timestamp provided in the request.
     * Availability changes for past dates are never returned
     *
     * @param int $propertyId
     * @return array
     */
    public function listPropertyAvbChanges(int $propertyId): ?array
    {
        $body = '<Pull_ListPropertyAvbChanges_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '<PropertyID>' . $propertyId . '</PropertyID>';
        $body .= '</Pull_ListPropertyAvbChanges_RQ>';

        $response = $this->sendRequest($body);
        $result = array();
        if ($response->successful() && $this->isRuSuccessful()) {
            $XMLObject = new \SimpleXMLElement($response->body());
            $collection = $XMLObject->AvbChanges->Day;
            foreach ($collection as $item) {
                $result[] = $this->xmlLeafToArray($item);
            }
        }

        $this->result = $result;
        return $result;
    }

    /**
     * Convert property xml to array
     * 
     * @param SimpleXMLElement $propXml
     * @return array
     */
    public function propertyToArray(\SimpleXMLElement $propXml): array
    {
        $resp = $this->xmlToArray($propXml, false);
        $property = $resp['Property'];
        $property['Currency'] = $resp['Currency'];

        $HowToArrive = [];
        if ($propXml->ArrivalInstructions->HowToArrive->Text) {
            foreach ($propXml->ArrivalInstructions->HowToArrive->Text as $text) {
                $HowToArrive[] = $this->xmlToArray($text, false);
            }
        }
        $property['ArrivalInstructions']['HowToArrive'] = $HowToArrive;

        $PickupService = [];
        if ($propXml->ArrivalInstructions->PickupService->Text) {
            foreach ($propXml->ArrivalInstructions->PickupService->Text as $text) {
                $PickupService[] = $this->xmlToArray($text, false);
            }
        }
        $property['ArrivalInstructions']['PickupService'] = $PickupService;

        $CancellationPolicies = [];
        if ($propXml->CancellationPolicies->CancellationPolicy) {
            if ($propXml->CancellationPolicies->CancellationPolicy) {
                foreach ($propXml->CancellationPolicies->CancellationPolicy as $item) {
                    $CancellationPolicies[] = $this->xmlToArray($item, false);
                }
            }
        }
        $property['CancellationPolicies'] = $CancellationPolicies;

        $ChargeProfiles = [];
        if ($propXml->ChargeProfiles->ChargeProfile) {
            foreach ($propXml->ChargeProfiles->ChargeProfile as $item) {
                $ChargeProfiles[] = $this->xmlToArray($item, false);
            }
        }
        $property['ChargeProfiles'] = $ChargeProfiles;

        $Descriptions = [];
        if ($propXml->Descriptions->Description) {
            foreach ($propXml->Descriptions->Description as $description) {
                $_desc = $this->xmlToArray($description, false);
                $desc['LanguageID'] = $_desc['LanguageID'] ?? null;
                $desc['Text'] = $_desc['Description']['Text'] ?? null;
                $desc['Image'] = $_desc['Description']['Image'] ?? null;
                $Descriptions[] = $desc;
            }
        }
        $property['Descriptions'] = $Descriptions;

        $CompositionRoomsAmenities = [];
        if ($propXml->CompositionRoomsAmenities->CompositionRoomAmenities) {
            foreach ($propXml->CompositionRoomsAmenities->CompositionRoomAmenities as $item) {
                $room = $this->xmlToArray($item, false);

                $amenities = [];
                foreach ($item->Amenities->Amenity as $itm) {
                    $amenities[] = $this->xmlToArray($itm, false);
                }
                $room['CompositionRoomAmenities'] = $amenities;

                $CompositionRoomsAmenities[] = $room;
            }
        }
        $property['CompositionRoomsAmenities'] = $CompositionRoomsAmenities;

        return $property;
    }
}
