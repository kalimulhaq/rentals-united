<?php

namespace Kalimulhaq\RentalsUnited;

/**
 * Class Dictionary
 */
class Dictionary extends RentalsUnited
{
    /**
     * Rentals United API will provide a response including:
     * unique response identifier (ResponseID). You will use it to troubleshoot when communication with Rentals United API Support. Make sure that you log it
     * response StatusInfo providing you with feedback on the performed transaction
     *
     * @return array
     */
    public function listStatuses(): ?array
    {
        $body = '<Pull_ListStatuses_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '</Pull_ListStatuses_RQ>';

        $response = $this->sendRequest($body);
        $result = array();
        if ($response->successful() && $this->isRuSuccessful()) {
            $XMLObject = new \SimpleXMLElement($response->body());
            $statuses = $XMLObject->Statuses->StatusInfo;
            foreach ($statuses as $item) {
                $result[] = $this->xmlLeafToArray($item);
            }
        }

        $this->result = $result;
        return $result;
    }

    /**
     * This method returns the list of available property types by number of bedrooms.
     *
     * @return array
     */
    public function listPropTypes(): ?array
    {
        $body = '<Pull_ListPropTypes_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '</Pull_ListPropTypes_RQ>';

        $response = $this->sendRequest($body);
        $result = array();
        if ($response->successful() && $this->isRuSuccessful()) {
            $XMLObject = new \SimpleXMLElement($response->body());
            $propertyTypes = $XMLObject->PropertyTypes->PropertyType;
            foreach ($propertyTypes as $item) {
                $result[] = $this->xmlLeafToArray($item);
            }
        }

        $this->result = $result;
        return $result;
    }

    /**
     * This method returns the list of available property types as classified by Open Travel Alliance Standard
     *
     * @return array
     */
    public function listOTAPropTypes(): ?array
    {
        $body = '<Pull_ListOTAPropTypes_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '</Pull_ListOTAPropTypes_RQ>';

        $response = $this->sendRequest($body);
        $result = array();
        if ($response->successful() && $this->isRuSuccessful()) {
            $XMLObject = new \SimpleXMLElement($response->body());
            $propertyTypes = $XMLObject->PropertyTypes->PropertyType;
            foreach ($propertyTypes as $item) {
                $result[] = $this->xmlLeafToArray($item);
            }
        }

        $this->result = $result;
        return $result;
    }

    /**
     * This method returns Rentals United location hierarchy.
     *
     * @return array
     */
    public function listLocationTypes(): ?array
    {
        $body = '<Pull_ListLocationTypes_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '</Pull_ListLocationTypes_RQ>';

        $response = $this->sendRequest($body);
        $result = array();
        if ($response->successful() && $this->isRuSuccessful()) {
            $XMLObject = new \SimpleXMLElement($response->body());
            $collection = $XMLObject->LocationTypes->LocationType;
            foreach ($collection as $item) {
                $result[] = $this->xmlLeafToArray($item);
            }
        }

        $this->result = $result;
        return $result;
    }

    /**
     * This method returns entire dictionary of locations supported by Rentals United.
     * If you are a property provider you may prefer we recommend to use Pull_GetLocationByCoordinates_RQ. If you don't retrieve location you expected then you should call Pull_GetLocationsListByName_RQ to choose appropriate location. We believe implementing these two methods will be handy and always up to date compared to mapping entire dictionary of locations.
     * If you are a Sales Channel or VR Service Provider pulling properties from Rentals United, you need to map the location to your system in order to retrieve properties by location.
     *
     * @return array
     */
    public function listLocations(): ?array
    {
        $body = '<Pull_ListLocations_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '</Pull_ListLocations_RQ>';

        $response = $this->sendRequest($body);
        $result = array();
        if ($response->successful() && $this->isRuSuccessful()) {
            $XMLObject = new \SimpleXMLElement($response->body());
            $collection = $XMLObject->Locations->Location;
            foreach ($collection as $item) {
                $result[] = $this->xmlLeafToArray($item);
            }
        }

        $this->result = $result;
        return $result;
    }

