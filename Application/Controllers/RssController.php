<?php
/**
 * RSS Controller
 * 
 * Manages the RSS feeds
 *
 * @author Valentin Bora <contact@valentinbora.com>
 * @version 1.0
 * @package Joobsbox_Controllers
 */
 
/**
 * @package Joobsbox_Controllers
 */

class RssController extends Zend_Controller_Action {
	protected $_model;
	
	public function __call($method, $args) {
		if(!method_exists($this, $method)) {
			$this->_forward("index");
		}
	}
	
	public function indexAction(){
		require_once "Application/Models/Jobs.php";
		
		$conf			= Zend_Registry::get("conf");
		$conf			= $conf['rss'];
		$params 		= $this->getRequest()->getParams();
		$this->_model 	= new Model_Jobs();
		$allJobs 		= false;
		
		if(isset($params['categorie'])) {
			$category = $this->_model->fetchCategories()->getCategory($params['categorie']);
			if($category) {
				$categoryId = $category->getProperty('ID');
				$jobs = $this->_model->fetchAllJobs(0)
									 ->order('ID DESC')
									 ->where("CategoryID = '$categoryId'")
									 ->limit($conf['ALL_JOBS_COUNT'], 0)
									 ->fetch();
				$jobs = $jobs->toArray();
				$lastUpdate = strtotime($jobs[0]['ChangedDate']);
			} else {
				header("HTTP/1.0 404 Not Found", true, 404);
                header("Status: 404 Not Found", true, 404);
				exit();
			}
		} else {
			$allJobs = true;
			$jobs = $this->_model->fetchAllJobs(0)
								 ->order('ID DESC')
								 ->limit($conf['ALL_JOBS_COUNT'], 0)
								 ->fetch();
			$jobs = $jobs->toArray();
			$lastUpdate = strtotime($jobs[0]['ChangedDate']);
		}
		
		// Generate the feed
		$siteUrl = "http://" . $_SERVER["HTTP_HOST"];
		$data		= array(
			"title"			  => $conf['COMMON_TITLE'] . " - " . (($allJobs) ? ("Toate joburile") : ($params['categorie'])),
			"link"			  => $siteUrl . $_SERVER["REQUEST_URI"],
			"lastUpdate"	  => $lastUpdate,
			'charset'		  => 'utf-8',
			'description'	  => $conf['DESCRIPTION'],
			'entries'		  => array()
		);
		foreach($jobs as $job) {
			$data['entries'][] = array(
				'title'		  => html_entity_decode($job['Title'], ENT_QUOTES, "UTF-8"),
				'link'		  => $siteUrl . $this->view->baseUrl . '/' . $this->view->MakeLink($job['Title']) . '-' . $job['ID'] . '.html',
				'description' => nl2br(strip_tags(html_entity_decode($job['Description'], ENT_QUOTES, "UTF-8"))),
			);
		}
		
		$feed = Zend_Feed::importArray($data, 'rss');
		echo $feed->saveXml();
		exit();
	}
}


