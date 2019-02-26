<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Helpers\RequestsParseHelper;
use App\Helpers\CommerceImagesHelper;
use App\Helpers\StatsHelper;
use App\Models\LinkModel;
use \Illuminate\Http\Request;
use \Illuminate\Http\Response;
use \Illuminate\Http\RedirectResponse;
use Cookie;

class LinksController extends Controller
{
    const STATIC_PAGES_URI = [
        // Set static pages URI here  
    ];

    const STATS_COOKIE_NAME = 'statinfo';

    /**
     * add добавление ссылки
     *
     * @param  Request $request
     *
     * @return void
     */
    public function add(Request $request)
    {
        $linkData = RequestsParseHelper::parseAddLinkPayload($request);

        $lm = new LinkModel;

        $resultID = $lm->addLink($linkData);
        $result = $lm->getLinkByID($resultID);

        $rsPayload = [
            'dstLink'   => $result->destinationLink,
            'shortLink' => $result->sourceLink,
            'statsLink' => $result->statsLink
        ];

        if($result->expireDate instanceof \DateTime) {
            $rsPayload['expire'] = $result->expireDate->format('Y-m-d');
        } else {
            $rsPayload['expire'] = 'неограничено';
        }

        return response()->json($rsPayload);
    }

    /**
     * go переход по ссылке
     *
     * @param  mixed $request
     *
     * @return void
     */
    public function go(Request $request)
    {
        $uri = (string)$request->route('path');
        
        if(in_array($uri, self::STATIC_PAGES_URI)) {
            return $this->showStaticPage($uri);
        } else {
            return $this->processLink($uri);
        }
    }

    private function showStaticPage(string $page)
    {
        return response('No static pages available');
    }

    private function processLink(string $uri)
    {
        $cookieStat = Cookie::get(self::STATS_COOKIE_NAME);
        $newCookie = null;
        
        if(empty($cookieStat)) {
            $cookieStat = bin2hex(random_bytes(10));
            $newCookie = Cookie::forever(
                self::STATS_COOKIE_NAME, 
                $cookieStat
            );
        }

        $lm = new LinkModel;
        $result = $lm->getLinkByURI($uri);

        if(empty($result)) {
            return $this->processResponse(
                'Объект не существует',
                'render',
                $newCookie,
                404
            );
        }

        if(!$result->state) {
            return $this->processResponse(
                'Ссылка была деактивирована',
                'render',
                $newCookie,
                400
            );
        }

        if(!is_null($result->expireDate)) {
            $now = new \DateTime;

            if($now > $result->expireDate) {
                return $this->processResponse(
                    'Срок жизни ссылки истек',
                    'render',
                    $newCookie,
                    400
                );
            }
        }

        if($result->isCommerce) {
            $randomImageId = rand(1,2);

            $data = [
                'image'         => CommerceImagesHelper::getBase64ImageByID($randomImageId),
                'destination'   => $result->destinationLink
            ];

            StatsHelper::addLinkTostats($result->linkId, $cookieStat, $randomImageId);

            return $this->processResponse(
                view('commerce_link', $data),
                'render',
                $newCookie
            );
        } else {
            StatsHelper::addLinkTostats($result->linkId, $cookieStat);

            return $this->processResponse(
                $result->destinationLink,
                'redirect',
                $newCookie
            );
        }
    }

    private function processResponse($content, $type, $cookie = null, $code = null)
    {
        if($type === 'render') {
            $response = new Response;
            $response->setContent($content);
            
            if(!empty($code)) {
                $response->setStatusCode($code);
            }
        } elseif($type === 'redirect') {
            $response = new RedirectResponse($content);

        } else {
            throw new \Exception('Invalid Response type');
        }

        if(!empty($cookie)) {
            $response->withCookie($cookie);
        }

        return $response;
    }
}