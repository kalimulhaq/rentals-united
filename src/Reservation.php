<?php

namespace Kalimulhaq\RentalsUnited;

class Reservation extends RentalsUnited
{
    /**
     * Put confirmed reservations
     *
     * <p>Use this method to create a confirmed reservation. You can book more than one property in a single reservation.</p>
     * <p>Rentals United validates business constraints i.e. price calculation, minimum stay, etc. before accepting a booking.
     * It is possible to create a reservation violating business conditions by using aprioprioate Push_PutConfirmedReservationMulti_RQ/QuoteModeId value.
     * In such case Rentals United does not accept responsibility for data correctness in this reservation. Make sure you receive a property provider acceptance before ignoring quote response</p>
     * <p>Using different than default quote mode needs to be agreed with Rentals United business representative and clearly communicated to property providers connecting to your Sales Channel.</p>
     *
     * @param  array $StayInfos
     * @param  array $CustomerInfo
     * @param  string $Comments
     * @param  int $ReservationID
     * @param  array $CreditCard
     * @param  int $QuoteModeId
     *
     * @return int Reservation ID
     */
    public function putConfirmedReservation(
        array $StayInfos,
        array $CustomerInfo,
        ?array $CreditCard = null,
        ?int $ReservationID = null,
        ?string $Currency = null,
        ?string $Comments = null,
        ?string $ReferenceID = null,
        ?int $QuoteModeId = null
    ): ?int {
        $body = '<Push_PutConfirmedReservationMulti_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '<Reservation>';
        $body .= '<StayInfos>';
        foreach ($StayInfos as $item) {
            $body .= '<StayInfo>';
            $body .= '<PropertyID>' . $item['PropertyID'] . '</PropertyID>';
            $body .= '<DateFrom>' . $item['DateFrom'] . '</DateFrom>';
            $body .= '<DateTo>' . $item['DateTo'] . '</DateTo>';
            $body .= '<NumberOfGuests>' . $item['NumberOfGuests'] . '</NumberOfGuests>';
            $body .= '<Costs>';
            $body .= '<RUPrice>' . $item['Costs']['RUPrice'] . '</RUPrice>';
            $body .= '<ClientPrice>' . $item['Costs']['ClientPrice'] . '</ClientPrice>';
            $body .= '<AlreadyPaid>' . $item['Costs']['AlreadyPaid'] . '</AlreadyPaid>';
            if (isset($item['Costs']['ChannelCommission'])) {
                $body .= '<ChannelCommission>' . $item['Costs']['ChannelCommission'] . '</ChannelCommission>';
            }
            $body .= '</Costs>';
            if (isset($item['Units'])) {
                $body .= '<Units>' . $item['Units'] . '</Units>';
            }
            if (isset($item['Comments'])) {
                $body .= '<Comments>' . $item['Comments'] . '</Comments>';
            }
            $body .= '</StayInfo>';
        }
        $body .= '</StayInfos>';
        $body .= '<CustomerInfo>';
        $body .= '<Name>' . $CustomerInfo['Name'] . '</Name>';
        $body .= '<SurName>' . $CustomerInfo['SurName'] . '</SurName>';
        $body .= '<Email>' . $CustomerInfo['Email'] . '</Email>';
        if (isset($CustomerInfo['Phone'])) {
            $body .= '<Phone>' . $CustomerInfo['Phone'] . '</Phone>';
        }
        if (isset($CustomerInfo['SkypeID'])) {
            $body .= '<SkypeID>' . $CustomerInfo['SkypeID'] . '</SkypeID>';
        }
        if (isset($CustomerInfo['Address'])) {
            $body .= '<Address>' . $CustomerInfo['Address'] . '</Address>';
        }
        if (isset($CustomerInfo['ZipCode'])) {
            $body .= '<ZipCode>' . $CustomerInfo['ZipCode'] . '</ZipCode>';
        }
        if (isset($CustomerInfo['LanguageID'])) {
            $body .= '<LanguageID>' . $CustomerInfo['LanguageID'] . '</LanguageID>';
        }
        if (isset($CustomerInfo['CountryID'])) {
            $body .= '<CountryID>' . $CustomerInfo['CountryID'] . '</CountryID>';
        }
        $body .= '</CustomerInfo>';
        if (!is_null($CreditCard)) {
            $body .= '<CreditCard>';
            $body .= '<CCNumber>' . $CreditCard['CCNumber'] . '</CCNumber>';
            if (isset($CreditCard['CVC'])) {
                $body .= '<CVC>' . $CreditCard['CVC'] . '</CVC>';
            }
            $body .= '<NameOnCard>' . $CreditCard['NameOnCard'] . '</NameOnCard>';
            $body .= '<Expiration>' . $CreditCard['Expiration'] . '</Expiration>';
            $body .= '<BillingAddress>' . $CreditCard['BillingAddress'] . '</BillingAddress>';
            $body .= '<CardType>' . $CreditCard['CardType'] . '</CardType>';
            if (isset($CreditCard['Comments'])) {
                $body .= '<Comments>' . $CreditCard['Comments'] . '</Comments>';
            }
            if (isset($CreditCard['SecureAuthentication'])) {
                $body .= '<SecureAuthentication>';
                $body .= '<CAVV>' . $CreditCard['SecureAuthentication']['CAVV'] . '</CAVV>';
                if (isset($CreditCard['SecureAuthentication']['XID'])) {
                    $body .= '<XID>' . $CreditCard['SecureAuthentication']['XID'] . '</XID>';
                }
                if (isset($CreditCard['SecureAuthentication']['DsTransID'])) {
                    $body .= '<DsTransID>' . $CreditCard['SecureAuthentication']['DsTransID'] . '</DsTransID>';
                }
                if (isset($CreditCard['SecureAuthentication']['ThreeDSVersion'])) {
                    $body .= '<ThreeDSVersion>' . $CreditCard['SecureAuthentication']['ThreeDSVersion'] . '</ThreeDSVersion>';
                }
                if (isset($CreditCard['SecureAuthentication']['ExceptionType'])) {
                    $body .= '<ExceptionType>' . $CreditCard['SecureAuthentication']['ExceptionType'] . '</ExceptionType>';
                }
                $body .= '</SecureAuthentication>';
            }
            $body .= '</CreditCard>';
        }
        if (!is_null($ReservationID)) {
            $body .= '<ReservationID>' . $ReservationID . '</ReservationID>';
        }
        if (!is_null($Currency)) {
            $body .= '<Currency>' . $Currency . '</Currency>';
        }
        if (!is_null($Comments)) {
            $body .= '<Comments>' . $Comments . '</Comments>';
        }
        if (!is_null($ReferenceID)) {
            $body .= '<ReferenceID>' . $ReferenceID . '</ReferenceID>';
        }
        $body .= '</Reservation>';
        if (!is_null($QuoteModeId)) {
            $body .= '<QuoteModeId>' . $QuoteModeId . '</QuoteModeId>';
        }
        $body .= '</Push_PutConfirmedReservationMulti_RQ>';

        $response = $this->sendRequest($body);
        $result = false;
        if ($response->successful() && $this->isRuSuccessful()) {
            $XMLObject = new \SimpleXMLElement($response->body());
            $result = (int)((string) $XMLObject->ReservationID);
        }

        $this->result = $result;
        return $result;
    }