    /**
     * This method returns the location details in a parent-child way. LocationID of any LocationTypeID can be requested.
     * If you are a Sales Channel or VR Service Provider pulling properties from Rentals United, you can use this method to pull a single location details in case of not recognized property location.
     *
     * @param int $locationId Rentals United Location ID
     * @return array
     */
    public function getLocationDetails(int $locationId): ?array
    {
        $body = '<Pull_GetLocationDetails_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '<LocationID>' . $locationId . '</LocationID>';
        $body .= '</Pull_GetLocationDetails_RQ>';

        $response = $this->sendRequest($body);
        $result = array();
        if ($response->successful() && $this->isRuSuccessful()) {
            $XMLObject = new \SimpleXMLElement($response->body());
            $collection = $XMLObject->Locations->Location;
            foreach ($collection as $item) {
                $result[] = $this->xmlLeafToArray($item);
            }
        }

        $this->result = $result;
        return $result;
    }

    /**
     * This method is deprecated. See Pull_GetLocationsListByName_RQ
     * This method returns a Rentals United LocationID based on the location name.
     * If you are a property provider pushing properties to Rentals United, you can check a specific Rentals United LocationID based on a location name.
     * It may be easier for you to get the location name and Rentals United LocationID by geo-coordinates of a property. Please review Pull_GetLocationByCoordinates_RQ API method for details.
     *
     * @param string $locationName Location Name
     * @return int
     */
    public function getLocationByName(string $locationName): ?int
    {
        $body = '<Pull_GetLocationByName_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '<LocationName>' . $locationName . '</LocationName>';
        $body .= '</Pull_GetLocationByName_RQ>';

        $response = $this->sendRequest($body);
        $result = array();
        if ($response->successful()) {
            $XMLObject = new \SimpleXMLElement($response->body());
            $result = (string) $XMLObject->LocationID;
        }

        $this->result = $result;
        return $result;
    }

    /**
     * This method returns a list of Rentals United locations details, based on the location name and country name (optional). Returned locations data contains: Currency, LocationID, CountryLocationID and LocationName.
     * The Currency field in returned data will be filled only if the provided location name is either a city or a district of a city. Otherwise, the field will be left empty. An empty Currency field also means that in such location an apartment cannot be placed.
     * If you are a property provider pushing properties to Rentals United, you can check a specific Rentals United data set associated with a location, based on a location name and country name (optional).
     * It may be easier for you to get the location name and Rentals United LocationID by geo-coordinates of a property. Please review Pull_GetLocationByCoordinates_RQ API method for details.
     *
     * @param  $countryName Country Name
     * @param  $locationName Location Name
     * @return array
     */
    public function getLocationsListByName(string $countryName, string $locationName): ?array
    {
        $body = '<Pull_GetLocationsListByName_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '<CountryName>' . $countryName . '</CountryName>';
        $body .= '<LocationName>' . $locationName . '</LocationName>';
        $body .= '</Pull_GetLocationsListByName_RQ>';

        $response = $this->sendRequest($body);
        $result = array();
        if ($response->successful() && $this->isRuSuccessful()) {
            $XMLObject = new \SimpleXMLElement($response->body());
            $collection = $XMLObject->Locations->LocationWithCurrency;
            foreach ($collection as $item) {
                $result[] = $this->xmlLeafToArray($item);
            }
        }

        $this->result = $result;
        return $result;
    }

    /**
     * This method returns Rentals United location details based on the geo-coordinates provided on the XML request.
     *
     * @param float $latitude
     * @param float $longitude
     * @return array
     */
    public function getLocationByCoordinates(float $latitude, float $longitude): ?array
    {
        $body = '<Pull_GetLocationByCoordinates_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '<Latitude>' . $latitude . '</Latitude>';
        $body .= '<Longitude>' . $longitude . '</Longitude>';
        $body .= '</Pull_GetLocationByCoordinates_RQ>';

        $response = $this->sendRequest($body);
        $result = array();
        if ($response->successful() && $this->isRuSuccessful()) {
            $XMLObject = new \SimpleXMLElement($response->body());
            $result[] = $this->xmlLeafToArray($XMLObject->Location);
        }

        $this->result = $result;
        return $result;
    }

