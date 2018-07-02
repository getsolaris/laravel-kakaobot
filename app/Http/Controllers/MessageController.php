<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
        $data = json_decode($request->getContent(), true);
        $content = $data['content'];
        $mainButtons = ['급식', '학교 일정'];

        // 버튼 리스트
        $mealCase = ['오늘 급식', '내일 급식', '돌아가기'];
        $scheduleCase = ['한달전', '이번달', '다음달', '다다음달', '다다다음달', '돌아가기'];

        // $content = '오늘 급식';

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
            case '오늘 급식': {
                $meal = $this->multiMessage->meal('오늘');
        
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
            case '내일 급식': {
                $meal = $this->multiMessage->meal('내일');
        
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
            case '한달전': {
                $schedule = $this->multiMessage->schedule('한달전');

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
            case '이번달': {
                $schedule = $this->multiMessage->schedule('이번달');

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
            case '다음달': {
                $schedule = $this->multiMessage->schedule('다음달');

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
            case '다다음달': {
                $schedule = $this->multiMessage->schedule('다다음달');

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
            case '다다다음달': {
                $schedule = $this->multiMessage->schedule('다다다음달');
                
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
        }
    }
}
