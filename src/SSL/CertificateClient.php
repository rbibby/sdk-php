<?php

namespace UKFast\SDK\SSL;

use UKFast\SDK\Client;
use UKFast\SDK\Page;
use UKFast\SDK\SSL\Entities\Certificate;
use UKFast\SDK\SSL\Entities\CertificatePEM;

class CertificateClient extends Client
{
    protected $basePath = 'ssl/';

    /**
     * Certificate API fields which need mapping
     *
     * @var array
     */
    public $certificateMap = [
        'common_name'       => 'commonName',
        'alternative_names' => 'alternativeNames',
        'valid_days'        => 'validDays',
        'ordered_date'      => 'orderedDate',
        'renewal_date'      => 'renewalDate',
    ];

    /**
     * Gets a paginated response of all certificates
     *
     * @param int $page
     * @param int $perPage
     * @param array $filters
     * @return Page
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPage($page = 1, $perPage = 15, $filters = [])
    {
        $filters = $this->friendlyToApi($filters, $this->certificateMap);

        $page = $this->paginatedRequest('v1/certificates', $page, $perPage, $filters);
        $page->serializeWith(function ($item) {
            return new Certificate($this->apiToFriendly($item, $this->certificateMap));
        });

        return $page;
    }

    /**
     * Gets an individual Certificate
     *
     * @param int $id
     * @return Certificate
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getById($id)
    {
        $response = $this->request("GET", "v1/certificates/$id");
        $body     = $this->decodeJson($response->getBody()->getContents());

        return new Certificate($this->apiToFriendly($body->data, $this->certificateMap));
    }

    /**
     * Get certificate private key
     *
     * @param int $id
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getCertificatePrivateKey($id)
    {
        $response = $this->request("GET", "v1/certificates/$id/private-key");
        $body = $this->decodeJson($response->getBody()->getContents());

        return $body->data->key;
    }


    /**
     * Download certificate in PEM format
     *
     * @param int $id
     * @return CertificatePEM
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getCertificatePEM($id)
    {
        $response = $this->request("GET", "v1/certificates/$id/download");
        $body = $this->decodeJson($response->getBody()->getContents());

        return new CertificatePEM($body->data);
    }
}
