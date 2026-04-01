<?php
class BudgetYear {
	public DateTime $startDate;
	public DateTime $endDate;
	public int $year;

	/**
	 * Explicit budget year (Aug → Jul)
	 */
	public function __construct(int $year) {
		$this->year = $year;
		$this->startDate = new DateTime("$year-08-01 00:00:00");
		$this->endDate   = new DateTime(($year + 1) . "-07-31 23:59:59");
	}

	/**
	 * Factory: current budget year based on today's date
	 */
	public static function current(): self {
		$year = (int) date('Y');
		$today = new DateTime();

		if ($today < new DateTime("$year-08-01")) {
			$year--;
		}

		return new self($year);
	}
	
	public static function yearsAgo(int $years): self {
		return new self(self::current()->year - $years);
	}
	
	public static function yearsFromNow(int $years): self {
		return new self(self::current()->year + $years);
	}

	public static function dropdownOptions(int $yearsBack = 4, int $yearsForward = 0): array {
		$currentYear = self::current()->year;
		$options = [];

		for ($year = $currentYear + $yearsForward; $year >= $currentYear - $yearsBack; $year--) {
			$budgetYear = new self($year);
			$options[] = [
				'label' => $budgetYear->label(),
				'year' => $budgetYear->year,
			];
		}

		return $options;
	}

	public static function fromRequest(string $key = 'year'): self {
		$currentYear = self::current()->year;
		$selectedYear = filter_input(INPUT_POST, $key, FILTER_VALIDATE_INT);

		if ($selectedYear === false || $selectedYear === null) {
			$selectedYear = filter_input(INPUT_GET, $key, FILTER_VALIDATE_INT);
		}

		if ($selectedYear === false || $selectedYear === null) {
			$selectedYear = $currentYear;
		}

		return new self($selectedYear);
	}

	public static function fromDate(DateTimeInterface|string $date): self {
		$date = is_string($date) ? new DateTime($date) : DateTime::createFromInterface($date);
		$year = (int) $date->format('Y');

		if ($date < new DateTime($year . '-08-01 00:00:00')) {
			$year--;
		}

		return new self($year);
	}

	public function __toString(): string {
		return $this->startDate->format('Y-m-d')
			. ' to '
			. $this->endDate->format('Y-m-d');
	}

	public function label(): string {
		$currentYear = self::current()->year;

		return match ($this->year <=> $currentYear) {
			-1 => $currentYear - $this->year === 1 ? 'Last Year' : ($currentYear - $this->year) . ' Years Ago',
			0 => 'This Year',
			1 => $this->year - $currentYear === 1 ? 'Next Year' : 'In ' . ($this->year - $currentYear) . ' Years',
		};
	}
}
