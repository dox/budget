<?php
class BudgetYear {
	private DateTime $today;

	public function __construct(?DateTime $date = null) {
		// If no date supplied, use today
		$this->today = $date ?? new DateTime();
	}

	/**
	 * Get the start of the current budget year
	 */
	public function getStart(): DateTime {
		$year = (int)$this->today->format('Y');

		// Budget year starts August 1st
		if ($this->today >= new DateTime("$year-08-01")) {
			return new DateTime("$year-08-01");
		} else {
			return new DateTime(($year-1) . "-08-01");
		}
	}

	/**
	 * Get the end of the current budget year
	 */
	public function getEnd(): DateTime {
		$start = $this->getStart();
		$end = clone $start;
		$end->modify('+1 year')->modify('-1 day'); // July 31st next year
		return $end;
	}

	/**
	 * Get start and end dates formatted for SQL (YYYY-MM-DD)
	 */
	public function getSqlRange(): array {
		return [
			'start' => $this->getStart()->format('Y-m-d'),
			'end'   => $this->getEnd()->format('Y-m-d'),
		];
	}
}