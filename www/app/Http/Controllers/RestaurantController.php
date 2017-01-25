<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Elasticsearch\ClientBuilder;
use Carbon\Carbon;

class RestaurantController extends Controller
{
    protected $client;

    public function __construct()
    {
        $this->client = ClientBuilder::create()->setHosts([env('ELASTICSEARCH_SERVER')])->build();
    }

    public function thaifood(Request $request)
    {
        try {
            // $aggs = $this->thaiFoodQuery();

            // $data = $this->client->search($aggs);

            $thaiFood = $this->thaiFoodViewData();
        } catch (\Exception $e) {
            $data = ['error' => $e->getMessage()];

            return response()->json($data);
        }

        return view('restaurant.index', ['thaiFood' => $thaiFood]);
    }

    public function thaiFoodMap()
    {
        return response()->json($this->thaiFoodMapData());
    }

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

    public function bestPlacesMap()
    {
        return response()->json($this->bestPlacesMapData());
    }

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

    private function formatDate($date)
    {
        return (new Carbon($date))->toDateString();
    }

    private function formatStreet($street)
    {
        return title_case($street);
    }

    private function formatDba($dba)
    {
        return title_case($dba);
    }

    private function bestPlacesQuery()
    {
        $aggs = [
                'index' => 'dohmh',
                'type' => 'log',
                'size' => 0,
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

        return $aggs;
    }

    private function thaiFoodQuery()
    {
        $aggs = [
            'index' => 'dohmh',
            'type' => 'log',
            'size' => 0,
            'body' => [
                'aggs' => [
                    'filter_docs' => [
                        'filter' => [
                            'bool' => [
                                'must' => [
                                    ['term' => ['cuisine-description' => 'thai']],
                                    ['term' => ['grade' => 'A']],
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

        return $aggs;
    }

    private function thaiFoodMapData()
    {
        $aggs = $this->thaiFoodQuery();

        $data = $this->client->search($aggs);

        foreach ($data['aggregations']['filter_docs']['dba']['buckets'] as $bucket) {
            foreach ($bucket['top_tag_hits']['hits']['hits'] as $hit) {
                $hit['_source']['dba'] = $this->formatDba($hit['_source']['dba']);

                $mapMarkers[] = [$hit['_source']['dba'], $hit['_source']['location']['lat'], $hit['_source']['location']['lon']];

                $mapInfoWindow[] = [sprintf('<div class="info_content"><h4>%s</h4><p>Grade: %s, Score: %d</p></div>', $hit['_source']['dba'], $hit['_source']['grade'], $hit['_source']['score'])];
            }
        }

        return ['markers' => $mapMarkers, 'infoWindow' => $mapInfoWindow];
    }

    private function thaiFoodViewData()
    {
        $aggs = $this->thaiFoodQuery();

        $data = $this->client->search($aggs);

        foreach ($data['aggregations']['filter_docs']['dba']['buckets'] as $bucket) {
            $thaiFood[$bucket['key']] = ['inspections' => $bucket['doc_count'], 'dba' => $this->formatDba($bucket['key'])];
            foreach ($bucket['top_tag_hits']['hits']['hits'] as $hit) {
                $thaiFood[$hit['_source']['dba']]['boro'] = $hit['_source']['boro'];
                $thaiFood[$hit['_source']['dba']]['score'] = $hit['_source']['score'];
                $thaiFood[$hit['_source']['dba']]['grade'] = $hit['_source']['grade'];
                $thaiFood[$hit['_source']['dba']]['action'] = $hit['_source']['action'];
                $thaiFood[$hit['_source']['dba']]['violation-code'] = $hit['_source']['violation-code'];
                $thaiFood[$hit['_source']['dba']]['inspection-date'] = $this->formatDate($hit['_source']['inspection-date']);
                $thaiFood[$hit['_source']['dba']]['street'] = $this->formatStreet($hit['_source']['street']);
                $thaiFood[$hit['_source']['dba']]['zip'] = $hit['_source']['zipcode'];
            }
        }

        return $thaiFood;
    }

    private function bestPlacesMapData()
    {
        $aggs = $this->bestPlacesQuery();

        $data = $this->client->search($aggs);

        foreach ($data['aggregations']['filter_docs']['dba']['buckets'] as $bucket) {
            foreach ($bucket['top_tag_hits']['hits']['hits'] as $hit) {
                $hit['_source']['dba'] = $this->formatDba($hit['_source']['dba']);

                $mapMarkers[] = [$hit['_source']['dba'], $hit['_source']['location']['lat'], $hit['_source']['location']['lon']];

                $mapInfoWindow[] = [sprintf('<div class="info_content"><h4>%s</h4><p>Grade: %s, Score: %d</p></div>', $hit['_source']['dba'], $hit['_source']['grade'], $hit['_source']['score'])];
            }
        }

        return ['markers' => $mapMarkers, 'infoWindow' => $mapInfoWindow];
    }

    private function bestPlacesViewData()
    {
        $aggs = $this->bestPlacesQuery();

        $data = $this->client->search($aggs);

        foreach ($data['aggregations']['filter_docs']['dba']['buckets'] as $bucket) {
            $restaurants[$bucket['key']] = ['inspections' => $bucket['doc_count']];
            foreach ($bucket['top_tag_hits']['hits']['hits'] as $hit) {
                $hit['_source']['dba'] = $this->formatDba($hit['_source']['dba']);
                $restaurants[$hit['_source']['phone']]['dba'] = $hit['_source']['dba'];
                $restaurants[$hit['_source']['phone']]['boro'] = $hit['_source']['boro'];
                $restaurants[$hit['_source']['phone']]['score'] = $hit['_source']['score'];
                $restaurants[$hit['_source']['phone']]['grade'] = $hit['_source']['grade'];
                $restaurants[$hit['_source']['phone']]['action'] = $hit['_source']['action'];
                $restaurants[$hit['_source']['phone']]['violation-code'] = isset($hit['_source']['violation-code']) ? $hit['_source']['violation-code'] : '';
                $restaurants[$hit['_source']['phone']]['inspection-date'] = $this->formatDate($hit['_source']['inspection-date']);
                $restaurants[$hit['_source']['phone']]['street'] = $this->formatStreet($hit['_source']['street']);
                $restaurants[$hit['_source']['phone']]['zip'] = $hit['_source']['zipcode'];
            }
        }

        return $restaurants;
    }
}
