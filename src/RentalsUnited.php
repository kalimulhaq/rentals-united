<?php

namespace Kalimulhaq\RentalsUnited;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;

/**
 * Class RentalsUnited
 */
class RentalsUnited
{
    /**
     * Rentals United api endpoint
     *
     * @var string
     */
    protected $apiEndpoint;

    /**
     * Rentals United account username
     *
     * @var string
     */
    protected $username;

    /**
     * Rentals United account password
     *
     * @var string
     */
    protected $password;

    /**
     * Request body XML string
     *
     * @var string
     */
    private $requestBody;

    /**
     * API request response
     *
     * @var Response
     */
    private $response;

    /**
     * Rentals United response status
     *
     * @var string
     */
    private $ruStatus;

    /**
     * Rentals United response status ID
     *
     * @var int
     */
    private $ruStatusId;

    /**
     * Rentals United response ID
     *
     * @var string
     */
    private $ruResponseId;

    /**
     * Rentals United response error
     *
     * @var string | null
     */
    private $ruError;

    /**
     * Rentals United Notifs collection.
     *
     * @var array
     */
    private $notifs;

    /**
     * The parsed result after successful response
     *
     * @var array
     */
    protected $result;

    public function __construct(?string $username = null, ?string $password = null)
    {
        $this->credentials($username, $password);
        $this->apiEndpoint = config('rentalsunited.apiEndpoint');
    }

    /**
     * Set credentials
     *
     * @param string $username
     * @param string $password
     */
    public function credentials(?string $username = null, ?string $password = null)
    {
        $this->username = $username ? $username : config('rentalsunited.auth.username');
        $this->password = $password ? $password : config('rentalsunited.auth.password');
    }

    /**
     * Get the request body
     *
     * @return string
     */
    public function getRequestBody(): ?string
    {
        return $this->requestBody;
    }

    /**
     * Get the Response
     *
     * @return Response
     */
    public function getResponse(): ?Response
    {
        return $this->response;
    }

    /**
     * Get  Rentals United response status
     *
     * @return string
     */
    public function getRUStatus(): ?string
    {
        return $this->ruStatus;
    }

    /**
     * Get  Rentals United response status id
     *
     * @return int
     */
    public function getRUStatusId(): ?int
    {
        return $this->ruStatusId;
    }

    /**
     * Get  Rentals United response status id
     *
     * @return string
     */
    public function getRUResponseId(): ?string
    {
        return $this->ruResponseId;
    }

    /**
     * Check if the Rentals United response was successful
     *
     * @return bool
     */
    public function isRuSuccessful(): bool
    {
        return $this->ruStatusId === 0;
    }

    /**
     * Check if the Rentals United reponed with error
     *
     * @return bool
     */
    public function isRuError(): bool
    {
        return $this->ruStatusId !== 0 || !empty($this->ruError);
    }

    /**
     * Get Rentals United error
     *
     * @return string
     */
    public function getRUError(): ?string
    {
        return $this->ruError;
    }

    /**
     * Get Rentals United Notif Collection
     *
     * @return array
     */
    public function getNotifs(): ?array
    {
        return $this->notifs;
    }


    /**
     * Get the result
     * 
     * @return maxid
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Check if the Rentals United connection works
     *
     * @return bool
     */
    public function isConnectionWorks(): bool
    {
        $body = '<Pull_ListStatuses_RQ>';
        $body .= $this->getAuthenticationXml();
        $body .= '</Pull_ListStatuses_RQ>';

        $response = $this->sendRequest($body);

        if ($response->successful() && $this->isRuSuccessful()) {
            return true;
        }

        return false;
    }

    /**
     * Post Request to Rentals United API Endpoint
     *
     * @param string $body
     * @return Response
     */
    protected function sendRequest(?string $body = null): ?Response
    {
        if (!empty($body)) {
            $this->requestBody = $body;
        }

        $this->response = Http::withBody($this->requestBody, 'application/xml')->post($this->apiEndpoint);
        $XMLObject = new \SimpleXMLElement($this->response->body());
        if (strtolower($XMLObject->getName()) === 'error') {
            $this->ruStatus = null;
            $this->ruStatusId = (int) $XMLObject['ID'];
            $this->ruResponseId = null;
            $this->ruError = (string) $XMLObject;
        } else {
            $this->ruStatus = (string) $XMLObject->Status;
            $this->ruStatusId = (int) $XMLObject->Status['ID'];
            $this->ruResponseId = (string) $XMLObject->ResponseID;
            $this->ruError = null;
            if ($this->ruStatusId !== 0) {
                $this->ruError = (string) $XMLObject->Status;
            }

            if (!empty($XMLObject->Notifs) && !empty($XMLObject->Notifs->Notif)) {
                foreach ($XMLObject->Notifs->Notif as $notif) {
                    $this->notifs[] = $this->xmlLeafToArray($notif);
                }
            }
        }

        return $this->response;
    }

    /**
     * Get the  Rentals United Authentication XML node to include with the request body
     *
     * @return string
     */
    protected function getAuthenticationXml(): string
    {
        $xml = '<Authentication>';
        $xml .= '<UserName>' . $this->username . '</UserName>';
        $xml .= '<Password>' . $this->password . '</Password>';
        $xml .= '</Authentication>';
        return $xml;
    }

    /**
     * Convert XML to Array
     *
     * @param SimpleXMLElement $xml
     * @return array
     */
    protected function xmlToArray(\SimpleXMLElement $xml, bool $flatList = true): array
    {
        $array = array();
        $name = $xml->getName();
        $array = $this->xmlLeafToArray($xml);

        $children = array();
        foreach ($xml->children() as $k => $v) {
            $value = $this->xmlToArray($v, $flatList);
            if ($flatList) {
                $children[] = $value;
            } else {
                $cName = $v->getName();
                if (count($value) === 1 && isset($value[$cName])) {
                    $children[$cName] = $value[$cName];
                } else if (isset($children[$cName])) {
                    $children = array_values($children);
                    $children[] = $value;
                } else {
                    $children[$cName] = $value;
                }
            }
        }

        if (count($children) > 0) {
            $array[$name] = $children;
        }

        if (count($array) === 1 && isset($array[$name]) && is_array($array[$name])) {
            $array = $array[$name];
        }

        return $array;
    }

    /**
     * Convert XML leaf node to Array
     *
     * @param SimpleXMLElement $xml
     * @return array
     */
    protected function xmlLeafToArray(\SimpleXMLElement $xml): array
    {
        $array = array();
        $array[$xml->getName()] = $this->getValue($xml);
        foreach ($xml->attributes() as $key => $val) {
            $array[$key] = $this->getValue($val);
        }
        return $array;
    }

    /**
     * Convert XML node attributes to Array
     *
     * @param SimpleXMLElement $xml
     * @return array
     */
    protected function xmlAttrsToArray(\SimpleXMLElement $xml): array
    {
        $array = array();
        foreach ($xml->attributes() as $key => $val) {
            $array[$key] = $this->getValue($val);
        }
        return $array;
    }

    /**
     * Get value from XML
     *
     * <p>Get value from XML with correct data type</p>
     *
     * @param mixed $val
     * @return mixed
     */
    public function getValue($val)
    {
        $value = (string) $val;
        if ($value === '') {
        } else if ($value === '0' || $value === '1') {
            $value =  (int) $value;
        } else if ($value === 'false' || $value === 'true') {
            $value =  ($value === 'true' ? true : false);
        } else if (is_numeric($value)) {
            if ($value == (float) $value) {
                $value =  (float) $value;
            } else if ($value == (int) $value) {
                $value =  (int) $value;
            }
        }
        return $value;
    }
}
