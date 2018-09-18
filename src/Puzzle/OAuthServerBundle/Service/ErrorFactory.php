<?php

namespace Puzzle\OAuthServerBundle\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\TranslatorInterface;
use Puzzle\OAuthServerBundle\Util\FormatUtil;

/**
 * Error Factory
 * 
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 */
class ErrorFactory
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;
    
    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator){
        $this->translator = $translator;
    }
    
    public function accessDenied(Request $request, $message = null){
        return self::factory($request, 403, $message);
    }
    
    public function notFound(Request $request, $message = null){
        return self::factory($request, 404, $message);
    }
    
    public function badRequest(Request $request, $message = null){
        return self::factory($request, 400, $message);
    }
    
    public function factory(Request $request, $code, $message = null){
        $message = $message ?? $this->translator->trans('message.'.$code.'.default', [], 'error');
        $response = ['code' => $code, 'message' => $message];
        return FormatUtil::formatView($request, $response, $code);
    }
}