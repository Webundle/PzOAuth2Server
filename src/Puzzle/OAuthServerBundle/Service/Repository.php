<?php

namespace Puzzle\OAuthServerBundle\Service;

use Symfony\Component\HttpFoundation\ParameterBag;
use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Puzzle\OAuthServerBundle\Util\StringUtil;

/**
 * Repository
 * 
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 */
class Repository
{
	/**
	 * @var string
	 */
	private $entityName;
	
	/**
	 * @var string
	 */
	private $tableName;
	
	/**
	 * @var array
	 */
	private $filters;
	
	/**
	 * @var array
	 */
	private $filtersMEMBEROF;
	
	/**
	 * @var array
	 */
	private $excludes;
	
	/**
	 * @var integer
	 */
	private $defaultLimit;
	
	/**
	 * @var integer
	 */
	private $defaultPage;
	
	/**
	 * @var ParameterBag
	 */
	private $query;
	
	/**
	 * @var EntityManager
	 */
	private $entityManager;
	
	/**
	 * @var RegistryInterface
	 */
	private $doctrine;
	
	/**
	 * @param RegistryInterface $doctrine
	 */
	public function __construct(RegistryInterface $doctrine, $defaultLimit, $defaultPage, $excludes){
	    $this->doctrine = $doctrine;
	    $this->defaultLimit = $defaultLimit;
	    $this->defaultPage = $defaultPage;
	    $this->excludes = $excludes;
	}
	
	/**
	 * Filter a resource
	 * 
	 * @param ParameterBag $query
	 * @param string $entityName
	 * @param string $connection
	 * @return array
	 */
	public function filter(ParameterBag $query, $entityName, $connection = 'default')
	{
		$this->query = $query;
		$this->entityName = $entityName;
		$this->entityManager = $this->doctrine->getManager($connection);
		$this->tableName = $this->entityManager->getClassMetadata($this->entityName)->getTableName();
		$this->filters = $this->entityManager->getClassMetadata($this->entityName)->getFieldNames();
		
		// Construct DQL
		$response = self::constructCriteria();
		$dql = self::dqlFilter($response['criteria']);
		
		if (!$dql){
			return ['resources' => 'Error: Unknown Filters'];
		}
		
		// Fetch resources
		$resources = $this->entityManager
						  ->createQuery($dql)
						  ->setParameters($response['params'])
						  ->getResult();
		
		if (!$resources){
		    return ['resources' => null];
		}
		
		// Paginate resources
		$paginate = self::paginate($resources, $dql, $response['params']);
		$resources = $paginate['resources'];
		
		return [
				'resources' => $resources, 
				'currentPage' => $paginate['currentPage'], 
				'lastPage' => $paginate['lastPage'],
				'limit' => $paginate['limit']
		 ];
	}
	
	
	/**
	 * Prepare DQL for Filtering
	 * 
	 * @param string $criteria
	 * @return string
	 */
	public function dqlFilter($criteria) {
	    $fields = 'object';
		if ($this->query->get('fields')){
			$array = explode(',', $this->query->get('fields'));
			foreach ($array as $key => $item){
				$array[$key] = 'object.'.$item;
			}
		
			$fields = implode(',', $array);
		}
		
		$dql = 'SELECT '.$fields.' FROM '.$this->entityName. ' object ';
		$dql = ! $criteria ? $dql : $dql.'WHERE '.$criteria;
		// Order by
		if ($dql && $this->query->get('orderBy')){
			$array = explode(':', $this->query->get('orderBy'));
			$array[0] = StringUtil::transformToCamelCase($array[0]);
			
			$dql .= ' ORDER BY object.'.$array[0].' '.$array[1];
		}
		
		return $dql;
	}
	
