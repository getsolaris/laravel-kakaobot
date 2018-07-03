# Laravel 5 Chatbot for Kakao
Hi! artisan!!



라라벨 5.6 으로 개발된 카카오톡 채팅봇입니다. 현재 구현된 기능은 급식정보, 학사일정입니다.

교육청에서 제공되는 식단정보와 학사일정을 크롤링 해옵니다.


## Installation

```
$ git clone https://github.com/getsolaris/laravel-kakaobot.git
$ cd laravel-kakaobot
$ composer install
$ cp .env.example .env
$ php artisan key:generate
```

## Usage

### 교육청 코드 (country)

- 서울시 교육청 : stu.sen.go.kr
- 경기도 교육청 : stu.goe.go.kr
- 강원도 교육청 : stu.kwe.go.kr
- 전라남도 교육청 : stu.jne.go.kr
- 전라북도 교육청 : stu.jbe.go.kr
- 경상남도 교육청 : stu.gne.go.kr
- 경상북도 교육청 : stu.kbe.go.kr
- 부산광역시 교육청 : stu.pen.go.kr
- 제주자치도 교육청 : stu.jje.go.kr
- 충청남도 교육청 : stu.cne.go.kr
- 충청북도 교육청 : stu.cbe.go.kr
- 광주광역시 교육청 : stu.gen.go.kr
- 울산광역시 교육청 : stu.use.go.kr
- 대전광역시 교육청 : stu.dje.go.kr
- 인천광역시 교육청 : stu.ice.go.kr
- 대구광역시 교육청 : stu.dge.go.kr

### 학교 코드 (code)

- [학교 코드 보러가기](https://www.meatwatch.go.kr/biz/bm/sel/schoolListPopup.do)


### 코드 수정

```app/Http/Controller/MultiMessageController.php``` 11번째 줄, 생성자의 매개변수(country, code)를 자신의 고등학교에 알맞게 수정합니다.

- country : 교육청
- code : 자신의 학교 코드


```php
// app/Http/Controller/MultiMessageController.php
public function __construct($country = 'stu.sen.go.kr', $code = 'B100000599') {
    $this->country = $country;
    $this->code = $code;
}
```


### 업데이트 로그
- [18.07.03][#1] 예외 처리 추가 및 함수 인자 통일
- [18.07.03][#2] 급식 앞에 `[중식]` 키워드 제거 및 괄호 안에 공백 존재 경우 괄호를 포함한 공백 제거


## Support Us
Mingeun Kim mingeun.k.k[at]gmail[dot]com

## License
MIT license