    /**
     * This method has been designed for distribution partners (Sales Channels and VR Service Providers).
     * It returns a breakdown of number of active properties per LocationID available for you to pull.
     *
     * @return array
     */
    public function listCitiesProps(): ?array
    {
        $body = '<Pull_ListCitiesProps_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '</Pull_ListCitiesProps_RQ>';

        $response = $this->sendRequest($body);
        $result = array();
        if ($response->successful() && $this->isRuSuccessful()) {
            $XMLObject = new \SimpleXMLElement($response->body());
            $collection = $XMLObject->CitiesProps->CityProps;
            foreach ($collection as $item) {
                $result[] = $this->xmlLeafToArray($item);
            }
        }

        $this->result = $result;
        return $result;
    }

    /**
     * Currency displayed in Rentals United is based on the city the property is located in (LocationID of LocationTypeID = 4 is used)
     * Currency per property is currently not supported. All properties in a given city across the whole Rentals United client base work with the same currency e.g. all properties in London (UK) work with GBP.
     * It is crucial to get this right. If you work with a currency different than Rentals United in a given city, you have to perform currency exchange before pushing rates into Rentals United. Rentals United has a built-in spot market rate currency exchange system and performs appropriate conversions when pushing rates to the Sales Channels.
     * Example: UK resident leaving in London manages a property in Spain. Given his clients are mostly UK based, his rates are in GBP. He works in GBP with Sales Channels already but wants to join Rentals United. Rentals United supports EUR for all cities in Spain. UK resident will convert GBP to EUR before pushing rates to Rentals United and configure Rentals United Sales Channel connection to GBP. Rentals United will perform currency conversion from EUR to GBP when sending rates to the Sales Channels.
     *
     * @return array
     */
    public function listCurrenciesWithCities(): ?array
    {
        $body = '<Pull_ListCurrenciesWithCities_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '</Pull_ListCurrenciesWithCities_RQ>';

        $response = $this->sendRequest($body);
        $result = array();
        if ($response->successful()) {
            $XMLObject = new \SimpleXMLElement($response->body());
            $collection = $XMLObject->Currencies->Currency;
            foreach ($collection as $item) {
                $curr = $this->xmlAttrsToArray($item);
                $curr['Locations'] = array();
                $Locations = $item->Locations->LocationID;
                foreach ($Locations as $loc) {
                    $curr['Locations'][] = $this->xmlLeafToArray($loc);
                }
                $result[] = $curr;
            }
        }

        $this->result = $result;
        return $result;
    }

    /**
     * Rentals United destinations are POIs to which you can define distances from the property.
     *
     * @return array
     */
    public function listDestinations(): ?array
    {
        $body = '<Pull_ListDestinations_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '</Pull_ListDestinations_RQ>';

        $response = $this->sendRequest($body);
        $result = array();
        if ($response->successful() && $this->isRuSuccessful()) {
            $XMLObject = new \SimpleXMLElement($response->body());
            $collection = $XMLObject->Destinations->Destination;
            foreach ($collection as $item) {
                $result[] = $this->xmlLeafToArray($item);
            }
        }

        $this->result = $result;
        return $result;
    }

    /**
     * This method returns available distance unit types from property to a POI (minutes, km, etc.).
     *
     * @return array
     */
    public function listDistanceUnits(): ?array
    {
        $body = '<Pull_ListDistanceUnits_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '</Pull_ListDistanceUnits_RQ>';

        $response = $this->sendRequest($body);
        $result = array();
        if ($response->successful() && $this->isRuSuccessful()) {
            $XMLObject = new \SimpleXMLElement($response->body());
            $collection = $XMLObject->DistanceUnits->DistanceUnit;
            foreach ($collection as $item) {
                $result[] = $this->xmlLeafToArray($item);
            }
        }

        $this->result = $result;
        return $result;
    }

    /**
     * This method returns a list of room types available for a property.
     *
     * @return array
     */
    public function listCompositionRooms(): ?array
    {
        $body = '<Pull_ListCompositionRooms_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '</Pull_ListCompositionRooms_RQ>';

        $response = $this->sendRequest($body);
        $result = array();
        if ($response->successful() && $this->isRuSuccessful()) {
            $XMLObject = new \SimpleXMLElement($response->body());
            $collection = $XMLObject->CompositionRooms->CompositionRoom;
            foreach ($collection as $item) {
                $result[] = $this->xmlLeafToArray($item);
            }
        }

        $this->result = $result;
        return $result;
    }

