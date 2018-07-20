<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class MessageController extends Controller
{
    protected $multiMessage;
    public function __construct(MultiMessageController $multiMessage)
    {
        $this->multiMessage = $multiMessage;
    }
    public function getContent($asResource = false) {
        if (false === $this->content || (true === $asResource && null !== $this->content)) 
            throw new \LogicException('호출은 한번만 가능합니다.');
        
        if (true === $asResource) {
            $this->content = false;
            return fopen('php://input', 'rb');
        }
        if (null === $this->content) 
            $this->content = file_get_contents('php://input');
    
        return $this->content;
    }

    public function index(Request $request) {
        $carbon = Carbon::now();
        $data = json_decode($request->getContent(), true);
        $content = $data['content'];
        $mainButtons = ['급식', '학교 일정', '운세'];

        // 버튼 리스트
        $mealCase = ['오늘 급식', '내일 급식', '돌아가기'];
        $scheduleCase = [Carbon::now()->addMonth(-1)->format('Y-m'), $carbon->now()->format('Y-m'), $carbon->addMonth()->format('Y-m'), $carbon->addMonth()->format('Y-m'), $carbon->addMonth()->format('Y-m'), $carbon->addMonth()->format('Y-m'), '돌아가기'];
        $luckCase = ['숫자 운세', '오늘 운세'];
        $luckAnimalsCase = ['쥐띠', '소띠', '호랑이띠','토끼띠', '용띠', '뱀띠', '말띠', '양띠', '원숭이띠', '닭띠', '개띠', '돼지띠'];

        // $content = '쥐띠';

        switch($content) {
            case '돌아가기': {
                $data = [
                    'message' => [
                        'text' => '초기 화면입니다.',
                    ],
                    'keyboard' => [
                        'type' => 'buttons',
                        'buttons' => $mainButtons
                    ]
                ];
                return response()->json($data);
                break;
            }

            /**
             * 급식 시작
             * ['오늘 급식', '내일 급식', '돌아가기']
             */

            case '급식': {
                $data = [
                    'message' => [
                        'text' => '언제 급식을 알고 싶으세요 ?',
                    ],
                    'keyboard' => [
                        'type' => 'buttons',
                        'buttons' => $mealCase
                    ]
                ];
                return response()->json($data);
                break;
            }
            case in_array($content, $mealCase): {
                $meal = $this->multiMessage->meal($content);
        
                $data = [
                    'message' => [
                        'text' => $meal,
                    ],
                    'keyboard' => [
                        'type' => 'buttons',
                        'buttons' => $mealCase
                    ]
                ];
                return response()->json($data);
                break;
            }
            
            /**
             * 급식 끝
             * 
             * 학교 일정 
             * ['한달전', '이번달', '다음달', '다다음달', '다다다음달', '돌아가기']
             */

            case '학교 일정': {
                $data = [
                    'message' => [
                        'text' => '일정을 선택해주세요.',
                    ],
                    'keyboard' => [
                        'type' => 'buttons',
                        'buttons' => $scheduleCase
                    ]
                ];
                return response()->json($data);
                break;
            }
            case in_array($content, $scheduleCase): {
                $schedule = $this->multiMessage->schedule($content);

                $data = [
                    'message' => [
                        'text' => $schedule,
                    ],
                    'keyboard' => [
                        'type' => 'buttons',
                        'buttons' => $scheduleCase
                    ]
                ];
                return response()->json($data);
                break;
            }

            /**
             * 학교 일정 끝
             * 운세 추가
             */

            case '운세': {
                $data = [
                    'message' => [
                        'text' => '종류를 선택해주세요.',
                    ],
                    'keyboard' => [
                        'type' => 'buttons',
                        'buttons' => $luckCase
                    ]
                ];
                return response()->json($data);
                break;
            }
            case in_array($content, $luckCase): {
                switch($content) {
                    case '숫자 운세':
                    $luck = $this->multiMessage->intLuck();
                    $data = [
                        'message' => [
                            'text' => '행운점수는: ' . $luck . "점\n\n위 숫자는 1부터 100까지 랜덤으로 추출된 정수입니다. 점수가 100점에 근접할수록 운이 좋습니다.",
                        ],
                        'keyboard' => [
                            'type' => 'buttons',
                            'buttons' => $mainButtons
                        ]
                    ];
                    return response()->json($data);
                    break;

                    case '오늘 운세':
                    $data = [
                        'message' => [
                            'text' => '띠를 선택해주세요.',
                        ],
                        'keyboard' => [
                            'type' => 'buttons',
                            'buttons' => $luckAnimalsCase
                        ]
                    ];
                    break;
                }
                return response()->json($data);
                break;
            }
            case in_array($content, $luckAnimalsCase): {
                $luck = $this->multiMessage->animalLuck($luckAnimalsCase, $content);
                $data = [
                    'message' => [
                        'text' => $luck,
                    ],
                    'keyboard' => [
                        'type' => 'buttons',
                        'buttons' => $mainButtons
                    ]
                ];
                return response()->json($data);
                break;
            }

            /**
             * 운세 끝
             */

            /**
             * 예외처리 추가
             */

            default: {
                $data = [
                    'message' => [
                        'text' => '초기화면 입니다.'
                    ],
                    'keyboard' => [
                        'type' => 'buttons',
                        'buttons' => $mainButtons
                    ]
                ];
                return response()->json($data);
                break;
            }
        }
    }
}