    /**
     * Put Property By Arry
     *
     * <p>This is the same method like putConfirmedReservation only receive all the parameters in one array</p>
     *
     * @param array $fields
     * @param int
     */
    public function putConfirmedReservationByArray(array $fields): ?int
    {
        return $this->putConfirmedReservation(
            $fields['StayInfos'],
            $fields['CustomerInfo'],
            isset($fields['CreditCard']) ? $fields['CreditCard'] : null,
            isset($fields['ReservationID']) ? $fields['ReservationID'] : null,
            isset($fields['Currency']) ? $fields['Currency'] : null,
            isset($fields['Comments']) ? $fields['Comments'] : null,
            isset($fields['ReferenceID']) ? $fields['ReferenceID'] : null,
            isset($fields['QuoteModeId']) ? $fields['QuoteModeId'] : null
        );
    }

    /**
     * Cancel reservation
     *
     * <p>Use this method to cancel previously created reservations. As soon as the reservation is cancelled, availability of a given property is increased. </p>
     * <p>You can optionally provide CancelTypeID which defines who cancelled the reservation (Property Provider or the Guest).</p>
     * <p>This method can be used also by Property Managers. List of sales channels supporting cancelling via Rentals United:</p>
     * <ul>
     * <li>HomeAway</li>
     * <li>Google</li>
     * <li>Atraveo</li>
     * <li>La Comunity</li>
     * <li>Expedia</li>
     * <li>Edomizil</li>
     * <li>Everystay</li>
     * <li>Kayak</li>
     * <li>Holidu</li>
     * <li>Florida Rentals</li>
     * </ul>
     * <p>In case reservation comes from another sales channel reach out directly to their support or cancel reservation in sales channel's extranet/management console/platform.</p>
     *
     * @param  int $ReservationID
     * @return bool
     */
    public function cancelReservation(int $ReservationID, ?int $CancelTypeID = null): ?bool
    {
        $body = '<Push_CancelReservation_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '<ReservationID>' . $ReservationID . '</ReservationID>';
        if (!is_null($CancelTypeID)) {
            $body .= '<CancelTypeID>' . $CancelTypeID . '</CancelTypeID>';
        }
        $body .= '</Push_CancelReservation_RQ>';

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
     * Archive reservation
     *
     * <p>Use this method to archive past reservations of any status.</p>
     *
     * @param  int $ReservationID
     * @param  bool $Archive
     * @return bool
     */
    public function archiveReservation(int $ReservationID, bool $Archive = true): ?bool
    {
        $body = '<Push_ArchiveReservation_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '<ReservationID>' . $ReservationID . '</ReservationID>';
        $body .= '<Archive>' . ($Archive ? 1 : 0) . '</Archive>';
        $body .= '</Push_ArchiveReservation_RQ>';

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
     * Push ModifyStay
     *
     * <p>Use this method to modify an existing confirmed reservation.</p>
     * <p>You can modify Guest details, property, dates, number of people, price, etc.</p>
     * <p>Typical modification reasons are * Guest modifies a reservation on the Sales Channel * Property Provider wants to move a reservation to another identical property to optimise the availability</p>
     * <p>Property Provider can modify reservations created by Sales Channel. In such case, Rentals United does not accept responsibility for data correctness in such reservation.</p>
     * <p>There are some restrictions if the reservation is a past, ongoing or a future reservation.
     * It is not possible to change anything in a past reservation (DateTo in the past). It is possible to change everything except DateFrom in an ongoing reservation.
     * It is possible to change everything in a future reservation (DateFrom in the future).</p>
     *
     * @param  int $ReservationID
     * @param  array $Current
     * @param  array $Modify
     * @param  bool $AllowOverbooking
     * @param  bool $UseCurrentPrice
     * @param  int $QuoteModeId
     *
     * @return bool
     */
    public function modifyStay(int $ReservationID, array $Current, array $Modify, ?bool $AllowOverbooking = null, ?bool $UseCurrentPrice = null, ?int $QuoteModeId = null): ?bool
    {
        $body = '<Push_ModifyStay_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '<ReservationID>' . $ReservationID . '</ReservationID>';
        $body .= '<Current>';
        $body .= '<PropertyID>' . $Current['PropertyID'] . '</PropertyID>';
        $body .= '<DateFrom>' . $Current['DateFrom'] . '</DateFrom>';
        $body .= '<DateTo>' . $Current['DateTo'] . '</DateTo>';
        if (isset($Current['ResApaID'])) {
            $body .= '<ResApaID>' . $Current['ResApaID'] . '</ResApaID>';
        }
        $body .= '</Current>';
        $body .= '<Modify>';
        if (isset($Modify['PropertyID'])) {
            $body .= '<PropertyID>' . $Modify['PropertyID'] . '</PropertyID>';
        }
        if (isset($Modify['DateFrom'])) {
            $body .= '<DateFrom>' . $Modify['DateFrom'] . '</DateFrom>';
        }
        if (isset($Modify['DateTo'])) {
            $body .= '<DateTo>' . $Modify['DateTo'] . '</DateTo>';
        }
        if (isset($Modify['NumberOfGuests'])) {
            $body .= '<NumberOfGuests>' . $Modify['NumberOfGuests'] . '</NumberOfGuests>';
        }
        if (isset($Modify['ClientPrice'])) {
            $body .= '<ClientPrice>' . $Modify['ClientPrice'] . '</ClientPrice>';
        }
        if (isset($Modify['AlreadyPaid'])) {
            $body .= '<AlreadyPaid>' . $Modify['AlreadyPaid'] . '</AlreadyPaid>';
        }
        if (isset($Modify['ChannelCommission'])) {
            $body .= '<ChannelCommission>' . $Modify['ChannelCommission'] . '</ChannelCommission>';
        }
        if (isset($Modify['PMSReservationId'])) {
            $body .= '<PMSReservationId>' . $Modify['PMSReservationId'] . '</PMSReservationId>';
        }
        $body .= '</Modify>';
        if (!is_null($AllowOverbooking)) {
            $body .= '<AllowOverbooking>' . ($AllowOverbooking ? 1 : 0) . '</AllowOverbooking>';
        }
        if (!is_null($UseCurrentPrice)) {
            $body .= '<UseCurrentPrice>' . ($UseCurrentPrice ? 1 : 0) . '</UseCurrentPrice>';
        }
        if (!is_null($QuoteModeId)) {
            $body .= '<QuoteModeId>' . $QuoteModeId . '</QuoteModeId>';
        }
        $body .= '</Push_ModifyStay_RQ>';

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
     * List reservations
     *
     * <p>This method returns a list of reservations (for your properties) sorted by last modification date.</p>
     * <p>Call this method periodically to obtain created, cancelled and modified reservations and transfer them to your system.</p>
     * <p>We encourage you to call this API method at least every 20 minutes to quickly update your calendar.</p>
     * <p>A reservation in Rentals United can be made for multiple properties. Multiple Pull_ListReservations_RS/Reservations/Reservation/StayInfos will be returned</p>
     * <p>Under no circumstances should your date range be greater than 7 days. The result set will be cut to reservations created or modified maximum 7 days in the past.</p>
     * <p>Statuses you can see in the response will be Confirmed or Cancelled. If you want to determine if any modifications occured please compare LastMod field in the response with the value you received on previous call.</p>
     * <p>Please be aware that you have to send requests to HTTPS endpoint in order to retrieve CreditCard info in a secure way</p>
     *
     * @param DateTime $DateFrom
     * @param DateTime $DateTo
     * @param int $locationId
     * @return array
     */
    public function listReservations(\DateTime $DateFrom, \DateTime $DateTo, int $locationId = 0): ?array
    {
        $body = '<Pull_ListReservations_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '<DateFrom>' . $DateFrom->format('Y-m-d H:i:s') . '</DateFrom>';
        $body .= '<DateTo>' . $DateTo->format('Y-m-d H:i:s') . '</DateTo>';
        $body .= '<LocationID>' . $locationId . '</LocationID>';
        $body .= '</Pull_ListReservations_RQ>';

        $response = $this->sendRequest($body);
        $result = array();
        if ($response->successful() && $this->isRuSuccessful()) {
            $XMLObject = new \SimpleXMLElement($response->body());
            $collection = $XMLObject->Reservations->Reservation;
            foreach ($collection as $item) {
                $result[] = $this->reservationToArray($item);
            }
        }

        $this->result = $result;
        return $result;
    }

    /**
     * Get reservations
     *
     * <p>An alise method of listReservations</p>
     *
     * @param DateTime $DateFrom
     * @param DateTime $DateTo
     * @param int $locationId
     * @return array
     */
    public function getReservations(\DateTime $DateFrom, \DateTime $DateTo, int $locationId = 0): ?array
    {
        return $this->listReservations($DateFrom, $DateTo, $locationId);
    }

    /**
     * List own reservations
     *
     * <p>This method is designed for Sales Channels. It returns a list of reservations created in Rentals United during a specified time frame.</p>
     * <p>If you are a property provider utilising Rentals United API and have inserted reservations into Rentals United (either via API or UI) you can use this method to reconcile reservation list.</p>
     *
     * @param DateTime $DateFrom
     * @param DateTime $DateTo
     * @param int $locationId
     * @return array
     */
    public function getOwnReservations(\DateTime $DateFrom, \DateTime $DateTo, int $locationId = 0): ?array
    {
        $body = '<Pull_GetOwnReservations_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '<DateFrom>' . $DateFrom->format('Y-m-d H:i:s') . '</DateFrom>';
        $body .= '<DateTo>' . $DateTo->format('Y-m-d H:i:s') . '</DateTo>';
        $body .= '<LocationID>' . $locationId . '</LocationID>';
        $body .= '</Pull_GetOwnReservations_RQ>';

        $response = $this->sendRequest($body);
        $result = array();
        if ($response->successful() && $this->isRuSuccessful()) {
            $XMLObject = new \SimpleXMLElement($response->body());
            $collection = $XMLObject->Reservations->Reservation;
            foreach ($collection as $item) {
                $result[] = $this->reservationToArray($item);
            }
        }

        $this->result = $result;
        return $result;
    }

    /**
     * Get reservation by ID
     *
     * <p>This method returns details of an individual reservation.</p>
     *
     * @param int $ReservationID
     * @return array
     */
    public function getReservationByID(int $ReservationID): ?array
    {
        $body = '<Pull_GetReservationByID_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '<ReservationID>' . $ReservationID . '</ReservationID>';
        $body .= '</Pull_GetReservationByID_RQ>';

        $response = $this->sendRequest($body);
        $result = array();
        if ($response->successful() && $this->isRuSuccessful()) {
            $XMLObject = new \SimpleXMLElement($response->body());
            $result = $this->reservationToArray($XMLObject->Reservation);
        }

        $this->result = $result;
        return $result;
    }

    /**
     * Get reservation
     *
     * <p>An alise method of getReservationByID</p>
     *
     * @param int $ReservationID
     * @return array
     */
    public function getReservation(int $ReservationID): ?array
    {
        return $this->getReservationByID($ReservationID);
    }

    /**
     * List Reservations Missing PMS Mapping
     *
     * <p>This method has been designed for property providers to help them reconcile reservations in Rentals United and their systems.</p>
     * <p>It returns a list of reservations for which Rentals United does not have PMS Reservation ID stored. The XML response includes all unique identifiers needed to identify a reservation in your system.</p>
     *
     * @param DateTime $DateFrom
     * @return array
     */
    public function listReservationsMissingPMSMapping(\DateTime $DateFrom): ?array
    {
        $body = '<Pull_ListReservationsMissingPMSMapping_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '<DateFrom>' . $DateFrom->format('Y-m-d H:i:s') . '</DateFrom>';
        $body .= '</Pull_ListReservationsMissingPMSMapping_RQ>';

        $response = $this->sendRequest($body);
        $result = array();
        if ($response->successful() && $this->isRuSuccessful()) {
            $XMLObject = new \SimpleXMLElement($response->body());
            $collection = $XMLObject->Reservations->ReservationMissingPMSMapping;
            foreach ($collection as $item) {
                $result[] = $this->xmlToArray($item, false);
            }
        }

        $this->result = $result;
        return $result;
    }

    /**
     * List reservations for owner
     *
     * <p>This method is designed for Revenue Management Systems.</p>
     * <p>This method returns a list of reservations for a Rentals United account sorted by last modification date.</p>
     * <p>Call this method periodically to obtain created, cancelled and modified reservations and transfer them to your system.</p>
     * <p>We encourage you to call this API method at least every 20 minutes to quickly update your calendar.</p>
     * <p>A reservation in Rentals United can be made for multiple properties. Multiple Pull_ListReservationsOwnerUser_RS/Reservations/Reservation/StayInfos will be returned.</p>
     * <p>Under no circumstances should your date range be greater than 7 days. The result set will be cut to reservations created or modified maximum 7 days in the past.</p>
     *
     * @param string $Username
     * @param DateTime $DateFrom
     * @param DateTime $DateTo
     * @param int $locationId
     * @return array
     */
    public function listReservationsOwnerUser(string $Username, \DateTime $DateFrom, \DateTime $DateTo, int $locationId = 0): ?array
    {
        $body = '<Pull_ListReservationsOwnerUser_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '<Username>' . $Username . '</Username>';
        $body .= '<DateFrom>' . $DateFrom->format('Y-m-d H:i:s') . '</DateFrom>';
        $body .= '<DateTo>' . $DateTo->format('Y-m-d H:i:s') . '</DateTo>';
        $body .= '<LocationID>' . $locationId . '</LocationID>';
        $body .= '</Pull_ListReservationsOwnerUser_RQ>';

        $response = $this->sendRequest($body);
        $result = array();
        if ($response->successful() && $this->isRuSuccessful()) {
            $XMLObject = new \SimpleXMLElement($response->body());
            $collection = $XMLObject->Reservations->Reservation;
            foreach ($collection as $item) {
                $result[] = $this->reservationToArray($item);
            }
        }

        $this->result = $result;
        return $result;
    }

    /**
     * List reservations of Subusers
     *
     * <p>This method is designed for Vacation Rentals services that rely on subuser mechanism</p>
     * <p>Logic of processing this request is exactly like listReservations is has just different scope of authorization that allows to pull ParentUser properties when your integration relies on Subuser mechanism.</p>
     *
     * @param string $Username
     * @param DateTime $DateFrom
     * @param DateTime $DateTo
     * @param int $locationId
     * @return array
     */
    public function listReservationsSubUsers(string $Username, \DateTime $DateFrom, \DateTime $DateTo, int $locationId = 0): ?array
    {
        $body = '<Pull_ListReservationsSubUsers_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '<Username>' . $Username . '</Username>';
        $body .= '<DateFrom>' . $DateFrom->format('Y-m-d H:i:s') . '</DateFrom>';
        $body .= '<DateTo>' . $DateTo->format('Y-m-d H:i:s') . '</DateTo>';
        $body .= '<LocationID>' . $locationId . '</LocationID>';
        $body .= '</Pull_ListReservationsSubUsers_RQ>';

        $response = $this->sendRequest($body);
        $result = array();
        if ($response->successful() && $this->isRuSuccessful()) {
            $XMLObject = new \SimpleXMLElement($response->body());
            $collection = $XMLObject->Reservations->Reservation;
            foreach ($collection as $item) {
                $result[] = $this->reservationToArray($item);
            }
        }

        $this->result = $result;
        return $result;
    }

    /**
     * Enabling RLNM
     *
     * <p>Use this method to subscribe to RLNM notifications.</p>
     *
     * @param  string|null $HandlerUrl pass null to unsubscribe
     * @return string Hash
     */
    public function putHandlerUrl(?string $HandlerUrl = null): ?string
    {
        $body = '<LNM_PutHandlerUrl_RQ>';
        $body .= $this->getAuthenticationXml();
        if (!is_null($HandlerUrl)) {
            $body .= '<HandlerUrl>' . $HandlerUrl . '</HandlerUrl>';
        }
        $body .= '</LNM_PutHandlerUrl_RQ>';

        $response = $this->sendRequest($body);
        $result = false;
        if ($response->successful() && $this->isRuSuccessful()) {
            $XMLObject = new \SimpleXMLElement($response->body());
            $result = (string) $XMLObject->Hash;
        }

        $this->result = $result;
        return $result;
    }

    /**
     * Convert reservation xml to array
     *
     * @param SimpleXMLElement $resXml
     * @return array
     */
    public function reservationToArray(\SimpleXMLElement $resXml): array
    {
        $res = $this->xmlToArray($resXml, false);
        $res['StayInfos'] = array();
        foreach ($resXml->StayInfos->StayInfo as $stay) {
            $stayInfo = $this->xmlToArray($stay, false);

            if ($stay->ReservationBreakdown) {
                $breakdown = array(
                    'RUBreakdown' => array(
                        'DayPrices' => array(),
                        'TotalFeesTaxes' => array(),
                        'Total' => $this->getValue($stay->ReservationBreakdown->RUBreakdown->Total),
                        'Rent' =>  $this->getValue($stay->ReservationBreakdown->RUBreakdown->Rent),
                    ),
                    'ChannelBreakdown' => array(),
                    'ChannelCommission' => $this->getValue($stay->ReservationBreakdown->ChannelCommission),
                );

                $totalFeeTax = $stay->ReservationBreakdown->RUBreakdown->TotalFeesTaxes->TotalFeeTax ?? [];
                foreach ($totalFeeTax as $tfTax) {
                    $breakdown['RUBreakdown']['TotalFeesTaxes'][] = $this->xmlLeafToArray($tfTax);
                }

                $dayPrices = $stay->ReservationBreakdown->RUBreakdown->DayPrices ?? [];
                foreach ($dayPrices as $stay) {
                    $dayPrice = $this->xmlAttrsToArray($stay);
                    $dayPrice['Rent'] = $this->getValue($stay->Rent);
                    $dayPrice['Price'] = $this->getValue($stay->Price);
                    if ($stay->Taxes->Tax) {
                        foreach ($stay->Taxes->Tax as $tx) {
                            $tax = $this->xmlLeafToArray($tx);
                            $dayPrice['Taxes'][] = $tax;
                        }
                    }
                    $breakdown['RUBreakdown']['DayPrices'][] = $dayPrice;
                }

                $stayInfo['ReservationBreakdown'] = $breakdown;
            }

            $res['StayInfos'][] = $stayInfo;
        }

        return $res;
    }
}