    /**
     * This method returns a list of amenities available for a property.
     *
     * @return array
     */
    public function listAmenities(): ?array
    {
        $body = '<Pull_ListAmenities_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '</Pull_ListAmenities_RQ>';

        $response = $this->sendRequest($body);
        $result = array();
        if ($response->successful() && $this->isRuSuccessful()) {
            $XMLObject = new \SimpleXMLElement($response->body());
            $collection = $XMLObject->Amenities->Amenity;
            foreach ($collection as $item) {
                $result[] = $this->xmlLeafToArray($item);
            }
        }

        $this->result = $result;
        return $result;
    }

    /**
     * This method returns a list of amenities available for a given room type.
     *
     * @return array
     */
    public function listAmenitiesAvailableForRooms(): ?array
    {
        $body = '<Pull_ListAmenitiesAvailableForRooms_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '</Pull_ListAmenitiesAvailableForRooms_RQ>';

        $response = $this->sendRequest($body);
        $result = array();
        if ($response->successful() && $this->isRuSuccessful()) {
            $XMLObject = new \SimpleXMLElement($response->body());
            $collection = $XMLObject->AmenitiesAvailableForRooms->AmenitiesAvailableForRoom;
            foreach ($collection as $item) {
                $data = $this->xmlLeafToArray($item);
                $data['AmenitiesAvailableForRoom'] = array();
                foreach ($item->Amenity as $val) {
                    $data['AmenitiesAvailableForRoom'][] = $this->xmlLeafToArray($val);
                }
                $result[] = $data;
            }
        }

        $this->result = $result;
        return $result;
    }

    /**
     * This method returns a list of image types available for a property.
     *
     * @return array
     */
    public function listImageTypes(): ?array
    {
        $body = '<Pull_ListImageTypes_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '</Pull_ListImageTypes_RQ>';

        $response = $this->sendRequest($body);
        $result = array();
        if ($response->successful() && $this->isRuSuccessful()) {
            $XMLObject = new \SimpleXMLElement($response->body());
            $collection = $XMLObject->ImageTypes->ImageType;
            foreach ($collection as $item) {
                $result[] = $this->xmlLeafToArray($item);
            }
        }

        $this->result = $result;
        return $result;
    }

    /**
     * This method returns a list of payment methods available for a property.
     *
     * @return array
     */
    public function listPaymentMethods(): ?array
    {
        $body = '<Pull_ListPaymentMethods_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '</Pull_ListPaymentMethods_RQ>';

        $response = $this->sendRequest($body);
        $result = array();
        if ($response->successful() && $this->isRuSuccessful()) {
            $XMLObject = new \SimpleXMLElement($response->body());
            $collection = $XMLObject->PaymentMethods->PaymentMethod;
            foreach ($collection as $item) {
                $result[] = $this->xmlLeafToArray($item);
            }
        }

        $this->result = $result;
        return $result;
    }

    /**
     * This method returns a list of reservation statuses available in Rentals United.
     * Some of the reservation statuses block, some of them do not block a property availability.
     * Confirmed - confirmed reservation, block availability
     * Cancelled - cancelled reservations, does not block availability
     * Modified - confirmed reservation that has been changed, blocks availability.
     *
     * @return array
     */
    public function listReservationStatuses(): ?array
    {
        $body = '<Pull_ListReservationStatuses_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '</Pull_ListReservationStatuses_RQ>';

        $response = $this->sendRequest($body);
        $result = array();
        if ($response->successful() && $this->isRuSuccessful()) {
            $XMLObject = new \SimpleXMLElement($response->body());
            $collection = $XMLObject->ReservationStatuses->ReservationStatus;
            foreach ($collection as $item) {
                $result[] = $this->xmlLeafToArray($item);
            }
        }

        $this->result = $result;
        return $result;
    }

