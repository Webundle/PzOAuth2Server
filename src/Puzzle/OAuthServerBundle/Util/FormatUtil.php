<?php

namespace Puzzle\OAuthServerBundle\Util;

use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Puzzle\OAuthServerBundle\Service\Negociator;

/**
 * FormatUtil 
 * 
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 */
class FormatUtil
{
	/**
	 * Pagination and Content negociation
	 *
	 * @param Request $request
	 * @param mixed $resources
	 * @return null|View
	 */
	public static function formatView(Request $request, $response, $code = 200) {
	    $resources = $response['resources'] ?? $response;
	    
	    if ($request->get('page')){
	        if (!is_array($resources)){
	            $resources->currentPage = $response['currentPage'];
	            $resources->lastPage = $response['lastPage'];
	        }else {
	            foreach ($resources as $item){
	                $item->currentPage = $response['currentPage'];
	                $item->lastPage = $response['lastPage'];
	                $item->limit = $response['limit'];
	            }
	        }
	    }
	
	    $view = new View($resources, $code);
		
		if (!$request->get('_format')){
			$format = Negociator::content($request);
			$view->setFormat($format);
		}
	
		return $view;
	}
	
	/**
	 * Merge and compute difference
	 *
	 * @param array $array
	 * @param array $toAdd
	 * @param array $toRemove
	 *
	 * @return NULL|array
	 */
	public static function refreshArray(array $oldItems = null, array $itemsToAdd = null, array $itemsToRemove = null) {
	    $oldItems = $oldItems ?? [];
	    $itemsToAdd = $itemsToAdd ?? [];
	    $itemsToRemove = $itemsToRemove ?? [];
	    
	    $refreshItems = array_merge(array_diff($oldItems, $itemsToRemove), $itemsToAdd);
	    return count($refreshItems) == 0 ? null : array_unique($refreshItems);
	}
}