	/**
	 * Construct criteria
	 * 
	 * @return array
	 */
	public function constructCriteria() {
		$criteria = '';
		$params = [];
		
		if ($filterString = $this->query->get('filter')) {
		    $arrayAND = explode(',', $filterString); // AND logic operator
		    
		    foreach ($arrayAND as $keyAND => $itemAND) {
		        $arrayOR = explode('|', $itemAND); // OR logic operator
		        $criteria .='(';
		        
		        foreach ($arrayOR as $keyOR => $itemOR) {
		            $response = self::getPartOfCriteria($itemOR, $criteria, $params);
		            $criteria = $response['criteria'];
		            $params = $response['params'];
		            
		            if ($keyOR < count($arrayOR) - 1) {
		                $criteria .= ' or ';
		            }
		        }
		        
		        $criteria .=')';
		        
		        if ($keyAND < count($arrayAND) - 1) {
		            $criteria .= ' and ';
		        }
		    }
		}
		
		return ['criteria' => $criteria, 'params' => $params];
	}
	
	
	/**
	 * Get part of criteria
	 * 
	 * @param  string   $expression
	 * @param  string   $criteria
	 * @param  array    $params
	 * @return array
	 */
	public function getPartOfCriteria(string $expression, string $criteria, array $params) {
// 	    $operator = preg_replace("#[a-z|A-Z|0-9|_|;]#",'',$expression);
	    $operators = ['=^', '=@', '!@', '=$', '=:', '!=', '<', '<=', '>', '>=', '=='];
	    $currentOperator = null;
	    
	    $array = [];
	    foreach ($operators as $operator) {
	        $array = explode($operator, $expression);
	        if (count($array) == 2) {
	            $currentOperator = $operator;
	            break;
	        }
	    }
	    
	    $key = StringUtil::transformToCamelCase($array[0]);
	    $value = $array[1];
	    
	    switch ($currentOperator) {
	        case "=^": // Begin by
	            $criteria .= "object.".$key." LIKE '" .$value. "%'";
	            break;
	        case "=@": // Contains
	            $criteria .= "object.".$key." LIKE '%" .$value. "%'";
	            break;
	        case "!@": // Not Contains
	            $criteria .= " NOT object.".$key." LIKE '%" .$value. "%'";
	            break;
	        case "=$": // End by
	            $criteria .= "object.".$key." LIKE '%" .$value. "'";
	            break;
	        case "=:": // IN
	            $array = explode(";", $value);
	            foreach ($array as $index => $item){
	                $array[$index] = "'".$item."'";
	            }
	            
	            $criteria .= 'object.'.$key.' IN ('.implode(",", $array).')';
	            break;
	        case "!=": // NOT
	            $olds = preg_grep("#:".$key."#", array_keys($params));
	            $keyToReplace = $key.'_'.count($olds);
	            $criteria .= "object.".$key." != :".$keyToReplace;
	            $params[':'.$keyToReplace] = $value;
	            break;
	        case "<": // Lower than
	            $olds = preg_grep("#:".$key."#", array_keys($params));
	            $keyToReplace = $key.'_'.count($olds);
	            $criteria .= "object.".$key." < :".$keyToReplace;
	            $params[':'.$keyToReplace] = $value;
	            break;
	        case "<=": // Lower than
	            $olds = preg_grep("#:".$key."#", array_keys($params));
	            $key = $key.'_'.count($olds);
	            $criteria .= "object.".$key." <= :".$key;
	            $params[':'.$keyToReplace] = $value;
	            break;
	        case ">": // Greater than
	            $olds = preg_grep("#:".$key."#", array_keys($params));
	            $keyToReplace = $key.'_'.count($olds);
	            $criteria .= "object.".$key." > :".$keyToReplace;
	            $params[':'.$keyToReplace] = $value;
	            break;
	        case ">=": // Greater than
	            $olds = preg_grep("#:".$key."#", array_keys($params));
	            $keyToReplace = $key.'_'.count($olds);
	            $criteria .= "object.".$key." >= :".$keyToReplace;
	            $params[':'.$keyToReplace] = $value;
	            break;
	        default: // Equal
	            if ($value === "NULL") {
	                $criteria .= "object.".$key." IS NULL";
	            }else {
	                $olds = preg_grep("#:".$key."#", array_keys($params));
	                $keyToReplace = $key.'_'.count($olds);
	                $criteria .= "object.".$key." = :".$keyToReplace;
	                $params[':'.$keyToReplace] = $value;
	            }
	            break;
	    }
	    
	    return ['criteria' => $criteria, 'params' => $params];
	}
	
	
	/**
	 * Paginate results if necessary
	 * 
	 * @param mixed $resources
	 * @param string $dql
	 * @param array $params
	 * @return number[]|mixed[]|\Doctrine\DBAL\Driver\Statement[]|array[]|NULL[]
	 */
	public function paginate($resources, string $dql, array $params) {
		// Limit Results
		if(! $this->query->get('limit')){
			$limit = $this->defaultLimit;
		}else{
			if($this->query->get('limit') <= count($resources)){
				$limit = $this->query->get('limit');
			}else{
				$limit = count($resources);
			}
		}

		// Pagination
		$totalPages = round(count($resources) / $limit);
		
		$currentPage = $this->query->get('page');
		$currentPage = $currentPage && ($currentPage > 0 || $currentPage < $totalPages ) ? $currentPage : $this->defaultPage;
		$currentPage = $currentPage > $totalPages ? $totalPages : $currentPage;
		$currentPage = $currentPage == 0 ? 1 : $currentPage;
		
		$offset = ($currentPage - 1) * $limit;
		
		$resources = $this->entityManager
					      ->createQuery($dql)
						  ->setFirstResult($offset)
						  ->setMaxResults($limit)
						  ->setParameters($params)
						  ->getResult();
       
		return [
				'resources' => $resources, 
				'currentPage' => $currentPage, 
				'lastPage' => $totalPages,
				'limit'	=> $limit
		];
	}
	
	/**
	 * Find field
	 * 
	 * @param string $field
	 * @param string $id
	 * @param string $entityName
	 * @param string $connection
	 * @return array
	 */
	public function findField(string $field, string $id, string $entityName, string $connection = 'default')
	{
		$this->entityManager = $this->doctrine->getManager($connection);
		$this->entityName = $entityName;
		$this->tableName = $this->entityManager->getClassMetadata($this->entityName)->getTableName();
	
		$sql = 'SELECT '.$field. ' FROM '.$this->tableName;
		$params= [];
		
		if ($id !== null){
			$sql .= ' WHERE id =:id';
			$params = [':id' => $id];
		}
		
		$conn = $this->entityManager->getConnection();
		$query = $conn->prepare($sql);
		$query->execute($params);
	
		return $query->fetchAll(\PDO::FETCH_ASSOC);
	}
}