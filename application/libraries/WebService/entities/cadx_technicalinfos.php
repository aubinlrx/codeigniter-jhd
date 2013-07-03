<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CAdxTechnicalInfos {

	/**
	 * [$busy description]
	 * @var boolean
	 */
	public $busy;

	/**
	 * [$changeLanguage description]
	 * @var boolean
	 */
	public $changeLanguage;

	/**
	 * [$changeUserId description]
	 * @var boolean
	 */
	public $changeUserId;

	/**
	 * [$flushAdx description]
	 * @var boolean
	 */
	public $flushAdx;

	/**
	 * [$loadWebsDuration description]
	 * @var double
	 */
	public $loadWebsDuration;

	/**
	 * [$nbDistributionCycle description]
	 * @var int
	 */
	public $nbDistributionCycle;

	/**
	 * [$poolDistribDuration description]
	 * @var double
	 */
	public $poolDistribDuration;

	/**
	 * [$poolEntryIdx description]
	 * @var int
	 */
	public $poolEntryIdx;

	/**
	 * [$poolExecDuration description]
	 * @var double
	 */
	public $poolExecDuration;

	/**
	 * [$poolRequestDuration description]
	 * @var double
	 */
	public $poolRequestDuration;

	/**
	 * [$poolWaitDuration description]
	 * @var double
	 */
	public $poolWaitDuration;

	/**
	 * [$processReport description]
	 * @var string
	 */
	public $processReport;

	/**
	 * [$processReportSize description]
	 * @var int
	 */
	public $processReportSize;

	/**
	 * [$reloadWebs description]
	 * @var boolean
	 */
	public $reloadWebs;

	/**
	 * [$resumitAfterDBOpen description]
	 * @var boolean
	 */
	public $resumitAfterDBOpen;

	/**
	 * [$rowInDistribStack description]
	 * @var int
	 */
	public $rowInDistribStack;

	/**
	 * [$totalDuration description]
	 * @var double
	 */
	public $totalDuration;

	/**
	 * [$traceRequest description]
	 * @var string
	 */
	public $traceRequest;

	/**
	 * [$traceRequestSize description]
	 * @var int
	 */
	public $traceRequestSize;
}