    /**
     * This method returns a list of deposit discriminator types. These types are used when specifying:
     * Prepayment - Push_PutProperty_RQ/Property/Deposit
     * Security deposit - Push_PutProperty_RQ/Property/Deposit
     *
     * @return array
     */
    public function listDepositTypes(): ?array
    {
        $body = '<Pull_ListDepositTypes_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '</Pull_ListDepositTypes_RQ>';

        $response = $this->sendRequest($body);
        $result = array();
        if ($response->successful() && $this->isRuSuccessful()) {
            $XMLObject = new \SimpleXMLElement($response->body());
            $collection = $XMLObject->DepositTypes->DepositType;
            foreach ($collection as $item) {
                $result[] = $this->xmlLeafToArray($item);
            }
        }

        $this->result = $result;
        return $result;
    }

    /**
     * This method returns a list of languages supported by Rentals United.
     *
     * @return array
     */
    public function listLanguages(): ?array
    {
        $body = '<Pull_ListLanguages_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '</Pull_ListLanguages_RQ>';

        $response = $this->sendRequest($body);
        $result = array();
        if ($response->successful() && $this->isRuSuccessful()) {
            $XMLObject = new \SimpleXMLElement($response->body());
            $collection = $XMLObject->Languages->Language;
            foreach ($collection as $item) {
                $result[] = $this->xmlLeafToArray($item);
            }
        }

        $this->result = $result;
        return $result;
    }

    /**
     * This method returns a list of property status on the Sales Channels.
     * If you are a PMS, you can pull a property status on connected Sales Channels and display it in your software to a client.
     * If you are a Sales Channel, you can push a property status to inform a property provider of a current property status on the Sales Channel.
     * Status description:
     * online – property is online/live/bookable on the Sales Channel
     * offline – property is not online/not live/not bookable on the Sales Channel
     * missing data – property does not meet a Minimum Content Quality of a Sales Channel
     * requested publishing – property provider has activated a property connection to a Sales Channel
     * requested removal – property provider has deactivated a property connection to a Sales Channel
     * disconnected – the property has been disconnected from a Sales Channel
     * requested update – property provider has requested a property update on a Sales Channel
     * requested deactivation – property provider has requested a property to be disconnected from a Sales Channel
     *
     * @return array
     */
    public function listPropExtStatuses(): ?array
    {
        $body = '<Pull_ListPropExtStatuses_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '</Pull_ListPropExtStatuses_RQ>';

        $response = $this->sendRequest($body);
        $result = array();
        if ($response->successful() && $this->isRuSuccessful()) {
            $XMLObject = new \SimpleXMLElement($response->body());
            $collection = $XMLObject->PropertyExternalStatuses->Status;
            foreach ($collection as $item) {
                $result[] = $this->xmlLeafToArray($item);
            }
        }

        $this->result = $result;
        return $result;
    }

    /**
     * This method returns a list of available changeover day types.
     *
     * @return array
     */
    public function listChangeoverTypes(): ?array
    {
        $body = '<Pull_ListChangeoverTypes_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '</Pull_ListChangeoverTypes_RQ>';

        $response = $this->sendRequest($body);
        $result = array();
        if ($response->successful() && $this->isRuSuccessful()) {
            $XMLObject = new \SimpleXMLElement($response->body());
            $collection = $XMLObject->ChangeoverTypes->ChangeoverType;
            foreach ($collection as $item) {
                $result[] = $this->xmlLeafToArray($item);
            }
        }

        $this->result = $result;
        return $result;
    }

    /**
     * This method returns a list of additional fee types (tax or an extra cost) so that you can properly classify extra fees.
     *
     * @return array
     */
    public function listAdditionalFeeKinds(): ?array
    {
        $body = '<Pull_ListAdditionalFeeKinds_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '</Pull_ListAdditionalFeeKinds_RQ>';

        $response = $this->sendRequest($body);
        $result = array();
        if ($response->successful() && $this->isRuSuccessful()) {
            $XMLObject = new \SimpleXMLElement($response->body());
            $collection = $XMLObject->AdditionalFeeKinds->AdditionalFeeKindInfo;
            foreach ($collection as $item) {
                $result[] = $this->xmlLeafToArray($item);
            }
        }

        $this->result = $result;
        return $result;
    }

