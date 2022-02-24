<?php

namespace Kalimulhaq\RentalsUnited;

class Lead extends RentalsUnited
{

    /**
     * Put Lead
     * 
     * <p>Use the method to create a reservation with a request status (Lead). Leads do not block property availability.
     * Leads can be confirmed or cancelled using Push_PutConfirmedReservationMulti_RQ or Push_CancelReservation_RQ respectively.</p>
     * 
     * @param  string $ExternalReservationID
     * @param  int $PropertyID
     * @param  string $DateFrom
     * @param  string $DateTo
     * @param  int $NumberOfGuests
     * @param  array $CustomerInfo
     * @param string $Comments
     * 
     * @return int Reservation ID
     */
    public function putLead(
        string $ExternalReservationID,
        int $PropertyID,
        string $DateFrom,
        string $DateTo,
        int $NumberOfGuests,
        array $CustomerInfo,
        ?string $Comments = null
    ): ?int {
        $body = '<Push_PutLead_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '<Lead>';

        $body .= '<ExternalReservationID>' . $ExternalReservationID . '</ExternalReservationID>';
        $body .= '<PropertyID>' . $PropertyID . '</PropertyID>';
        $body .= '<DateFrom>' . $DateFrom . '</DateFrom>';
        $body .= '<DateTo>' . $DateTo . '</DateTo>';
        $body .= '<NumberOfGuests>' . $NumberOfGuests . '</NumberOfGuests>';
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
        if (isset($CustomerInfo['Passport'])) {
            $body .= '<Passport>' . $CustomerInfo['Passport'] . '</Passport>';
        }
        $body .= '</CustomerInfo>';
        if (!is_null($Comments)) {
            $body .= '<Comments>' . $Comments . '</Comments>';
        }
        $body .= '</Lead>';
        $body .= '</Push_PutLead_RQ>';

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
     * Put Lead By Arry
     * 
     * <p>This is the same method like putLead only receive all the parameters in one array</p>
     * 
     * @param array $fields
     * @param int
     */
    public function putLeadByArray(array $fields): ?int
    {
        return $this->putLead(
            $fields['ExternalReservationID'],
            $fields['PropertyID'],
            $fields['DateFrom'],
            $fields['DateTo'],
            $fields['NumberOfGuests'],
            $fields['CustomerInfo'],
            isset($fields['Comments']) ? $fields['Comments'] : null
        );
    }

    /**
     * Archive lead
     * 
     * <p>Use this method to archive past leads of any status.</p>
     * 
     * @param  int $LeadID
     * @param  bool $Archive
     * @return bool
     */
    public function archiveLead(int $LeadID, bool $Archive): ?bool
    {
        $body = '<Push_ArchiveReservation_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '<ReservationID>' . $LeadID . '</ReservationID>';
        $body .= '<Archive>' . $Archive . '</Archive>';
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
     * Get Leads
     * 
     * <p>Lead in the API represents a request status in Rentals United user interface</p>
     * <p>This method returns all reservations with a request status</p>
     *
     * @param DateTime $DateFrom
     * @param DateTime $DateTo
     * @param int $locationId
     * @return array
     */
    public function getLeads(\DateTime $DateFrom, \DateTime $DateTo, int $locationId = 0): ?array
    {
        $body = '<Pull_GetLeads_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '<DateFrom>' . $DateFrom->format('Y-m-d H:i:s') . '</DateFrom>';
        $body .= '<DateTo>' . $DateTo->format('Y-m-d H:i:s') . '</DateTo>';
        $body .= '<LocationID>' . $locationId . '</LocationID>';
        $body .= '</Pull_GetLeads_RQ>';

        $response = $this->sendRequest($body);
        $result = array();
        if ($response->successful() && $this->isRuSuccessful()) {
            $XMLObject = new \SimpleXMLElement($response->body());
            $collection = $XMLObject->Leads->Lead;
            foreach ($collection as $item) {
                $result[] = $this->leadToArray($item);
            }
        }

        $this->result = $result;
        return $result;
    }

    /**
     * Convert lead xml to array
     * 
     * @param SimpleXMLElement $resXml
     * @return array
     */
    public function leadToArray(\SimpleXMLElement $resXml): array
    {
        $lead = $this->xmlToArray($resXml, false);
        return $lead;
    }
}
