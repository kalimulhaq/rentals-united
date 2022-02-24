<?php

namespace Kalimulhaq\RentalsUnited;

class User extends RentalsUnited
{
    /**
     * Create User
     * 
     * <p>This method inserts a single building into the Rentals United system.</p>
     *
     * @param  string $FirstName
     * @param  string $LastName
     * @param  string $Email
     * @param  string $Password
     * @param  array $Locations
     * @param  int $PMSId
     * @param  string $ConfigurationString
     * @return bool
     */
    public function createUser(
        string $FirstName,
        string $LastName,
        string $Email,
        string $Password,
        array $Locations,
        ?int $PMSId = null,
        ?string $ConfigurationString = null
    ): ?int {
        $body = '<Push_CreateUser_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '<FirstName>' . $FirstName . '</FirstName>';
        $body .= '<LastName>' . $LastName . '</LastName>';
        $body .= '<Email>' . $Email . '</Email>';
        $body .= '<Password>' . $Password . '</Password>';
        $body .= '<Locations>';
        foreach ($Locations as $item) {
            $body .= '<LocationId>' . $item . '</LocationId>';
        }
        $body .= '</Locations>';
        if (!is_null($PMSId)) {
            $body .= '<PMSId>' . $PMSId . '</PMSId>';
        }
        if (!is_null($ConfigurationString)) {
            $body .= '<ConfigurationString>' . $ConfigurationString . '</ConfigurationString>';
        }
        $body .= '</Push_CreateUser_RQ>';

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
     * List created users
     * 
     * <p>This method is designed for PMSes and property providers who maintain multiple Rentals United accounts. This method returns a list of users</p>
     *
     * @return array
     */
    public function listMyUsers(): ?array
    {
        $body = '<Pull_ListMyUsers_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '</Pull_ListMyUsers_RQ>';

        $response = $this->sendRequest($body);
        $result = array();
        if ($response->successful() && $this->isRuSuccessful()) {
            $XMLObject = new \SimpleXMLElement($response->body());
            $collection = $XMLObject->Owners->Owner;
            foreach ($collection as $item) {
                $result[] = $this->xmlToArray($item, false);
            }
        }

        $this->result = $result;
        return $result;
    }

    /**
     * Get Users
     * 
     * <p>An alise method of listMyUsers to get a list of users</p>
     *
     * @return array
     */
    public function getUsers(): ?array
    {
        return $this->listMyUsers();
    }

    /**
     * List Parent Users
     * 
     * <p>Use this method when you're a subuser and need to know a pool of users available to you.</p>
     *
     * @return array
     */
    public function listAllParentUsers(): ?array
    {
        $body = '<Pull_ListAllParentUsers_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '</Pull_ListAllParentUsers_RQ>';

        $response = $this->sendRequest($body);
        $result = array();
        if ($response->successful() && $this->isRuSuccessful()) {
            $XMLObject = new \SimpleXMLElement($response->body());
            $collection = $XMLObject->ParentUsers->User;
            foreach ($collection as $item) {
                $result[] = $this->xmlToArray($item, false);
            }
        }

        $this->result = $result;
        return $result;
    }

    /**
     * Get Parent Users
     * 
     * <p>An alise method of listAllParentUsers</p>
     *
     * @return array
     */
    public function getParentUsers(): ?array
    {
        return $this->listAllParentUsers();
    }

    /**
     * Fill Company Details
     * 
     * <p>Provide the vital company information via API. Useful for creation of the Rentals United company profile via API, e.g. PMS partners. Information provided will be used to connect properties to sales channels, e.g. for contracting and contacting purposes. It is possible to modify these details in Rentals United UI. To do this go to the Company Profile tab from the User Profile menu.</p>
     *
     * @param  array $ContactInfo
     * @param  array $CompanyInfo
     * @return bool
     */
    public function fillCompanyDetails(array $ContactInfo, array $CompanyInfo): ?bool
    {
        $body = '<Push_FillCompanyDetails_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '<ContactInfo>';
        if (isset($ContactInfo['FirstName'])) {
            $body .= '<FirstName>' . $ContactInfo['FirstName'] . '</FirstName>';
        }
        if (isset($ContactInfo['LastName'])) {
            $body .= '<LastName>' . $ContactInfo['LastName'] . '</LastName>';
        }
        if (isset($ContactInfo['Email'])) {
            $body .= '<Email>' . $ContactInfo['Email'] . '</Email>';
        }
        if (isset($ContactInfo['FirstName'])) {
            $body .= '<FirstName>' . $ContactInfo['FirstName'] . '</FirstName>';
        }
        if (isset($ContactInfo['Phone'])) {
            $body .= '<Phone>' . $ContactInfo['Phone'] . '</Phone>';
        }
        if (isset($ContactInfo['City'])) {
            $body .= '<City>' . $ContactInfo['City'] . '</City>';
        }
        if (isset($ContactInfo['CountryId'])) {
            $body .= '<CountryId>' . $ContactInfo['CountryId'] . '</CountryId>';
        }
        if (isset($ContactInfo['Address'])) {
            $body .= '<Address>' . $ContactInfo['Address'] . '</Address>';
        }
        if (isset($ContactInfo['ZipCode'])) {
            $body .= '<ZipCode>' . $ContactInfo['ZipCode'] . '</ZipCode>';
        }
        if (isset($ContactInfo['BirthDate'])) {
            $body .= '<BirthDate>' . $ContactInfo['BirthDate'] . '</BirthDate>';
        }
        if (isset($ContactInfo['LanguageId'])) {
            $body .= '<LanguageId>' . $ContactInfo['LanguageId'] . '</LanguageId>';
        }
        $body .= '</ContactInfo>';
        $body .= '<CompanyInfo>';
        if (isset($CompanyInfo['CompanyName'])) {
            $body .= '<CompanyName>' . $CompanyInfo['CompanyName'] . '</CompanyName>';
        }
        if (isset($CompanyInfo['WebsiteAddress'])) {
            $body .= '<WebsiteAddress>' . $CompanyInfo['WebsiteAddress'] . '</WebsiteAddress>';
        }
        if (isset($CompanyInfo['CompanyCity'])) {
            $body .= '<CompanyCity>' . $CompanyInfo['CompanyCity'] . '</CompanyCity>';
        }
        if (isset($CompanyInfo['MerchantName'])) {
            $body .= '<MerchantName>' . $CompanyInfo['MerchantName'] . '</MerchantName>';
        }
        if (isset($CompanyInfo['NumberOfProperties'])) {
            $body .= '<NumberOfProperties>' . $CompanyInfo['NumberOfProperties'] . '</NumberOfProperties>';
        }
        if (isset($CompanyInfo['Locations'])) {
            $body .= '<Locations>';
            foreach ($CompanyInfo['Locations'] as $item) {
                $body .= '<Location ID="' . $item . '" />';
            }
            $body .= '</Locations>';
        }
        $body .= '</CompanyInfo>';
        $body .= '</Push_FillCompanyDetails_RQ>';

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
     * Close user account
     * 
     * <p>This method is designed for PMS integration who would like to fully automate Property Manager account lifecycle. Starting with creation of account via Push_CreateUser_RQ and filling company details with Push_FillCompanyDetails_RQ. Ending up closing user account via Push_ArchiveUser_RQ.</p>
     * <p>Closing user account via this method will result with:</p>
     * <ul>
     * <li>this user losing access to RU dashboard</li>
     * <li>removal of all connections to Sales Channels</li>
     * <li>all properties archivization</li>
     * </ul>
     * <p>This action is not reversible. Once user's account is closed, it cannot be opened back again.</p>
     * <p>This action occupies many resources of Rentals United platform. It may take up to several minutes to process the request. In case you experience a timeout please try calling our API once again with the same request.</p>
     * 
     * @param  int $ReservationID
     * @param  bool $Archive
     * @return bool
     */
    public function archiveUser(): ?bool
    {
        $body = '<Push_ArchiveUser_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '</Push_ArchiveUser_RQ>';

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
     * Delete account
     * 
     * <p>An alise method of archiveUser</p>
     * 
     * @return bool
     */
    public function deleteAccount(): ?bool
    {
        return $this->archiveUser();
    }
}