    /**
     * This method returns a list of extra fee discriminators.
     * Full description of each extra fee discriminator is available in the XML response.
     *
     * @return array
     */
    public function listAdditionalFeeDiscriminators(): ?array
    {
        $body = '<Pull_ListAdditionalFeeDiscriminators_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '</Pull_ListAdditionalFeeDiscriminators_RQ>';

        $response = $this->sendRequest($body);
        $result = array();
        if ($response->successful() && $this->isRuSuccessful()) {
            $XMLObject = new \SimpleXMLElement($response->body());
            $collection = $XMLObject->AdditionalFeeDiscriminators->AdditionalFeeDiscriminatorInfo;
            foreach ($collection as $item) {
                $result[] = $this->xmlLeafToArray($item);
            }
        }

        $this->result = $result;
        return $result;
    }

    /**
     * This method returns a list of all supported taxes and extra costs in Rentals United.
     *
     * @return array
     */
    public function listAdditionalFeeTypes(): ?array
    {
        $body = '<Pull_ListAdditionalFeeTypes_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '</Pull_ListAdditionalFeeTypes_RQ>';

        $response = $this->sendRequest($body);
        $result = array();
        if ($response->successful() && $this->isRuSuccessful()) {
            $XMLObject = new \SimpleXMLElement($response->body());
            $collection = $XMLObject->AdditionalFeeTypes->AdditionalFeeTypeInfo;
            foreach ($collection as $item) {
                $result[] = $this->xmlLeafToArray($item);
            }
        }

        $this->result = $result;
        return $result;
    }

    /**
     * This method returns a list of supported cancellation types.
     * A confirmed reservation can be cancelled either by a guest or refused by a property provider.
     *
     * @return array
     */
    public function cancellationTypes(): ?array
    {
        $body = '<Pull_CancellationTypes_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '</Pull_CancellationTypes_RQ>';

        $response = $this->sendRequest($body);
        $result = array();
        if ($response->successful() && $this->isRuSuccessful()) {
            $XMLObject = new \SimpleXMLElement($response->body());
            $collection = $XMLObject->CancellationTypes->CancellationType;
            foreach ($collection as $item) {
                $result[] = $this->xmlLeafToArray($item);
            }
        }

        $this->result = $result;
        return $result;
    }

    /**
     * This method is designed for Sales Channels.
     * It returns a list of available modes that can be used when inserting a new reservation to Rentals United.
     * DoNotIgnore (ID=0) Rentals United will quote a PMS to validate the price calculation and availability of property. It is a DEPRECATED mode and we do not recommend using it, as with the update on 13-04-2021 Rentals United became an ultimate source of truth regarding property's pricing and availability.
     * IgnorePMS (ID=1) Rentals United quotes a PMS only to check if the property is available in this PMS. However, the price validation is based on the Rentals United pricing only. This mode applies only to properties from PMS partners which support quote feature. It is a DEPRECATED mode and we do not recommend using it, as with the update on 13-04-2021 Rentals United became an ultimate source of truth regarding property's pricing and availability.
     * IgnorePMSAndRU (ID=2) No price calculation validation is performed. Reservations with any prices can be created in Rentals United.
     * IgnorePMSAvbPrice (ID=3) It is the default mode. Before accepting a new reservation, Rentals United validates the price calculation and property availability based on the Rentals United pricing and availability calendar.
     * IgnorePMSAvbPriceRUPrice (ID=4) No price calculation validation is performed. Availability is checked only in Rentals United, PMS availability is ignored.
     *
     * @return array
     */
    public function quoteModes(): ?array
    {
        $body = '<Pull_QuoteModes_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '</Pull_QuoteModes_RQ>';

        $response = $this->sendRequest($body);
        $result = array();
        if ($response->successful() && $this->isRuSuccessful()) {
            $XMLObject = new \SimpleXMLElement($response->body());
            $collection = $XMLObject->QuoteModes->QuoteMode;
            foreach ($collection as $item) {
                $result[] = $this->xmlLeafToArray($item);
            }
        }

        $this->result = $result;
        return $result;
    }
}
