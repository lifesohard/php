<?php
class DateTimeDiff {

    const MONDAY = 1;
    const TUESDAY = 2;
    const WEDNESDAY = 3;
    const THURSDAY = 4;
    const FRIDAY = 5;
    const SATURDAY = 6;
    const SUNDAY = 7;

    private $date;

    private $holidays;

    private $nonBusinessDays;

    private $specialBusinessDay;
    public function __construct( $startDate,  $holidays = [],  $nonBusinessDays = [],  $specialBusinessDay = [])
    {
        $this->date = $startDate;
        $this->holidays = [];
        foreach ($holidays as $holiday) {
            array_push($this->holidays, new DateTime($holiday));
        }

        $this->nonBusinessDays = $nonBusinessDays;
        $this->specialBusinessDay = $specialBusinessDay;
    }

    public function addBusinessDays($howManyDays)
    {
        $i = 0;
        while ($i < $howManyDays) {
            $this->date->modify("+1 day");
            if ($this->isBusinessDay($this->date)) {
                $i++;
            }
        }
    }

    public function getDate()
    {
        return $this->date->format('Y-m-d H:i:s');
    }

    private function isBusinessDay( $date)
    {
        if (in_array($date->format('Y-m-d'), $this->specialBusinessDay)) {
            return true; //判断当前日期是否是因法定节假日调休而上班的周末，这种情况也算工作日
        }
        if (in_array((int)$date->format('N'), $this->nonBusinessDays)) {
            return false; //当前日期是周末
        }
        foreach ($this->holidays as $day) {
            if ($date->format('Y-m-d') == $day->format('Y-m-d')) {
                return false; //当前日期是法定节假日
            }
        }
        return true;
    }
}
