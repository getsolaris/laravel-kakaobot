<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Goutte;

class MultiMessageController extends Controller
{
    public function __construct($country = 'stu.sen.go.kr', $code = 'B100000599', $school = 'high') {
        $this->country = $country;
        $this->code = $code;

        if ($school == 'high') $this->school = 4;
        elseif ($school == 'middle') $this->school = 3;
        else throw new \LogicException('존재하지 않는 학교 종류입니다.');
    }

    public function meal($date) {
        $url = 'https://' . $this->country . '/sts_sci_md00_001.do?schulCode=' . $this->code . '&schulCrseScCode=' . $this->school . '&schMmealScCode=2';
        
        $crawler = Goutte::request('GET', $url);
        $result = $crawler->filter('tbody tr td div')->each(function ($content){
            $text = $content->text();
            
            $text = preg_replace('/[\d]|\./',' ', $text);
            $text = preg_replace('/([\s])\1+/', ' ', $text);

            /**
             * 급식 앞에 [중식] 단어를 제거하고 싶을때 아래의 주석 해제
             */

            // $text = preg_replace('/\[[^\]]*\]/','',$text); 
            
            $text = preg_replace('/\( \)/','',$text);

            if (strstr($text, '밥')) {
                $search = strpos($text, '밥');
                $back = strstr($text, '밥', 0);

                return trim(substr($text, 0, $search+3) . ' ' . substr($back, 3));
            } else {
                return trim($text);
            }
        });
        

        switch($date) {
            case '오늘 급식': 
                $content = $result[Carbon::today()->format('j')-1];
                return empty($content) ? '오늘 급식이 없습니다.' : $content;
            
            case '내일 급식': 
                $content = $result[Carbon::tomorrow()->format('j')-1];
                return empty($content) ? '내일 급식이 없습니다.' : $content;
            
        }
    } 

    public function schedule($month) {
        $carbon = Carbon::now();
        if($month == '다음달')
            $carbon = $carbon->addMonth();
        elseif($month == '다다음달')
            $carbon = $carbon->addMonth(2);
        elseif($month == '다다다음달')
            $carbon = $carbon->addMonth(3);
        elseif($month == '한달전')
            $carbon = $carbon->addMonth(-1);
        else
            $carbon = Carbon::now();
    

        $url = 'https://' . $this->country . '/sts_sci_sf01_001.do?schulCode=' . $this->code . '&schulCrseScCode=' . $this->school . '&schulKndScCode=4&ay=' . $carbon->year . '&mm=' . $carbon->format('m');
        
        $crawler = Goutte::request('GET', $url);
        $result = $crawler->filter('tbody tr td .textL')->each(function ($content){
            return preg_replace('/\s+/', ' ', trim($content->text()));
        });
        return implode("\n",$result);
    }
}
