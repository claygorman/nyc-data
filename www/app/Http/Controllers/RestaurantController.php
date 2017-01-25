<?php

namespace App\Http\Controllers;

use Elasticsearch\ClientBuilder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class RestaurantController extends Controller
{
    /**
     * Set the elasticsearch client connection.
     *
     * @var ClientBuilder
     */
    protected $client;

    public function __construct()
    {
        $this->client = ClientBuilder::create()->setHosts([env('ELASTICSEARCH_SERVER')])->build();
    }

    /**
     * This loads the view for the top 10 thai restaurants.
     *
     * @return Response
     */
    public function thaifood()
    {
        try {
            $thaiFood = $this->thaiFoodViewData();
        } catch (\Exception $e) {
            $data = ['error' => $e->getMessage()];

            return response()->json($data);
        }

        return view('restaurant.index', ['thaiFood' => $thaiFood]);
    }

    /**
     * API response for the map data for thai food.
     *
     * @return ResponseJson
     */
    public function thaiFoodMap()
    {
        return response()->json($this->thaiFoodMapData());
    }

    /**
     * This loads the view for the best places in NY.
     *
     * @return Response
     */
    public function bestplaces()
    {
        try {
            $restaurants = $this->bestPlacesViewData();
        } catch (\Exception $e) {
            $data = ['error' => $e->getMessage()];

            return response()->json($data);
        }

        return view('restaurant.bestplaces', ['restaurants' => $restaurants]);
    }

    /**
     * API response for the map data for best placs.
     *
     * @return Response
     */
    public function bestPlacesMap()
    {
        return response()->json($this->bestPlacesMapData());
    }

    /**
     * This shows more in depth data about a restaurant.
     *
     * @param string $restaurant This is the name of the restaurant
     *
     * @return Response
     */
    public function show($restaurant)
    {
        $aggs = [
            'index' => 'dohmh',
            'type' => 'log',
            'size' => 50,
            'body' => [
                'sort' => [
                    ['inspection-date' => ['order' => 'desc']],
                ],
                'query' => [
                    'bool' => [
                        'filter' => [
                            ['match' => ['dba' => $restaurant]],
                        ],
                    ],
                ],
            ],
        ];

        $data = $this->client->search($aggs);

        foreach ($data['hits']['hits'] as $hit) {
            $restaurantData[] = [
                'boro' => $hit['_source']['boro'],
                'score' => isset($hit['_source']['score']) ? $hit['_source']['score'] : '',
                'grade' => isset($hit['_source']['grade']) ? $hit['_source']['grade'] : '',
                'action' => isset($hit['_source']['action']) ? $hit['_source']['action'] : '',
                'violation-code' => isset($hit['_source']['violation-code']) ? $hit['_source']['violation-code'] : '',
                'violation-description' => isset($hit['_source']['violation-description']) ? $hit['_source']['violation-description'] : '',
                'inspection-date' => $this->formatDate($hit['_source']['inspection-date']),
                'street' => $this->formatStreet($hit['_source']['street']),
                'zip' => $hit['_source']['zipcode'],
            ];

            if (isset($hit['_source']['score'])) {
                $chartTitle[] = $this->formatDate($hit['_source']['inspection-date']);
                $chartData[] = $hit['_source']['score'];
            }

            $hit['_source']['dba'] = $this->formatDba($hit['_source']['dba']);
        }

        return view('restaurant.data', ['restaurantData' => $restaurantData, 'restaurant' => $restaurant, 'chartData' => $chartData, 'chartTitle' => $chartTitle]);
    }

    /**
     * Formats the date to our spec.
     *
     * @param string $date
     *
     * @return string
     */
    private function formatDate(string $date)
    {
        return (string) (new Carbon($date))->toDateString();
    }

    /**
     * Formats the steet name to our spec.
     *
     * @param string $street
     *
     * @return string
     */
    private function formatStreet(string $street)
    {
        return (string) title_case($street);
    }

    /**
     * Formats the business name to our spec.
     *
     * @param string $dba
     *
     * @return string
     */
    private function formatDba(string $dba)
    {
        return (string) title_case($dba);
    }

    /**
     * This is the query builder for the best places.
     *
     * @return array
     */
    private function bestPlacesQuery()
    {
        $aggs = [
            'index' => 'dohmh',
            'type' => 'log',
            'size' => 0,
            'request_cache' => 1,
            'body' => [
                'aggs' => [
                    'filter_docs' => [
                        'filter' => [
                            'bool' => [
                                'must' => [
                                    ['terms' => ['grade' => ['A', 'B']]],
                                ],
                            ],
                        ],
                        'aggs' => [
                            'dba' => [
                                'terms' => [
                                    'field' => 'phone',
                                    'size' => 50,
                                    'min_doc_count' => 10,
                                    'order' => ['_count' => 'desc'],
                                ],
                                'aggs' => [
                                   'top_tag_hits' => [
                                       'top_hits' => [
                                           'sort' => [['inspection-date' => ['order' => 'desc']]],
                                            '_source' => ['includes' => ['*']],
                                            'size' => 1,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        return (array) $aggs;
    }

    /**
     * This is the query builder for the best thai food places.
     *
     * @return array
     */
    private function thaiFoodQuery()
    {
        $aggs = [
            'index' => 'dohmh',
            'type' => 'log',
            'size' => 0,
            'request_cache' => 1,
            'body' => [
                'aggs' => [
                    'filter_docs' => [
                        'filter' => [
                            'bool' => [
                                'must' => [
                                    ['term' => ['cuisine-description' => 'thai']],
                                    ['terms' => ['grade' => ['A', 'B']]],
                                ],
                            ],
                        ],
                        'aggs' => [
                            'dba' => [
                                'terms' => [
                                    'field' => 'dba.raw',
                                    'size' => 10,
                                    'min_doc_count' => 10,
                                    'order' => ['_count' => 'desc'],
                                ],
                                'aggs' => [
                                   'top_tag_hits' => [
                                       'top_hits' => [
                                           'sort' => [['inspection-date' => ['order' => 'desc']]],
                                            '_source' => ['includes' => ['*']],
                                            'size' => 1,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        return (array) $aggs;
    }

    /**
     * This will return the thai food map data.
     *
     * @return array
     */
    private function thaiFoodMapData()
    {
        $mapMarkers = [];
        $mapInfoWindow = [];

        $aggs = $this->thaiFoodQuery();

        if (Cache::has('thaifood_map_data')) {
            $data = Cache::get('thaifood_map_data');
        } else {
            $data = $this->client->search($aggs);
            Cache::forever('thaifood_map_data', $data);
        }

        foreach ($data['aggregations']['filter_docs']['dba']['buckets'] as $bucket) {
            foreach ($bucket['top_tag_hits']['hits']['hits'] as $hit) {
                $hit['_source']['dba'] = $this->formatDba($hit['_source']['dba']);

                $mapMarkers[] = [$hit['_source']['dba'], $hit['_source']['location']['lat'], $hit['_source']['location']['lon']];

                $mapInfoWindow[] = [sprintf('<div class="info_content"><h4>%s</h4><p>Grade: %s, Score: %d</p></div>', $hit['_source']['dba'], $hit['_source']['grade'], $hit['_source']['score'])];
            }
        }

        return ['markers' => $mapMarkers, 'infoWindow' => $mapInfoWindow];
    }

    /**
     * This is the data builder for the view.
     *
     * @return array
     */
    private function thaiFoodViewData()
    {
        $aggs = $this->thaiFoodQuery();

        if (Cache::has('thaifood_data')) {
            $data = Cache::get('thaifood_data');
        } else {
            $data = $this->client->search($aggs);
            Cache::forever('thaifood_data', $data);
        }

        foreach ($data['aggregations']['filter_docs']['dba']['buckets'] as $bucket) {
            $thaiFood[$bucket['key']] = ['inspections' => $bucket['doc_count'], 'dba' => $this->formatDba($bucket['key'])];
            foreach ($bucket['top_tag_hits']['hits']['hits'] as $hit) {
                $thaiFood[$hit['_source']['dba']] += [
                    'boro' => $hit['_source']['boro'],
                    'score' => $hit['_source']['score'],
                    'grade' => $hit['_source']['grade'],
                    'action' => $hit['_source']['action'],
                    'violation-code' => $hit['_source']['violation-code'],
                    'inspection-date' => $this->formatDate($hit['_source']['inspection-date']),
                    'street' => $this->formatStreet($hit['_source']['street']),
                    'zip' => $hit['_source']['zipcode'],
                ];
            }
        }

        return (array) $thaiFood;
    }

    /**
     * This is the api response map data.
     *
     * @return array
     */
    private function bestPlacesMapData()
    {
        $mapMarkers = [];
        $mapInfoWindow = [];

        $aggs = $this->bestPlacesQuery();

        if (Cache::has('best_places_map_data')) {
            $data = Cache::get('best_places_map_data');
        } else {
            $data = $this->client->search($aggs);
            Cache::forever('best_places_map_data', $data);
        }

        foreach ($data['aggregations']['filter_docs']['dba']['buckets'] as $bucket) {
            foreach ($bucket['top_tag_hits']['hits']['hits'] as $hit) {
                $hit['_source']['dba'] = $this->formatDba($hit['_source']['dba']);

                $mapMarkers[] = [$hit['_source']['dba'], $hit['_source']['location']['lat'], $hit['_source']['location']['lon']];

                $mapInfoWindow[] = [sprintf('<div class="info_content"><h4>%s</h4><p>Grade: %s, Score: %d</p></div>', $hit['_source']['dba'], $hit['_source']['grade'], $hit['_source']['score'])];
            }
        }

        return ['markers' => $mapMarkers, 'infoWindow' => $mapInfoWindow];
    }

    /**
     * This is the best places map view data.
     *
     * @return array
     */
    private function bestPlacesViewData()
    {
        $aggs = $this->bestPlacesQuery();

        if (Cache::has('best_places_view_data')) {
            $data = Cache::get('best_places_view_data');
        } else {
            $data = $this->client->search($aggs);
            Cache::forever('best_places_view_data', $data);
        }

        foreach ($data['aggregations']['filter_docs']['dba']['buckets'] as $bucket) {
            $restaurants[$bucket['key']] = ['inspections' => $bucket['doc_count']];
            foreach ($bucket['top_tag_hits']['hits']['hits'] as $hit) {
                $hit['_source']['dba'] = $this->formatDba($hit['_source']['dba']);
                $restaurants[$hit['_source']['phone']] += [
                    'dba' => $hit['_source']['dba'],
                    'boro' => $hit['_source']['boro'],
                    'score' => $hit['_source']['score'],
                    'grade' => $hit['_source']['grade'],
                    'action' => $hit['_source']['action'],
                    'violation-code' => isset($hit['_source']['violation-code']) ? $hit['_source']['violation-code'] : '',
                    'inspection-date' => $this->formatDate($hit['_source']['inspection-date']),
                    'street' => $this->formatStreet($hit['_source']['street']),
                    'zip' => $hit['_source']['zipcode'],
                ];
            }
        }

        return (array) $restaurants;
    }
}
