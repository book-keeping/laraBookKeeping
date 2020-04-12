<?php

namespace Tests\Unit;

use App\Http\Responder\v1\ShowTopViewResponder;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Response;
use Mockery;
use Tests\TestCase;

class Http_Responder_v1_ShowTopViewResponderTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    /**
     * @test
     */
    public function response_ReturnResponse()
    {
        $countext = [
            'date'             => '2019-09-30',
            'income_statement' => [
                'expense' => [
                    'amount' => 559319,
                    'groups' => [
                        '844db4d1-bdc2-4e1c-a56e-82a166b13afe' => [
                            'title' => 'GroupTitle1', 'isCurrent' => 0, 'amount' => 148112, 'bk_code' => 4300,
                            'items' => [
                                '2bfd6973-2622-49ea-884a-8e18dc15d05e' => ['title' => 'ItemTitle11', 'amount' => 79692, 'bk_code' => 4302],
                                '9a453a41-ba5b-45cc-a29a-d6e12cc08fc0' => ['title' => 'ItemTitle12', 'amount' => 57000, 'bk_code' => 4301],
                                'a4adb10e-ecfc-4497-9501-72813c5e4b54' => ['title' => 'ItemTitle13', 'amount' => 11420, 'bk_code' => 4303],
                            ],
                        ],
                        '1bd92f3a-19d3-4ea2-bc7b-3e791446ddac' => [
                            'title' => 'GroupTitle2', 'isCurrent' => 0, 'amount' => 3610, 'bk_code' => 4400,
                            'items' => [
                                '4bc0d8d9-e8ec-4258-a812-b53c8f7babd7' => ['title' => 'ItemTitle21', 'amount' => 57, 'bk_code' => 4403],
                                '57d3c146-0205-470a-9c3a-409e72432681' => ['title' => 'ItemTitle22', 'amount' => 2457, 'bk_code' => 4404],
                                '6eb9015e-039f-4028-8b2b-3f3279c8849c' => ['title' => 'ItemTitle23', 'amount' => 346, 'bk_code' => 4402],
                                'f4c7b564-79e4-4037-9814-ec4bb040c58e' => ['title' => 'ItemTitle24', 'amount' => 750, 'bk_code' => 4407],
                            ],
                        ],
                        '9b0c599e-28f8-4974-9ae1-4145f3770f52' => [
                            'title' => 'GroupTitle3', 'isCurrent' => 0, 'amount' => 13446, 'bk_code' => 4100,
                            'items' => [
                                '515a27c0-ad28-4f08-9ac9-624b5f45c3d6' => ['title' => 'ItemTitle31', 'amount' => 13246, 'bk_code' => 4104],
                                '7c707a00-0e95-4ae4-886c-b46678aeb5b5' => ['title' => 'ItemTitle32', 'amount' => 200, 'bk_code' => 4103],
                            ],
                        ],
                        'ed88bc2c-8b82-45cf-b276-f2046b7b22e9' => [
                            'title' => 'GroupTitle4', 'isCurrent' => 0, 'amount' => 394151, 'bk_code' => 4200,
                            'items' => [
                                '944ddb3d-6cc9-43ad-9ba1-2dffd2ee5b58' => ['title' => 'ItemTitle41', 'amount' => 394151, 'bk_code' => 4213],
                            ],
                        ],
                    ],
                ],
                'revenue' => [
                    'amount' => 546070,
                    'groups' => [
                        'a6b22699-2459-4b62-87d4-2ad422aba824' => [
                            'title' => 'GroupTitle5', 'isCurrent' => 0, 'amount' => 3461, 'bk_code' => 5200,
                            'items' => [
                                '22803ae3-ee3b-4bab-9574-c250bf822c43' => ['title' => 'ItemTitle51', 'amount' => 3457, 'bk_code' => 5202],
                                '8dc19eb8-4707-4d3e-844d-4dd570f768d8' => ['title' => 'ItemTitle52', 'amount' => 4, 'bk_code' => 5203],
                            ],
                        ],
                        '7e102a91-fcda-49a7-8154-8a3821bb0ddb' => [
                            'title' => 'GroupTitle6', 'isCurrent' => 0, 'amount' => 542609, 'bk_code' => 5100,
                            'items' => [
                                '704b007b-f700-4bd0-a183-fc0ab79cffbd' => ['title' => 'ItemTitle61', 'amount' => 170909, 'bk_code' => 5102],
                                'da5766aa-464d-4673-bce3-d1b5cd0aa5d8' => ['title' => 'ItemTitle62', 'amount' => 371700, 'bk_code' => 5101],
                            ],
                        ],
                    ],
                ],
                'net_income' => ['amount' => -13249],
            ],
            'balance_sheet' => [
                'asset' => [
                    'amount' => 24863926,
                    'groups' => [
                        'c46db1a8-daf3-4cd0-befd-2539885d8406' => [
                            'title' => 'GroupTitle7', 'isCurrent' => 1, 'amount' => 267830, 'bk_code' => 1200,
                            'items' => [
                                '12c52f0f-3fd1-4076-bab0-521c6903ebe9' => ['title' => 'ItemTitle71', 'amount' => 218842, 'bk_code' => 1209],
                                '26934102-c58a-42d1-b884-d0cc729d1eeb' => ['title' => 'ItemTitle72', 'amount' => 27000, 'bk_code' => 1203],
                                '90372161-36a2-4be3-9128-2c142c97e246' => ['title' => 'ItemTitle73', 'amount' => 988, 'bk_code' => 1210],
                                '9e5b447f-042f-42dd-b41b-5d41e33f2da6' => ['title' => 'ItemTitle74', 'amount' => 1000, 'bk_code' => 1212],
                                'dd406a58-d95e-43b6-8d86-b07690ee8983' => ['title' => 'ItemTitle75', 'amount' => 20000, 'bk_code' => 1211],
                            ],
                        ],
                        '34262cf1-d616-41ab-984f-6422cb923a99' => [
                            'title' => 'GroupTitle8', 'isCurrent' => 1, 'amount' => 14468896, 'bk_code' => 1100,
                            'items' => [
                                '3aabc800-db4e-4688-bfc0-1c36f33ed577' => ['title' => 'ItemTitle81', 'amount' => 38856, 'bk_code' => 1101],
                                '54f52183-8497-4e6a-98f7-baabb77c6722' => ['title' => 'ItemTitle82', 'amount' => 186475, 'bk_code' => 1103],
                                '7209a097-7287-455d-87d1-18aa13f45471' => ['title' => 'ItemTitle83', 'amount' => 8067167, 'bk_code' => 1102],
                                '874347ae-2e03-4ec9-97c1-2aa3396657f7' => ['title' => 'ItemTitle84', 'amount' => 2076240, 'bk_code' => 1107],
                                'e4dd1fd4-91ec-49c1-b1c3-1fbc376f1ec9' => ['title' => 'ItemTitle85', 'amount' => 3907985, 'bk_code' => 1106],
                                'f94c0efc-b82f-4852-b59a-eca5e30fba3c' => ['title' => 'ItemTitle86', 'amount' => 192173, 'bk_code' => 1105],
                            ],
                        ],
                        'f69193bb-7c97-4a0d-a0cf-8b8811f5a735' => [
                            'title' => 'GroupTitle9', 'isCurrent' => 0, 'amount' => 10127200, 'bk_code' => 1400,
                            'items' => [
                                '49774f80-4fb4-41e6-b186-475438e4ed2b' => ['title' => 'ItemTitle91', 'amount' => 500000, 'bk_code' => 1406],
                                '6b78e142-df13-49ad-943f-73e5481b4eb8' => ['title' => 'ItemTitle92', 'amount' => 5087200, 'bk_code' => 1402],
                                '96e7f7d0-e5fa-4946-850d-d3b1138db320' => ['title' => 'ItemTitle93', 'amount' => 4540000, 'bk_code' => 1407],
                            ],
                        ],
                    ],
                ],
                'liability' => [
                    'amount' => 357386,
                    'groups' => [
                        'e0c81e5c-3b81-4237-837e-e004c2695ee1' => [
                            'title' => 'GroupTitle10', 'isCurrent' => 1, 'amount' => 33235, 'bk_code' => 2300,
                            'items' => [
                                '62cb01ca-fc2f-44f9-a846-43a5a67f7491' => ['title' => 'ItemTitle101', 'amount' => 11187, 'bk_code' => 2304],
                                'fcadddd5-c5b3-4296-a2cd-81af02db5016' => ['title' => 'ItemTitle102', 'amount' => 22048, 'bk_code' => 2306],
                            ],
                        ],
                        'f4d64c3f-991e-4d52-bd10-946587589cac' => [
                            'title' => 'GroupTitle11', 'isCurrent' => 1, 'amount' => 324151, 'bk_code' => 2200,
                            'items' => [
                                'bf9fbc93-f496-4b5c-ab6f-6e0b09f7b20a' => ['title' => 'ItemTitle111', 'amount' => 324151, 'bk_code' => 2205],
                            ],
                        ],
                    ],
                ],
                'current_net_asset' => ['amount' => 14379340],
                'net_asset'         => ['amount' => 24506540],
            ],
            'slips' => [
                '3238ed0a-265a-408d-8e7f-03b5c138abc2' => [
                    'date'         => '2019-09-01',
                    'slip_outline' => 'slip_outline1',
                    'slip_memo'    => null,
                    'items'        => [
                        '9d7d0222-ef76-487c-8319-220b2e6911a7' => [
                            'debit' => [
                                'account_id'    => 'dd406a58-d95e-43b6-8d86-b07690ee8983',
                                'account_title' => 'account_title1',
                            ],
                            'credit' => [
                                'account_id'    => 'fcadddd5-c5b3-4296-a2cd-81af02db5016',
                                'account_title' => 'account_title2',
                            ],
                            'amount'  => 1000,
                            'client'  => 'client1',
                            'outline' => 'outline1',
                        ],
                    ],
                ],
                'bd9ca022-8d49-4589-9eba-f05ee1af1856' => [
                    'date'         => '2019-09-05',
                    'slip_outline' => 'slip_outline2',
                    'slip_memo'    => null,
                    'items'        => [
                        '2847e922-8a45-48f4-97f8-cdeee40ed8f9' => [
                            'debit' => [
                                'account_id'    => '96e7f7d0-e5fa-4946-850d-d3b1138db320',
                                'account_title' => 'account_title3',
                            ],
                            'credit' => [
                                'account_id'    => 'f94c0efc-b82f-4852-b59a-eca5e30fba3c',
                                'account_title' => 'account_title4',
                            ],
                            'amount'  => 50000,
                            'client'  => 'client2',
                            'outline' => 'outline2',
                        ],
                        'e5cade29-f4e4-477b-8e3c-e4ab6ca7caa8' => [
                            'debit' => [
                                'account_id'    => 'ff65f247-427d-4484-a964-6c81588b9a12',
                                'account_title' => 'account_title5',
                            ],
                            'credit' => [
                                'account_id'    => 'f94c0efc-b82f-4852-b59a-eca5e30fba3c',
                                'account_title' => 'account_title4',
                            ],
                            'amount'  => 750,
                            'client'  => 'client3',
                            'outline' => 'outline3',
                        ],
                    ],
                ],
            ],
        ];
        /** @var \Illuminate\Http\Response|\Mockery\MockInterface $ResponseMock */
        $ResponseMock = Mockery::mock(Response::class);
        $ResponseMock->shouldReceive('setContent')->once();
        $ResponseMock->shouldReceive('setStatusCode')->once();
        /** @var \Illuminate\Contracts\View\Factory|\Mockery\MockInterface $ViewFactoryMock */
        $ViewFactoryMock = Mockery::mock(Factory::class);
        $ViewFactoryMock->shouldReceive('make')->once();

        $responder = new ShowTopViewResponder($ResponseMock, $ViewFactoryMock);
        $response = $responder->response($countext);

        $this->assertTrue(true);
    }
}
