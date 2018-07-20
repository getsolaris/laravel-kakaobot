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
        if(isset($month)) {
            $argv = explode('-', $month);
        }

        $url = 'https://' . $this->country . '/sts_sci_sf01_001.do?schulCode=' . $this->code . '&schulCrseScCode=' . $this->school . '&schulKndScCode=4&ay=' . $argv[0] . '&mm=' . $argv[1];
        
        $crawler = Goutte::request('GET', $url);
        $result = $crawler->filter('tbody tr td .textL')->each(function ($content){
            return preg_replace('/\s+/', ' ', trim($content->text()));
        });
        return implode("\n",$result);
    }

    public function animalLuck($cases, $animal) {
        $key = array_keys($cases, $animal);

        $url = 'https://fortune.nate.com/contents/freeunse/weekjiji.nate?jijiPage=0&jijiparam=' . sprintf('%02d', $key);
        
        $crawler = Goutte::request('GET', $url);

        $result = $crawler->filter('#con_box')->each(function ($content){
            $text = trim(preg_replace('/[\r\n]/', "\n", $content->text()));
            $text = str_replace(' ', '', $text);
            $text = str_replace("\n\n\n\n\n\n", '', $text);

            return $text;
        });

        return implode('', $result);
    }

    public function intLuck() {
        $num = rand(1, 100);

        return $num;
    }
}
