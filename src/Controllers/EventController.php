<?php

namespace App\Controllers;

use App\Database\Db;
use App\Models\Event;
use Exception;
use PDO;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class EventController
{
    public function addEvent(Request $request, Response $response): Response
    {
        $queryData = $request->getQueryParams();

        //validation
        $acceptableStatuses = ['0', '1'];
        if (!isset($queryData['name']) || !in_array($queryData['status'], $acceptableStatuses)) {
            $response->getBody()->write('Error: incorrect request data');
            return $response->withStatus(400);
        }

        try {
            $db = Db::connect();
            $params = [
                ':name' => $queryData['name'],
                ':status' => $queryData['status'],
                ':user' => $request->getAttribute('ip_address'),
                ':add_date' => date('Y-m-d H:i:s'),
            ];
            $stmt = $db->prepare('INSERT INTO events (name, status, "user", add_date) VALUES (:name, :status, :user, :add_date)');
            if ($stmt->execute($params)) {
                return $response->withStatus(200);
            } else {
                $response->getBody()->write('Error when adding an event');
                return $response->withStatus(500);
            }
        } catch (Exception $exception) {
            $response->getBody()->write($exception->getMessage());
            return $response->withStatus(500);
        }
    }

    public function getEvents(Request $request, Response $response): Response
    {
        $bodyData = $request->getParsedBody();

        //build filter query
        $filterParams = [];
        $useFilter = false;
        $filterQuery = 'WHERE ';
        if (isset($bodyData['filter'])) {
            $complexQuery = false;
            foreach ($bodyData['filter'] as $fieldName => $fieldData) {
                if (in_array($fieldName, Event::getFields())) {
                    if ($fieldName == 'add_date_to' && is_string($fieldData)) {
                        if ($complexQuery) $filterQuery .= 'AND ';
                        else $complexQuery = true;

                        $filterQuery .= 'add_date <= :add_date_to ';
                        $filterParams[':add_date_to'] = $fieldData;
                    } elseif ($fieldName == 'add_date_from' && is_string($fieldData)) {
                        if ($complexQuery) $filterQuery .= 'AND ';
                        else $complexQuery = true;

                        $filterQuery .= 'add_date >= :add_date_from ';
                        $filterParams[':add_date_from'] = $fieldData;
                    } elseif (is_array($fieldData) && !empty($fieldData)) {
                        if ($complexQuery) $filterQuery .= 'AND ';
                        else $complexQuery = true;

                        $in = "";
                        $i = 0;
                        foreach ($fieldData as $item) {
                            $key = ":param" . $i++;
                            $in .= ($in ? "," : "") . $key; // :id0,:id1,:id2
                            $filterParams[$key] = $item; // collecting values into a key-value array
                        }
                        $filterQuery .= "$fieldName IN ($in) ";
                    } elseif (!is_array($fieldData)) {
                        if ($complexQuery) $filterQuery .= 'AND ';
                        else $complexQuery = true;

                        $filterQuery .= "$fieldName = :$fieldName ";
                        $filterParams[":$fieldName"] = $fieldData;
                    }
                }
            }

            if ($filterQuery != 'WHERE ') $useFilter = true;
        }


        $query = match ($bodyData['agr']['type']) {
            1 => 'SELECT name, COUNT(*) as count FROM 
                    (SELECT * FROM events ' . ($useFilter ? $filterQuery : '') . ') tmp ' .
                'GROUP BY name',
            2 => 'SELECT "user", name, COUNT(*) as count FROM 
                    (SELECT * FROM events ' . ($useFilter ? $filterQuery : '') . ') tmp ' .
                'GROUP BY "user", name',
            3 => 'SELECT "user", status, COUNT(*) as count FROM 
                    (SELECT * FROM events' . ($useFilter ? $filterQuery : '') . ') tmp ' .
                'GROUP BY "user", status ',
            default => 'SELECT * FROM events ' . ($useFilter ? $filterQuery : ''),
        };

        try {
            $db = Db::connect();
            $params = $filterParams;
            $stmt = $db->prepare($query);
            if ($stmt->execute($params)) {
                $response->getBody()->write(json_encode($stmt->fetchAll(PDO::FETCH_ASSOC)));
                return $response->withStatus(200)->withHeader('Content-type', 'application/json');
            } else {
                $response->getBody()->write('Error when select data');
                return $response->withStatus(500);
            }
        } catch (Exception $exception) {
            $response->getBody()->write('Error connecting to the database');
            return $response->withStatus(500);
        }
    }
}