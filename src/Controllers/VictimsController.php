<?php

namespace Vanier\Api\Controllers;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Vanier\Api\controllers\BaseController;
use Vanier\Api\Models\VictimsModel;

// helpers
use Vanier\Api\Helpers\ValidateHelper;

// exceptions
use Exception;
use Fig\Http\Message\StatusCodeInterface;
use Vanier\Api\exceptions\HttpNotFound;
use Vanier\Api\exceptions\HttpBadRequest;
use Vanier\Api\exceptions\HttpUnprocessableContent;


class VictimsController extends BaseController
{
    private $victims_model = null;

    public function __construct()
    {
        $this->victims_model = new VictimsModel();
    }

    /**
     * Handle the HTTP GET request to retrieve all victims with optional filters.
     *
     * @param Request $request 
     * @param Response $response 
     * @return Response 
     */
    public function handleGetAllVictims(Request $request, Response $response)
    {
        // constant values
        define('DEFAULT_PAGE', 1);
        define("DEFAULT_PAGE_SIZE", 10);

        $filters = $request->getQueryParams();
        $this->validateFilters($request, $filters);
        
        $victims_model = new VictimsModel();

        $data = $victims_model->handleGetAllVictims($filters);

        $page = $filters["page"] ?? DEFAULT_PAGE;
        $pageSize = $filters["pageSize"] ?? DEFAULT_PAGE_SIZE;

        // check if the params are numeric
        if (!ValidateHelper::validatePageNumbers($page, $pageSize)) {
            throw new HttpBadRequest($request, "Expected numeric");
        }
        $dataParams = ['page' => $page, 'pageSize' => $pageSize, 'pageMin' => 1, 'pageSizeMin' => 5, 'pageSizeMax' => 10];
        // check if page is within in range 
        if (!ValidateHelper::validatePagingParams($dataParams)) {
            throw new HttpUnprocessableContent($request, "Out of range, unable to process your request");
        }

        $this->victims_model->setPaginationOptions($page, $pageSize);

        // catch any DB exceptions
        try {
            $data = $this->victims_model->handleGetAllVictims($filters);
        } catch (Exception $e) {
            throw new HttpBadRequest($request, "Not the right syntax, consult the documentation");
        }
        // throw a HttpNotFound error if data is empty
        if (!$data['data']) {
            throw new HttpNotFound($request, 'Please check you parameter or consult the documentation');
        }
        
        return $this->prepareOkResponse($response, $data, StatusCodeInterface::STATUS_OK);

    }

     /**
     * Handle the HTTP GET request to retrieve a victim by ID.
     * 
     * @param Request $request
     * @param Response $response
     * @param array $uri_args
     * @return Response
     */
    public function handleGetVictimById(Request $request, Response $response, array $uri_args) {     

        $victim_id = $uri_args ["victim_id"];

        // Instantiate the VictimsModel to retrieve the victim data.
        $victims_model = new VictimsModel();
        $data = $victims_model->handleGetVictimById($victim_id);

        // Http Exception
        if (!$data['Victim']) {
            throw new HttpNotFound($request, "Please check your query parameter or consult the documentation.");
        }

         // Extract the victim and prosecutor data from the retrieved data.
        $victim_data = $data['Victim'];
        $prosecutor_data = $data['Prosecutor'];

        // formatting the response
        $response_data = [
            'Victim' => $victim_data,
            'Prosecutor' => $prosecutor_data
        ];
        return $this->prepareOkResponse($response, $response_data);
    }

    /**
    * Validates the filters for retrieving all victims
    *
    * @param array $filters The filters to validate
    * @throws HttpBadRequest If any of the filters are invalid
     */
    private function validateFilters(Request $request, array $filters)
    {
        $valid_filters = ['last_name', 'marital_status', 'age', 'victim_id', 'prosecutor_id'];
        foreach ($filters as $filter => $value) {
            if (!in_array($filter, $valid_filters)) {
                throw new HttpBadRequest($request, "Invalid filter: $filter");
            }
            if ($filter == 'age' && !is_numeric($value)) {
                throw new HttpBadRequest($request, "Expected numeric for age");
            }
        }
    }
}